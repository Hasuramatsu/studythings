<?php

class Top8Scrapper
{
    //Properties
    private string $strURL = 'https://www.mtgtop8.com/';
    private string $strDateRange;
    private string $strFormat;
    private int $iDecksAmount = 0; // Total amount of decks processed
    private array $arCardIgnoreList = ['Plains', 'Island', 'Swamp', 'Mountain', 'Forest'];

    public function __construct(string $format, string $dateRange)
    {
        $this->strFormat = $format;
        $this->strDateRange = $dateRange;
    }

    /** Scrap format's page for event links and extract ID
     *  Return all collected events' IDs
     */
    public function collectEvents() : array// Collect all event IDs with construction parameters
    {
        $strPageURL = $this->strURL . 'format?f=' . $this->strFormat . '&meta=' . $this->findRangeID($this->strDateRange) . '&cp=';
        $arEventIDs = [];
        $iNextPageNumber = 1;
        $bNextAvailable = true;

        while ($bNextAvailable) // Stop when next page link not found
        {
            //echo "Check page " . $iNextPageNumber . "\n";
            $strCurrentPageURL = $strPageURL . $iNextPageNumber; //New page URL
            //Looking for block containing events
            $obEventBlockNodes = $this->getElements($strCurrentPageURL, "table[@class='Stable']", "[last()]");
            //Collect events' nodes from found block
            $obEventNodes = $this->getElements($strCurrentPageURL, "td[@class='S14']", null, $obEventBlockNodes->item(1));
            foreach ($obEventNodes as $obEventNode)
            {   // Extract event ID
                $link = $obEventNode->firstChild->getAttribute('href');
                $exploded = explode('=', $link);
                $exploded = explode('&', $exploded[1]);
                $arEventIDs[] = (int)$exploded[0];
            }
          $bNextFound = false;
            //Looking for navigation block
          $obNavNodes = $this->getElements($strCurrentPageURL, "div[@class='Nav_norm']");
          foreach ($obNavNodes as $element)
          {
              if($element->textContent == "Next") //Node with link for next page
              {
                  $link = $element->firstChild->getAttribute('href');
                  $exploded = explode('=', $link);
                  $iNextPageNumber = $exploded[3];
                  $bNextFound = true;
              }
          }
          $bNextAvailable = $bNextFound;
        }
        //echo "Checked " . $iNextPageNumber . " pages";
        return $arEventIDs;
    }

    /** Scrap event's page for deck links and extract ID
     *  Return all collected decks' IDs
     */
    public function scrapEvent(int $iEventID) : array//Return all deck IDs in event
    {
        $strEventURL = $this->strURL . 'event?e=' . $iEventID . '&f=' . $this->strFormat;

        //echo "Start scraping event " . $eventId . "\n";
        $arDeckIDs = [];
        // Collect deck IDs and iterate through them
        $obElements = $this->getElements($strEventURL, 'div[@class="hover_tr" or @class="chosen_tr"]');
        foreach ($obElements as $obElement)
        {   //Extract deck's IDs
            $strLink = $obElement->getElementsByTagName('a')->item(1)->getAttribute('href');
            $exploded = explode('=', $strLink);
            $exploded = explode('&', $exploded[2]);
            $arDeckIDs[] = (int)$exploded[0];
        }
        return $arDeckIDs;
    }

    /** Scrap deck for cards' info and return as array
     */
    public function scrapDeck(int $eventId, int $deckId) : array//Return card array
    {
        $strDeckURL = $this->strURL . 'event?e=' . $eventId . '&d='. $deckId . '&f=' . $this->strFormat;
        $arDeckList = [];
        $bSideboard = false;
        //echo "Start scraping deck " . $deckId . "\n";
        // Looking for "card lines"
        $obElements = $this->getElements($strDeckURL, 'div[@class="deck_line hover_tr"]');
        foreach ($obElements as $element)
        {
            //All cards after SIDEBOARD string are sideboard cards
            if(!is_null($element->previousSibling) &&
                        $element->previousSibling->textContent == 'SIDEBOARD') {
                $bSideboard = true;
            }
            //Break line for amount and name
            $arCardData = explode(' ', $element->textContent);
            $iCardAmount = (int)$arCardData[0]; // Amount of card in line
            array_shift($arCardData);// Remove 1st element which is 'amount'
            $strCardName = trim(implode(' ', $arCardData)); //Combine name to string

            if (in_array($strCardName, $this->arCardIgnoreList)) continue; //Skip ignored cards
            $arAmountArray = array('InDeck'=>0, 'InSide'=>0, 'TotalDecks'=>1);
            if($bSideboard) {
                $arAmountArray['InSide'] += $iCardAmount;
            }else {
                $arAmountArray['InDeck'] += $iCardAmount;
            }
            if(in_array($strCardName, $arDeckList)) { // If card is in array - add value
                $arDeckList[$strCardName]['InDeck'] += $arAmountArray['InDeck'];
                $arDeckList[$strCardName]['InSide'] += $arAmountArray['InSide'];
            }else{ // else - add in array
                $arDeckList[$strCardName] = $arAmountArray;
                $arDeckList[$strCardName]["CardName"] = $strCardName;
            }
        }
        return $arDeckList;
    }

    /** Scrap format page for events, then events for deck, and then deck for cards.
     *  Return cards' info array.
     */
    public function startScrapping(): array
    {
        $arEvents = [];
        $arCardList = [];

        $arEventIDs = $this->collectEvents();
        foreach ($arEventIDs as $iEventID)
        {
            $arEvents[$iEventID] = $this->scrapEvent($iEventID);
        }

        foreach ($arEvents as $iEventID => $arDeckIDs)
        {
            foreach ($arDeckIDs as $iDeckID)
            {
                $this->iDecksAmount++; // Increment processed decks
                $arDeckList = $this->scrapDeck($iEventID, $iDeckID);
                foreach ($arDeckList as $strCardName => $arAmountArray)
                {
                    if(!array_key_exists($strCardName, $arCardList)) //Card not added yet
                    {
                        //Add empty card element
                        $arCardList[$strCardName]['CardName'] = $strCardName;
                        $arCardList[$strCardName]["InDeck"] = 0;
                        $arCardList[$strCardName]["InSide"] = 0;
                        $arCardList[$strCardName]["TotalDecks"] = 0;
                    }
                    //Sum cards' info
                    foreach ($arAmountArray as $strAmountKey => $mAmountValue)
                    {
                        if ($strAmountKey == "CardName") continue; // Skip sum name
                        $arCardList[$strCardName][$strAmountKey] = $arCardList[$strCardName][$strAmountKey] + $mAmountValue;
                    }
                }
            }
        }
        return $arCardList;
    }

    /** Return amount of processed decks */
    public function getIDecksAmount() : int
    {
        return $this->iDecksAmount;
    }

    /** Helper function for searching nodes.
     *  Construct DOMXPath object and call query with params.
     *  Return result.
     */
    private function getElements(string $url, string $expression, string $expParams = null, $contextNode = null) //Return all Nodes of given expression
    {
        usleep(100000);
        $strPage = file_get_contents($url);
        $obDom = new DOMDocument();
        @$obDom->loadHTML($strPage);
        $obXPath = new DOMXPath($obDom);

        //Check if we need to use namespaces
        $strNS = $obDom->documentElement->namespaceURI;
        if ($strNS)
        {
            $obXPath->registerNamespace('ns', $strNS);
            $expString = '//ns:' . $expression;
        }
        else {$expString = '//' . $expression;}
        //Apply parameters if needed
        if (!empty($expParams)) {$expString = '(' . $expString . ')' . $expParams;}
        if ($contextNode) {return $obXPath->query($expString, $contextNode);}

        return $obXPath->query($expString);
    }

    /** Helper function for convert DateRange string into id */
    public function findRangeID(string $strDateRange) : int
    {
        $strURL = "https://www.mtgtop8.com/format?f=" . $this->strFormat;
        $iSafeCode = 0; // Safe code to return if DateRange for format not exist

        //Looking for element of dropdown menu with range
        $obLinks = $this->getElements($strURL, 'div[@class="S14 hover_tr"]');
        // Get first found element, it's parent is a link, which parent is dropdown menu
        foreach ($obLinks[0]->parentNode->parentNode->childNodes as $child) {
            if ($child->childNodes->item(0)->textContent == $strDateRange) { // Looking for desired element
                $strCode = $child->getAttribute('href');
                $strCode = explode('=', $strCode)[2];
                return (int)explode('&', $strCode)[0];
            }elseif ($child->childNodes->item(0)->textContent == "Last 2 Months") { //It is safe range, cuz all formats have it
                $strSafeCode = $child->getAttribute('href');
                $strSafeCode = explode('=', $strSafeCode)[2];
                $iSafeCode = (int)explode('&', $strSafeCode)[0];
            }
        }
        // TODO Somehow message about safe code used
        return $iSafeCode;
    }

}
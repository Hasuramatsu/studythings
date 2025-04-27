<?php

namespace App\Scrapper;

use App\DataContainers\CardContainer;
use App\DataContainers\CardData;
use App\DataContainers\CardList;
use App\DataContainers\DeckList;
use DOMDocument;
use DOMXPath;


class Top8Scraper extends Scraper
{
    //Properties
    private string $strURL = 'https://www.mtgtop8.com/';
    //private ScrapedData $obScrapedData;// Total amount of decks processed
    private array $arCardIgnoreList = ['Plains', 'Island', 'Swamp', 'Mountain', 'Forest'];
    private string $strCurrentFormat = '';

    /** @var string[] $arScrappedTables */



    /** Scrap format's page for event links and extract ID
     *  Return all collected events' IDs
     */
    public function collectEvents(): array// Collect all event IDs with construction parameters
    {
        $strPageURL = $this->strURL . 'format?f=' . $this->strCurrentFormat . '&meta=' . $this->findRangeID($this->strDateRange) . '&cp=';
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
            foreach ($obEventNodes as $obEventNode) {   // Extract event ID
                $link = $obEventNode->firstChild->getAttribute('href');
                $exploded = explode('=', $link);
                $exploded = explode('&', $exploded[1]);
                $arEventIDs[] = (int)$exploded[0];
            }
            $bNextFound = false;
            //Looking for navigation block
            $obNavNodes = $this->getElements($strCurrentPageURL, "div[@class='Nav_norm']");
            foreach ($obNavNodes as $element) {
                if ($element->textContent == "Next") //Node with link for next page
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
    public function scrapEvent(int $iEventID): array//Return all deck IDs in event
    {
        $strEventURL = $this->strURL . 'event?e=' . $iEventID . '&f=' . $this->strCurrentFormat;

        //echo "Start scraping event " . $eventId . "\n";
        $arDeckIDs = [];
        // Collect deck IDs and iterate through them
        $obElements = $this->getElements($strEventURL, 'div[@class="hover_tr" or @class="chosen_tr"]');
        foreach ($obElements as $obElement) {   //Extract deck's IDs
            $strLink = $obElement->getElementsByTagName('a')->item(1)->getAttribute('href');
            $exploded = explode('=', $strLink);
            $exploded = explode('&', $exploded[2]);
            $arDeckIDs[] = (int)$exploded[0];
        }
        return $arDeckIDs;
    }

    /** Scrap deck for cards' info and return as CardContainer
     */
    public function scrapDeck(int $eventId, int $deckId): CardContainer
    {
        $strDeckURL = $this->strURL . 'event?e=' . $eventId . '&d=' . $deckId . '&f=' . $this->strCurrentFormat;
        $obDeckList = new DeckList();
        $bSideboard = false;
        //echo "Start scraping deck " . $deckId . "\n";
        // Looking for "card lines"
        $obElements = $this->getElements($strDeckURL, 'div[@class="deck_line hover_tr"]');
        foreach ($obElements as $element) {
            //All cards after SIDEBOARD string are sideboard cards
            if (!is_null($element->previousSibling) &&
                $element->previousSibling->textContent == 'SIDEBOARD') {
                $bSideboard = true;
            }
            //Break line for amount and name
            $arCardData = explode(' ', $element->textContent);
            $iCardAmount = (int)$arCardData[0]; // Amount of card in line
            array_shift($arCardData);// Remove 1st element which is 'amount'
            $strCardName = trim(implode(' ', $arCardData)); //Combine name to string

            if (in_array($strCardName, $this->arCardIgnoreList)) continue; //Skip ignored cards
            $obCard = new CardData($strCardName);
            if ($bSideboard) {
                $obCard->addSideQuantity($iCardAmount);
            } else {
                $obCard->addDeckQuantity($iCardAmount);
            }
            $obDeckList->addCard($obCard);
        }
        $obDeckList->incrementDeck();
        return $obDeckList;
    }

    /** Scrap format page for events, then events for deck, and then deck for cards.
     */
    public function run(): void
    {
        foreach ($this->arFormats as $strFormat) {
            $this->setCurrentFormat($strFormat);
            $iDecksAmount = 0;
            $obCardList = new CardList();
            $arEventIDs = $this->collectEvents();
            foreach ($arEventIDs as $iEventID) {
                $arDecksIDs = $this->scrapEvent($iEventID);
                foreach ($arDecksIDs as $iDeckID) {
                    $iDecksAmount++; // Increment processed decks
                    $obDeckList = $this->scrapDeck($iEventID, $iDeckID);
                    $obCardList->addContainer($obDeckList);
                }
            }
            $obScrapedData = new ScrapedData(
                $obCardList,
                $iDecksAmount,
                $this->strCurrentFormat,
                $this->strDateRange,
                $this->obDatabase);
            $strTableName = $obScrapedData->save();
            $this->addScrappedData($strTableName, $obScrapedData);
            $this->addScrapedTableName($strTableName);
        }
    }

    protected function addScrapedTableName(string $strTableName) : void
    {
        $this->arScrapedTablesNames[] = $strTableName;
    }

    protected function addScrappedData(string $strTableName, ScrapedData $obScrapedData) : void
    {
        $this->arScrapedData[$strTableName] = $obScrapedData;
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
        if ($strNS) {
            $obXPath->registerNamespace('ns', $strNS);
            $expString = '//ns:' . $expression;
        } else {
            $expString = '//' . $expression;
        }
        //Apply parameters if needed
        if (!empty($expParams)) {
            $expString = '(' . $expString . ')' . $expParams;
        }
        if ($contextNode) {
            return $obXPath->query($expString, $contextNode);
        }

        return $obXPath->query($expString);
    }

    private function setCurrentFormat(string $strNewFormat) : void
    {
        $this->strCurrentFormat = $strNewFormat;
    }

    /** Helper function for convert DateRange string into id */
    private function findRangeID(string $strDateRange): int
    {
        $strURL = "https://www.mtgtop8.com/format?f=" . $this->strCurrentFormat;
        $iSafeCode = 0; // Safe code to return if DateRange for format not exist

        //Looking for element of dropdown menu with range
        $obLinks = $this->getElements($strURL, 'div[@class="S14 hover_tr"]');
        // Get first found element, it's parent is a link, which parent is dropdown menu
        foreach ($obLinks[0]->parentNode->parentNode->childNodes as $child) {
            if ($child->childNodes->item(0)->textContent == $strDateRange) { // Looking for desired element
                $strCode = $child->getAttribute('href');
                $strCode = explode('=', $strCode)[2];
                return (int)explode('&', $strCode)[0];
            } elseif ($child->childNodes->item(0)->textContent == "Last 2 Months") { //It is safe range, cuz all formats have it
                $strSafeCode = $child->getAttribute('href');
                $strSafeCode = explode('=', $strSafeCode)[2];
                $iSafeCode = (int)explode('&', $strSafeCode)[0];
            }
        }
        // TODO Somehow message about safe code used
        return $iSafeCode;
    }

}
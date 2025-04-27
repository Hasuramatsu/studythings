<?php

namespace App\DataProcessors;

use App\Database;
use App\DataContainers\CardContainer;
use App\Scrapper\ScrapedData;

/** Class for calculation various stats of cards */
class DataProcessor
{
    private CardContainer $obCardList; //Array with cards' info
    private int $iDecksAmount; // Number of decks collected for data
    private float $fMaxUsePercent = 0;
    private string $strFormat;

    public function __construct(ScrapedData $obScrapedData, private readonly Database $obDatabase)
    {
        $this->obCardList = $obScrapedData->getCardList();
        $this->iDecksAmount = $obScrapedData->getScrappedCount();
        $this->strFormat = $obScrapedData->getFormat();
    }

    public function getCardList(): CardContainer
    {
        return $this->obCardList;
    }

    /** Calculate usage rate of a cards:
     *  Total amount of card / Amount of decks
     *  Save value to card's info array['UsePercent']
     */
    public function calculateUsageRate(): void
    {
        $arData = $this->obCardList->getArray();
        foreach ($arData as $key => $value) {
            $fUsePercent = $value->getTotalDecks() / $this->iDecksAmount;
            $this->obCardList->setUsePercent($key, $fUsePercent);
            if ($fUsePercent > $this->fMaxUsePercent) {
                //Save max percent for future calculations
                $this->fMaxUsePercent = $fUsePercent;
            }
        }
    }

    /** Calculate Staple value of cards:
     *  Card usage percent / Max usage percent;
     *  Save value to card's info array['StapleValue']
     */
    public function calculateStapleValue(): void
    {
        if ($this->fMaxUsePercent <= 0) return;
        $arData = $this->obCardList->getArray();
        foreach ($arData as $key => $value) {
            $this->obCardList->setStapleValue($key, $value->getUsePercent() / $this->fMaxUsePercent);
        }
    }

    /** Call all calculate methods and return array of cards' info */
    public function process(): void
    {
        $this->calculateUsageRate();
        $this->calculateStapleValue();
    }

    /**
     * Save processed data to table with passed name. If name not passed use default generated name.
     */
    public function saveData(string $strTableName = null): void
    {
        if ($strTableName == null) {
            $strTableName = $this->strFormat . " | " . date("d/m/y");
        }
        $this->obDatabase->createTable($strTableName);
        foreach ($this->obCardList->getArray() as $obCardData) {
            $this->obDatabase->insert($strTableName, $obCardData);
        }
    }
}
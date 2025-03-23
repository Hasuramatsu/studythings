<?php

/** Class for calculation various stats of cards */
class DataProcessor
{
    private array $arData = []; //Array with cards' info
    private int $iDecksAmount; // Number of decks collected for data
    private float $fMaxUsePercent = 0;

    public function __construct(array $data, int $deckNum)
    {
        $this->arData = $data;
        $this->iDecksAmount = $deckNum;
    }

    /** Calculate usage rate of a cards:
     *  Total amount of card / Amount of decks
     *  Save value to card's info array['UsePercent']
     */
    public function calcUsageRate(): void
    {
        foreach ($this->arData as $key=>$value)
        {
            $this->arData[$key]['UsePercent'] = $value['TotalDecks'] / $this->iDecksAmount;
            if ($this->arData[$key]['UsePercent'] > $this->fMaxUsePercent)
            {
                //Save max percent for future calculations
                $this->fMaxUsePercent =  $this->arData[$key]['UsePercent'];
            }
        }
    }

    /** Calculate Staple value of cards:
     *  Card usage percent / Max usage percent;
     *  Save value to card's info array['StapleValue']
     */
    public function calcStapleValue(): void
    {
        if ($this->fMaxUsePercent <= 0) return;
        foreach ($this->arData as $key => $value)
        {
            $this->arData[$key]['StapleValue'] =  $value['UsePercent'] / $this->fMaxUsePercent;
        }
    }

    /** Call all calculate methods and return array of cards' info */
    public function process(): array
    {
        $this->calcUsageRate();
        $this->calcStapleValue();
        return $this->arData;
    }
}
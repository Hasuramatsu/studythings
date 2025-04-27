<?php

namespace App\DataContainers;
class CardData
{
    public function __construct(private readonly string $strName,
                                private int             $iDeckQuantity = 0,
                                private int             $iSideQuantity = 0,
                                private int             $iTotalDecks = 0,
                                private float           $fUsePercent = 0.0,
                                private float           $fStapleValue = 0.0)
    {
    }

    /////////////////////
    /// Getters
    /// ////////////////
    public function getName(): string
    {
        return $this->strName;
    }

    public function getDeckQuantity(): int
    {
        return $this->iDeckQuantity;
    }

    public function getSideQuantity(): int
    {
        return $this->iSideQuantity;
    }

    public function getTotalDecks(): int
    {
        return $this->iTotalDecks;
    }

    public function getUsePercent(): float
    {
        return $this->fUsePercent;
    }

    public function getStapleValue(): float
    {
        return $this->fStapleValue;
    }

    ////////////////////
    /// Setters
    /// ////////////////

    public function setStapleValue(float $fStapleValue): void
    {
        $this->fStapleValue = $fStapleValue;
    }

    public function setUsePercent(float $fUsePercent): void
    {
        $this->fUsePercent = $fUsePercent;
    }

    public function addCardData(CardData $obCardData): void
    {
        $this->iDeckQuantity += $obCardData->getDeckQuantity();
        $this->iSideQuantity += $obCardData->getSideQuantity();
    }

    public function addDeckQuantity(int $iQuantity): void
    {
        $this->iDeckQuantity += $iQuantity;
    }

    public function addSideQuantity(int $iQuantity): void
    {
        $this->iSideQuantity += $iQuantity;
    }

    public function incrementTotalDecks(): void
    {
        $this->iTotalDecks += 1;
    }
}
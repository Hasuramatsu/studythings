<?php

namespace App\DataContainers;

//require 'vendor/autoload.php';

class DeckList extends CardContainer
{
    public function addCard(CardData $obCardData): void
    {
        if (!array_key_exists($obCardData->getName(), $this->arContainer)) {
            $this->arContainer[$obCardData->getName()] = $obCardData;
        } else {
            $this->arContainer[$obCardData->getName()]->addCardData($obCardData);
        }
    }

    public function incrementDeck(): void
    {
        foreach ($this->arContainer as $obCardData) {
            $obCardData->incrementTotalDecks();
        }
    }
}
<?php

namespace App\DataContainers;

//require APP_PATH . '/vendor/autoload.php';

class CardList extends CardContainer
{
    public function addContainer(CardContainer $obContainer): void
    {
        foreach ($obContainer->getArray() as $strName => $obCardData) {
            if (!array_key_exists($strName, $this->arContainer)) {
                $this->arContainer[$strName] = $obCardData;
            } else {
                $this->arContainer[$strName]->addCardData($obCardData);
                $this->arContainer[$strName]->incrementTotalDecks();
            }
        }
    }
}
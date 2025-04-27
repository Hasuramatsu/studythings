<?php

namespace App\DataContainers;

use App\Database;

//require 'vendor/autoload.php';

abstract class CardContainer
{
    /** @var CardData[] arContainer */
    protected array $arContainer = [];

    public function __construct()
    {
    }

    public function getArray(): array
    {
        return $this->arContainer;
    }

    public function setStapleValue(string $strElement, float $fValue): void
    {
        if (!isset($this->arContainer[$strElement])) return;
        $this->arContainer[$strElement]->setStapleValue($fValue);
    }

    public function setUsePercent(string $strElement, float $fValue): void
    {
        if (!isset($this->arContainer[$strElement])) return;
        $this->arContainer[$strElement]->setUsePercent($fValue);
    }

    public function loadFromTable(Database $obDatabase, string $strTableName): void
    {
        $arTableData = $obDatabase->select_all($strTableName);
        foreach ($arTableData->fetchAll(\PDO::FETCH_ASSOC) as $arData) {
            $arCardData = new CardData($arData['CardName'],
                $arData['InDeck'],
                $arData['InSide'],
                $arData['TotalDecks'],
                $arData['UsePercent'],
                $arData['StapleValue']);
            $this->arContainer[$arCardData->getName()] = $arCardData;
        }
    }
}
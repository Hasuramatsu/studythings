<?php

namespace App\Scrapper;

use App\Database;
use App\DataContainers\CardContainer;

class ScrapedData
{
    private string $strScrapedDate;

    public function __construct(private readonly CardContainer $obCardContainer,
                                private readonly int           $iScrappedCount,
                                private readonly string        $strFormat,
                                private readonly string        $strDateRange,
                                private readonly Database      $obDatabase)
    {
        $this->strScrapedDate = date("d/m/y");
    }

    public function getScrappedCount(): int
    {
        return $this->iScrappedCount;
    }

    public function getCardList(): CardContainer
    {
        return $this->obCardContainer;
    }

    public function getFormat(): string
    {
        return $this->strFormat;
    }

    public function getDateRange(): string
    {
        return $this->strDateRange;
    }

    public function getScrapedDate(): string
    {
        return $this->strScrapedDate;
    }

    public function save($strTableName = null): string
    {
        if ($strTableName == null) {
            $strTableName = $this->strFormat . " | " . date("d/m/y");
        }
        $this->obDatabase->createTable($strTableName);
        foreach ($this->obCardContainer->getArray() as $obCardData) {
            $this->obDatabase->insert($strTableName, $obCardData);
        }
        return $strTableName;
    }
}
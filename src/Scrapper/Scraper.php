<?php

namespace App\Scrapper;

use App\Database;
use App\Interfaces\IScraper;

abstract class Scraper implements IScraper
{
    protected array $arScrapedData;
    protected array $arScrapedTablesNames = [];


    /** @var string[] $arFormats */
    public function __construct(protected array $arFormats,protected string $strDateRange, protected Database $obDatabase){}

    abstract public function run(): void;
    abstract protected function addScrapedTableName(string $strTableName): void;
    abstract protected function addScrappedData(string $strTableName,ScrapedData $obScrapedData): void;

    public function getResult() : array
    {
        /** @var array{tableName:string, data:ScrapedData} $test */
        $test = $this->arScrapedData;
        return $test;
    }

    public function getTables() : array
    {
        return $this->arScrapedTablesNames;
    }

}
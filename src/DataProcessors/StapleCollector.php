<?php

namespace App\DataProcessors;
use App\Database;
use App\Downloader\StapleListFile;
use DateTime;
use const Classes\FORMATS_LIST;

class StapleCollector
{
    /** @var string[] $arLatestTables */
    private array $arLatestTables;

    /** @var string[] $arStapleList */
    private array $arStapleList = [];

    public float $fStapleValueFilter = 0.1;

    public function __construct(private readonly Database $obDatabase, array $arFormats = [])
    {
        $this->arLatestTables = $this->calculateLatestTables($arFormats);
    }

    private function calculateLatestTables(array $arFormats): array
    {
        $arFormats = sizeof($arFormats) > 0 ? $arFormats : FORMATS_LIST;
        $arTableNames = $this->obDatabase->getTableNames()->fetchAll();
        $tempTableNames = [];
        foreach ($arTableNames as $tableName) {
            $temp = explode(" | ", $tableName[0]);
            if (!in_array($temp[0], $arFormats)) {
                continue;
            }
            if (!key_exists($temp[0], $tempTableNames)) {
                $tempTableNames[$temp[0]] = [];
            }
            $tempTableNames[$temp[0]][] = DateTime::createFromFormat('d/m/y', $temp[1])->format('Y-m-d');
        }
        $arTableNames = null;

        foreach ($tempTableNames as $tempTableName => $tempTableData) {
            $latestDate = max($tempTableData);
            if ($latestDate) {
                $latestDate = new DateTime($latestDate);
                $latestDate = $latestDate->format("d/m/y");
            }
            $arTableNames[] = $tempTableName . ' | ' . $latestDate;
        }
        return $arTableNames;
    }

    public function updateLatestTables(array $arFormats): void
    {
        $this->arLatestTables = $this->calculateLatestTables($arFormats);
    }

    public function collectStaples(): void
    {
        foreach ($this->arLatestTables as $strTableName) {
            $arStaples = $this->obDatabase->query(
                "SELECT CardName FROM `" . $strTableName . "` WHERE StapleValue > {$this->fStapleValueFilter}");

            foreach ($arStaples->fetchAll() as $staple) {
                $bDuplicate = false;
                foreach ($this->arStapleList as $arItem) {
                    if ($arItem[0] == $staple['CardName']) {
                        $bDuplicate = true;
                        break;
                    }
                }
                if ($bDuplicate) {
                    continue;
                }
                $this->arStapleList[] = [$staple['CardName']];
            }
        }

    }

    public function getStapleListFile(string $strFileName = 'Staple'): StapleListFile
    {
        return new StapleListFile($strFileName, $this->arStapleList);
    }
}
<?php

use src\Classes\Database;

require 'vendor/autoload.php';

function calc_latest_tables(Database $db,array $formats = []): array //Return array with latest table name for each format
{
    $arFormats = sizeof($formats) > 0 ? $formats : ['st', 'pi', 'mo', 'pau', 'cedh', 'edh'];
    $tableNames = $db->getTableNames()->fetchAll();
    $tempTableNames = [];
    foreach ($tableNames as $tableName)
    {
        $temp = explode(" | ", $tableName[0]);
        if (!in_array($temp[0], $arFormats)){continue;}
        if (!key_exists($temp[0], $tempTableNames))
        {
            $tempTableNames[$temp[0]] = [];
        }
        $tempTableNames[$temp[0]][] = DateTime::createFromFormat('d/m/y', $temp[1])->format('Y-m-d');
    }
    $tableNames = null;

    foreach ($tempTableNames as $tempTableName => $tempTableData)
    {
        $latestDate = max($tempTableData);
        if ($latestDate)
        {
            $latestDate = new DateTime($latestDate);
            $latestDate = $latestDate->format("d/m/y");
        }
        $tableNames[] = $tempTableName . ' | ' . $latestDate;
    }
    return $tableNames;
}
function collect_staples(Database $db): array
{
    $arTableNames = calc_latest_tables($db);
    $arStapleList = [];

    foreach ($arTableNames as $table)
    {
        $arStaples = $db->query(
            "SELECT CardName FROM `" . $table . "` WHERE StapleValue > 0.1");

        foreach ($arStaples->fetchAll() as $staple) {
            $arStapleList[] = [$staple['CardName']];
        }
    }
    return $arStapleList;
}

function download_staples(array $staples,string $filename): void
{
    ob_end_clean();
    header('Content-type: application/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'.csv";');
    $csv = fopen('php://output', 'w');
    foreach ($staples as $staple)
    {
        fputs($csv, implode(',', $staple)."\n");
    }
    fclose($csv);
    ob_flush();
}
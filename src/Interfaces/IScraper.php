<?php

namespace App\Interfaces;

use App\Scrapper\ScrapedData;

interface IScraper
{
    public function run() : void;

    public function getResult() : array;

    public function getTables() : array;
}
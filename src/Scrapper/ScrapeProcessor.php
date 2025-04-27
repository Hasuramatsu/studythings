<?php

namespace App\Scrapper;

use App\Database;
use App\DataProcessors\DataProcessor;
use App\Request;

readonly class ScrapeProcessor
{
    public function __construct(
        private Database $obDatabase,
        private Request $obRequest){}

    public function run(array $arFormats): void
    {
        set_time_limit(9000);
        $obParser = new Top8Scraper($arFormats, $this->obRequest->postDateRange(), $this->obDatabase);
        $obParser->run();

        foreach ($obParser->getResult() as $obScrapedData) {
            $obProcessor = new DataProcessor($obScrapedData, $this->obDatabase);
            $obProcessor->process();
            $obProcessor->saveData();
        }
    }
}
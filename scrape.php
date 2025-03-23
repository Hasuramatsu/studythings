<?php
require_once "functions.php";

    ob_start();
    $strResultMsg = "Scrapped successfully.";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $arFormats = [];
        if(isset($_POST["formats"])){
            $arFormats = $_POST["formats"];
        }
        set_time_limit(9000);
        if (sizeof($arFormats) == 0) {
            echo "No formats selected. Scrapped none.<br>";;
            echo "<a href='/TestingThings/'>Return to main page.</a><br>";
            ob_flush();
        }else {
            echo "Processing...<br>";
            ob_end_flush();
            ob_flush();
            flush();
            ob_start();
            foreach ($arFormats as $format) {
                $parser = new Top8Scrapper($format, $_POST["date_range"]);
                $database = new Database();
                $strTableName = $format . " | " . date("d/m/y");

                $arCardList = $parser->startScrapping();
                $database->create_table($strTableName);
                $processor = new DataProcessor($arCardList, $parser->getIDecksAmount());
                $arCardList = $processor->process();
                foreach ($arCardList as $card) {
                    $database->insert($strTableName, $card);
                }
            }
            echo "Scrapping completed.<br>";
            echo "<a href='/TestingThings/'>Return to main page.</a><br>";
            echo "<a href='/TestingThings/download.php'>Download csv.</a><br>";
        }
}



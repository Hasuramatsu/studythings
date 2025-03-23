<?php
    require_once "functions.php";

    ob_start();
    $database = new Database();
    $arStaples = collect_staples($database);
    download_staples($arStaples, "staples");

    header("Location: /TestingThings/");

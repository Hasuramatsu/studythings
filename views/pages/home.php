<?php

use App\View\View;

$strAppPath = APP_PATH;

/** @var View $view */
$view->loadComponent('header');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<form  method="POST" action="../scrape">
    <div>
        <input type="checkbox" id = 'Standard' name = 'formats[]' value = 'st'>
        <label for = 'Standard'>Standard</label><br>
        <input type="checkbox" id = 'Pioneer' name = 'formats[]' value = 'pi'>
        <label for = 'Pioneer'>Pioneer</label><br>
        <input type="checkbox" id = 'Modern' name = 'formats[]' value = 'mo'>
        <label for = 'Modern'>Modern</label><br>
        <input type="checkbox" id = 'Pauper' name = 'formats[]' value = 'pau'>
        <label for = 'Pauper'>Pauper</label><br>
        <input type="checkbox" id = 'cEDH' name = 'formats[]' value = 'cedh'>
        <label for = 'cEDH'>cEDH</label><br>
        <input type="checkbox" id = 'dEDH' name = 'formats[]' value = 'edh'>
        <label for = 'dEDH'>dEDH</label><br>
    </div>
    <div>
        <label> Range
            <select name = 'date_range'>
                <option value="Last 2 Weeks">Last 2 Weeks</option>
                <option value="Last 2 Months">Last 2 Months</option>
        </label>
    </div>
    <div>
        <input type="submit" value="scrape">
    </div>
</form><br>
<a href="/download">Download table</a><br>
<a href="/test">Test Page</a><br>
<a href="/scrape">Scrape test link</a><br>

</body>
</html>

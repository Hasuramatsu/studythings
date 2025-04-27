<?php
define("APP_PATH", dirname(__DIR__));
const HOME_LINK = "<a href='/home'>Return to main page.</a><br>";

require APP_PATH . '/vendor/autoload.php';

use App\App;

$obApp= new App();
$obApp->run();
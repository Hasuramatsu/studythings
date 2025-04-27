<?php

use App\Session\Session;
use App\View\View;
/** @var View $view */
$view->loadComponent('header');

/** @var Session $session */
if (!$session->has('status_code')){
    echo 'Wrong request<br>';
    exit(HOME_LINK);
}

switch ($session->getFlash('status_code')) {
    case 0:
        echo "Scrapping completed.<br>";
        echo "<a href='/download'>Download csv.</a><br>";
        break;
    case 1:
        echo "Request method must be POST.<br>";
        break;
    case 2:
        echo "No formats selected. Scrapped none.<br>";
        break;
}

echo HOME_LINK;





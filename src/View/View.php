<?php

namespace App\View;

use App\Session\Session;

class View
{
    public function __construct(private readonly Session $obSession){}
    public function loadPage(string $strPageName) : void
    {
        extract($this->getServices());

        include_once APP_PATH . "/views/pages/$strPageName.php";
    }

    public function loadComponent(string $strComponentName) : void
    {
        include_once APP_PATH . "/views/components/$strComponentName.php";
    }

    private function getServices() : array
    {
        return [
            'view'=>$this,
            'session'=>$this->obSession,
        ];
    }
}
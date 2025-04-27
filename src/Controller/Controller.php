<?php

namespace App\Controller;

use App\Database;
use App\Request;
use App\Router\Redirect;
use App\Session\Session;
use App\View\View;

abstract class Controller
{
    private View $obView;
    private Request $obRequest;

    private Database $obDatabase;
    private Session $obSession;
    public function __construct()
    {
        //$this->obView = Container::getInstance()->obView;
    }
    abstract function index() : void;

    public function getRequest(): Request
    {
        return $this->obRequest;
    }

    public function setRequest(Request $obRequest): void
    {
        $this->obRequest = $obRequest;
    }

    public function getDatabase(): Database
    {
        return $this->obDatabase;
    }

    public function setDatabase(Database $obDatabase): void
    {
        $this->obDatabase = $obDatabase;
    }


    public function setView(View $obView): void
    {
        $this->obView = $obView;
    }

    public function getSession(): Session
    {
        return $this->obSession;
    }

    public function setSession(Session $obSession): void
    {
        $this->obSession = $obSession;
    }

    protected function view(string $strPageName) : void
    {
        $this->obView->loadPage($strPageName);
    }

    protected function redirect(string $strPageName) : void
    {
        Redirect::to($strPageName);
    }

    protected function redirectError(int $iCode, string $strPageUri) : void
    {
        $this->getSession()->set('status_code', $iCode);
        $this->redirect($strPageUri);
    }


}
<?php

namespace App\Container;

use App\Database;
use App\Request;
use App\Router\Router;
use App\Session\Session;
use App\View\View;

class Container
{
    private static Container $obInstance;
    public readonly Database $obDatabase;
    public readonly Router $obRouter;
    public readonly Request $obRequest;
    public readonly View $obView;
    public readonly Session $obSession;

    public function __construct()
    {
        $this->registerServices();
    }

    private function registerServices() : void
    {
        $this->obSession = new Session();
        $this->obDatabase = new Database();
        $this->obRequest = Request::createFromGlobals();
        $this->obView = new View($this->obSession);
        $this->obRouter = new Router(
            $this->obDatabase,
            $this->obView,
            $this->obRequest,
            $this->obSession);

    }

    public static function getInstance() : static
    {
        if (!isset(static::$obInstance)) {
            static::$obInstance = new Container();
        }
        return static::$obInstance;
    }
}
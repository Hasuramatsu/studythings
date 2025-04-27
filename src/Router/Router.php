<?php

namespace App\Router;

use App\Controller\Controller;
use App\Database;
use App\Session\Session;
use App\View\View;
use App\Request;
use JetBrains\PhpStorm\NoReturn;

class Router
{
    private array $arRoutes = [
        'GET' => [],
        'POST' => [],
    ];

    public function __construct(private readonly Database $obDatabase,
                                private readonly View $obView,
                                private readonly Request $obRequest,
                                private readonly Session $obSession)
    {
        $this->initRoutes();
    }

    public function dispatch(string $strURI, string $strMethod) : void
    {
        $obRoute = $this->findRoute($strURI, $strMethod);
        if (!$obRoute) {
            $this->routeNotFound();
        }

        if (is_array($obRoute->getAction())) {
            /** @var Controller $obController */
            [$obController, $obAction] = $obRoute->getAction();
            $obController = new $obController();

            call_user_func([$obController, 'setView'], $this->obView);
            call_user_func([$obController, 'setRequest'], $this->obRequest);
            call_user_func([$obController, 'setDatabase'], $this->obDatabase);
            call_user_func([$obController, 'setSession'], $this->obSession);
            call_user_func([$obController, $obAction]);
        } else {
            call_user_func($obRoute->getAction());
        }
    }

    #[NoReturn] private function routeNotFound() : void
    {
        echo '404 | Page not found<br>';
        exit(HOME_LINK);
    }

    private function findRoute(string $strURI, string $strMethod) : Route|false
    {
        if (!isset($this->arRoutes[$strMethod][$strURI])){
            return false;
        }

        return $this->arRoutes[$strMethod][$strURI];
    }
    private function initRoutes(): void
    {
        $arRoutes = $this->getRoutes();

        foreach ($arRoutes as $obRoute) {
            $this->arRoutes[$obRoute->getMethod()][$obRoute->getURI()] = $obRoute;
        }
    }

    /**
     * @return Route[]
     */
    private function getRoutes() : array
    {
        return require_once APP_PATH . '/config/routes.php';
    }

}
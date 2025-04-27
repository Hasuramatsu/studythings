<?php

namespace App;

use App\Container\Container;

class App
{
    public function run(): void
    {
        $obContainer = Container::getInstance();

        $obContainer->obRouter->dispatch(
            $obContainer->obRequest->uri(),
            $obContainer->obRequest->method());
    }
}
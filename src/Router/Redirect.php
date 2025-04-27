<?php

namespace App\Router;

class Redirect
{
    public static function to($url): void
    {
        header('Location: ' . $url);
    }
}
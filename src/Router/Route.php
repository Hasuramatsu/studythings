<?php

namespace App\Router;

class Route
{
    public function __construct(private readonly string $strURI,
    private readonly string $strMethod,
    private $action){}

    public function getURI(): string
    {
        return $this->strURI;
    }

    public function getMethod(): string
    {
        return $this->strMethod;
    }

    public function getAction() : mixed
    {
        return $this->action;
    }

    public static function get(string $strURI, $strAction): static
    {
        return new static($strURI, 'GET', $strAction);
    }

    public static function post(string $strURI, $strAction): static
    {
        return new static($strURI, 'POST', $strAction);
    }
}
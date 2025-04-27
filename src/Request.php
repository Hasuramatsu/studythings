<?php

namespace App;

readonly class Request
{
    public function __construct(private readonly array $arGet,
                                private readonly array $arPost,
                                private readonly array $arServer){}

    public static function createFromGlobals() : static
    {
        return new static($_GET, $_POST, $_SERVER);
    }

    public function get() : array
    {
        return $this->arGet;
    }

    public function post() : array
    {
        return $this->arPost;
    }

    public function method() : string
    {
        return $this->arServer['REQUEST_METHOD'];
    }

    public function uri() : string
    {
        return strtok($_SERVER['REQUEST_URI'], '?');
    }

    public function postFormats() : array|false
    {
        if(!isset($this->arPost['formats'])){
            return false;
        }
        return $this->arPost['formats'];
    }

    public function postDateRange() : array|false
    {
        if(!isset($this->arPost['dateRange'])){
            return false;
        }
        return $this->arPost['dateRange'];
    }
}
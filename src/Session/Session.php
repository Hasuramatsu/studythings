<?php

namespace App\Session;

class Session
{

    public function __construct()
    {
        session_start();
    }

    public function set($key, $value) : void
    {
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function getFlash($key, $default = null)
    {
        $value = $this->get($key, $default);
        $this->remove($key);
        return $value;
    }

    public function has($key) : bool
    {
        return isset($_SESSION[$key]);
    }

    public function remove($key) : void
    {
        unset($_SESSION[$key]);
    }
}
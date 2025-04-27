<?php

namespace App\Controller;

class HomeController extends Controller
{

    function index() : void
    {
        $this->view('home');
    }
}
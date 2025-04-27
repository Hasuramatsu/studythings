<?php

namespace App\Controller;

use App\Controller\Controller;

class TestController extends Controller
{

    function index(): void
    {
        $this->view('testpage');
    }
}
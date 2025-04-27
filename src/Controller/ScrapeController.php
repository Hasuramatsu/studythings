<?php

namespace App\Controller;

use App\Scrapper\ScrapeProcessor;


class ScrapeController extends Controller
{

    function index(): void
    {
        $this->view('scrape');
    }

    function scrape(): void
    {
        if ($this->getRequest()->method() != "POST") {
            $this->redirectError(1, '/scrape');
        }

        $arFormats = $this->getRequest()->postFormats() ?: [];
        if (sizeof($arFormats) == 0) {
            $this->redirectError(2, '/scrape');
        }

        (new ScrapeProcessor($this->getDatabase(), $this->getRequest()))->run($arFormats);
        $this->redirect('/scrape');
    }


}
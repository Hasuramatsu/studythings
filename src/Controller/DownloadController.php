<?php

namespace App\Controller;

use App\DataProcessors\StapleCollector;
use App\Downloader\Downloader;

class DownloadController extends Controller
{
    function index(): void
    {

        ob_start();

        $obStapleCollector = new StapleCollector($this->getDatabase());
        $obStapleCollector->collectStaples();
        $obDownloader = new Downloader($obStapleCollector->getStapleListFile());
        $obDownloader->download();

        $this->view('download');
    }
}
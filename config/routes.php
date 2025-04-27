<?php

use App\Controller\DownloadController;
use App\Controller\HomeController;
use App\Controller\ScrapeController;
use App\Controller\TestController;
use App\Router\Route;

return [
    Route::get('/home', [HomeController::class, 'index']),
    Route::get('/',  [HomeController::class, 'index']),
    Route::get('/download', [DownloadController::class, 'index']),
    Route::post('/scrape',[ScrapeController::class,'scrape']),
    Route::get('/scrape',[ScrapeController::class,'index']),
    Route::get('/test', [TestController::class, 'index']),
];
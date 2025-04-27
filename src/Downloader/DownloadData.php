<?php

namespace App\Downloader;
//require 'vendor/autoload.php';

readonly class DownloadData
{
    public function __construct(private string $strContentType,
                                private string $strFileName,
                                private string $strFileExtension,
                                private array  $arData)
    {
    }

    public function getContentType(): string
    {
        return $this->strContentType;
    }

    public function getFileName(): string
    {
        return $this->strFileName;
    }

    public function getFileExtension(): string
    {
        return $this->strFileExtension;
    }

    public function getData(): array
    {
        return $this->arData;
    }
}
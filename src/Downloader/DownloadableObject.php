<?php

namespace App\Downloader;

use Interfaces\IDownloadable;


abstract class DownloadableObject implements IDownloadable
{

    protected string $strFileExtension;
    protected string $strContentType;

    public function __construct(private readonly string $strFileName, private readonly array $arData)
    {
    }

    public function getFileName(): string
    {
        return $this->strFileName;
    }

    public function getFileExtension(): string
    {
        return $this->strFileExtension;
    }

    public function getContentType(): string
    {
        return $this->strContentType;
    }

    protected function getData(): array
    {
        return $this->arData;
    }
}
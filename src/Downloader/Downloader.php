<?php

namespace App\Downloader;

use Interfaces\IDownloadable;


readonly class Downloader
{
    public function __construct(private IDownloadable $obDownloadable){}

    public function download(): void
    {
        ob_end_clean();
        header("Content-type: {$this->obDownloadable->getContentType()}");
        header("Content-Disposition: attachment; filename={$this->obDownloadable->getFileName()}.{$this->obDownloadable->getFileExtension()};");
        $this->obDownloadable->execute();
        ob_flush();
    }
}
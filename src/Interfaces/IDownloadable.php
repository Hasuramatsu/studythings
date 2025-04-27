<?php

namespace Interfaces;

interface IDownloadable
{
    public function execute() : bool;
    public function validate() : bool;
    public function getContentType(): string;
    public function getFileName(): string;
    public function getFileExtension(): string;
}
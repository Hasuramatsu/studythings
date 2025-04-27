<?php

namespace App\Downloader;

class StapleListFile extends DownloadableObject
{
    public function __construct(string $strFileName, array $arData)
    {
        parent::__construct($strFileName, $arData);
        $this->strFileExtension = 'csv';
        $this->strContentType = 'application/csv';
    }

    public function execute(): bool
    {
        if (!$this->validate()) return false;
        $csv = fopen('php://output', 'w');
        foreach ($this->getData() as $staple) {
            fputs($csv, implode(',', $staple) . "\n");
        }
        return fclose($csv);
    }

    public function validate(): bool
    {
        return !empty($this->getData());
    }
}
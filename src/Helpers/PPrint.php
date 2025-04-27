<?php

namespace App\Helpers;

class PPrint
{
    public static function print($output) : void
    {
        echo "<pre>";
        print_r($output);
        echo "</pre>";
    }
}
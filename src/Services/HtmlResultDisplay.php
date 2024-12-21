<?php

namespace App\Services;

use App\Interfaces\ResultDisplay;

class HtmlResultDisplay implements ResultDisplay
{
    public function display($result): void
    {
        echo "<pre>" . htmlspecialchars(print_r($result, true)) . "</pre>";
    }
}
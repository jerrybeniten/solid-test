<?php

namespace App\Services;

use App\Interfaces\ResultDisplayInterface;

class HtmlResultDisplayService implements ResultDisplayInterface
{
    public function display($result): void
    {
        echo "<pre>" . htmlspecialchars(print_r($result, true)) . "</pre>";
    }
}

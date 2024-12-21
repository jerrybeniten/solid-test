<?php 

namespace App\Services;

use App\Interfaces\ErrorHandler;

class SimpleErrorHandler implements ErrorHandler
{
    public function handleError(string $message): void
    {
        echo "Error: " . $message . "\n";
    }
}

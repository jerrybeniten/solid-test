<?php 

namespace App\Services;

use App\Interfaces\ErrorHandlerInterface;

class ErrorHandlerService implements ErrorHandlerInterface
{
    public function handleError(string $message): void
    {
        echo "Error: " . $message . "\n";
    }
}

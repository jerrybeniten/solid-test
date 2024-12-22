<?php

namespace App\Interfaces;

interface ErrorHandlerInterface
{
    public function handleError(string $message): void;
}

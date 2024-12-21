<?php

namespace App\Interfaces;

interface ErrorHandler
{
    public function handleError(string $message): void;
}

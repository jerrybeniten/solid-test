<?php

namespace App\Interfaces;

/**
 * Interface for database interaction services.
 */
interface DatabaseInterface
{
    public function insertAuthors(array $data): void;
}

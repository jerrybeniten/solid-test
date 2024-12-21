<?php

namespace App\Interfaces;

interface SearchInterface
{
    public function search(string $query): array;
}

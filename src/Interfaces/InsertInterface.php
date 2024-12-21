<?php

namespace App\Interfaces;

interface InsertInterface
{
    public function upsert(): bool;
}

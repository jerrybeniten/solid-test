<?php

namespace App\Interfaces;

interface ResponseInterface
{
    public function send(array $data): void;
}

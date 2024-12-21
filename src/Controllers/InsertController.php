<?php

namespace App\Controllers;

use App\Models\InsertService;
use App\Models\SearchService;


class InsertController
{
    private $response;
    private $upsertService;

    public function __construct(InsertService $upsertService)
    {
        $this->upsertService = $upsertService;
    }

    public function handleRequest()
    {
        $this->upsertService->upsert();
    }
}

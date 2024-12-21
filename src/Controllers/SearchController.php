<?php

namespace App\Controllers;

use App\Interfaces\ResponseInterface;
use App\Models\SearchService;


class SearchController
{
    private $response;
    private $searchService;

    public function __construct(ResponseInterface $response, SearchService $searchService)
    {
        $this->response = $response;
        $this->searchService = $searchService;
    }

    public function handleRequest(string $query)
    {        
        $results = $this->searchService->search($query);     
        $this->response->send($results);
    }
}

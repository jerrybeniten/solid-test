<?php

namespace App\Controllers;

use App\Interfaces\ResponseInterface;
use App\Interfaces\SearchInterface;

class SearchController
{
    private $response;
    private $searchService;

    public function __construct(ResponseInterface $response, SearchInterface $searchService)
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

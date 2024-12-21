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
        // Fetch the search results from the service
        $results = $this->searchService->search($query);

        // Send the response (could be JSON or HTML depending on the chosen implementation)
        $this->response->send($results);
    }
}

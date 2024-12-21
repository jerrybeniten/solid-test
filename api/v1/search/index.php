<?php

use App\Config\Config;
use App\Controllers\SearchController;
use App\Models\Database;
use App\Models\SearchService;
use App\Responses\JsonResponse;

require_once __DIR__ . '/../../../vendor/autoload.php';

$inputData = json_decode(file_get_contents('php://input'), true);
$query = $inputData['query'];

$dbConfig = Config::getDbConfig();
$database = new Database($dbConfig);
$searchService = new SearchService($database);
$response = new JsonResponse();
$controller = new SearchController($response, $searchService);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($query)) {   
    $controller->handleRequest($query);
} else {
    $response->send(['error' => 'Invalid request']);
}

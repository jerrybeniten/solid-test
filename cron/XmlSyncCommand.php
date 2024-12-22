<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Config;
use App\Controllers\InsertController;
use App\Models\Database;
use App\Models\InsertService;
use App\Services\ErrorHandlerService;
use App\Services\HtmlResultDisplayService;
use App\Services\XmlDirectoryReaderService;
use App\Services\XmlProcessorService;
use Cron\XmlFileHandler;

try {
    // Configurations
    $directoryConfig = Config::getDirectories();
    $dbConfig = Config::getDbConfig();

    // Scanner tool for all xml
    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($directoryConfig['xmlDirectory'])
    );

    // Returns the collection of XML objects
    $xmlReader = new XmlDirectoryReaderService(
        $iterator, 
        $directoryConfig['processedXmlDirectory'], 
        $directoryConfig['errorLog']);

    // Process all collection to get all unique data sets
    $processor = new XmlProcessorService(
        $directoryConfig['xmlDirectory'],        
        $xmlReader
    );

    $resultDisplay = new HtmlResultDisplayService();
    $errorHandler = new ErrorHandlerService();

    $fileHandler = new XmlFileHandler(
        $directoryConfig['xmlDirectory'],
        $directoryConfig['processedXmlDirectory'],
        $processor,
        $resultDisplay,
        $errorHandler
    );

    if (!$fileHandler->validateDirectories()) {
        echo "Error: One or more directories do not exist.\n";
        exit;
    }

    // Processed items
    $items = $fileHandler->processFiles();

    // Insert items to database
    $database = new Database($dbConfig);
    $upsertService = new InsertService($database, $items);
    $insertController = new InsertController($upsertService);
    $insertController->handleRequest();

    echo "[INFO] Done with the sync.\n";

} catch (Exception $e) {
    echo $e->getMessage();
}

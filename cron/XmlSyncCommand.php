<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Config;
use App\Models\Database;
use App\Models\InsertService;
use App\Services\HtmlResultDisplay;
use App\Services\SimpleErrorHandler;
use App\Services\XmlProcessor;
use Cron\XmlFileHandler;
use Lib\XmlHelper\XmlFileReader;

try {
    $directoryConfig = Config::getDirectories();
    $dbConfig = Config::getDbConfig();

    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($directoryConfig['xmlDirectory'])
    );

    $xmlReader = new XmlFileReader($iterator, $directoryConfig['processedXmlDirectory']);

    $processor = new XmlProcessor(
        $directoryConfig['xmlDirectory'],
        $directoryConfig['processedXmlDirectory'],
        $xmlReader
    );

    $resultDisplay = new HtmlResultDisplay();
    $errorHandler = new SimpleErrorHandler();

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

    $items = $fileHandler->processFiles();

    $database = new Database($dbConfig);
    $insertService = new InsertService($database, $items);
    $insertService->upsert();

    echo "[INFO] Done with the sync.\n";

} catch (Exception $e) {
    echo $e->getMessage();
}

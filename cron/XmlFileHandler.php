<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Config;
use App\Interfaces\ErrorHandler;
use DB\Database;
use App\Services\HtmlResultDisplay;
use App\Services\SimpleErrorHandler;
use App\Services\XmlProcessor;
use Lib\XmlHelper\XmlFileReader;

class XmlFileHandler
{
    private $xmlDirectory;
    private $processedDirectory;
    private $processor;
    private $resultDisplay;
    private $errorHandler;

    public function __construct(
        string $xmlDirectory,
        string $processedDirectory,
        XmlProcessor $processor,
        HtmlResultDisplay $resultDisplay,
        ErrorHandler $errorHandler
    ) {
        $this->xmlDirectory = $xmlDirectory;
        $this->processedDirectory = $processedDirectory;
        $this->processor = $processor;
        $this->resultDisplay = $resultDisplay;
        $this->errorHandler = $errorHandler;
    }

    public function validateDirectories(): bool
    {
        return is_dir($this->xmlDirectory) && is_dir($this->processedDirectory);
    }

    public function processFiles(): void
    {
        try {
            $result = $this->processor->process();
            $this->resultDisplay->display($result);
        } catch (Exception $e) {
            $this->errorHandler->handleError($e->getMessage());
        }
    }

    public function run(): void {}
}

$directoryConfig = Config::getDirectories();
$dbConfig = Config::getDbConfig();

$db = new Database(
    $dbConfig['host'],
    $dbConfig['database'],
    $dbConfig['username'],
    $dbConfig['password'],
);

$iterator = new \RecursiveIteratorIterator(
    new \RecursiveDirectoryIterator($directoryConfig['xmlDirectory'])
);

$xmlReader = new XmlFileReader($iterator, $directoryConfig['processedXmlDirectory']);

$processor = new XmlProcessor(
    $directoryConfig['xmlDirectory'],
    $directoryConfig['processedXmlDirectory'],
    $db,
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

$fileHandler->processFiles();

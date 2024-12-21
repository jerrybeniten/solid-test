<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Config;
use App\Interfaces\ErrorHandler;
use App\Services\HtmlResultDisplay;
use App\Services\SimpleErrorHandler;
use App\Services\XmlProcessor;

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
}

$directoryConfig = Config::getDirectories();

$processor = new XmlProcessor(
    $directoryConfig['xmlDirectory'],
    $directoryConfig['processedXmlDirectory']
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

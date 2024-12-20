<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\XmlProcessor;

class XmlFileHandler
{
    private $xmlDirectory;
    private $processedDirectory;
    private $processor;

    public function __construct(string $xmlDirectory, string $processedDirectory)
    {
        $this->xmlDirectory = $xmlDirectory;
        $this->processedDirectory = $processedDirectory;
        $this->processor = new XmlProcessor($this->xmlDirectory, $this->processedDirectory);
    }

    public function validateDirectories(): bool
    {
        return is_dir($this->xmlDirectory) && is_dir($this->processedDirectory);
    }

    public function processFiles(): void
    {
        try {
            $result = $this->processor->process();
            $this->displayResult($result);
        } catch (Exception $e) {
            $this->handleError($e->getMessage());
        }
    }

    private function displayResult($result): void
    {
        echo "<pre>" . htmlspecialchars(print_r($result, true)) . "</pre>";
    }

    private function handleError(string $message): void
    {
        echo "Error: " . $message . "\n";
    }
}

// Initialize the handler with directories
$xmlDirectory = __DIR__ . '/../data/xml_files';
$processedDirectory = __DIR__ . '/../data/xml_files_processed';

$fileHandler = new XmlFileHandler($xmlDirectory, $processedDirectory);

// Validate directories and process files
if (!$fileHandler->validateDirectories()) {
    echo "Error: One or more directories do not exist.\n";
    exit;
}

$fileHandler->processFiles();

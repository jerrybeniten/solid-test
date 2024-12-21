<?php

namespace Cron;

use App\Interfaces\ErrorHandler;
use App\Services\HtmlResultDisplay;
use App\Services\XmlProcessor;
use Exception;

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

    public function processFiles(): array
    {
        try {
            $result = $this->processor->process();
            $this->resultDisplay->display($result);
            return $result;
        } catch (Exception $e) {
            $this->errorHandler->handleError($e->getMessage());
        }
    }    
}

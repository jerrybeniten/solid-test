<?php

namespace Cron;

use App\Interfaces\ErrorHandlerInterface;
use App\Services\HtmlResultDisplayService;
use App\Services\XmlProcessorService;
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
        XmlProcessorService $processor,
        HtmlResultDisplayService $resultDisplay,
        ErrorHandlerInterface $errorHandler
    ) {
        $this->xmlDirectory = $xmlDirectory;
        $this->processedDirectory = $processedDirectory;
        $this->processor = $processor;
        $this->resultDisplay = $resultDisplay;
        $this->errorHandler = $errorHandler;
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

    public function validateDirectories(): bool
    {
        return is_dir($this->xmlDirectory) && is_dir($this->processedDirectory);
    }      
}

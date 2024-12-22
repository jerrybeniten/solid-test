<?php

namespace Lib\XmlHelper;

use App\Interfaces\XmlFileReaderInterface;
use RecursiveIteratorIterator;
use SimpleXMLElement;
use Exception;

class XmlFileReader implements XmlFileReaderInterface
{
    private RecursiveIteratorIterator $iterator;
    private string $processedDirectory;  // New property for processed files' destination
    private string $errorLog;

    /**
     * Constructor with dependency injection for RecursiveIteratorIterator.
     *
     * @param RecursiveIteratorIterator $iterator
     * @param string $processedDirectory Directory where processed files will be moved.
     */
    public function __construct(RecursiveIteratorIterator $iterator, string $processedDirectory, string $errorLog)
    {
        $this->iterator = $iterator;
        $this->processedDirectory = $processedDirectory;
        $this->errorLog = $errorLog;
    }

    /**
     * Reads all XML files recursively from a directory and returns an array of SimpleXMLElement objects.
     *
     * @param string $directory
     * @return SimpleXMLElement[]   
     */
    public function readXmlFiles(string $directory): array
    {
        $xmlFiles = [];

        $this->validateDirectory($directory);
        $this->iterator->rewind();

        foreach ($this->iterator as $file) {
            if ($this->isXmlFile($file)) {
                $xmlFiles[] = $this->processXmlFile($file);
            }
        }

        return $xmlFiles;
    }

    private function validateDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            throw new Exception("Invalid directory: $directory");
        }
    }

    private function processXmlFile($file): SimpleXMLElement
    {
        $filePath = $file->getRealPath();
        $content = file_get_contents($filePath);

        // Ensure the XML file has a root element
        $wrappedContent = $this->ensureRootElement($content, 'roots');

        // Load the XML
        $xml = simplexml_load_string($wrappedContent);
        if ($xml === false) {
            $this->handleInvalidXml($filePath);
            return null;  // Skip invalid XML files
        }

        // Move the processed file to the 'processed' directory
        $this->moveFile($filePath);

        return $xml;
    }

    private function handleInvalidXml(string $filePath): void
    {
        $errorMessage = "[ERROR] " . date("Y-m-d H:i:s") . " Skipping invalid XML file: " . $filePath . "\n";
        echo $errorMessage;
        file_put_contents($this->errorLog, $errorMessage, FILE_APPEND);
    }

    /**
     * Checks if a file is an XML file.
     *
     * @param mixed $file
     * @return bool
     */
    private function isXmlFile($file): bool
    {
        return $file->isFile() && strtolower($file->getExtension()) === 'xml';
    }

    /**
     * Ensures that the XML content has a root element.
     * If no root element is present, wraps the content in the specified root element.
     *
     * @param string $xmlContent
     * @param string $rootElement
     * @return string
     */
    private function ensureRootElement(string $xmlContent, string $rootElement): string
    {
        // Suppress errors for invalid XML
        libxml_use_internal_errors(true);

        // Attempt to parse the XML
        $parsedXml = simplexml_load_string($xmlContent);

        // If parsing fails, wrap the content in the root element
        if ($parsedXml === false) {
            $xmlContent = "<{$rootElement}>\n" . $xmlContent . "\n</{$rootElement}>";
        }

        // Clear any libxml errors
        libxml_clear_errors();

        return $xmlContent;
    }

    /**
     * Moves the processed XML file to the specified directory.
     *
     * @param string $filePath Path of the file to move.
     * @return bool True on success, false on failure.
     */
    private function moveFile(string $filePath): bool
    {
        $destination = $this->processedDirectory . DIRECTORY_SEPARATOR . basename($filePath);

        // Attempt to move the file
        if (rename($filePath, $destination)) {
            echo "File moved to: " . $destination . "\n";
            return true;
        } else {
            echo "Failed to move file: " . $filePath . "\n";
            return false;
        }
    }
}

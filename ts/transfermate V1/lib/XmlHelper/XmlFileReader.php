<?php

namespace Lib;

use RecursiveIteratorIterator;
use SimpleXMLElement;
use Exception;

class XmlFileReader
{
    private RecursiveIteratorIterator $iterator;

    /**
     * Constructor with dependency injection for RecursiveIteratorIterator.
     *
     * @param RecursiveIteratorIterator $iterator
     */
    public function __construct(RecursiveIteratorIterator $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * Reads all XML files recursively from a directory and returns an array of SimpleXMLElement objects.
     *
     * @param string $directory
     * @return SimpleXMLElement[]
     * @throws Exception If the directory is invalid or XML file cannot be loaded.
     */
    public function readXmlFiles(string $directory): array
    {
        $xmlFiles = [];

        if (!is_dir($directory)) {
            throw new Exception("Invalid directory: $directory");
        }

        $this->iterator->rewind();

        foreach ($this->iterator as $file) {
            if ($this->isXmlFile($file)) {
                $filePath = $file->getRealPath();
                $content = file_get_contents($filePath);

                // Ensure the XML file has a root element
                $wrappedContent = $this->ensureRootElement($content, 'roots');

                // Load the XML
                $xml = simplexml_load_string($wrappedContent);
                if ($xml === false) {
                    throw new Exception("Failed to load XML file: " . $filePath);
                }
                $xmlFiles[] = $xml;
            }
        }

        return $xmlFiles;
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
}

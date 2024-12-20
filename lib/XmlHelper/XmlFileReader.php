<?php

namespace Lib\XmlHelper;

use RecursiveIteratorIterator;
use SimpleXMLElement;
use Exception;

class XmlFileReader
{
    private RecursiveIteratorIterator $iterator;

    public function __construct(RecursiveIteratorIterator $iterator)
    {
        $this->iterator = $iterator;
    }

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

                $wrappedContent = $this->ensureRootElement($content, 'roots');
                $xml = simplexml_load_string($wrappedContent);
                if ($xml === false) {
                    throw new Exception("Failed to load XML file: " . $filePath);
                }
                $xmlFiles[] = $xml;
            }
        }

        return $xmlFiles;
    }

    private function isXmlFile($file): bool
    {
        return $file->isFile() && strtolower($file->getExtension()) === 'xml';
    }

    private function ensureRootElement(string $xmlContent, string $rootElement): string
    {
        libxml_use_internal_errors(true);

        $parsedXml = simplexml_load_string($xmlContent);

        if ($parsedXml === false) {
            $xmlContent = "<{$rootElement}>\n" . $xmlContent . "\n</{$rootElement}>";
        }

        libxml_clear_errors();

        return $xmlContent;
    }
}

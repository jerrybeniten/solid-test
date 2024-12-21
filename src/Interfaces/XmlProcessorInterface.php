<?php

namespace App\Interfaces;

/**
 * Interface for XML file reading services.
 */
interface XmlFileReaderInterface
{
    public function readXmlFiles(string $directory): array;
}

<?php

namespace App\Interfaces;

interface XmlDirectoryReaderInterface
{
    public function readXmlFiles(string $directory): array;
}

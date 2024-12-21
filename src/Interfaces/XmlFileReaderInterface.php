<?php

namespace App\Interfaces;

interface XmlFileReaderInterface
{
    public function readXmlFiles(string $directory): array;
}

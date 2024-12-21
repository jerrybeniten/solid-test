<?php

namespace App\Services;

use App\Interfaces\XmlFileReaderInterface;

class XmlFileReader implements XmlFileReaderInterface
{
    public function readXmlFiles(string $directory): array
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        $xmlData = [];

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'xml') {
                $xmlData[] = simplexml_load_file($file->getPathname());
            }
        }

        return $xmlData;
    }
}

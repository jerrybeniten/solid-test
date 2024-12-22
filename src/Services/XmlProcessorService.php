<?php

namespace App\Services;

use App\Interfaces\XmlFileReaderInterface;


class XmlProcessorService
{
    private string $directory;
    private string $processedDirectory;
    private XmlDirectoryReaderService $xmlReader;

    public function __construct(
        string $directory,
        string $processedDirectory,
        XmlDirectoryReaderService $xmlReader
    ) {
        $this->directory = $directory;
        $this->processedDirectory = $processedDirectory;
        $this->xmlReader = $xmlReader;
    }

    public function process(): array
    {
        $data = $this->xmlReader->readXmlFiles($this->directory);
        $uniqueCollection = $this->getUniqueCollection($data);
        return $uniqueCollection;
    }

    private function getUniqueCollection(array $data): array
    {
        $uniqueKeys = [];
        $uniqueCollection = [];

        foreach ($data as $entry) {
            if (!empty($entry->book)) {
                foreach ($entry->book as $book) {
                    $author = (string)$book->author;
                    $name = (string)$book->name;
                    $entryKey = $author . '|' . $name;

                    if (!isset($uniqueKeys[$entryKey])) {
                        $uniqueKeys[$entryKey] = true;
                        $uniqueCollection[] = ['author' => $author, 'name' => $name];
                    }
                }
            } else {
                $author = (string)$entry->author;
                $name = (string)$entry->name;
                $entryKey = $author . '|' . $name;

                if (!isset($uniqueKeys[$entryKey])) {
                    $uniqueKeys[$entryKey] = true;
                    $uniqueCollection[] = ['author' => $author, 'name' => $name];
                }
            }
        }

        return $uniqueCollection;
    }
}

<?php

namespace App\Services;

use App\Interfaces\XmlFileReaderInterface;
use Lib\XmlHelper\XmlFileReader;
use DB\Database;

class XmlProcessor
{
    private string $directory;
    private string $processedDirectory;
    private XmlFileReader $xmlReader;
    private Database $db;

    public function __construct(
        string $directory,
        string $processedDirectory,
        Database $db,
        XmlFileReaderInterface $xmlReader
    ) {
        $this->directory = $directory;
        $this->processedDirectory = $processedDirectory;
        $this->xmlReader = $xmlReader;
        $this->db = $db;
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

    public function process(): array
    {
        $data = $this->xmlReader->readXmlFiles($this->directory);
        $uniqueCollection = $this->getUniqueCollection($data);
        $this->db->insertAuthors($uniqueCollection);
        return $uniqueCollection;
    }
}

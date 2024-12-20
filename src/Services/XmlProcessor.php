<?php

namespace App\Services;

use Lib\XmlHelper\XmlFileReader;
use DB\Database;

class XmlProcessor
{
    private string $directory;
    private XmlFileReader $xmlReader;
    private Database $db;

    public function __construct(string $directory)
    {
        $this->directory = $directory;
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->directory));
        $this->xmlReader = new XmlFileReader($iterator);
        $this->db = new Database('postgres', 'mydb', 'docker', 'docker'); // Adjust DB credentials as needed
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

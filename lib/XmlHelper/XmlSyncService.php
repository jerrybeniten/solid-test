<?php

namespace Lib\XmlHelper;

use DB\Database;

class XmlSyncService
{
    private $db;
    private $xmlFileReader;

    public function __construct(Database $db, XmlFileReader $xmlFileReader)
    {
        $this->db = $db;
        $this->xmlFileReader = $xmlFileReader;
    }

    public function syncXmlData(string $directory)
    {
        $xmlFiles = $this->xmlFileReader->readXmlFiles($directory);

        foreach ($xmlFiles as $xml) {
            $books = [];
            foreach ($xml->book as $book) {
                $books[] = [
                    'author' => (string)$book->author,
                    'name' => (string)$book->name,
                ];
            }

            $this->db->insertAuthors($books);
        }
    }
}

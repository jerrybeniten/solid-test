<?php

namespace App\Services;

use App\Interfaces\DatabaseInterface;
use PDO;

class Database implements DatabaseInterface
{
    private PDO $connection;

    public function __construct(string $dsn, string $username, string $password)
    {
        $this->connection = new PDO($dsn, $username, $password);
    }

    public function insertAuthors(array $data): void
    {
        $stmt = $this->connection->prepare('INSERT INTO authors (author, name) VALUES (:author, :name)');

        foreach ($data as $entry) {
            $stmt->execute([
                ':author' => $entry['author'],
                ':name' => $entry['name'],
            ]);
        }
    }
}

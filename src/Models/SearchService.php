<?php

namespace App\Models;

use App\Interfaces\SearchInterface;

class SearchService implements SearchInterface
{
    private $pdo;

    public function __construct(Database $database)
    {
        $this->pdo = $database->getPDO();
    }

    public function search(string $query): array
    {
        $sql = "
            SELECT books.id AS book_id, books.title AS book_title, authors.name AS author_name
            FROM books
            INNER JOIN authors ON books.author_id = authors.id
            WHERE books.title ILIKE :query OR authors.name ILIKE :query
            LIMIT 5
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindValue(':query', '%' . $query . '%', \PDO::PARAM_STR);
            $stmt->execute();

            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (!$results) {
                return ['error' => 'No results found'];
            }

            return $results;
        } catch (\PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }
}

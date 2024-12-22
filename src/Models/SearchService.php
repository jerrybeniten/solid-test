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
            SELECT 
                books.id AS book_id, 
                books.title AS book_title, 
                authors.name AS author_name
            FROM 
                books
            RIGHT JOIN 
                authors ON authors.id = books.author_id
            WHERE 
                authors.name ILIKE :query
            LIMIT 10
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

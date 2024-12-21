<?php

namespace App\Models;

use App\Interfaces\InsertInterface;
use PDO;
use PDOException;

class InsertService implements InsertInterface
{
    private $pdo;
    private array $data;

    public function __construct(Database $database, array $data)
    {
        $this->pdo = $database->getPDO();
        $this->data = $data;
    }
    
    public function upsert(): bool
    {

        $items = $this->data;

        if (empty($items)) {
            return false;
        }

        $this->pdo->beginTransaction();

        try {
            $sqlAuthor = "INSERT INTO authors (name) VALUES (:name) ON CONFLICT (name) DO NOTHING RETURNING id";
            $sqlBook = "INSERT INTO books (title, author_id) VALUES (:title, :author_id) ON CONFLICT (title, author_id) DO NOTHING";

            $stmtAuthor = $this->pdo->prepare($sqlAuthor);
            $stmtBook = $this->pdo->prepare($sqlBook);

            foreach ($items as $item) {
                $stmtAuthor->execute(['name' => $item['author']]);
                $authorResult = $stmtAuthor->fetch(PDO::FETCH_ASSOC);

                if ($authorResult) {
                    $authorId = $authorResult['id'];
                } else {
                    $stmtSelect = $this->pdo->prepare("SELECT id FROM authors WHERE name = :name");
                    $stmtSelect->execute(['name' => $item['author']]);
                    $authorResult = $stmtSelect->fetch(PDO::FETCH_ASSOC);
                    $authorId = $authorResult['id'] ?? null;
                }

                if ($authorId) {
                    $stmtBook->execute(['title' => $item['name'], 'author_id' => $authorId]);
                }
            }

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            $this->handleError($e);
            return false;
        }    
    }

    private function handleError(PDOException $e)
    {
        echo $e->getMessage();
        echo "Database error occurred. Please try again later.";
        exit;
    }  
}

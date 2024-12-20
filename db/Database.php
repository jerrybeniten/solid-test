<?php

namespace DB;

use PDO;
use PDOException;

class Database
{
    private $pdo;

    public function __construct($host, $dbname, $user, $password)
    {
        $this->pdo = $this->connect($host, $dbname, $user, $password);
    }

    private function connect($host, $dbname, $user, $password)
    {
        try {
            $dsn = "pgsql:host={$host};dbname={$dbname}";
            $pdo = new PDO($dsn, $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    private function handleError(PDOException $e)
    {
        echo $e->getMessage();
        echo "Database error occurred. Please try again later.";
        exit;
    }

    public function insertAuthors(array $books)
    {
        if (empty($books)) {
            return false; 
        }

        $this->pdo->beginTransaction();

        try {
            $sqlAuthor = "INSERT INTO authors (name) VALUES (:name) ON CONFLICT (name) DO NOTHING RETURNING id";
            $sqlBook = "INSERT INTO books (title, author_id) VALUES (:title, :author_id) ON CONFLICT (title, author_id) DO NOTHING";

            $stmtAuthor = $this->pdo->prepare($sqlAuthor);
            $stmtBook = $this->pdo->prepare($sqlBook);

            foreach ($books as $book) {
                var_dump($book);
                $stmtAuthor->execute(['name' => $book['author']]);
                $authorResult = $stmtAuthor->fetch(PDO::FETCH_ASSOC);

                $authorId = $authorResult ? $authorResult['id'] : null;
                if (!$authorId) {
                    $stmtSelect = $this->pdo->prepare("SELECT id FROM authors WHERE name = :name");
                    $stmtSelect->execute(['name' => $book['author']]);
                    $authorResult = $stmtSelect->fetch(PDO::FETCH_ASSOC);
                    $authorId = $authorResult['id'];
                }

                if ($authorId) {
                    $stmtBook->execute(['title' => $book['name'], 'author_id' => $authorId]);
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
}

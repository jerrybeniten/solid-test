<?php

namespace DB;

// Enable all error reporting
error_reporting(E_ALL);

// Display errors in the browser (for development purposes only)
ini_set('display_errors', 1);

use PDO;
use PDOException;

class Database
{
    private $pdo;

    /**
     * Constructor to initialize the PDO connection.
     *
     * @param string $host Database host.
     * @param string $dbname Database name.
     * @param string $user Database user.
     * @param string $password Database password.
     */
    public function __construct($host, $dbname, $user, $password)
    {
        $this->pdo = $this->connect($host, $dbname, $user, $password);
    }

    /**
     * Connects to the PostgreSQL database.
     *
     * @param string $host Database host.
     * @param string $dbname Database name.
     * @param string $user Database user.
     * @param string $password Database password.
     * @return PDO Returns the PDO instance.
     */
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

    /**
     * Handles errors by logging them or displaying them to the user.
     *
     * @param PDOException $e The exception to handle.
     */
    private function handleError(PDOException $e)
    {
        // Log the error, for example, to a file (this is just an example).
        //error_log($e->getMessage(), 3, '/path/to/error.log');
        echo $e->getMessage();
        // Display the error message to the user in a user-friendly way (or hide it in production).
        echo "Database error occurred. Please try again later.";
        exit;
    }

    /**
     * Retrieves all books from the database.
     *
     * @return array Returns an array of books.
     */
    public function getAllBooks()
    {
        return $this->fetchBooks("SELECT * FROM books");
    }

    /**
     * Fetches books based on a specific query.
     *
     * @param string $query The SQL query.
     * @return array Returns an array of books.
     */
    private function fetchBooks($query)
    {
        try {
            $stmt = $this->pdo->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    /**
     * Insert a new book into the database.
     *
     * @param string $author The author of the book.
     * @param string $name The name of the book.
     * @return bool Returns true if the book was inserted successfully.
     */
    /**
     * Insert multiple authors and their books into the database.
     *
     * @param array $books An array of books where each book is an associative array with 'author' and 'name'.
     * @return bool Returns true if all books were inserted successfully.
     */
    public function insertBooks(array $books)
    {
        if (empty($books)) {
            return false; // No books to insert
        }

        // Begin a transaction for better performance and consistency
        $this->pdo->beginTransaction();

        try {
            $sql = "INSERT INTO books (author, name) VALUES (:author, :name)";
            $stmt = $this->pdo->prepare($sql);

            // Loop through each book and execute the insert statement
            foreach ($books as $book) {
                $stmt->execute([
                    'author' => $book['author'],
                    'name' => $book['name']
                ]);
            }

            // Commit the transaction if all insertions were successful
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            // Rollback the transaction if there was an error
            $this->pdo->rollBack();
            $this->handleError($e);
            return false;
        }
    }

    public function insertAuthors(array $books)
    {
        if (empty($books)) {
            return false; // No books to insert
        }

        // Begin a transaction for better performance and consistency
        $this->pdo->beginTransaction();

        try {

            $sqlAuthor = "
                INSERT INTO authors (name)
                VALUES (:name)
                ON CONFLICT (name) 
                DO NOTHING
                RETURNING id";

            $sqlBook = "
                INSERT INTO books (title, author_id) 
                VALUES (:title, :author_id)
                ON CONFLICT (title, author_id) 
                DO NOTHING";

            $stmtAuthor = $this->pdo->prepare($sqlAuthor);
            $stmtBook = $this->pdo->prepare($sqlBook);


            // Loop through each book and execute the insert statement
            foreach ($books as $book) {
                // First, try inserting the author
                $stmtAuthor->execute([                    
                    'name' => $book['author']
                ]);
            
                // Fetch the inserted or existing author ID
                $authorResult = $stmtAuthor->fetch(PDO::FETCH_ASSOC);
            
                // If the author was inserted, $authorResult will contain the id
                if ($authorResult) {
                    $authorId = $authorResult['id'];
                } else {
                    // If the author already exists, fetch the existing author's id
                    $stmtSelect = $this->pdo->prepare("SELECT id FROM authors WHERE name = :name");
                    $stmtSelect->execute(['name' => $book['author']]);
                    $authorResult = $stmtSelect->fetch(PDO::FETCH_ASSOC);
                    $authorId = $authorResult['id'] ?? null;
                }
            
                // Insert the book with the author ID
                if ($authorId) {
                    $stmtBook->execute([
                        'title' => $book['name'],
                        'author_id' => $authorId
                    ]);
                }
            }


            // Commit the transaction if all insertions were successful
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            // Rollback the transaction if there was an error
            $this->pdo->rollBack();
            $this->handleError($e);
            return false;
        }
    }

    /**
     * Updates the details of an existing book.
     *
     * @param int $id The ID of the book.
     * @param string $author The updated author name.
     * @param string $name The updated book name.
     * @return bool Returns true if the update was successful.
     */
    public function updateBook($id, $author, $name)
    {
        $sql = "UPDATE books SET author = :author, name = :name WHERE id = :id";
        return $this->executeQuery($sql, ['author' => $author, 'name' => $name, 'id' => $id]);
    }

    /**
     * Deletes a book by its ID.
     *
     * @param int $id The ID of the book.
     * @return bool Returns true if the book was deleted successfully.
     */
    public function deleteBook($id)
    {
        $sql = "DELETE FROM books WHERE id = :id";
        return $this->executeQuery($sql, ['id' => $id]);
    }

    /**
     * Executes a query with the given parameters.
     *
     * @param string $sql The SQL query.
     * @param array $params The parameters to bind to the query.
     * @return bool Returns true if the query was executed successfully.
     */
    private function executeQuery($sql, $params)
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }
}

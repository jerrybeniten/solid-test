<?php



namespace App;

// Enable all error reporting
error_reporting(E_ALL);

// Display errors in the browser (for development purposes only)
ini_set('display_errors', 1);

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Lib\XmlFileReader;
use DB\Database;

class XmlProcessor
{
    private string $directory;
    private XmlFileReader $xmlReader;
    private $db;

    /**
     * Constructor to initialize the processor.
     *
     * @param string $directory Path to the directory containing XML files.
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->directory));
        $this->xmlReader = new XmlFileReader($iterator);
        $this->db = new Database('postgres', 'mydb', 'docker', 'docker');
    }

    /**
     * Process the input array and return a collection of unique authors and books.
     *
     * @param array $data The input array of SimpleXMLElement objects.
     * @return array The resulting array with unique entries.
     */
    private function getUniqueCollection(array $data): array
    {
        $uniqueKeys = []; // Associative array for uniqueness
        $uniqueCollection = []; // Final collection to return

        foreach ($data as $entry) {
            // Check if there are books
            if (!empty($entry->book)) {
                foreach ($entry->book as $book) {
                    $author = (string)$book->author;
                    $name = (string)$book->name;

                    $entryKey = $author . '|' . $name; // Simple concatenation for uniqueness

                    if (!isset($uniqueKeys[$entryKey])) {
                        $uniqueKeys[$entryKey] = true; // Mark as seen
                        $uniqueCollection[] = ['author' => $author, 'name' => $name];
                    }
                }
            } else {
                // Direct author and name processing
                $author = (string)$entry->author;
                $name = (string)$entry->name;

                $entryKey = $author . '|' . $name; // Simple concatenation for uniqueness

                if (!isset($uniqueKeys[$entryKey])) {
                    $uniqueKeys[$entryKey] = true; // Mark as seen
                    $uniqueCollection[] = ['author' => $author, 'name' => $name];
                }
            }
        }

        return $uniqueCollection;
    }

    /**
     * Process the XML files and return the unique collection.
     *
     * @return array The unique collection of authors and books.
     */
    public function process(): array
    {
        $data = $this->xmlReader->readXmlFiles($this->directory);
        $uniqueCollection = $this->getUniqueCollection($data);
        $this->db->insertAuthors($uniqueCollection);
        return $uniqueCollection;
    }
}

// Usage example
require '../vendor/autoload.php'; // Adjust path as needed

// Define the root directory to scan for XML files
$directory = '../start';

// Create an instance of XmlProcessor
$processor = new XmlProcessor($directory);

// Process XML files and get the unique collection
echo "<pre>";
var_dump($processor->process());

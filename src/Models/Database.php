<?php

namespace App\Models;

use App\Config\Config;

class Database
{
    private $pdo;

    public function __construct(array $config)
    {
        try {
            $this->pdo = new \PDO(
                "pgsql:host={$config['host']};dbname={$config['database']}",
                $config['username'],
                $config['password']
            );
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die("Could not connect to the database: " . $e->getMessage());
        }
    }

    public function getPDO()
    {
        return $this->pdo;
    }
}
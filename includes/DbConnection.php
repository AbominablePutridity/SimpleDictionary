<?php

class DbConnection
{
    private $pdo;

    public function __construct($dbPath) {
        // DSN specifies the SQLite driver and the absolute path to the file
        $dsn = "sqlite:" . $dbPath;

        try {
            $this->pdo = new PDO($dsn);
            // Set error mode to exceptions for easier debugging
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}
<?php

class Database {
    private $host = 'localhost';
    private $dbname = 'my_youdemy';
    private $user = 'root';
    private $pass = 'Ren-ji24';

    public function connect(){
        $dsn = "mysql:host={$this->host};dbname={$this->dbname}";
        try {
            $pdo = new PDO($dsn, $this->user, $this->pass);
            return $pdo;
        } catch (PDOException $e) {
            // Handle connection errors
            die("Database connection failed: " . $e->getMessage());
        }
    }
}

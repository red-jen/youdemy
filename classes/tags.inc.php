<?php

require_once('config.inc.php');
class Tag {
    private $id;
    private $name;

    public function __construct($id = null, $name = null) {
        $this->id = $id;
        $this->name = $name;
    }

    public function save() {
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("INSERT INTO tag (name) VALUES (?)");
        return $stmt->execute([$this->name]);
    }

    public static function getAll() {
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("SELECT * FROM tag");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

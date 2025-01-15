<?php

require_once('config.inc.php');

class Category {
    private $id;
    private $name;

    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public  static function getid($name) {
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("SELECT * FROM category where id = ?");
        $stmt->execute([$name]);
     
         $result =   $stmt->fetch(PDO::FETCH_ASSOC);
         if ($result) {
            return $result['id'];
        } else {
           
            return null; 
        }
  
    }

    public function save() {
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("INSERT INTO category (name) VALUES (?)");
        return $stmt->execute([$this->name]);
    }

    public static function getAll() {
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("SELECT * FROM category");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
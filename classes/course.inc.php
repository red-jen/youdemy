<?php
require_once('config.inc.php');


class Course {
    private $id;
    private $title;
    private $description;
    private $content;
    private $categoryId;
    private $teacherId;
    private $tags = [];

    public function __construct($title, $description, $content, $categoryId, $teacherId) {
        $this->title = $title;
        $this->description = $description;
        $this->content = $content;
        $this->categoryId = $categoryId;
        $this->teacherId = $teacherId;
    }

    public function save() {
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("
            INSERT INTO course (title, description, content, categoryId, teacherId) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $this->title,
            $this->description,
            $this->content,
            $this->categoryId,
            $this->teacherId
        ]);
        $this->getid();
    }
    public function getid(){
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->query("select * from course where title = $this->title and teacherId = $this->teacherId ");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->id = $result['id'];
        return $this->id;
        
    }

    public static function search($keyword) {
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("
            SELECT c.*, u.name as teacher_name, cat.name as category_name
            FROM course c
            JOIN user u ON c.teacherId = u.id
            JOIN category cat ON c.categoryId = cat.id
            WHERE c.title LIKE ? OR c.description LIKE ?
        ");
        
        $keyword = "%$keyword%";
        $stmt->execute([$keyword, $keyword]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("
            SELECT c.*, u.name as teacher_name, cat.name as category_name
            FROM course c
            JOIN user u ON c.teacherId = u.id
            JOIN category cat ON c.categoryId = cat.id
            WHERE c.id = ?
        ");
        
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
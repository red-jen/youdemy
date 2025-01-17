<?php
require_once('config.inc.php');
class Statistics {
    private $teacher;
    private $courseId;
    private $numberOfStudents;
    private $numcourse;


    public function __construct($courseId = null) {
        $this->courseId = $courseId;
        $this->numberOfStudents = 0;
    }

    public function updateStudentCount() {
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("
            SELECT COUNT(DISTINCT studentId) as count 
            FROM enrollment 
            WHERE courseId = ?
        ");
        $stmt->execute([$this->courseId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->numberOfStudents = $result['count'];
        
        return $result['count'];
    
       
    }
    public function updateStudent($teacherid) {
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("
            SELECT COUNT( id) as count 
            FROM course 
            WHERE teacherId = ?
        ");
        $stmt->execute([$this->courseId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->numcourse = $result['count'];
        
        return $result['count'];
    
       
    }
}
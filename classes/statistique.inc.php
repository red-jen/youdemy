<?php
require_once('config.inc.php');
class Statistics {
    private $id;
    private $courseId;
    private $numberOfStudents;

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
        
        // Update in database
        $stmt = $conn->prepare("
            UPDATE statistics 
            SET numberOfStudents = ? 
            WHERE courseId = ?
        ");
        return $stmt->execute([$this->numberOfStudents, $this->courseId]);
    }
}
<?php

require_once('config.inc.php');
require_once('statistique.inc.php');
// Enrollment.php
class Enrollment {
    private $id;
    private $studentId;
    private $courseId;
    private $enrollmentDate;

    public function __construct($studentId, $courseId) {
        $this->studentId = $studentId;
        $this->courseId = $courseId;
        $this->enrollmentDate = date('Y-m-d H:i:s');
    }

    public function save() {
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("
            INSERT INTO enrollment (studentId, courseId, enrollmentDate) 
            VALUES (?, ?, ?)
        ");
        
        if($stmt->execute([$this->studentId, $this->courseId, $this->enrollmentDate])) {
            // Update course statistics
            $stats = new Statistics($this->courseId);
            $stats->updateStudentCount();
            return true;
        }
        return false;
    }
}
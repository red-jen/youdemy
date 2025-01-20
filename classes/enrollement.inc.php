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
        
        $stmt = $conn->prepare("SELECT id FROM enrollment WHERE studentId = ? AND courseId = ?");
        $stmt->execute([$this->studentId,  $this->courseId]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Already enrolled in this course'];
        }
        
        // Create enrollment
        $stmt = $conn->prepare("INSERT INTO enrollment (studentId, courseId, enrollment_date) VALUES (?, ?, NOW())");
        $success = $stmt->execute([$this->studentId,  $this->courseId]);
        
        return ['success' => $success, 'message' => $success ? 'Enrolled successfully' : 'Enrollment failed'];;
        
        if($success) {
            // Update course statistics
            $stats = new Statistics($this->courseId);
            $stats->updateStudentCount();
            return true;
        }
        return false;
    }


    public static function cancelEnrollment($courseId, $studentId) {
        $db = new Database();
        $conn = $db->connect();
        
        // Delete the enrollment
        $stmt = $conn->prepare("DELETE FROM enrollment WHERE courseId = ? AND studentId = ?");
        $success = $stmt->execute([$courseId, $studentId]);
        
        if ($success) {
            // Update course statistics
            $stats = new Statistics($courseId);
            $stats->updateStudentCount();
            return ['success' => true, 'message' => 'Enrollment cancelled successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to cancel enrollment'];
    }

    public static function getEnrolledStudents($courseId) {
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("
            SELECT u.id, u.name, u.email, e.enrollment_date
            FROM enrollment e
            JOIN user u ON e.studentId = u.id
            WHERE e.courseId = ?
            ORDER BY e.enrollment_date DESC
        ");
        
        $stmt->execute([$courseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function isEnrolled($courseId, $studentId) {
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("SELECT id FROM enrollment WHERE courseId = ? AND studentId = ?");
        $stmt->execute([$courseId, $studentId]);
        
        return $stmt->fetch() !== false;
    }
}

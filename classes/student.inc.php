<?php
require_once('user.inc.php');
class Student extends User {
    private $enrolledCourses = [];

    public function __construct($id, $name, $email, $password) {
        parent::__construct($id, $name, $email, $password, 'student');
    }

    public function enrollInCourse($courseId) {
        $db = new Database();
        $conn = $db->connect();
        
        // Check if already enrolled
        $stmt = $conn->prepare("SELECT id FROM enrollment WHERE studentId = ? AND courseId = ?");
        $stmt->execute([$this->getId(), $courseId]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Already enrolled in this course'];
        }
        
        // Create enrollment
        $stmt = $conn->prepare("INSERT INTO enrollment (studentId, courseId, enrollmentDate) VALUES (?, ?, NOW())");
        $success = $stmt->execute([$this->getId(), $courseId]);
        
        return ['success' => $success, 'message' => $success ? 'Enrolled successfully' : 'Enrollment failed'];
    }

    public function viewMyCourses() {
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("
            SELECT c.*, u.name as teacher_name 
            FROM course c 
            JOIN enrollment e ON c.id = e.courseId 
            JOIN user u ON c.teacherId = u.id 
            WHERE e.studentId = ?
        ");
        $stmt->execute([$this->getId()]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
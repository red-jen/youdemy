<?php
require_once('user.inc.php');
require_once('enrollement.inc.php');
class Student extends User {
    private $enrolledCourses = [];

    public function __construct($id, $name, $email, $password , $created_at,$status) {
        parent::__construct($id, $name, $email, $password, 'student' , $created_at , $status);
    }
    
    public function enrollInCourse($courseId) {
        // $db = new Database();
        // $conn = $db->connect();
          $student = $this->getId();
        $enr = new Enrollment($student , $courseId);
        return $enr->save();
        
       
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
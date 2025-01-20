<?php
require_once('category.inc.php');
require_once('user.inc.php');
require_once('course.inc.php');
class Teacher extends User {
    public function __construct($id, $name, $email, $password , $created_at, $status) {
        parent::__construct($id, $name, $email, $password, 'teacher' , $created_at , $status);
    }

    

    public function addCourse($courseData) {
        $jib = Category::getid($courseData['category']);
        // Pass the file path correctly to the Course constructor
        $course = new Course(
            $courseData['title'],
            $courseData['description'],
            $courseData['content'],
            $jib,
            $this->getId(),
            $courseData['file_path'] // Pass the file path here
        );
    
        if ($course->save()) {
            $courseId = $course->getId();
            if (isset($courseData['tags']) && is_array($courseData['tags'])) {
                foreach ($courseData['tags'] as $tagId) {
                    $course->addTag($tagId);
                }
            }
            return $courseId;
        }
        return false;
    }

    public function viewCourseStatistics($courseId = null) {
        $db = new Database();
        $conn = $db->connect();
        
        if ($courseId) {
            // Statistics for specific course
            $stmt = $conn->prepare("
                SELECT 
                    c.title,
                    COUNT(e.id) as total_students,
                    c.created_at as course_date
                FROM course c
                LEFT JOIN enrollment e ON c.id = e.courseId
                WHERE c.id = ? AND c.teacherid = ?
                GROUP BY c.id
            ");
            $stmt->execute([$courseId, $this->getId()]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        // Statistics for all teacher's courses
        $stmt = $conn->prepare("
            SELECT 
                c.id,
                c.title,
                COUNT(e.id) as total_students
            FROM course c
            LEFT JOIN enrollment e ON c.id = e.courseId
            WHERE c.teacherid = ?
            GROUP BY c.id
        ");
        $stmt->execute([$this->getId()]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function numcourses() {
        $db = new Database();
        $conn = $db->connect();
        
       
        
            $stmt = $conn->prepare("
                SELECT 
                    
                    COUNT(c.id) as numcourse
                   
                FROM course c
                
                WHERE c.teacherid = ?
                
            ");
            $stmt->execute([$this->getId()]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['numcourse'];
        }
        
}
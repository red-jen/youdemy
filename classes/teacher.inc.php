<?php
require_once('category.inc.php');
require_once('user.inc.php');
require_once('course.inc.php');
class Teacher extends User {
    public function __construct($id, $name, $email, $password , $created_at) {
        parent::__construct($id, $name, $email, $password, 'teacher' , $created_at);
    }

    public function addCourse($courseData) {

        $jib = Category::getid($courseData['category']);
        $db = new Database();
        $conn = $db->connect();
        $ncourse = new Course(  $courseData['title'],$courseData['description'],$courseData['content'],$jib,$this->getId());
        // $stmt = $conn->prepare("
        //     INSERT INTO course (title, description, content, categoryId, teacherId) 
        //     VALUES (?, ?, ?, ?, ?)
        // ");
        
        // $success = $stmt->execute([
        //     $courseData['title'],
        //     $courseData['description'],
        //     $courseData['content'],
        //     $jib,
        //     $courseData['teacherId'],
        // ]);
        $success = $ncourse->save();

        if ($success && isset($courseData['tags'])) {
            $courseId = $ncourse->getid();
            $this->addCourseTags($courseId, $courseData['tags']);
        }
        
        return ['success' => $success, 'message' => $success ? 'Course added successfully' : 'Failed to add course'];
    }

    private function addCourseTags($courseId, $tags) {
        $db = new Database();
        $conn = $db->connect();
        
        foreach ($tags as $tagId) {
            $stmt = $conn->prepare("INSERT INTO course_tag (courseId, tagId) VALUES (?, ?)");
            $stmt->execute([$courseId, $tagId]);
        }
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
                WHERE c.id = ? AND c.teacherId = ?
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
                COUNT(e.id) as total_students,
                c.created_at as course_date
            FROM course c
            LEFT JOIN enrollment e ON c.id = e.courseId
            WHERE c.teacherId = ?
            GROUP BY c.id
        ");
        $stmt->execute([$this->getId()]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
<?php
require_once('user.inc.php');
require_once('config.inc.php');

class Admin extends User {
    public function __construct($id, $name, $email, $password, $created_at) {
        parent::__construct($id, $name, $email, $password, 'admin', $created_at,'active');
    }

    // User Management Methods
    public function getAllUsers() {
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("SELECT * FROM user WHERE role != 'admin' ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUserStatus($userId, $status) {
        if (!in_array($status, ['active', 'suspended', 'pending'])) {
            return ['success' => false, 'message' => 'Invalid status'];
        }

        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("UPDATE user SET status = ? WHERE id = ?");
        $success = $stmt->execute([$status, $userId]);
        
        return [
            'success' => $success,
            'message' => $success ? 'User status updated successfully' : 'Failed to update user status'
        ];
    }

    public function deleteUser($userId) {
        $db = new Database();
        $conn = $db->connect();
        
      
       
        
        
            // Delete user's enrollments first (if they're a student)
            $stmt = $conn->prepare("DELETE FROM enrollment WHERE studentId = ?");
            $stmt->execute([$userId]);
            
            // Delete user's courses if they're a teacher
            $stmt = $conn->prepare("DELETE FROM course WHERE teacherId = ?");
            $stmt->execute([$userId]);
            
            // Finally delete the user
            $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
            $result = $stmt->execute([$userId]);
            
            
            return ['success' => true, 'message' => 'User deleted successfully'];
               if($result){
             
            return ['success' => false, 'message' => 'Failed to delete user'];
        }
        
    }

   
    public function getAllCategories() {
        return Category::getAll();
    }

    public function addCategory($name) {
        $category = new Category(null, $name);
        $success = $category->save();
        return [
            'success' => $success,
            'message' => $success ? 'Category added successfully' : 'Failed to add category'
        ];
    }

    public function deleteCategory($categoryId) {
        $db = new Database();
        $conn = $db->connect();
        
     
        $stmt = $conn->prepare("SELECT COUNT(*) FROM course WHERE categoryId = ?");
        $stmt->execute([$categoryId]);
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'Category cannot be deleted as it is being used by courses'];
        }
        
        $stmt = $conn->prepare("DELETE FROM category WHERE id = ?");
        $success = $stmt->execute([$categoryId]);
        
        return [
            'success' => $success,
            'message' => $success ? 'Category deleted successfully' : 'Failed to delete category'
        ];
    }

  
    public function getAllTags() {
        return Tag::getAll();
    }

    public function addTag($name) {
        $tag = new Tag(null, $name);
        $success = $tag->save();
        return [
            'success' => $success,
            'message' => $success ? 'Tag added successfully' : 'Failed to add tag'
        ];
    }

    public function deleteTag($tagId) {
        $db = new Database();
        $conn = $db->connect();
        
       
        
   
            $stmt = $conn->prepare("DELETE FROM course_tag WHERE tagId = ?");
            $stmt->execute([$tagId]);
            
          
            $stmt = $conn->prepare("DELETE FROM tag WHERE id = ?");
            $stmt->execute([$tagId]);
            
     
            return ['success' => true, 'message' => 'Tag deleted successfully'];
     
          
            return ['success' => false, 'message' => 'Failed to delete tag'];
    
    }







public function getGlobalStatistics() {
    $db = new Database();
    $conn = $db->connect();
    
    // Total courses
    $totalCourses = $conn->query("SELECT COUNT(*) FROM course")->fetchColumn();
    
    // Courses by category
    $coursesByCategory = $conn->query("
        SELECT c.name as category, COUNT(co.id) as count 
        FROM category c 
        LEFT JOIN course co ON c.id = co.categoryId 
        GROUP BY  c.name
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Course with most students
    $mostPopularCourse = $conn->query("
        SELECT c.title, COUNT(e.studentId) as student_count 
        FROM course c 
        JOIN enrollment e ON c.id = e.courseId 
        GROUP BY  c.title 
        ORDER BY student_count DESC 
        LIMIT 1
    ")->fetch(PDO::FETCH_ASSOC);
    
    // Top 3 teachers
    $topTeachers = $conn->query("
        SELECT u.name as teacher_name, 
               COUNT(DISTINCT c.id) as course_count,
               COUNT(DISTINCT e.studentId) as total_students
        FROM user u 
        JOIN course c ON u.id = c.teacherId 
        LEFT JOIN enrollment e ON c.id = e.courseId
        WHERE u.role = 'teacher'
        GROUP BY u.id, u.name
        ORDER BY total_students DESC
        LIMIT 3
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    return [
        'totalCourses' => $totalCourses,
        'coursesByCategory' => $coursesByCategory,
        'mostPopularCourse' => $mostPopularCourse,
        'topTeachers' => $topTeachers
    ];
}
}
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
        
        // Start transaction
        $conn->beginTransaction();
        
        try {
            // Delete user's enrollments first (if they're a student)
            $stmt = $conn->prepare("DELETE FROM enrollment WHERE studentId = ?");
            $stmt->execute([$userId]);
            
            // Delete user's courses if they're a teacher
            $stmt = $conn->prepare("DELETE FROM course WHERE teacherId = ?");
            $stmt->execute([$userId]);
            
            // Finally delete the user
            $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
            $stmt->execute([$userId]);
            
            $conn->commit();
            return ['success' => true, 'message' => 'User deleted successfully'];
        } catch (Exception $e) {
            $conn->rollBack();
            return ['success' => false, 'message' => 'Failed to delete user'];
        }
    }

    // Category Management Methods
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
        
        // Check if category is in use
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

    // Tag Management Methods
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
        
        // Start transaction
        $conn->beginTransaction();
        
        try {
            // Remove tag from all courses first
            $stmt = $conn->prepare("DELETE FROM course_tag WHERE tagId = ?");
            $stmt->execute([$tagId]);
            
            // Delete the tag
            $stmt = $conn->prepare("DELETE FROM tag WHERE id = ?");
            $stmt->execute([$tagId]);
            
            $conn->commit();
            return ['success' => true, 'message' => 'Tag deleted successfully'];
        } catch (Exception $e) {
            $conn->rollBack();
            return ['success' => false, 'message' => 'Failed to delete tag'];
        }
    }
}
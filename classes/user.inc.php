<?php
require_once('config.inc.php');
require_once('student.inc.php');
require_once('admin.inc.php');
require_once('teacher.inc.php');


class User {
    private $id;
    private $name;
    private $email;
    private $password;
    private $role;
    private $status; // For account status (active, suspended, pending)
    private $createdAt;
    // private $lastLogin;
  
    public function __construct($id, $name, $email, $password, $role,$createdAt,$status) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->status = 'pending'; // Default status for new users
        $this->createdAt = $createdAt ; //date('Y-m-d H:i:s');
        // $this->lastLogin = null;
    }

    // Enhanced getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }
    // public function getStatus() { return $this->status; }
    public function getCreatedAt() { return $this->createdAt; }
    // public function getLastLogin() { return $this->lastLogin; }

    // Setters for updatable fields
    public function setName($name) { $this->name = $name; }
    public function setEmail($email) { $this->email = $email; }
    // public function setStatus($status) { $this->status = $status; }

    // Enhanced login method
    static  function login($email, $password) {
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("SELECT * FROM user WHERE email = ? or name = ? AND status = 'active' "); // add this for statu//AND status = 'active'
        $stmt->execute([$email , $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Email not found or account not active'];
        }
        
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Incorrect password'];
        }
        
        // Update last login time
        // $updateStmt = $conn->prepare("UPDATE user SET last_login = NOW() WHERE id = ?");
        // $updateStmt->execute([$user['id']]);
         if($user['role'] == 'student'){

            $_SESSION['user'] =serialize( new Student($user['id'],$user['name'],$user['email'],$user['password'],$user['created_at']));
    }else  if($user['role'] == 'teacher'){

        $_SESSION['user'] =serialize( new Teacher($user['id'],$user['name'],$user['email'],$user['password'],$user['created_at']));
    }else  if($user['role'] == 'admin'){

        $_SESSION['user'] = serialize(new Admin($user['id'],$user['name'],$user['email'],$user['password'],$user['created_at']));
    }else{

        return false;
    }
        

        return ['success' => true, 'message' => 'Login successful'];
    }

    // Logout method
    public function logout() {
        session_unset();
        session_destroy();
        
        return true;
    }

    // // Register new user
    // public function register($name, $email, $password, $role) {
    //     $db = new Database();
    //     $conn = $db->connect();
        
    //     // Check if email already exists
    //     $stmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
    //     $stmt->execute([$email]);
    //     if ($stmt->fetch()) {
    //         return ['success' => false, 'message' => 'Email already exists'];
    //     }
        
    //     // Hash password
    //     $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
    //     // Determine initial status based on role
    //     $status = ($role === 'teacher') ? 'pending' : 'active';
        
    //     // Insert new user
    //     $stmt = $conn->prepare("INSERT INTO user (name, email, password, role, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    //     $success = $stmt->execute([$name, $email, $hashedPassword, $role, $status]);
        
    //     if ($success) {
    //         return ['success' => true, 'message' => 'Registration successful'];
    //     }
    //     return ['success' => false, 'message' => 'Registration failed'];
    // }

    // Update user profile
    // public function updateProfile($userData) {
    //     $db = new Database();
    //     $conn = $db->connect();
        
    //     $allowedFields = ['name', 'email'];
    //     $updates = [];
    //     $params = [];
        
    //     foreach ($allowedFields as $field) {
    //         if (isset($userData[$field])) {
    //             $updates[] = "$field = ?";
    //             $params[] = $userData[$field];
    //         }
    //     }
        
    //     if (empty($updates)) {
    //         return ['success' => false, 'message' => 'No fields to update'];
    //     }
        
    //     $params[] = $this->id;
    //     $sql = "UPDATE user SET " . implode(', ', $updates) . " WHERE id = ?";
        
    //     $stmt = $conn->prepare($sql);
    //     $success = $stmt->execute($params);
        
    //     return ['success' => $success, 'message' => $success ? 'Profile updated' : 'Update failed'];
    // }

    // Check if user is logged in
    public static function isLoggedIn() {
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    // Check if user has specific role
    public function hasRole($role) {
        return $this->role === $role;
    }

    // Check if account is active
    // public function isActive() {
    //     return $this->status === 'active';
    // }
}
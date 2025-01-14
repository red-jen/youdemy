<?php
require_once('classes/config.inc.php');
// class User {
//     private $id;
//     private $name;
//     private $email;
//     private $password;
//     private $role; 

//     public function __construct($id, $name, $email, $password, $role) {
//         $this->id = $id;
//         $this->name = $name;
//         $this->email = $email;
//         $this->password = $password;
//         $this->role = $role;
//     }

//     // Getters
//     public function getId() {
//         return $this->id;
//     }

//     public function getName() {
//         return $this->name;
//     }

//     public function getEmail() {
//         return $this->email;
//     }

//     public function getRole() {
//         return $this->role;
//     }
//     public function login(string $email, string $password): bool {
//         $db = new Database();
//         $conn = $db->connect();
    
//         // Prepare the SQL statement
//         $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
//         $stmt->execute([$email]);
    
//         // Fetch the user data
//         $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
//         if (!$user) {
//             // Email does not exist
//             echo "Error: The email address does not exist.";
//             return false;
//         }
    
//         if (!password_verify($password, $user['password'])) {
//             // Password is incorrect
//             echo "Error: Incorrect password.";
//             return false;
//         }
    
//         // If email and password are correct, set session
//         $_SESSION['user'] = [
//             'id' => $user['id'],
//             'name' => $user['name'],
//             'role' => $user['role']
//         ];
    
//         return true;
//     }
    
    
// }








class User {
    private $id;
    private $name;
    private $email;
    private $password;
    private $role;
    // private $status; // For account status (active, suspended, pending)
    private $createdAt;
    // private $lastLogin;

    public function __construct($id, $name, $email, $password, $role) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        // $this->status = 'pending'; // Default status for new users
        $this->createdAt = date('Y-m-d H:i:s');
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
    public function login($email, $password) {
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("SELECT * FROM user WHERE email = ? "); // add this for statu//AND status = 'active'
        $stmt->execute([$email]);
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
        
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'role' => $user['role'],
            'status' => $user['status']
        ];
        
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
    public function updateProfile($userData) {
        $db = new Database();
        $conn = $db->connect();
        
        $allowedFields = ['name', 'email'];
        $updates = [];
        $params = [];
        
        foreach ($allowedFields as $field) {
            if (isset($userData[$field])) {
                $updates[] = "$field = ?";
                $params[] = $userData[$field];
            }
        }
        
        if (empty($updates)) {
            return ['success' => false, 'message' => 'No fields to update'];
        }
        
        $params[] = $this->id;
        $sql = "UPDATE user SET " . implode(', ', $updates) . " WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $success = $stmt->execute($params);
        
        return ['success' => $success, 'message' => $success ? 'Profile updated' : 'Update failed'];
    }

    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    // Check if user has specific role
    public function hasRole($role) {
        return $this->isLoggedIn() && $_SESSION['user']['role'] === $role;
    }

    // Check if account is active
    // public function isActive() {
    //     return $this->status === 'active';
    // }
}
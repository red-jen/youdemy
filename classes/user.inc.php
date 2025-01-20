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
        $this->status ; // Default status for new users
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
        
        $stmt = $conn->prepare("SELECT * FROM user WHERE email = ? or name = ? AND status != 'suspended' "); // add this for statu//AND status = 'active'
        $stmt->execute([$email , $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo $user['name'];
        if (!$user) {
            return ['success' => false, 'message' => 'Email not found or account not active'];
        }
        
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Incorrect password'];
        }
        
       
         if($user['role'] == 'student'){

            $_SESSION['user'] =serialize( new Student($user['id'],$user['name'],$user['email'],$user['password'],$user['created_at'],$user['status']));
    }else  if($user['role'] == 'teacher'){

        $_SESSION['user'] =serialize( new Teacher($user['id'],$user['name'],$user['email'],$user['password'],$user['created_at'],$user['status']));
    }else  if($user['role'] == 'admin'){

        $_SESSION['user'] = serialize(new Admin($user['id'],$user['name'],$user['email'],$user['password'],$user['created_at']));
    }else{

        return false;
    }
        

        return ['success' => true, 'message' => 'Login successful'];
    }

    public function logout() {
        session_unset();
        session_destroy();
        
        return true;
    }


    public function hasRole($role) {
        return $this->role === $role;
    }
    public function addCourse($courseData) {
        return "This user role cannot add courses.";
    }
    public function displayCourse($courseId) {
        return "This user role cannot display courses.";
    }
  
}
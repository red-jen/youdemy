<?php

require_once('config.inc.php');

class Visitor {
    public static function getCourseDetails($courseId) {
        $db = new Database();
        $conn = $db->connect();
        
        // Get course details with teacher information
        $stmt = $conn->prepare("
            SELECT c.*, 
                   u.name as teacher_name,
                   u.email as teacher_email,
                   cat.name as category_name,
                   (SELECT COUNT(*) FROM enrollment WHERE courseId = c.id) as enrolled_students
            FROM course c
            LEFT JOIN user u ON c.teacherId = u.id
            LEFT JOIN category cat ON c.categoryId = cat.id
            WHERE c.id = ?
        ");
        $stmt->execute([$courseId]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($course) {
            // Get course tags
            $stmt = $conn->prepare("
                SELECT t.name 
                FROM tag t
                JOIN course_tag ct ON t.id = ct.tagId
                WHERE ct.courseId = ?
            ");
            $stmt->execute([$courseId]);
            $course['tags'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        
        return $course;
    }
    public function searchCourses( $keyword){
            $db = new database();
            $conn =$db->connect();
            $stmt = $conn->prepare("SELECT * FROM course WHERE title LIKE ?");
            
        
            $stmt->execute(["%$keyword%"]);

            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $courses;
    }
  
    public static function viewCourseCatalog() {
        $db = new Database();
        $conn = $db->connect();

       
            
            $stmt = $conn->prepare("SELECT * FROM course");
            $stmt->execute();

         
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $courses; 
    }

    public static function registerUser($userData) {
        $signupValidator = new SignupContr(
            $userData['name'],
            $userData['email'],
            $userData['password'],
            $userData['passwordRepeat'],
            $userData['role']
        );

        // Run all validations
        $validationErrors = $signupValidator->validateAll();

        // If there are validation errors, return them
        if (!empty($validationErrors)) {
            return ['success' => false, 'errors' => $validationErrors];
        }

        // If validations pass, insert into the database
        $db = new Database();
$connection = $db->connect();
        $hashedPwd = password_hash($userData['password'], PASSWORD_DEFAULT);

        $stmt = $connection->prepare("INSERT INTO User (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $userData['name'],
            $userData['email'],
            $hashedPwd,
            $userData['role']
        ]);

        return ['success' => true, 'message' => 'User registered successfully'];
    }
}

class SignupContr {
    private $name;
    private $email;
    private $password;
    private $passwordRepeat;
    private $role;

    public function __construct($name, $email, $password, $passwordRepeat, $role) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->passwordRepeat = $passwordRepeat;
        $this->role = $role;
    }

    public function validateAll() {
        $errors = [];

        if ($this->validateEmptyInput()) $errors[] = $this->validateEmptyInput();
        if ($this->validateName()) $errors[] = $this->validateName();
        if ($this->validateEmail()) $errors[] = $this->validateEmail();
        if ($this->validateEmailExists()) $errors[] = $this->validateEmailExists();
        if ($this->validatePassword()) $errors[] = $this->validatePassword();
        if ($this->validatePasswordMatch()) $errors[] = $this->validatePasswordMatch();
        if ($this->validateRole()) $errors[] = $this->validateRole();

        return $errors;
    }

    private function validateEmptyInput() {
        if (empty($this->name) || empty($this->email) || empty($this->password) || empty($this->passwordRepeat) || empty($this->role)) {
            return "All fields are required.";
        }
        return null;
    }

    private function validateName() {
        if (strlen($this->name) < 2 || strlen($this->name) > 50) {
            return "Name must be between 2 and 50 characters.";
        }
        if (!preg_match("/^[a-zA-Z\s]+$/", $this->name)) {
            return "Name can only contain letters and spaces.";
        }
        return null;
    }

    private function validateEmail() {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format.";
        }
        return null;
    }

    private function validateEmailExists() {
        $db = new Database();
$connection = $db->connect();
        $stmt = $connection ->prepare("SELECT COUNT(*) FROM User WHERE email = ?");
        $stmt->execute([$this->email]);

        if ($stmt->fetchColumn() > 0) {
            return "Email already exists.";
        }
        return null;
    }

    private function validatePassword() {
        if (strlen($this->password) < 8) {
            return "Password must be at least 8 characters long.";
        }
        if (!preg_match("/[A-Z]/", $this->password)) {
            return "Password must contain at least one uppercase letter.";
        }
        if (!preg_match("/[a-z]/", $this->password)) {
            return "Password must contain at least one lowercase letter.";
        }
        if (!preg_match("/[0-9]/", $this->password)) {
            return "Password must contain at least one number.";
        }
        return null;
    }

    private function validatePasswordMatch() {
        if ($this->password !== $this->passwordRepeat) {
            return "Passwords do not match.";
        }
        return null;
    }

    private function validateRole() {
        $validRoles = ['Student', 'Teacher'];
        if (!in_array($this->role, $validRoles)) {
            return "Invalid role selected.";
        }
        return null;
    }
}

// Example usage
// $userData = [
//     'name' => 'John Doe',
//     'email' => 'johndoe@example.com',
//     'password' => 'Password123',
//     'passwordRepeat' => 'Password123',
//     'role' => 'Student'
// ];

// $result = Visitor::registerUser($userData);

// if ($result['success']) {
//     echo $result['message'];
// } else {
//     echo "Registration failed: " . implode(', ', $result['errors']);
// }

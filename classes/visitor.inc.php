<?php

require_once('config.inc.php');
class Visitor {
     


    public function searchCourses(string $keyword) {
        
        return [];
    }

    static function viewCourseCatalog(): array {
        // Implementation to view all available courses
        return [];
    }

    public function register(string $name, string $email, string $password, string $role): User {
        // Implementation for user registration
        switch ($role) {
            case 'Student':
                return new Student($name, $email, $password);
            case 'Teacher':
                return new Teacher($name, $email, $password);
            case 'Admin':
                return new Admin($name, $email, $password);
            default:
                throw new InvalidArgumentException("Invalid role");
        }
    }
}




class signupContr {
    private $uid;
    private $pwd;
    private $pwdRepeat;
    private $email;


    public function __construct($uid, $pwd, $pwdRepeat, $email){
        $this->uid = $uid;
        $this->pwd = $pwd;
        $this->pwdRepeat = $pwdRepeat;
        $this->email = $email;
    }


    function emptyInput(){
        $result;
        if(empty($this->uid) ||empty($this->pwd) ||empty($this->pwdRepeat) ||empty($this->email) ){
            $result = false;
        }else {
            $result = true;
        }
        return $result;
    }




    
    function invaliduid(){
        $result;
        if(!preg_match("/^[a-zA-Z0-9]*$/",$this->uid) ){
            $result = false;
        }else {
            $result = true;
        }
        return $result;
    }



    private function invalidEmail(){

        $result;
        
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL))
        
        $result = false;
        
        else
        
        $result = true;
        
        return $result;


    }


    private function pwdMatch() {
        $result;
        if ($this->pwd !== $this->pwdRepeat) {
            $result = false;
        } else {
            $result = true;
        }
        return $result;
    }
}
  
<?php
class User {
    private $id;
    private $name;
    private $email;
    private $password;
    private $role; 

    public function __construct($id, $name, $email, $password, $role) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }
}
<?php
class User {
    protected $name;
    protected $email;
    protected $password;
    protected $role;

    public function __construct($name, $email, $password, $role = 'client') {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getRole() {
        return $this->role;
    }
}
?>

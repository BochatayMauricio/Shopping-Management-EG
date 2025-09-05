<?php
class User {
    private $codUser;
    private $userName;
    private $userPassword;
    private $userType;
    private $userCategory;

    public function __construct($codUser, $userName, $userPassword, $userType, $userCategory) {
        $this->codUser = $codUser;
        $this->userName = $userName;
        $this->userPassword = $userPassword;
        $this->userType = $userType;
        $this->userCategory = $userCategory;
    }

    public function getCodUser() {
        return $this->codUser;
    }

    public function getUserName() {
        return $this->userName;
    }

    public function getUserPassword() {
        return $this->userPassword;
    }

    public function getUserType() {
        return $this->userType;
    }

    public function getUserCategory() {
        return $this->userCategory;
    }

    public function getEmail() {
        return $this->email;
    }
}

?>
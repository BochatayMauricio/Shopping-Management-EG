<?php

class User implements ArrayAccess {
    private $codUser;
    private $userName;
    private $userEmail;
    private $userPassword;
    private $userType;
    private $userCategory;

    private static $keyMap = [
        'cod' => 'codUser',
        'id' => 'codUser',
        'name' => 'userName',
        'email' => 'userEmail',
        'password' => 'userPassword',
        'type' => 'userType',
        'category' => 'userCategory'
    ];

    public function __construct($codUser, $userName, $userEmail, $userPassword, $userType, $userCategory) {
        $this->codUser = $codUser;
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->userPassword = $userPassword;
        $this->userType = $userType;
        $this->userCategory = $userCategory;
    }

    public static function fromArray($data) {
        return new self(
            $data['cod'] ?? $data['id'] ?? null,
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? '',
            $data['type'] ?? 'client',
            $data['category'] ?? 'inicial'
        );
    }

    // Implementación de ArrayAccess
    public function offsetExists($offset): bool {
        $prop = self::$keyMap[$offset] ?? $offset;
        return property_exists($this, $prop);
    }

    public function offsetGet($offset): mixed {
        $prop = self::$keyMap[$offset] ?? $offset;
        return $this->$prop ?? null;
    }

    public function offsetSet($offset, $value): void {
        $prop = self::$keyMap[$offset] ?? $offset;
        if (property_exists($this, $prop)) {
            $this->$prop = $value;
        }
    }

    public function offsetUnset($offset): void {
        $prop = self::$keyMap[$offset] ?? $offset;
        if (property_exists($this, $prop)) {
            $this->$prop = null;
        }
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

    public function getUserEmail() {
        return $this->userEmail;
    }

    // Setters
    public function setUserName($userName) {
        $this->userName = $userName;
    }

    public function setUserPassword($userPassword) {
        $this->userPassword = $userPassword;
    }

    public function setUserType($userType) {
        $this->userType = $userType;
    }

    public function setUserCategory($userCategory) {
        $this->userCategory = $userCategory;
    }

    public function setUserEmail($userEmail) {
        $this->userEmail = $userEmail;
    }

    // Método útil para convertir a array
    public function toArray() {
        return [
            'cod' => $this->codUser,
            'name' => $this->userName,
            'email' => $this->userEmail,
            'type' => $this->userType,
            'category' => $this->userCategory
        ];
    }
}

?>
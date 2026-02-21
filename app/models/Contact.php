<?php

class Contact {
    private $id;
    private $name;
    private $email;
    private $subjectType;
    private $message;
    private $createdAt;
    private $status;

    public function __construct($id, $name, $email, $subjectType, $message, $createdAt = null, $status = 'pending') {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->subjectType = $subjectType;
        $this->message = $message;
        $this->createdAt = $createdAt ?? date('Y-m-d H:i:s');
        $this->status = $status;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getSubjectType() {
        return $this->subjectType;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function getStatus() {
        return $this->status;
    }

    // Setters
    public function setName($name) {
        $this->name = $name;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setSubjectType($subjectType) {
        $this->subjectType = $subjectType;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    // Métodos útiles
    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'subject_type' => $this->subjectType,
            'message' => $this->message,
            'created_at' => $this->createdAt,
            'status' => $this->status
        ];
    }

    public function getFormattedDate($format = 'd/m/Y H:i') {
        return date($format, strtotime($this->createdAt));
    }

    public function isPending() {
        return $this->status === 'pending';
    }

    public function isResolved() {
        return $this->status === 'resolved';
    }
}

?>

<?php

class News implements ArrayAccess {
    private $id;
    private $title;
    private $description;
    private $image;
    private $author;
    private $date;

    private static $keyMap = [
        'id' => 'id',
        'title' => 'title',
        'description' => 'description',
        'image' => 'image',
        'author' => 'author',
        'date' => 'date'
    ];

    public function __construct($id, $title, $description, $image, $author, $date) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->author = $author;
        $this->date = $date;
    }

    public static function fromArray($data) {
        return new self(
            $data['id'] ?? null,
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['image'] ?? '',
            $data['author'] ?? '',
            $data['date'] ?? ''
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

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getImage() {
        return $this->image;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function getDate() {
        return $this->date;
    }

    // Setters
    public function setTitle($title) {
        $this->title = $title;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setImage($image) {
        $this->image = $image;
    }

    public function setAuthor($author) {
        $this->author = $author;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    // Método útil para convertir a array
    public function toArray() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'author' => $this->author,
            'date' => $this->date
        ];
    }

    // Formatear fecha para mostrar
    public function getFormattedDate($format = 'd/m/Y') {
        return date($format, strtotime($this->date));
    }
}

?>

<?php

class Store implements ArrayAccess {
    private $id;
    private $name;
    private $localNumber;
    private $ubication;
    private $category;
    private $color;
    private $logo;
    private $idOwner;

    // Mapeo de claves de array a propiedades
    private static $keyMap = [
        'id' => 'id',
        'name' => 'name',
        'local_number' => 'localNumber',
        'ubication' => 'ubication',
        'category' => 'category',
        'color' => 'color',
        'logo' => 'logo',
        'id_owner' => 'idOwner'
    ];

    public function __construct($id, $name, $localNumber, $ubication, $category, $color, $logo, $idOwner) {
        $this->id = $id;
        $this->name = $name;
        $this->localNumber = $localNumber;
        $this->ubication = $ubication;
        $this->category = $category;
        $this->color = $color;
        $this->logo = $logo;
        $this->idOwner = $idOwner;
    }

    // Método estático para crear desde array de BD
    public static function fromArray($data) {
        return new self(
            $data['id'] ?? null,
            $data['name'] ?? '',
            $data['local_number'] ?? '',
            $data['ubication'] ?? '',
            $data['category'] ?? '',
            $data['color'] ?? '',
            $data['logo'] ?? '',
            $data['id_owner'] ?? null
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

    public function getName() {
        return $this->name;
    }

    public function getLocalNumber() {
        return $this->localNumber;
    }

    public function getUbication() {
        return $this->ubication;
    }

    public function getCategory() {
        return $this->category;
    }

    public function getColor() {
        return $this->color;
    }

    public function getLogo() {
        return $this->logo;
    }

    public function getIdOwner() {
        return $this->idOwner;
    }

    // Setters
    public function setName($name) {
        $this->name = $name;
    }

    public function setLocalNumber($localNumber) {
        $this->localNumber = $localNumber;
    }

    public function setUbication($ubication) {
        $this->ubication = $ubication;
    }

    public function setCategory($category) {
        $this->category = $category;
    }

    public function setColor($color) {
        $this->color = $color;
    }

    public function setLogo($logo) {
        $this->logo = $logo;
    }

    public function setIdOwner($idOwner) {
        $this->idOwner = $idOwner;
    }

    // Método útil para convertir a array
    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'local_number' => $this->localNumber,
            'ubication' => $this->ubication,
            'category' => $this->category,
            'color' => $this->color,
            'logo' => $this->logo,
            'id_owner' => $this->idOwner
        ];
    }
}

?>

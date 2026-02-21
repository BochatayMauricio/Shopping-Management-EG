<?php

class Promotion implements ArrayAccess {
    private $id;
    private $title;
    private $description;
    private $image;
    private $dateFrom;
    private $dateUntil;
    private $clientCategory;
    private $weekDays;
    private $status;
    private $discount;
    private $price;
    private $originalPrice;
    private $idStore;
    
    // Propiedades adicionales para datos de JOIN
    private $storeName;
    private $storeLogo;
    private $storeColor;
    private $storeCategory;
    private $localNumber;
    private $validUntil;
    private $discountLabel;
    private $isExpired;
    private $obtainedAt;

    private static $keyMap = [
        'id' => 'id',
        'title' => 'title',
        'description' => 'description',
        'image' => 'image',
        'date_from' => 'dateFrom',
        'date_until' => 'dateUntil',
        'client_category' => 'clientCategory',
        'week_days' => 'weekDays',
        'status' => 'status',
        'discount' => 'discount',
        'price' => 'price',
        'original_price' => 'originalPrice',
        'id_store' => 'idStore',
        'store_name' => 'storeName',
        'store_logo' => 'storeLogo',
        'store_color' => 'storeColor',
        'store_category' => 'storeCategory',
        'local_number' => 'localNumber',
        'valid_until' => 'validUntil',
        'discount_label' => 'discountLabel',
        'is_expired' => 'isExpired',
        'obtained_at' => 'obtainedAt'
    ];

    public function __construct(
        $id, 
        $title, 
        $description, 
        $image, 
        $dateFrom, 
        $dateUntil, 
        $clientCategory, 
        $weekDays, 
        $status, 
        $discount, 
        $price, 
        $originalPrice, 
        $idStore
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->dateFrom = $dateFrom;
        $this->dateUntil = $dateUntil;
        $this->clientCategory = $clientCategory;
        $this->weekDays = $weekDays;
        $this->status = $status;
        $this->discount = $discount;
        $this->price = $price;
        $this->originalPrice = $originalPrice;
        $this->idStore = $idStore;
    }

    public static function fromArray($data) {
        $promo = new self(
            $data['id'] ?? null,
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['image'] ?? '',
            $data['date_from'] ?? '',
            $data['date_until'] ?? '',
            $data['client_category'] ?? '',
            $data['week_days'] ?? '',
            $data['status'] ?? '',
            $data['discount'] ?? 0,
            $data['price'] ?? 0,
            $data['original_price'] ?? 0,
            $data['id_store'] ?? null
        );
        
        // Datos adicionales de JOINs
        $promo->storeName = $data['store_name'] ?? '';
        $promo->storeLogo = $data['store_logo'] ?? '';
        $promo->storeColor = $data['store_color'] ?? '';
        $promo->storeCategory = $data['store_category'] ?? '';
        $promo->localNumber = $data['local_number'] ?? '';
        $promo->validUntil = $data['valid_until'] ?? '';
        $promo->discountLabel = $data['discount_label'] ?? '';
        $promo->isExpired = $data['is_expired'] ?? 0;
        $promo->obtainedAt = $data['obtained_at'] ?? '';
        
        return $promo;
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

    public function getDateFrom() {
        return $this->dateFrom;
    }

    public function getDateUntil() {
        return $this->dateUntil;
    }

    public function getClientCategory() {
        return $this->clientCategory;
    }

    public function getWeekDays() {
        return $this->weekDays;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getDiscount() {
        return $this->discount;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getOriginalPrice() {
        return $this->originalPrice;
    }

    public function getIdStore() {
        return $this->idStore;
    }

    // Getters para datos de JOIN
    public function getStoreName() {
        return $this->storeName;
    }

    public function getStoreLogo() {
        return $this->storeLogo;
    }

    public function getStoreColor() {
        return $this->storeColor;
    }

    public function getStoreCategory() {
        return $this->storeCategory;
    }

    public function getLocalNumber() {
        return $this->localNumber;
    }

    public function getValidUntil() {
        return $this->validUntil;
    }

    public function getIsExpired() {
        return $this->isExpired;
    }

    public function getObtainedAt() {
        return $this->obtainedAt;
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

    public function setDateFrom($dateFrom) {
        $this->dateFrom = $dateFrom;
    }

    public function setDateUntil($dateUntil) {
        $this->dateUntil = $dateUntil;
    }

    public function setClientCategory($clientCategory) {
        $this->clientCategory = $clientCategory;
    }

    public function setWeekDays($weekDays) {
        $this->weekDays = $weekDays;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setDiscount($discount) {
        $this->discount = $discount;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function setOriginalPrice($originalPrice) {
        $this->originalPrice = $originalPrice;
    }

    public function setIdStore($idStore) {
        $this->idStore = $idStore;
    }

    // Métodos útiles
    public function toArray() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'date_from' => $this->dateFrom,
            'date_until' => $this->dateUntil,
            'client_category' => $this->clientCategory,
            'week_days' => $this->weekDays,
            'status' => $this->status,
            'discount' => $this->discount,
            'price' => $this->price,
            'original_price' => $this->originalPrice,
            'id_store' => $this->idStore
        ];
    }

    public function getDiscountLabel() {
        return '-' . intval($this->discount) . '% OFF';
    }

    public function getFormattedValidUntil($format = 'd/m') {
        return date($format, strtotime($this->dateUntil));
    }

    public function isActive() {
        return $this->status === 'active';
    }

    public function isExpired() {
        return strtotime($this->dateUntil) < time();
    }
}

?>

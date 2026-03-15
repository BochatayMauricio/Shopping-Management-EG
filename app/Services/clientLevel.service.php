<?php

/**
 * Clase de Niveles de Cliente
 * Centraliza la configuración de categorías/niveles de usuarios.
 */

class ClientLevel
{
    // Constantes de niveles
    const INICIAL = 'inicial';
    const MEDIUM = 'medium';
    const PREMIUM = 'premium';

    // Umbrales de promociones canjeadas para cada nivel
    const THRESHOLD_MEDIUM = 3;
    const THRESHOLD_PREMIUM = 5;

    // Pesos para comparación de niveles
    const WEIGHTS = [
        self::INICIAL => 1,
        self::MEDIUM => 2,
        self::PREMIUM => 3
    ];

    // Labels para mostrar en la UI
    const LABELS = [
        self::INICIAL => 'Inicial',
        self::MEDIUM => 'Medium',
        self::PREMIUM => 'Premium'
    ];

    /**
     * Obtiene todos los niveles disponibles
     * @return array
     */
    public static function getAll()
    {
        return [self::INICIAL, self::MEDIUM, self::PREMIUM];
    }

    /**
     * Obtiene el label formateado para mostrar en UI
     * @param string $level
     * @return string
     */
    public static function getLabel($level)
    {
        $normalizedLevel = strtolower($level);
        return self::LABELS[$normalizedLevel] ?? ucfirst($level);
    }

    /**
     * Obtiene el peso de un nivel para comparaciones
     * @param string $level
     * @return int
     */
    public static function getWeight($level)
    {
        $normalizedLevel = strtolower($level);
        return self::WEIGHTS[$normalizedLevel] ?? 0;
    }

    /**
     * Determina el nivel basado en cantidad de promos canjeadas
     * @param int $usedPromos Cantidad de promociones usadas
     * @return string El nivel correspondiente
     */
    public static function calculateLevel($usedPromos)
    {
        if ($usedPromos >= self::THRESHOLD_PREMIUM) {
            return self::PREMIUM;
        } elseif ($usedPromos >= self::THRESHOLD_MEDIUM) {
            return self::MEDIUM;
        }
        return self::INICIAL;
    }

    /**
     * Determina el próximo nivel y cuántas promos faltan
     * @param int $usedPromos
     * @return array ['next_level' => string|null, 'remaining' => int]
     */
    public static function getNextLevelInfo($usedPromos)
    {
        if ($usedPromos >= self::THRESHOLD_PREMIUM) {
            return ['next_level' => null, 'remaining' => 0, 'is_max' => true];
        } elseif ($usedPromos >= self::THRESHOLD_MEDIUM) {
            return [
                'next_level' => self::PREMIUM,
                'remaining' => self::THRESHOLD_PREMIUM - $usedPromos,
                'is_max' => false
            ];
        }
        return [
            'next_level' => self::MEDIUM,
            'remaining' => self::THRESHOLD_MEDIUM - $usedPromos,
            'is_max' => false
        ];
    }

    /**
     * Verifica si un usuario puede acceder a una promo de cierto nivel
     * @param string $userLevel Nivel del usuario
     * @param string $promoLevel Nivel requerido por la promo
     * @return bool
     */
    public static function canAccess($userLevel, $promoLevel)
    {
        return self::getWeight($userLevel) >= self::getWeight($promoLevel);
    }

    /**
     * Verifica si el nivel es el máximo (Premium)
     * @param string $level
     * @return bool
     */
    public static function isMaxLevel($level)
    {
        return strtolower($level) === self::PREMIUM;
    }

    /**
     * Obtiene opciones para un select HTML
     * @return array
     */
    public static function getSelectOptions()
    {
        return self::LABELS;
    }
}

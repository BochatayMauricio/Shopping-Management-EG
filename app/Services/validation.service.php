<?php
/**
 * Servicio de Validación Reutilizable
 * Centraliza las validaciones de email, contraseña y otros campos comunes.
 */

class ValidationService {
    
    // Constantes de configuración
    const PASSWORD_MIN_LENGTH = 6;
    const PASSWORD_MAX_LENGTH = 20;
    const USERNAME_MIN_LENGTH = 3;
    const USERNAME_MAX_LENGTH = 50;

    /**
     * Valida el formato del email
     * @param string $email
     * @return bool
     */
    public static function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida la longitud de la contraseña (entre 6 y 20 caracteres)
     * @param string $password
     * @return bool
     */
    public static function isValidPassword($password) {
        $length = strlen($password);
        return $length >= self::PASSWORD_MIN_LENGTH && $length <= self::PASSWORD_MAX_LENGTH;
    }

    /**
     * Valida que dos contraseñas coincidan
     * @param string $password
     * @param string $confirmPassword
     * @return bool
     */
    public static function passwordsMatch($password, $confirmPassword) {
        return $password === $confirmPassword;
    }

    /**
     * Valida la longitud del nombre de usuario
     * @param string $username
     * @return bool
     */
    public static function isValidUsername($username) {
        $length = strlen(trim($username));
        return $length >= self::USERNAME_MIN_LENGTH && $length <= self::USERNAME_MAX_LENGTH;
    }

    /**
     * Valida que un campo no esté vacío
     * @param mixed $value
     * @return bool
     */
    public static function isNotEmpty($value) {
        if (is_string($value)) {
            return strlen(trim($value)) > 0;
        }
        return !empty($value);
    }

    /**
     * Valida múltiples campos requeridos a la vez
     * @param array $fields Array de valores a validar
     * @return bool True si todos tienen contenido
     */
    public static function areFieldsNotEmpty(array $fields) {
        foreach ($fields as $field) {
            if (!self::isNotEmpty($field)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Obtiene el mensaje de error para contraseña inválida
     * @return string
     */
    public static function getPasswordErrorMessage() {
        return 'La contraseña debe tener entre ' . self::PASSWORD_MIN_LENGTH . ' y ' . self::PASSWORD_MAX_LENGTH . ' caracteres.';
    }

    /**
     * Obtiene el mensaje de error para email inválido
     * @return string
     */
    public static function getEmailErrorMessage() {
        return 'El formato del correo electrónico no es válido.';
    }

    /**
     * Obtiene el mensaje de error para contraseñas que no coinciden
     * @return string
     */
    public static function getPasswordMismatchMessage() {
        return 'Las contraseñas no coinciden.';
    }

    /**
     * Obtiene el mensaje de error para campos vacíos
     * @return string
     */
    public static function getEmptyFieldsMessage() {
        return 'Por favor, completa todos los campos.';
    }

    /**
     * Obtiene el mensaje de error para username duplicado
     * @return string
     */
    public static function getUsernameExistsMessage() {
        return 'Este nombre de usuario ya está registrado.';
    }

    /**
     * Obtiene el mensaje de error para email duplicado
     * @return string
     */
    public static function getEmailExistsMessage() {
        return 'Este correo electrónico ya está registrado.';
    }
}

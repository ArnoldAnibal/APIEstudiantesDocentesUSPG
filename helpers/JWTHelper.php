<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHelper {
    private static $jwt_secret = 'B8AHgIk26d55';

    /**
     * Extrae el país del token JWT en el header Authorization.
     * Espera "Bearer TOKEN".
     * Retorna GT por defecto si no encuentra el token o hay error.
     */
    public static function getPaisFromToken(string $authHeader): string {
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            try {
                $decoded = JWT::decode($token, new Key(self::$jwt_secret, 'HS256'));
                return strtoupper($decoded->pais ?? 'GT');
            } catch (Exception $ex) {
                return 'GT';
            }
        }
        return 'GT';
    }

    public static function getUserIdFromToken(string $authHeader): ?int {
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
        try {
            $decoded = JWT::decode($token, new Key(self::$jwt_secret, 'HS256'));
            return isset($decoded->id) ? (int)$decoded->id : null;
        } catch (Exception $ex) {
            error_log("Error al decodificar token: " . $ex->getMessage());
            return null;
        }
    }
    error_log("No se encontró token en Authorization");
    return null;
}





    /**
     * Extrae el país del token JWT en el header Authorization.
     * Espera "Bearer TOKEN".
     * Lanza excepción si no hay token o es inválido.
     */
    public static function obtenerPaisToken(): string {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        if (!$authHeader) {
            throw new Exception("Authorization header no encontrado.");
        }

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            throw new Exception("Formato del token inválido. Esperado: 'Bearer TOKEN'.");
        }

        $token = $matches[1];
        try {
            $decoded = JWT::decode($token, new Key(self::$jwt_secret, 'HS256'));
            if (!isset($decoded->pais)) {
                throw new Exception("El token no contiene el campo 'pais'.");
            }
            return strtoupper($decoded->pais);
        } catch (Exception $ex) {
            throw new Exception("Token inválido: " . $ex->getMessage());
        }
    }
}

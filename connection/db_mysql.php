<?php
class MySQLConnection {
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn === null) {

            $host = "localhost";
            $user = "root";
            $pass = "";  // déjalo vacío si no usas contraseña
            $db   = "uspg";

            self::$conn = new mysqli($host, $user, $pass, $db);

            if (self::$conn->connect_error) {
                throw new Exception("Error de conexión MySQL via mysqli: " . self::$conn->connect_error);
            }

            self::$conn->set_charset("utf8mb4");
        }

        return self::$conn;
    }
}

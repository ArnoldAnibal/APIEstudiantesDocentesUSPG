<?php
class PostgreSQLConnection {
    private static $conn;

    public static function getConnection() {
        if (!self::$conn) {
            $dsn = "pgsql:host=192.168.153.133;dbname=uspg;port=5432";
            self::$conn = new PDO($dsn, "postgres", "admin1234", [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        }
        return self::$conn;
    }
}

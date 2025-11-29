<?php
class SQLServerConnection {
    private static $conn;

    public static function getConnection() {
        if (!self::$conn) {
            $dsn = "sqlsrv:Server=192.168.153.136,1433;Database=uspg";
            self::$conn = new PDO($dsn, "admin", "admin1234", [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        }
        return self::$conn;
    }
}

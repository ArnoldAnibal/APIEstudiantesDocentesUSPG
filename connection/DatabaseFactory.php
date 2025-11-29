<?php
require_once __DIR__ . '/db_mysql.php';
require_once __DIR__ . '/db_postgres.php';
require_once __DIR__ . '/db_sqlserver.php';

class DatabaseFactory {
    public static function getConnection($pais) {
        switch ($pais) {
            case 'GT':
                return MySQLConnection::getConnection();
            case 'SV':
                return PostgreSQLConnection::getConnection();
            case 'HN':
                return SQLServerConnection::getConnection();
            default:
                throw new Exception("País no reconocido: $pais");
        }
    }
}

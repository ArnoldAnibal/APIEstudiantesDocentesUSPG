<?php
// Clase de conexión a la base de datos usando mysqli

class Database {  // clase de conexión
    private static $host = "localhost";
    private static $usuario = "root";  // usuario de la bd
    private static $clave= "";  
    private static $bd = "uspg";  // nombre de la bd

    public static function getConnection(): mysqli {  // declaramos un metodo estatico publico que devuelva un objeto tipo mysqli. Lo estatico nos ayuda a llamarle sin hacerle una instancia a la clase get conection
        $mysqli = new mysqli(self::$host, self::$usuario, self::$clave, self::$bd);

        if ($mysqli->connect_error) {  // si hay un error de conexion, terminamos el codigo con el die.
            die("Error de conexión mysqli: " . $mysqli->connect_error);
        }

        // Forzar UTF-8
        $mysqli->set_charset("utf8");

        return $mysqli; // devolvemos el objeto de conexión mysqli para poder usarlo en otros lugares
    }

}
?>
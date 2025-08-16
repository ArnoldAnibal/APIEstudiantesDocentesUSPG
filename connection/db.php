<?php
// Clase de conexión a la base de datos usando mysqli

class ConexionBD {  // clase de conexión
    private $host = "localhost";
    private $usuario = "root";  // usuario de la bd
    private $clave = "";  
    private $bd = "uspg";  // nombre de la bd
    private $conexion;

    public function __construct() {  // constructor que se ejecuta para crear un objeto de conexión
        // Creamos la conexión a la base de datos
        $this->conexion = new mysqli($this->host, $this->usuario, $this->clave, $this->bd);

        // Verificamos si hay error en la conexión
        if ($this->conexion->connect_error) {
            die("Error de conexión a la base de datos: " . $this->conexion->connect_error);
        }
    }

    public function getConexion() {  // creamos un método publico para obtener la conexión activa
        return $this->conexion;
    }
}
?>
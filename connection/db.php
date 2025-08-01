<?php
class DB {
    // parametros para la conexión con la bd
private $host = "localhost";
private $dbName = "uspg";
private $username = "root";
private $password = "";
public $connection;

// funcion de conexión con mysqli ya que no se pudo con PDO
public function connectionDB(){
    //inicio de conexión
    $this->connection = new mysqli(
        $this->host,
            $this->username,
            $this->password,
            $this->dbName
    );

    // condicional para confirmar si funciono
    if ($this->connection->connect_error) {
            die(json_encode(["error" => "Error de conexión: " . $this->connection->connect_error]));
        }

    //configuracion del charset a usar en la conexion
    $this->connection->set_charset("utf8mb4");

    // retornar la conexion exitosa para usarla en los otros elementos
    return $this->connection;
}
}

?>
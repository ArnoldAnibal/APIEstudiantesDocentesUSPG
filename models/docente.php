<?php
class Docente{
    // creamos el objeto de conexión a la bd y tambien declaramos el nombre de la tabla
    private $connection;
    private $table = "docentes";

    // declaramos las variables que nos serviran para manipular los datos que ingresemos en los queries
    public $id;
    public $nombres;
    public $apellidos;

    // iniciamos una nueva conexion o instancia de conexion
    public function __construct($db) {
        $this->connection = $db;
    }

    // hacemos un get con la conexion ya iniciada
    public function obtenerTodosRegistros(){
        $sql = "SELECT * FROM {$this->table}";
        $result = $this->connection->query($sql);
        return $result; // mysqli_result object
    }

    // hacemos un create/post con la conexion ya iniciada
    public function crear(){
        $sql = "INSERT INTO {$this->table} (nombres, apellidos) VALUES (?, ?)";
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param("ss", $this->nombres, $this->apellidos);
        return $stmt->execute();
    }

    // hacemos un update/put con la conexion ya iniciada
    public function actualizar(){
        $sql = "UPDATE {$this->table} SET nombres = ?, apellidos = ? WHERE id = ?";
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param("ssi", $this->nombres, $this->apellidos, $this->id);
        return $stmt->execute();
    }

    // hacemos un delete con la conexion ya iniciada
    public function eliminar(){
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param("i", $this->id);
        return $stmt->execute();
    }

}


?>
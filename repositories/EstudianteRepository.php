<?php
// Repository que maneja el acceso a datos de los estudiantes

// se incluye la conexion a la base de datos y el modelo
require_once __DIR__ . '/../connection/db.php';
require_once __DIR__ . '/../models/Estudiante.php';

class EstudianteRepository {
     // almacenamos la conexion a la base de datos
    private $conexion;

    // constructor que inicia la conexion a la base de datos
    public function __construct() {
        $db = new ConexionBD(); // se hace la instancia
        $this->conexion = $db->getConexion(); // se obtiene el objeto de conexion y se asigna a conexion
    }

// metodo parra obtener todos los registros de la bd
    public function obtenerTodos() {
        $resultado = $this->conexion->query("SELECT * FROM estudiantes");  // se ejecuta la consulta de sql
        return $resultado->fetch_all(MYSQLI_ASSOC); // se devuelven los resultados en tipo arreglo
    }

    public function obtenerPorId($id) {
        $stmt = $this->conexion->prepare("SELECT * FROM estudiantes WHERE id = ?");  // solo preparamos  query
        $stmt->bind_param("i", $id); // asociamos el valor de id con el parametro que nos dan
        $stmt->execute();  // ejecutamos
        return $stmt->get_result()->fetch_assoc(); // devolvemos el resultado como un arreglo asociado
    }

    public function crear(Estudiante $estudiante) {
        $stmt = $this->conexion->prepare("INSERT INTO estudiantes (nombres, apellidos) VALUES (?, ?)");  // preparamos la consulta
        $nombres = $estudiante->getNombres();  // sacamos los nombres del objeto
        $apellidos = $estudiante->getApellidos(); // sacamos los apellidos del objeto 
        $stmt->bind_param("ss", $nombres, $apellidos);  // determinamos el tipo de dato s de string a los parametros de la conslta
        return $stmt->execute();  // ejecutamos y luego devolvemos un tru
    }

    public function actualizar(Estudiante $estudiante) {
        $stmt = $this->conexion->prepare("UPDATE estudiantes SET nombres=?, apellidos=? WHERE id=?");  // preparamos consulta
        $nombres = $estudiante->getNombres();  // sacamos el nombre del objeto
        $apellidos = $estudiante->getApellidos();  // sacamos el apellido de objeto
        $id = $estudiante->getId();  // sacamos el ID del objeto 
        $stmt->bind_param("ssi", $nombres, $apellidos, $id);  // determinamos el tipo de datos, i de entero
        return $stmt->execute();
    }

    public function eliminar($id) {
        $stmt = $this->conexion->prepare("DELETE FROM estudiantes WHERE id=?");  // prepara la consulta
        $stmt->bind_param("i", $id); // asocia el id que es parametro con nuestra consulta
        return $stmt->execute();
    }
}
?>
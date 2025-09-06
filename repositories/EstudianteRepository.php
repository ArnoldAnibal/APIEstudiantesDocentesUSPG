<?php
// Repositorio que maneja el acceso a datos de los estudiantes, aca se implementan las capas de acceso a los datos para la entidad Estudiante. Se encarga de consultar, insertar, actualizar y eliminar los docuemtnes de la base de datos

// se incluye la conexion a la base de datos y el modelo
require_once __DIR__ . '/../connection/db.php';
require_once __DIR__ . '/../models/Estudiante.php';
require_once __DIR__ . '/../mapper/EstudianteMapper.php';

class EstudianteRepository {
     // almacenamos la conexion a la base de datos
    private $conexion;

    // constructor que inicia la conexion a la base de datos
    public function __construct() {
        $this->conexion = Database::getConnection();
    }

// metodo parra obtener todos los registros de la bd
    public function findAll(): array {
        $result = $this->conexion->query("SELECT * FROM estudiantes");  // se ejecuta la consulta de sql
        $estudiantes = [];

        if (!$result) {
            die("Error en la consulta: " . $this->conexion->error);
        }

        // se recorre cada fila y se convierte en un objeto estudiante con el mapper luego se guarda en un array para retornar el arry con todos los estudiantes

        while ($row = $result->fetch_assoc()) {
            $estudiantes[] = EstudianteMapper::mapRowToEstudiante($row);
        }

        return $estudiantes;
    }

    // busca un estudiante por su ID usando una consulta preparada, si encuentra el registro, lo transforma a un objeto Estudiante si no, da null
    public function findById($id): ?Estudiante {
        $stmt = $this->conexion->prepare("SELECT * FROM estudiantes WHERE id = ?");  // solo preparamos  query
        $stmt->bind_param("i", $id); // asociamos el valor de id con el parametro que nos dan
        $stmt->execute();  // ejecutamos
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row ? EstudianteMapper::mapRowToEstudiante($row) : null;
    }

    // se recibe un objeto estudiante, lo convierte en array con Mapper, y luego lo inserta en la BD para devolver true o false en base a la operación
    public function create(Estudiante $estudiante): bool {
        $data = EstudianteMapper::mapEstudianteToRow($estudiante);
        $stmt = $this->conexion->prepare("INSERT INTO estudiantes (nombres, apellidos) VALUES (?, ?)");  // preparamos la consulta
        $stmt->bind_param("ss", $data['nombres'], $data['apellidos']);
        return $stmt->execute();  // ejecutamos y luego devolvemos un tru
    }

    public function update(Estudiante $estudiante): bool {
        $data = EstudianteMapper::mapEstudianteToRow($estudiante);
        $stmt = $this->conexion->prepare("UPDATE estudiantes SET nombres=?, apellidos=? WHERE id=?");  // preparamos consulta
        $stmt->bind_param("ssi", $data['nombres'], $data['apellidos'], $data['id']);
        $stmt->execute();
        
        // Retorna true solo si alguna fila fue modificada
        return $stmt->affected_rows > 0;
    }

    public function delete($id): bool {
        $stmt = $this->conexion->prepare("DELETE FROM estudiantes WHERE id=?");  // prepara la consulta
        $stmt->bind_param("i", $id); // asocia el id que es parametro con nuestra consulta
        $stmt->execute();

        // Retorna true solo si alguna fila fue eliminada
        return $stmt->affected_rows > 0;
    }
}
?>
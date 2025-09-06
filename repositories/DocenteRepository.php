<?php
// Repositorio que maneja el acceso a datos de los docentes, aca se implementan las capas de acceso a los datos para la entidad Docente. Se encarga de consultar, insertar, actualizar y eliminar los docuemtnes de la base de datos

// se incluye la conexion a la base de datos y el modelo
require_once __DIR__ . '/../connection/db.php';
require_once __DIR__ . '/../models/Docente.php';
require_once __DIR__ . '/../mapper/DocenteMapper.php';

class DocenteRepository {
    // almacenamos la conexion a la base de datos
    private $conexion;

    // constructor que inicia la conexion a la base de datos
    public function __construct() {
        $this->conexion = Database::getConnection(); //creamos la conexión
    }

    // metodo parra obtener todos los registros de la bd
    public function findAll(): array {
        $result = $this->conexion->query("SELECT * FROM docentes");
        $docentes = [];

        if (!$result) {
            die("Error en la consulta: " . $this->conexion->error);
        }

        // se recorre cada fila y se convierte en un objeto docente con el mapper luego se guarda en un array para retornar el arry con todos los docentes
        while ($row = $result->fetch_assoc()) {
            $docentes[] = DocenteMapper::mapRowToDocente($row);
        }

        return $docentes;
    }

    // busca un docente por su ID usando una consulta preparada, si encuentra el registro, lo transforma a un objeto Docente si no, da null
    public function findById($id): ?Docente {
        $stmt = $this->conexion->prepare("SELECT * FROM docentes WHERE id = ?"); // solo preparamos  query
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row ? DocenteMapper::mapRowToDocente($row) : null;
    }

    // se recibe un objeto docente, lo convierte en array con Mapper, y luego lo inserta en la BD para devolver true o false en base a la operación
    public function create(Docente $docente): bool {
        $data = DocenteMapper::mapDocenteToRow($docente);
        $stmt = $this->conexion->prepare("INSERT INTO docentes (nombres, apellidos) VALUES (?, ?)");
        $stmt->bind_param("ss", $data['nombres'], $data['apellidos']);
        return $stmt->execute();
    }

    public function update(Docente $docente): bool {
        $data = DocenteMapper::mapDocenteToRow($docente);
        $stmt = $this->conexion->prepare("UPDATE docentes SET nombres = ?, apellidos = ? WHERE id = ?");
        $stmt->bind_param("ssi", $data['nombres'], $data['apellidos'], $data['id']);
        $stmt->execute();

        // Retorna true solo si alguna fila fue modificada
        return $stmt->affected_rows > 0;
    }

    public function delete($id): bool {
        $stmt = $this->conexion->prepare("DELETE FROM docentes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Retorna true solo si alguna fila fue eliminada
        return $stmt->affected_rows > 0;
    }
}
?>
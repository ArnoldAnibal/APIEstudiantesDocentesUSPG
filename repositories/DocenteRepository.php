<?php
// Repositorio que maneja el acceso a datos de los docentes, aca se implementan las capas de acceso a los datos para la entidad Docente. Se encarga de consultar, insertar, actualizar y eliminar los docuemtnes de la base de datos

// se incluye la conexion a la base de datos y la entity y los mapper
require_once __DIR__ . '/../connection/db.php';
require_once __DIR__ . '/../entities/Docente.php';
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
            die("Error en la consulta: " . $this->conexion->error);  // manejo de errores
        }

        // se recorre cada fila y se convierte en un objeto docente con el mapper luego se guarda en un array para retornar el arry con todos los docentes
        while ($row = $result->fetch_assoc()) {
            $docentes[] = DocenteMapper::mapRowToEntity($row);
        }

        return $docentes;  // devolvemos el array con todos los docentes
    }

    // busca un docente por su ID usando una consulta preparada, si encuentra el registro, lo transforma a un DTO Docente si no, da null
    public function findById($id): ?DocenteResponseDTO {
        $stmt = $this->conexion->prepare("SELECT * FROM docentes WHERE id = ?"); // solo preparamos  query
        $stmt->bind_param("i", $id);  // asociamos el ID con el parámetro
        $stmt->execute();
        $result = $stmt->get_result();  // obtenemos el resultado
        $row = $result->fetch_assoc();  // tomamos la primer fila

        // si existe la fina, devolvemos el DTO si no, mandamos null
        return $row ? new DocenteResponseDTO($row['id'], $row['nombres'], $row['apellidos']) : null;
    }

    // se recibe un objeto docente, lo convierte en DTO con Mapper, y luego lo inserta en la BD para devolver true o false en base a la operación
    public function create(Docente $docente): DocenteResponseDTO {
        $data = DocenteMapper::mapDocenteToRow($docente);  // convertir la entidad en un array
        $stmt = $this->conexion->prepare("INSERT INTO docentes (nombres, apellidos, UsuarioCreacion, FechaCreacion) VALUES (?, ?, ?, ?)");  // preparamos la consulta
        $stmt->bind_param("ssss", $data['nombres'], $data['apellidos'], $data['UsuarioCreacion'], $data['FechaCreacion']);
        if (!$stmt->execute()) {
        return null; // fallo en la inserción
        }
        
        // obtenemos el ID generado al objeto
        $id = $this->conexion->insert_id;
        
        // Solo devolvemos un DTO con los campos que queremos en la respuesta
        return new DocenteResponseDTO($id, $data['nombres'], $data['apellidos']);

    }

    // se recibe un objeto docente con los datos actualizaos y retornaremos un DTO
    public function update(Docente $docente): DocenteResponseDTO {
        $data = DocenteMapper::mapDocenteToRow($docente);  // convertimos la entidad en un array
        $stmt = $this->conexion->prepare("UPDATE docentes SET nombres=?, apellidos=?, UsuarioModificacion=?, FechaModificacion=? WHERE id=?");  // preparamos consulta
        $stmt->bind_param("ssssi", $data['nombres'], $data['apellidos'], $data['UsuarioModificacion'], $data['FechaModificacion'], $data['id']);
        $stmt->execute();

        // retornamos el dto del docente actualizado
        return $this->findById($data['id']);
    }

    // eliminar, recibimos un id del docente y devolvemos un bool
    public function delete($id): bool {
        $stmt = $this->conexion->prepare("DELETE FROM docentes WHERE id = ?");
        $stmt->bind_param("i", $id);  // asociamos el ID con el parámetro
        $stmt->execute();

        // retornamos true solo si alguna fila fue eliminada
        return $stmt->affected_rows > 0;
    }
}
?>
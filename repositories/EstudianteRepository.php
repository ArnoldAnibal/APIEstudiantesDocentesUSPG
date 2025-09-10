<?php
// Repositorio que maneja el acceso a datos de los esdutiantes, aca se implementan las capas de acceso a los datos para la entidad Estudiante. Se encarga de consultar, insertar, actualizar y eliminar los docuemtnes de la base de datos

// se incluye la conexion a la base de datos y la entity y los mapper
require_once __DIR__ . '/../connection/db.php';
require_once __DIR__ . '/../entities/Estudiante.php';
require_once __DIR__ . '/../mapper/EstudianteMapper.php';

class EstudianteRepository {
     // almacenamos la conexion a la base de datos
    private $conexion;

    // constructor que inicia la conexion a la base de datos
    public function __construct() {
        $this->conexion = Database::getConnection(); //creamos la conexión
    }

// metodo parra obtener todos los registros de la bd
    public function findAll(): array {
        $result = $this->conexion->query("SELECT * FROM estudiantes");  // se ejecuta la consulta de sql
        $estudiantes = [];

        if (!$result) {
            die("Error en la consulta: " . $this->conexion->error);  // manejo de errores
        }

        // se recorre cada fila y se convierte en un objeto estudiante con el mapper luego se guarda en un array para retornar el arry con todos los estudiantes

        while ($row = $result->fetch_assoc()) {
            $estudiantes[] = EstudianteMapper::mapRowToEntity($row);
        }

        return $estudiantes;  // devolvemos el array con todos los estudiantes
    }

    // busca un estudiante por su ID usando una consulta preparada, si encuentra el registro, lo transforma a un DTO Estudiante si no, da null
    public function findById($id): ?EstudianteResponseDTO  {
        $stmt = $this->conexion->prepare("SELECT * FROM estudiantes WHERE id = ?");  // solo preparamos  query
        $stmt->bind_param("i", $id); // asociamos el valor de id con el parametro que nos dan
        $stmt->execute();  // ejecutamos
        $result = $stmt->get_result(); // obtenemos el resultado
        $row = $result->fetch_assoc(); // tomamos la primer fila

        // si existe la fina, devolvemos el DTO si no, mandamos null
        return $row ? new EstudianteResponseDTO($row['id'], $row['nombres'], $row['apellidos']) : null;
    }

    // se recibe un objeto estudiante, lo convierte en DTO con Mapper, y luego lo inserta en la BD para devolver true o false en base a la operación
    public function create(Estudiante $estudiante): ?EstudianteResponseDTO  {
        $data = EstudianteMapper::mapEstudianteToRow($estudiante);  // convertir la entidad en un array
        $stmt = $this->conexion->prepare("INSERT INTO estudiantes (nombres, apellidos, UsuarioCreacion, FechaCreacion) VALUES (?, ?, ?, ?)");  // preparamos la consulta
        $stmt->bind_param("ssss", $data['nombres'], $data['apellidos'], $data['UsuarioCreacion'], $data['FechaCreacion']);
        
        if (!$stmt->execute()) {
        return null; // fallo en la inserción
        }
        
        // Asignamos el ID generado al objeto
        $id = $this->conexion->insert_id;
        
        // Solo devolvemos un DTO con los campos que queremos en la respuesta
        return new EstudianteResponseDTO($id, $data['nombres'], $data['apellidos']);
    }

    // se recibe un objeto docente con los datos actualizaos y retornaremos un DTO
    public function update(Estudiante $estudiante): EstudianteResponseDTO {
        $data = EstudianteMapper::mapEstudianteToRow($estudiante); // convertimos la entidad en un array
        $stmt = $this->conexion->prepare("UPDATE estudiantes SET nombres=?, apellidos=?, UsuarioModificacion=?, FechaModificacion=? WHERE id=?");  // preparamos consulta
        $stmt->bind_param("ssssi", $data['nombres'], $data['apellidos'], $data['UsuarioModificacion'], $data['FechaModificacion'], $data['id']);
        $stmt->execute();

        // retornamos el dto del docente actualizado
        return $this->findById($data['id']);
    }

    // eliminar, recibimos un id del docente y devolvemos un bool
    public function delete($id): bool {
        $stmt = $this->conexion->prepare("DELETE FROM estudiantes WHERE id=?");  // prepara la consulta
        $stmt->bind_param("i", $id); // asociamos el ID con el parámetro
        $stmt->execute();

        // Retorna true solo si alguna fila fue eliminada
        return $stmt->affected_rows > 0;
    }
}
?>
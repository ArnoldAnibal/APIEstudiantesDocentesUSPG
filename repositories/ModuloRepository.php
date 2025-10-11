<?php
require_once __DIR__ . '/../connection/db.php';
require_once __DIR__ . '/../entities/Modulo.php';
require_once __DIR__ . '/../mapper/ModuloMapper.php';
require_once __DIR__ . '/../dto/ModuloResponseDTO.php';

class ModuloRepository {
    private $conexion;

    public function __construct() {
        $this->conexion = Database::getConnection();
    }

    // Obtener todos los módulos
    public function findAll(): array {
        $result = $this->conexion->query("SELECT * FROM modulo");
        $modulos = [];

        if (!$result) {
            die("Error en la consulta: " . $this->conexion->error);
        }

        while ($row = $result->fetch_assoc()) {
            $modulos[] = ModuloMapper::mapRowToEntity($row);
        }

        return $modulos;
    }

    // Obtener módulo por ID
    public function findById(int $id): ?Modulo {
        $stmt = $this->conexion->prepare("SELECT * FROM modulo WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row ? ModuloMapper::mapRowToEntity($row) : null;
    }

    // Crear un módulo
    public function create(Modulo $modulo): ?ModuloResponseDTO {
        $data = ModuloMapper::mapEntityToRow($modulo);

        $stmt = $this->conexion->prepare("INSERT INTO modulo (nombre) VALUES (?)");
        $stmt->bind_param("s", $data['nombre']);
        
        if (!$stmt->execute()) {
            return null;
        }

        $modulo->setId($this->conexion->insert_id);

        return ModuloMapper::mapEntityToResponseDTO($modulo);
    }

    // Actualizar un módulo
    public function update(Modulo $modulo): ?ModuloResponseDTO {
        $data = ModuloMapper::mapEntityToRow($modulo);

        $stmt = $this->conexion->prepare("UPDATE modulo SET nombre=? WHERE id=?");
        $stmt->bind_param("si", $data['nombre'], $data['id']);
        $stmt->execute();

        $updatedModulo = $this->findById($data['id']);
        return $updatedModulo ? ModuloMapper::mapEntityToResponseDTO($updatedModulo) : null;
    }

    // Eliminar un módulo
    public function delete(int $id): bool {
        $stmt = $this->conexion->prepare("DELETE FROM modulo WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->affected_rows > 0;
    }
}
?>

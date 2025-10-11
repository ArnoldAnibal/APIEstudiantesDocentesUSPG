<?php
require_once __DIR__ . '/../connection/db.php';
require_once __DIR__ . '/../dto/UsuarioAccesoResponseDTO.php';
require_once __DIR__ . '/../mapper/UsuarioAccesoMapper.php';

class UsuarioAccesoRepository {
    private mysqli $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // Traer todos los registros
    // Traer todos los registros
public function findAll(): array {
    $res = $this->conn->query("SELECT * FROM usuarioacceso");
    $rows = $res->fetch_all(MYSQLI_ASSOC);
    $response = [];

    foreach ($rows as $row) {
        $dto = UsuarioAccesoMapper::mapRowToResponseDTO($row); // DTO, no array
        $response[] = $dto; 
    }

    return $response; // Devuelve array de DTOs
}



    // Traer por ID
    public function findById(int $id): ?array {
        $stmt = $this->conn->prepare("SELECT * FROM usuarioacceso WHERE idUsuarioAcceso = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }

    // Crear un acceso nuevo
    public function create(array $data): array {
        $stmt = $this->conn->prepare(
            "INSERT INTO usuarioacceso (idUsuario, idRol, idModulo, idAcceso) VALUES (?,?,?,?)"
        );
        $stmt->bind_param(
            'iiii',
            $data['idUsuario'],
            $data['idRol'],
            $data['idModulo'],
            $data['idAcceso']
        );

        if (!$stmt->execute()) {
            throw new Exception("Error al crear usuario acceso: " . $stmt->error);
        }

        return $this->findById($stmt->insert_id);
    }

    // Actualizar un acceso
    public function update(int $id, array $data): ?array {
        $stmt = $this->conn->prepare(
            "UPDATE usuarioacceso SET idUsuario=?, idRol=?, idModulo=?, idAcceso=? WHERE idUsuarioAcceso=?"
        );
        $stmt->bind_param(
            'iiiii',
            $data['idUsuario'],
            $data['idRol'],
            $data['idModulo'],
            $data['idAcceso'],
            $id
        );

        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar usuario acceso: " . $stmt->error);
        }

        return $this->findById($id);
    }

    // Eliminar un acceso
    public function delete(int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM usuarioacceso WHERE idUsuarioAcceso=?");
        $stmt->bind_param('i', $id);

        if (!$stmt->execute()) {
            throw new Exception("Error al eliminar usuario acceso: " . $stmt->error);
        }

        return $stmt->affected_rows > 0;
    }

    // Traer accesos de un usuario
    public function findByUsuarioId(int $usuario_id): array {
    $stmt = $this->conn->prepare("
        SELECT 
            idUsuarioAcceso,
            idUsuario,
            idRol,
            idModulo,
            idAcceso
        FROM usuarioacceso
        WHERE idUsuario = ?
    ");
    $stmt->bind_param('i', $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    // Normaliza las claves para que coincidan con el mapper
    $normalized = [];
    foreach ($rows as $row) {
        $normalized[] = [
            'idUsuarioAcceso' => $row['idUsuarioAcceso'] ?? null,
            'idUsuario' => $row['idUsuario'] ?? null,
            'idRol' => $row['idRol'] ?? $row['idrol'] ?? null,
            'idModulo' => $row['idModulo'] ?? $row['idmodulo'] ?? null,
            'idAcceso' => $row['idAcceso'] ?? $row['idacceso'] ?? null
        ];
    }

    return $normalized;
}


    // Clonar accesos de un usuario a otro
    public function cloneAccesos(int $usuarioOriginalId, int $nuevoUsuarioId): bool {
    $accesos = $this->findByUsuarioId($usuarioOriginalId);

    if (empty($accesos)) {
        throw new Exception("El usuario original ($usuarioOriginalId) no tiene accesos para clonar.");
    }

    foreach ($accesos as $acc) {
        // Normalizamos los posibles nombres de columnas
        $idRol = isset($acc['idRol']) ? (int)$acc['idRol'] :
                 (isset($acc['idrol']) ? (int)$acc['idrol'] : null);

        $idModulo = isset($acc['idModulo']) ? (int)$acc['idModulo'] :
                    (isset($acc['idmodulo']) ? (int)$acc['idmodulo'] : null);

        $idAcceso = isset($acc['idAcceso']) ? (int)$acc['idAcceso'] :
                    (isset($acc['idacceso']) ? (int)$acc['idacceso'] : null);

        if ($idRol === null || $idModulo === null || $idAcceso === null) {
            throw new Exception("Error: el DTO contiene null antes de insertar: " . json_encode([
                'idUsuario' => $nuevoUsuarioId,
                'idRol' => $idRol,
                'idModulo' => $idModulo,
                'idAcceso' => $idAcceso,
                'fila_original' => $acc
            ]));
        }

        $this->create([
            'idUsuario' => $nuevoUsuarioId,
            'idRol' => $idRol,
            'idModulo' => $idModulo,
            'idAcceso' => $idAcceso
        ]);
    }

    return true;
}




}
?>

<?php
require_once __DIR__ . '/../connection/DatabaseFactory.php';
require_once __DIR__ . '/../dto/UsuarioAccesoResponseDTO.php';
require_once __DIR__ . '/../mapper/UsuarioAccesoMapper.php';

class UsuarioAccesoRepository {
    private $conn;
    private $dbType;

    public function __construct($pais) {
        $this->conn = DatabaseFactory::getConnection($pais);

        // Detectamos tipo de conexión
        if ($this->conn instanceof mysqli) $this->dbType = 'mysql';
        else $this->dbType = 'pdo';
    }

    public function create(array $data): array {
        if ($this->dbType === 'mysql') {
            $stmt = $this->conn->prepare(
                "INSERT INTO usuarioacceso (idUsuario, idRol, idModulo, idAcceso) VALUES (?,?,?,?)"
            );
            $stmt->bind_param('iiii',
                $data['idUsuario'],
                $data['idRol'],
                $data['idModulo'],
                $data['idAcceso']
            );
            if (!$stmt->execute()) throw new Exception("Error al crear usuario acceso: ".$stmt->error);
            $insertId = $this->conn->insert_id;

        } else {
            $driver = $this->conn->getAttribute(PDO::ATTR_DRIVER_NAME);

            if ($driver === 'pgsql') {
                $sql = "INSERT INTO usuarioacceso (idUsuario, idRol, idModulo, idAcceso) 
                        VALUES (:idUsuario, :idRol, :idModulo, :idAcceso) RETURNING idUsuarioAcceso";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([
                    ':idUsuario' => $data['idUsuario'],
                    ':idRol' => $data['idRol'],
                    ':idModulo' => $data['idModulo'],
                    ':idAcceso' => $data['idAcceso']
                ]);
                $insertId = $stmt->fetchColumn();

            } elseif ($driver === 'sqlsrv') {
                // SQL Server: insert + obtener SCOPE_IDENTITY() en una query separada
                $sqlInsert = "INSERT INTO usuarioacceso (idUsuario, idRol, idModulo, idAcceso)
                              VALUES (:idUsuario, :idRol, :idModulo, :idAcceso)";
                $stmt = $this->conn->prepare($sqlInsert);
                $stmt->execute([
                    ':idUsuario' => $data['idUsuario'],
                    ':idRol' => $data['idRol'],
                    ':idModulo' => $data['idModulo'],
                    ':idAcceso' => $data['idAcceso']
                ]);

                // Segundo query para obtener el último ID insertado
                $insertIdRow = $this->conn->query("SELECT CAST(SCOPE_IDENTITY() AS INT) AS idUsuarioAcceso")
                                          ->fetch(PDO::FETCH_ASSOC);
                $insertId = $insertIdRow['idUsuarioAcceso'] ?? null;

                if ($insertId === null) {
                    throw new Exception("No se pudo obtener el ID del usuario acceso insertado en SQL Server");
                }

            } else {
                throw new Exception("Driver PDO no soportado: $driver");
            }
        }

        return $this->findById((int)$insertId);
    }

    public function findById(int $id): ?array {
        if ($this->dbType === 'mysql') {
            $stmt = $this->conn->prepare("SELECT * FROM usuarioacceso WHERE idUsuarioAcceso=?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            return $row ?: null;
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM usuarioacceso WHERE idUsuarioAcceso=:id");
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        }
    }

    public function findByUsuarioId(int $usuarioId): array {
        if ($this->dbType === 'mysql') {
            $stmt = $this->conn->prepare("SELECT * FROM usuarioacceso WHERE idUsuario=?");
            $stmt->bind_param('i', $usuarioId);
            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM usuarioacceso WHERE idUsuario=:idUsuario");
            $stmt->execute([':idUsuario' => $usuarioId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

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
}
?>

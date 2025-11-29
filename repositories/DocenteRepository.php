<?php
require_once __DIR__ . '/../connection/DatabaseFactory.php';
require_once __DIR__ . '/../entities/Docente.php';
require_once __DIR__ . '/../mapper/DocenteMapper.php';
require_once __DIR__ . '/../dto/DocenteResponseDTO.php';

class DocenteRepository {
    private $conexion;
    private string $pais;
    private string $dbType; // mysql o pdo

    public function __construct($pais) {
        // Repositorio preparado para MySQL, Postgres y SQL Server
        $this->pais = $pais;
        $this->conexion = DatabaseFactory::getConnection($pais);
        $this->dbType = $this->conexion instanceof mysqli ? 'mysql' : 'pdo';
    }

    public function findAll(): array {
        if ($this->pais === 'SV') { // Postgres
            $query = 'SELECT d.*, 
                           uc.username AS usuario_creacion_username, 
                           um.username AS usuario_modificacion_username
                      FROM docentes d
                      LEFT JOIN usuarios uc ON d."UsuarioCreacion" = uc.id
                      LEFT JOIN usuarios um ON d."UsuarioModificacion" = um.id';
        } else { // MySQL y SQL Server
            $query = 'SELECT d.*, 
                           uc.username AS usuario_creacion_username, 
                           um.username AS usuario_modificacion_username
                      FROM docentes d
                      LEFT JOIN usuarios uc ON d.UsuarioCreacion = uc.id
                      LEFT JOIN usuarios um ON d.UsuarioModificacion = um.id';
        }

        $docentes = [];
        if ($this->dbType === 'mysql') {
            $result = $this->conexion->query($query);
            if (!$result) throw new Exception("Error en consulta MySQL: " . $this->conexion->error);
            while ($row = $result->fetch_assoc()) {
                $docentes[] = DocenteMapper::mapRowToEntity($row);
            }
        } else { // PDO
            $stmt = $this->conexion->query($query);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $row['usuario_creacion_username'] = $row['usuario_creacion_username'] ?? null;
                $row['usuario_modificacion_username'] = $row['usuario_modificacion_username'] ?? null;
                $docentes[] = DocenteMapper::mapRowToEntity($row);
            }
        }

        return $docentes;
    }

    public function findById($id): ?Docente {
        if ($this->pais === 'SV') { // Postgres
            $query = 'SELECT d.*, 
                           uc.username AS usuario_creacion_username, 
                           um.username AS usuario_modificacion_username
                      FROM docentes d
                      LEFT JOIN usuarios uc ON d."UsuarioCreacion" = uc.id
                      LEFT JOIN usuarios um ON d."UsuarioModificacion" = um.id
                      WHERE d.id = :id';
        } else { // MySQL y SQL Server
            $query = 'SELECT d.*, 
                           uc.username AS usuario_creacion_username, 
                           um.username AS usuario_modificacion_username
                      FROM docentes d
                      LEFT JOIN usuarios uc ON d.UsuarioCreacion = uc.id
                      LEFT JOIN usuarios um ON d.UsuarioModificacion = um.id
                      WHERE d.id = ?';
        }

        if ($this->dbType === 'mysql') {
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
        } else { // PDO
            $stmt = $this->conexion->prepare($query);
            $stmt->execute($this->pais === 'SV' ? [':id' => $id] : [$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if (!$row) return null;

        $row['usuario_creacion_username'] = $row['usuario_creacion_username'] ?? null;
        $row['usuario_modificacion_username'] = $row['usuario_modificacion_username'] ?? null;

        return DocenteMapper::mapRowToEntity($row);
    }

    public function create(Docente $docente): DocenteResponseDTO {
        $data = DocenteMapper::mapDocenteToRow($docente);
        $id = null;

        if ($this->dbType === 'mysql') {
            // Insercion MySQL con auto increment
            $stmt = $this->conexion->prepare("INSERT INTO docentes (nombres, apellidos, UsuarioCreacion, FechaCreacion) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $data['nombres'], $data['apellidos'], $data['UsuarioCreacion'], $data['FechaCreacion']);
            if (!$stmt->execute()) {
                throw new Exception("Error al crear docente MySQL: " . $stmt->error);
            }
            $id = $this->conexion->insert_id;
        } else { // PDO
            $driver = $this->conexion->getAttribute(PDO::ATTR_DRIVER_NAME);
            if ($driver === 'sqlsrv') { // SQL Server
                // OUTPUT para devolver el id generado
                $query = "INSERT INTO docentes (nombres, apellidos, UsuarioCreacion, FechaCreacion)
                          OUTPUT INSERTED.id
                          VALUES (:nombres, :apellidos, :UsuarioCreacion, :FechaCreacion)";
                $stmt = $this->conexion->prepare($query);
                $stmt->execute([
                    ':nombres' => $data['nombres'],
                    ':apellidos' => $data['apellidos'],
                    ':UsuarioCreacion' => $data['UsuarioCreacion'],
                    ':FechaCreacion' => $data['FechaCreacion']
                ]);
                $id = (int)$stmt->fetchColumn();
            } elseif ($driver === 'pgsql') { // Postgres
                // RETURNING para capturar id en Postgres
                $query = 'INSERT INTO docentes (nombres, apellidos, "UsuarioCreacion", "FechaCreacion")
                          VALUES (:nombres, :apellidos, :UsuarioCreacion, :FechaCreacion)
                          RETURNING id';
                $stmt = $this->conexion->prepare($query);
                $stmt->execute([
                    ':nombres' => $data['nombres'],
                    ':apellidos' => $data['apellidos'],
                    ':UsuarioCreacion' => $data['UsuarioCreacion'],
                    ':FechaCreacion' => $data['FechaCreacion']
                ]);
                $id = (int)$stmt->fetchColumn();
            } else {
                $query = "INSERT INTO docentes (nombres, apellidos, UsuarioCreacion, FechaCreacion)
                          VALUES (:nombres, :apellidos, :UsuarioCreacion, :FechaCreacion)";
                $stmt = $this->conexion->prepare($query);
                $stmt->execute([
                    ':nombres' => $data['nombres'],
                    ':apellidos' => $data['apellidos'],
                    ':UsuarioCreacion' => $data['UsuarioCreacion'],
                    ':FechaCreacion' => $data['FechaCreacion']
                ]);
                $id = (int)$this->conexion->lastInsertId();
            }
        }

        if (!$id) throw new Exception("No se pudo obtener el ID del docente insertado en {$this->pais}");

        return new DocenteResponseDTO($id, $data['nombres'], $data['apellidos']);
    }

    public function update(Docente $docente): DocenteResponseDTO {
        $data = DocenteMapper::mapDocenteToRow($docente);
        if ($this->pais === 'SV') { // Postgres
            $query = 'UPDATE docentes 
                      SET nombres = :nombres, apellidos = :apellidos, "UsuarioModificacion" = :UsuarioModificacion, "FechaModificacion" = :FechaModificacion
                      WHERE id = :id';
            $stmt = $this->conexion->prepare($query);
            $stmt->execute([
                ':nombres' => $data['nombres'],
                ':apellidos' => $data['apellidos'],
                ':UsuarioModificacion' => $data['UsuarioModificacion'],
                ':FechaModificacion' => $data['FechaModificacion'],
                ':id' => $data['id']
            ]);
        } elseif ($this->dbType === 'mysql') {
            $stmt = $this->conexion->prepare("UPDATE docentes SET nombres=?, apellidos=?, UsuarioModificacion=?, FechaModificacion=? WHERE id=?");
            $stmt->bind_param("ssssi", $data['nombres'], $data['apellidos'], $data['UsuarioModificacion'], $data['FechaModificacion'], $data['id']);
            $stmt->execute();
        } else { // SQL Server
            $stmt = $this->conexion->prepare("UPDATE docentes SET nombres=?, apellidos=?, UsuarioModificacion=?, FechaModificacion=? WHERE id=?");
            $stmt->execute([$data['nombres'], $data['apellidos'], $data['UsuarioModificacion'], $data['FechaModificacion'], $data['id']]);
        }

        $updatedDocente = $this->findById($data['id']);
        return $updatedDocente ? DocenteMapper::mapEntityToResponseDTO($updatedDocente) : null;
    }

    public function delete($id): bool {
        if ($this->pais === 'SV') { // Postgres
            $query = 'DELETE FROM docentes WHERE id = :id';
            $stmt = $this->conexion->prepare($query);
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } elseif ($this->dbType === 'mysql') {
            $stmt = $this->conexion->prepare("DELETE FROM docentes WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            return $stmt->affected_rows > 0;
        } else { // SQL Server
            $stmt = $this->conexion->prepare("DELETE FROM docentes WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;
        }
    }
}
?>

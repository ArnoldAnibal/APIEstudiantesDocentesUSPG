<?php
require_once __DIR__ . '/../connection/DatabaseFactory.php';
require_once __DIR__ . '/../entities/Estudiante.php';
require_once __DIR__ . '/../mapper/EstudianteMapper.php';
require_once __DIR__ . '/../dto/EstudianteResponseDTO.php';

class EstudianteRepository {
    private $conexion;
    private string $pais;
    private string $dbType; // mysql o pdo

    public function __construct(string $pais) {
        $this->pais = $pais;
        $this->conexion = DatabaseFactory::getConnection($pais);
        $this->dbType = $this->conexion instanceof mysqli ? 'mysql' : 'pdo';
    }

    public function findAll(): array {
        $estudiantes = [];

        if ($this->pais === 'SV') { // Postgres
            $query = 'SELECT d.*, 
                        uc.username AS usuario_creacion_username, 
                        um.username AS usuario_modificacion_username
                      FROM estudiantes d
                      LEFT JOIN usuarios uc ON d."UsuarioCreacion" = uc.id
                      LEFT JOIN usuarios um ON d."UsuarioModificacion" = um.id';
        } else { // MySQL y SQL Server
            $query = 'SELECT d.*, 
                        uc.username AS usuario_creacion_username, 
                        um.username AS usuario_modificacion_username
                      FROM estudiantes d
                      LEFT JOIN usuarios uc ON d.UsuarioCreacion = uc.id
                      LEFT JOIN usuarios um ON d.UsuarioModificacion = um.id';
        }

        if ($this->dbType === 'mysql') {
            $result = $this->conexion->query($query);
            if (!$result) throw new Exception("Error en consulta MySQL: " . $this->conexion->error);
            while ($row = $result->fetch_assoc()) {
                $estudiantes[] = EstudianteMapper::mapRowToEntity($row);
            }
        } else { // PDO (Postgres o SQL Server)
            $stmt = $this->conexion->query($query);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $row['usuario_creacion_username'] = $row['usuario_creacion_username'] ?? null;
                $row['usuario_modificacion_username'] = $row['usuario_modificacion_username'] ?? null;
                $estudiantes[] = EstudianteMapper::mapRowToEntity($row);
            }
        }

        return $estudiantes;
    }

    public function findById($id): ?Estudiante {
        if ($this->pais === 'SV') { // Postgres
            $query = 'SELECT d.*, 
                        uc.username AS usuario_creacion_username, 
                        um.username AS usuario_modificacion_username
                      FROM estudiantes d
                      LEFT JOIN usuarios uc ON d."UsuarioCreacion" = uc.id
                      LEFT JOIN usuarios um ON d."UsuarioModificacion" = um.id
                      WHERE d.id = :id';
        } else { // MySQL y SQL Server
            $query = 'SELECT d.*, 
                        uc.username AS usuario_creacion_username, 
                        um.username AS usuario_modificacion_username
                      FROM estudiantes d
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

        if (!$row) throw new Exception("Estudiante no encontrado en {$this->pais}");

        $row['usuario_creacion_username'] = $row['usuario_creacion_username'] ?? null;
        $row['usuario_modificacion_username'] = $row['usuario_modificacion_username'] ?? null;

        return EstudianteMapper::mapRowToEntity($row);
    }

    public function create(Estudiante $estudiante): ?EstudianteResponseDTO {
        $data = EstudianteMapper::mapEstudianteToRow($estudiante);
        $id = null;

        if ($this->dbType === 'mysql') {
            $query = "INSERT INTO estudiantes (nombres, apellidos, UsuarioCreacion, FechaCreacion) VALUES (?, ?, ?, ?)";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("ssss", $data['nombres'], $data['apellidos'], $data['UsuarioCreacion'], $data['FechaCreacion']);
            if (!$stmt->execute()) throw new Exception("Error al crear estudiante MySQL: " . $stmt->error);
            $id = $this->conexion->insert_id;
        } else { // PDO
            $driver = $this->conexion->getAttribute(PDO::ATTR_DRIVER_NAME);
            if ($driver === 'sqlsrv') { // SQL Server
                $query = "INSERT INTO estudiantes (nombres, apellidos, UsuarioCreacion, FechaCreacion)
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
                // Usar secuencia si existe; si no, calcular max(id)+1
                $seqStmt = $this->conexion->prepare("SELECT pg_get_serial_sequence('estudiantes','id')");
                $seqStmt->execute();
                $seqName = $seqStmt->fetchColumn();
                if ($seqName) {
                    $nextId = (int)$this->conexion->query("SELECT nextval('{$seqName}')")->fetchColumn();
                } else {
                    $nextId = (int)$this->conexion->query("SELECT COALESCE(MAX(id),0)+1 FROM estudiantes")->fetchColumn();
                }

                $query = 'INSERT INTO estudiantes (id, nombres, apellidos, "UsuarioCreacion", "FechaCreacion")
                          VALUES (:id, :nombres, :apellidos, :UsuarioCreacion, :FechaCreacion)
                          RETURNING id';
                $stmt = $this->conexion->prepare($query);
                $stmt->execute([
                    ':id' => $nextId,
                    ':nombres' => $data['nombres'],
                    ':apellidos' => $data['apellidos'],
                    ':UsuarioCreacion' => $data['UsuarioCreacion'],
                    ':FechaCreacion' => $data['FechaCreacion']
                ]);
                $id = (int)$stmt->fetchColumn();
            } else { // fallback PDO without returning
                $query = "INSERT INTO estudiantes (nombres, apellidos, UsuarioCreacion, FechaCreacion)
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

        if (!$id) throw new Exception("No se pudo obtener el ID del estudiante insertado en {$this->pais}");

        return new EstudianteResponseDTO($id, $data['nombres'], $data['apellidos']);
    }

    public function update(Estudiante $estudiante): ?EstudianteResponseDTO {
        $data = EstudianteMapper::mapEstudianteToRow($estudiante);

        if ($this->pais === 'SV') { // Postgres
            $query = 'UPDATE estudiantes 
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
            $query = "UPDATE estudiantes SET nombres=?, apellidos=?, UsuarioModificacion=?, FechaModificacion=? WHERE id=?";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("ssssi", $data['nombres'], $data['apellidos'], $data['UsuarioModificacion'], $data['FechaModificacion'], $data['id']);
            $stmt->execute();
        } else { // SQL Server
            $query = "UPDATE estudiantes SET nombres=?, apellidos=?, UsuarioModificacion=?, FechaModificacion=? WHERE id=?";
            $stmt = $this->conexion->prepare($query);
            $stmt->execute([$data['nombres'], $data['apellidos'], $data['UsuarioModificacion'], $data['FechaModificacion'], $data['id']]);
        }

        $updatedEstudiante = $this->findById($data['id']);
        return $updatedEstudiante ? EstudianteMapper::mapEntityToResponseDTO($updatedEstudiante) : null;
    }

    public function delete($id): bool {
        if ($this->pais === 'SV') { // Postgres
            $query = 'DELETE FROM estudiantes WHERE id = :id';
            $stmt = $this->conexion->prepare($query);
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } elseif ($this->dbType === 'mysql') {
            $query = 'DELETE FROM estudiantes WHERE id=?';
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            return $stmt->affected_rows > 0;
        } else { // SQL Server
            $query = 'DELETE FROM estudiantes WHERE id=?';
            $stmt = $this->conexion->prepare($query);
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;
        }
    }
}
?>

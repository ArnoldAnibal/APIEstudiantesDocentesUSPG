<?php
require_once __DIR__ . '/../connection/DatabaseFactory.php';
require_once __DIR__ . '/../entities/Modulo.php';
require_once __DIR__ . '/../mapper/ModuloMapper.php';
require_once __DIR__ . '/../dto/ModuloResponseDTO.php';

class ModuloRepository {
    private $conexion;
    private string $dbType;

    public function __construct($pais) {
        // Repositorio preparado para MySQL, Postgres y SQL Server
        $this->conexion = DatabaseFactory::getConnection($pais);
        $this->dbType = $this->conexion instanceof mysqli ? 'mysql' : 'pdo';
    }

    public function findAll(): array {
        $query = "SELECT * FROM modulo";
        $modulos = [];

        if ($this->dbType === 'mysql') {
            $result = $this->conexion->query($query);
            if (!$result) {
                throw new Exception("Error en la consulta: " . $this->conexion->error);
            }
            while ($row = $result->fetch_assoc()) {
                $modulos[] = ModuloMapper::mapRowToEntity($row);
            }
        } else { // PDO
            $stmt = $this->conexion->query($query);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $modulos[] = ModuloMapper::mapRowToEntity($row);
            }
        }

        return $modulos;
    }

    public function findById(int $id): ?Modulo {
        $query = "SELECT * FROM modulo WHERE id=?";
        if ($this->dbType === 'mysql') {
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
        } else {
            $stmt = $this->conexion->prepare($query);
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $row ? ModuloMapper::mapRowToEntity($row) : null;
    }

    public function create(Modulo $modulo): ?ModuloResponseDTO {
        $data = ModuloMapper::mapEntityToRow($modulo);

        if ($this->dbType === 'mysql') {
            $stmt = $this->conexion->prepare("INSERT INTO modulo (nombre) VALUES (?)");
            $stmt->bind_param("s", $data['nombre']);
            
            if (!$stmt->execute()) {
                return null;
            }

            $modulo->setId($this->conexion->insert_id);
        } else { // PDO
            $driver = $this->conexion->getAttribute(PDO::ATTR_DRIVER_NAME);
            if ($driver === 'sqlsrv') {
                $stmt = $this->conexion->prepare("INSERT INTO modulo (nombre) OUTPUT INSERTED.id VALUES (?)");
                $stmt->execute([$data['nombre']]);
                $modulo->setId((int)$stmt->fetchColumn());
            } elseif ($driver === 'pgsql') {
                $stmt = $this->conexion->prepare("INSERT INTO modulo (nombre) VALUES (?) RETURNING id");
                $stmt->execute([$data['nombre']]);
                $modulo->setId((int)$stmt->fetchColumn());
            } else {
                $stmt = $this->conexion->prepare("INSERT INTO modulo (nombre) VALUES (?)");
                $stmt->execute([$data['nombre']]);
                $modulo->setId((int)$this->conexion->lastInsertId());
            }
        }

        return ModuloMapper::mapEntityToResponseDTO($modulo);
    }

    public function update(Modulo $modulo): ?ModuloResponseDTO {
        $data = ModuloMapper::mapEntityToRow($modulo);

        if ($this->dbType === 'mysql') {
            $stmt = $this->conexion->prepare("UPDATE modulo SET nombre=? WHERE id=?");
            $stmt->bind_param("si", $data['nombre'], $data['id']);
            $stmt->execute();
        } else {
            $stmt = $this->conexion->prepare("UPDATE modulo SET nombre=? WHERE id=?");
            $stmt->execute([$data['nombre'], $data['id']]);
        }

        $updatedModulo = $this->findById($data['id']);
        return $updatedModulo ? ModuloMapper::mapEntityToResponseDTO($updatedModulo) : null;
    }

    public function delete(int $id): bool {
        if ($this->dbType === 'mysql') {
            $stmt = $this->conexion->prepare("DELETE FROM modulo WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            return $stmt->affected_rows > 0;
        } else {
            $stmt = $this->conexion->prepare("DELETE FROM modulo WHERE id=?");
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;
        }
    }
}
?>

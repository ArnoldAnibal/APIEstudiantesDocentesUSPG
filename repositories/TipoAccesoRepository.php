<?php
require_once __DIR__ . '/../connection/DatabaseFactory.php';

class TipoAccesoRepository {
    private $conn;
    private string $dbType;

    public function __construct($pais) {
        // Repositorio preparado para MySQL, Postgres y SQL Server
        $this->conn = DatabaseFactory::getConnection($pais);
        $this->dbType = $this->conn instanceof mysqli ? 'mysql' : 'pdo';
    }

    public function findAll() {
        $sql = "SELECT id, nombre FROM tipoacceso";
        if ($this->dbType === 'mysql') {
            $res = $this->conn->query($sql);
            return $res->fetch_all(MYSQLI_ASSOC);
        } else {
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public function findById(int $id) {
        $sql = "SELECT id, nombre FROM tipoacceso WHERE id = ?";
        if ($this->dbType === 'mysql') {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } else {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    public function create(array $data) {
        if ($this->dbType === 'mysql') {
            $stmt = $this->conn->prepare("INSERT INTO tipoacceso (nombre) VALUES (?)");
            $stmt->bind_param('s', $data['nombre']);
            if (!$stmt->execute()) throw new Exception($stmt->error);
            $insertId = $this->conn->insert_id;
        } else {
            $driver = $this->conn->getAttribute(PDO::ATTR_DRIVER_NAME);
            if ($driver === 'sqlsrv') {
                $stmt = $this->conn->prepare("INSERT INTO tipoacceso (nombre) OUTPUT INSERTED.id VALUES (?)");
                $stmt->execute([$data['nombre']]);
                $insertId = (int)$stmt->fetchColumn();
            } elseif ($driver === 'pgsql') {
                $stmt = $this->conn->prepare("INSERT INTO tipoacceso (nombre) VALUES (?) RETURNING id");
                $stmt->execute([$data['nombre']]);
                $insertId = (int)$stmt->fetchColumn();
            } else {
                $stmt = $this->conn->prepare("INSERT INTO tipoacceso (nombre) VALUES (?)");
                $stmt->execute([$data['nombre']]);
                $insertId = (int)$this->conn->lastInsertId();
            }
        }
        return $this->findById($insertId);
    }

    public function update(int $id, array $data) {
        $sql = "UPDATE tipoacceso SET nombre=? WHERE id=?";
        if ($this->dbType === 'mysql') {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('si', $data['nombre'], $id);
            if (!$stmt->execute()) throw new Exception($stmt->error);
        } else {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$data['nombre'], $id]);
        }
        return $this->findById($id);
    }

    public function delete(int $id) {
        $sql = "DELETE FROM tipoacceso WHERE id=?";
        if ($this->dbType === 'mysql') {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $id);
            if (!$stmt->execute()) throw new Exception($stmt->error);
        } else {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
        }
        return ['mensaje' => "TipoAcceso $id eliminado"];
    }
}

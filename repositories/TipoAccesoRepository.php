<?php
require_once __DIR__ . '/../connection/db.php';

class TipoAccesoRepository {
    private mysqli $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function findAll() {
        $res = $this->conn->query("SELECT id, nombre FROM tipoacceso");
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function findById(int $id) {
        $stmt = $this->conn->prepare("SELECT id, nombre FROM tipoacceso WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create(array $data) {
        $stmt = $this->conn->prepare("INSERT INTO tipoacceso (nombre) VALUES (?)");
        $stmt->bind_param('s', $data['nombre']);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        return $this->findById($stmt->insert_id);
    }

    public function update(int $id, array $data) {
        $stmt = $this->conn->prepare("UPDATE tipoacceso SET nombre=? WHERE id=?");
        $stmt->bind_param('si', $data['nombre'], $id);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        return $this->findById($id);
    }

    public function delete(int $id) {
        $stmt = $this->conn->prepare("DELETE FROM tipoacceso WHERE id=?");
        $stmt->bind_param('i', $id);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        return ['mensaje' => "TipoAcceso $id eliminado"];
    }
}

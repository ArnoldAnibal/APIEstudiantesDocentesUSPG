<?php

//repositorio que maneja la interacción con la tabla de usuarios
// incluye la conexión a la base de datos y el mapper para convertir entidadesa DTOs
require_once __DIR__ . '/../connection/db.php';
require_once __DIR__ . '/../mapper/UsuarioMapper.php';

class UsuarioRepository {
    private $conn;  // propiedad para almacenar la conexión a la base de datos
    public function __construct() { $this->conn = Database::getConnection(); } // constructor que inicializa la conexión

    public function findById($id) {
        $stmt = $this->conn->prepare('SELECT id, username, nombres, apellidos FROM usuarios WHERE id = ? LIMIT 1');
        $stmt->bind_param('i',$id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc();  // retorna una fila como un array asociativo
    }

    public function buscarPorUsername($username) {
        $stmt = $this->conn->prepare('SELECT id, username, password_hash, nombres, apellidos FROM usuarios WHERE username = ? LIMIT 1');
        $stmt->bind_param('s',$username);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc(); // retorna una fila como un array asociativo
    }

    public function create($username, $password_hash, $nombres=null, $apellidos=null) {
        $stmt = $this->conn->prepare('INSERT INTO usuarios (username, password_hash, nombres, apellidos) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $username, $password_hash, $nombres, $apellidos);
        $stmt->execute();
        return $this->findById($stmt->insert_id);
    }
}

<?php
require_once __DIR__ . '/../connection/db.php';
require_once __DIR__ . '/../mapper/UsuarioMapper.php';
require_once __DIR__ . '/../entities/Usuario.php';

class UsuarioRepository {
    private mysqli $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    /**
     * Busca un usuario por su ID.
     */
    public function findById(int $id): ?array {
        $stmt = $this->conn->prepare(
            'SELECT id, username, password_hash, nombres, apellidos, correo, activo 
             FROM usuarios WHERE id = ? LIMIT 1'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        return $row ?: null;
    }

    /**
     * Busca un usuario por su nombre de usuario.
     */
    public function findByUsername(string $username): ?array {
        $stmt = $this->conn->prepare(
            'SELECT id, username, password_hash, nombres, apellidos, correo, activo 
             FROM usuarios WHERE username = ? LIMIT 1'
        );
        $stmt->bind_param('s', $username);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        return $row ?: null;
    }

    /**
     * Crea un nuevo usuario con hash de contraseña ya generado.
     */
    public function create(
        string $username,
        string $password_hash,
        ?string $nombres = null,
        ?string $apellidos = null,
        ?string $correo = null
    ): UsuarioResponseDTO {
        $stmt = $this->conn->prepare(
            'INSERT INTO usuarios (username, password_hash, nombres, apellidos, correo)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('sssss', $username, $password_hash, $nombres, $apellidos, $correo);

        if (!$stmt->execute()) {
            throw new Exception('Error al crear el usuario: ' . $stmt->error);
        }

        $nuevo = $this->findById($stmt->insert_id);
        return UsuarioMapper::mapEntityToResponseDTO($nuevo);
    }

    /**
     * Clona un usuario existente con nuevos datos.
     */
    public function clonarUsuario(Usuario $usuarioClon): UsuarioResponseDTO {
        $stmt = $this->conn->prepare(
            'INSERT INTO usuarios (username, password_hash, nombres, apellidos, correo, activo)
             VALUES (?, ?, ?, ?, ?, ?)'
        );

        $stmt->bind_param(
            'sssssi',
            $usuarioClon->username,
            $usuarioClon->password_hash,
            $usuarioClon->nombres,
            $usuarioClon->apellidos,
            $usuarioClon->correo,
            $usuarioClon->activo
        );

        if (!$stmt->execute()) {
            throw new Exception('Error al clonar el usuario: ' . $stmt->error);
        }

        $nuevo = $this->findById($stmt->insert_id);
        return UsuarioMapper::mapEntityToResponseDTO($nuevo);
    }
}

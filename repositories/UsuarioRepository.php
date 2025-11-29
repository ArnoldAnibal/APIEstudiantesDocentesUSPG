<?php
require_once __DIR__ . '/../connection/DatabaseFactory.php';
require_once __DIR__ . '/../mapper/UsuarioMapper.php';
require_once __DIR__ . '/../entities/Usuario.php';
require_once __DIR__ . '/../dto/UsuarioRequestDTO.php';

class UsuarioRepository {
    private $conn;
    private string $pais;

    public function __construct(string $pais) {
        $this->pais = strtoupper($pais);
        if (!in_array($this->pais, ['GT', 'SV', 'HN'])) {
            throw new Exception("País no soportado: {$this->pais}");
        }
        $this->conn = DatabaseFactory::getConnection($this->pais);
    }

    public function getConnection() {
        return $this->conn;
    }

    public function findById(int $id): ?array {
        $sql = 'SELECT id, username, password_hash, nombres, apellidos, correo, activo, pais FROM usuarios WHERE id = ?';

        if ($this->conn instanceof mysqli) {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $res = $stmt->get_result();
            return $res->fetch_assoc() ?: null;
        } else { // PDO para Postgres / SQL Server
            $stmt = $this->conn->prepare('SELECT id, username, password_hash, nombres, apellidos, correo, activo, pais FROM usuarios WHERE id = :id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }
    }

    public function findByUsername(string $username): ?array {
        $sql = 'SELECT id, username, password_hash, nombres, apellidos, correo, activo, pais FROM usuarios WHERE username = ?';

        if ($this->conn instanceof mysqli) {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $res = $stmt->get_result();
            return $res->fetch_assoc() ?: null;
        } else { // PDO
            $stmt = $this->conn->prepare('SELECT id, username, password_hash, nombres, apellidos, correo, activo, pais FROM usuarios WHERE username = :username');
            $stmt->bindValue(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }
    }

    public function create(UsuarioRequestDTO|array $usuario): array {
        if ($usuario instanceof UsuarioRequestDTO) {
            $username = $usuario->username;
            $password_hash = $usuario->password;
            $nombres = $usuario->nombres;
            $apellidos = $usuario->apellidos;
            $correo = $usuario->correo;
            $pais = strtoupper($usuario->pais);
        } elseif (is_array($usuario)) {
            $username = $usuario['username'] ?? null;
            $password_hash = $usuario['password_hash'] ?? $usuario['password'] ?? null;
            $nombres = $usuario['nombres'] ?? null;
            $apellidos = $usuario['apellidos'] ?? null;
            $correo = $usuario['correo'] ?? null;
            $pais = strtoupper($usuario['pais'] ?? '');
        } else {
            throw new Exception("Tipo de dato no soportado para crear usuario");
        }

        if (!$username || !$password_hash || !$pais) {
            throw new Exception("Faltan campos obligatorios para crear usuario");
        }

        if ($this->conn instanceof mysqli) {
            $stmt = $this->conn->prepare(
                "INSERT INTO usuarios (username, password_hash, nombres, apellidos, correo, pais) 
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param('ssssss', $username, $password_hash, $nombres, $apellidos, $correo, $pais);
            if (!$stmt->execute()) {
                throw new Exception("Error al crear usuario: " . $stmt->error);
            }
            $insertId = $this->conn->insert_id;
            return $this->findById($insertId);
        } else { // PDO: Postgres o SQL Server
            $sql = "INSERT INTO usuarios (username, password_hash, nombres, apellidos, correo, pais) 
                    VALUES (:username, :password_hash, :nombres, :apellidos, :correo, :pais)";
            
            if ($this->pais === 'SV') { // SQL Server: usar OUTPUT INSERTED.id
                $sql = "INSERT INTO usuarios (username, password_hash, nombres, apellidos, correo, pais)
                        OUTPUT INSERTED.id
                        VALUES (:username, :password_hash, :nombres, :apellidos, :correo, :pais)";
            } elseif ($this->conn->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql') { // Postgres
                $sql .= " RETURNING id";
            }

            $stmt = $this->conn->prepare($sql);
            $ok = $stmt->execute([
                ':username' => $username,
                ':password_hash' => $password_hash,
                ':nombres' => $nombres,
                ':apellidos' => $apellidos,
                ':correo' => $correo,
                ':pais' => $pais
            ]);

            if (!$ok) {
                $err = $stmt->errorInfo();
                throw new Exception("Error al crear usuario (PDO): " . ($err[2] ?? json_encode($err)));
            }

            $insertId = ($this->pais === 'SV') ? (int)$stmt->fetchColumn() :
                        ($this->conn->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql' ? (int)$stmt->fetchColumn() : (int)$this->conn->lastInsertId());

            return $this->findById($insertId);
        }
    }

    public function update(Usuario $usuario): array {
        $sql = 'UPDATE usuarios SET username=?, nombres=?, apellidos=?, correo=?, pais=? WHERE id=?';
        if ($this->conn instanceof mysqli) {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                'sssssi',
                $usuario->username,
                $usuario->nombres,
                $usuario->apellidos,
                $usuario->correo,
                $usuario->pais,
                $usuario->id
            );
            $stmt->execute();
        } else {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $usuario->username,
                $usuario->nombres,
                $usuario->apellidos,
                $usuario->correo,
                $usuario->pais,
                $usuario->id
            ]);
        }
        return $this->findById($usuario->id);
    }

    public function delete(int $id): bool {
        $sql = 'DELETE FROM usuarios WHERE id=?';
        if ($this->conn instanceof mysqli) {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            return $stmt->affected_rows > 0;
        } else {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;
        }
    }

    public function clonarUsuario(Usuario $usuarioClon): array {
    // Convertimos Usuario a array compatible con create()
    $usuarioArray = [
        'username' => $usuarioClon->username,
        'password_hash' => $usuarioClon->password_hash,
        'nombres' => $usuarioClon->nombres,
        'apellidos' => $usuarioClon->apellidos,
        'correo' => $usuarioClon->correo,
        'pais' => $usuarioClon->pais,
    ];

    return $this->create($usuarioArray);
}


    public function findAll(): array {
        $sql = 'SELECT id, username, nombres, apellidos, correo, activo, pais FROM usuarios';
        if ($this->conn instanceof mysqli) {
            $res = $this->conn->query($sql);
            return array_map(fn($row) => UsuarioMapper::mapEntityToResponseDTO($row), $res->fetch_all(MYSQLI_ASSOC));
        } else {
            $stmt = $this->conn->query($sql);
            return array_map(fn($row) => UsuarioMapper::mapEntityToResponseDTO($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
        }
    }
}
?>

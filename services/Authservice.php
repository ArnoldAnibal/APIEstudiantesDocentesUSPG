<?php
require_once __DIR__ . '/../repositories/UsuarioRepository.php';
require_once __DIR__ . '/../dto/UsuarioResponseDTO.php';
require_once __DIR__ . '/../dto/UsuarioRequestDTO.php';
require_once __DIR__ . '/../mapper/UsuarioMapper.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;

class AuthService {
    private $repo;
    private $jwt_secret = 'B8AHgIk26d55'; // Clave secreta
    private $mapper;

    public function __construct() {
        $this->repo = new UsuarioRepository();
        $this->mapper = new UsuarioMapper();
    }

    public function register(UsuarioRequestDTO $dto) {
        if (!$dto->username || !$dto->password) {
            throw new Exception("El nombre de usuario y contraseña son obligatorios");
        }

        $existing = $this->repo->findByUsername($dto->username);
        if ($existing) throw new Exception("El usuario ya existe");

        $hash = password_hash($dto->password, PASSWORD_DEFAULT);
        $user = $this->repo->create($dto->username, $hash, $dto->nombres, $dto->apellidos, $dto->correo);

        return $user;
    }

    public function login(UsuarioRequestDTO $dto) {
        if (!$dto->username || !$dto->password) {
            throw new Exception("El nombre de usuario y contraseña son obligatorios");
        }

        $user = $this->repo->findByUsername($dto->username);
        if (!$user || !password_verify($dto->password, $user['password_hash'])) {
            throw new Exception("Credenciales inválidas");
        }

        $now = time();
        $payload = [
            'iat' => $now,
            'exp' => $now + 3600 * 24,
            'sub' => $user['id'],
            'username' => $user['username']
        ];

        $jwt = JWT::encode($payload, $this->jwt_secret, 'HS256');

        return ['token' => $jwt]; // Solo devolvemos el token
    }
}

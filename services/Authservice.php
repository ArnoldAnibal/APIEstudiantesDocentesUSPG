<?php
require_once __DIR__ . '/../repositories/UsuarioRepository.php';
require_once __DIR__ . '/../dto/UsuarioRequestDTO.php';
require_once __DIR__ . '/../dto/UsuarioResponseDTO.php';
require_once __DIR__ . '/../mapper/UsuarioMapper.php';
require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;

class AuthService {
    private $jwt_secret = 'B8AHgIk26d55';
    private $mapper;

    public function __construct() {
        $this->mapper = new UsuarioMapper();
    }

    public function register(UsuarioRequestDTO $dto) {
        if (!$dto->username || !$dto->password || !$dto->pais) {
            throw new Exception("Username, password y país son obligatorios");
        }
        $pais = strtoupper($dto->pais);
        if (!in_array($pais, ['GT', 'SV', 'HN'])) {
            throw new Exception("País no válido. Solo se permiten: GT, SV, HN.");
        }
        $repoPais = new UsuarioRepository($pais);
        if ($repoPais->findByUsername($dto->username)) {
            throw new Exception("El usuario ya existe");
        }
        $dto->password = password_hash($dto->password, PASSWORD_DEFAULT);
        $createdArray = $repoPais->create($dto);
        $responseDTO = $this->mapper->mapEntityToResponseDTO($createdArray);
        return $responseDTO->toArray();
    }

    public function login(UsuarioRequestDTO $dto) {
        if (!$dto->username || !$dto->password || !$dto->pais) {
            throw new Exception("Username, password y país son obligatorios");
        }
        $pais = strtoupper($dto->pais);
        if (!in_array($pais, ['GT','SV','HN'])) {
            throw new Exception("País no válido. Solo se permiten: GT, SV, HN.");
        }
        $repoPais = new UsuarioRepository($pais);
        $usuario = $repoPais->findByUsername($dto->username);
        if (!$usuario) throw new Exception("Usuario no encontrado en el país especificado");
        if (!password_verify($dto->password, $usuario['password_hash'])) {
            throw new Exception("Credenciales inválidas");
        }

        $payload = [
            "id" => $usuario["id"],
            "username" => $usuario["username"],
            "correo" => $usuario["correo"],
            "pais" => $usuario["pais"],
            "iat" => time(),
            "exp" => time() + (60 * 60 * 6)
        ];

        $token = JWT::encode($payload, $this->jwt_secret, 'HS256');

        return [
            "token" => $token,
            "usuario" => $this->mapper->mapEntityToResponseDTO($usuario)->toArray()
        ];
    }
}
?>

<?php
require_once __DIR__ . '/../services/UsuarioService.php';
require_once __DIR__ . '/../dto/UsuarioRequestDTO.php';
require_once __DIR__ . '/../mapper/UsuarioMapper.php';
require_once __DIR__ . '/../helpers/JWTHelper.php';

class UsuarioController {
    private UsuarioService $service;

    public function __construct() {
        // Obtener país desde token JWT
        $pais = JWTHelper::obtenerPaisToken(); // debe devolver GT, SV o HN
        $this->service = new UsuarioService($pais);
    }

    // Registro de usuario
    public function register(array $requestData) {
        try {
            $dto = new UsuarioRequestDTO($requestData);
            $usuario = $this->service->register($dto);
            return ['success' => true, 'data' => $usuario];
        } catch (Exception $ex) {
            return ['success' => false, 'error' => $ex->getMessage()];
        }
    }

    // Actualizar usuario
    public function update(int $id, array $requestData) {
        try {
            $dto = new UsuarioRequestDTO($requestData);
            $usuarioActualizado = $this->service->update($id, $dto);
            return ['success' => true, 'data' => $usuarioActualizado];
        } catch (Exception $ex) {
            return ['success' => false, 'error' => $ex->getMessage()];
        }
    }

    // Clonar usuario
    public function clonar(?int $idUsuarioOriginal, array $requestData) {
    // Tomar ID del body si no viene como parámetro
    $idUsuarioOriginal = $idUsuarioOriginal ?? ($requestData['id'] ?? null);

    if ($idUsuarioOriginal === null) {
        throw new Exception("Debe proporcionarse el ID del usuario a clonar");
    }

    $dto = new UsuarioRequestDTO($requestData);

    // País del token
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $pais = JWTHelper::getPaisFromToken($authHeader);

    // Crear servicio temporal con país del token para clonar
    $service = new UsuarioService($pais);

    $nuevoUsuario = $service->clonar($idUsuarioOriginal, $dto);

    return [
        'success' => true,
        'data' => is_array($nuevoUsuario) ? $nuevoUsuario : $nuevoUsuario->toArray()
    ];
}


    // Obtener todos
    public function getAll() {
        try {
            $usuarios = $this->service->getAll();
            return ['success' => true, 'data' => $usuarios];
        } catch (Exception $ex) {
            return ['success' => false, 'error' => $ex->getMessage()];
        }
    }

    // Obtener por ID
    public function getById(int $id) {
        try {
            $usuario = $this->service->getById($id);
            return ['success' => true, 'data' => $usuario];
        } catch (Exception $ex) {
            return ['success' => false, 'error' => $ex->getMessage()];
        }
    }

    // Eliminar
    public function delete(int $id) {
        try {
            $resultado = $this->service->delete($id);
            return ['success' => true, 'message' => "Usuario eliminado correctamente"];
        } catch (Exception $ex) {
            return ['success' => false, 'error' => $ex->getMessage()];
        }
    }

    // Manejar rutas
    public function manejar(string $method, ?int $id = null, array $inputData = []) {
        switch($method) {
            case 'GET':
                return $id !== null ? $this->getById($id) : $this->getAll();
            case 'POST':
                if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/usuarios/clonar') !== false) {
                    return $this->clonar($inputData);
                }
                return $this->register($inputData);
            case 'PUT':
            case 'PATCH':
                if ($id === null) throw new Exception("ID requerido para actualizar");
                return $this->update($id, $inputData);
            case 'DELETE':
                if ($id === null) throw new Exception("ID requerido para eliminar");
                return $this->delete($id);
            default:
                http_response_code(405);
                return ['success' => false, 'error' => 'Método no permitido'];
        }
    }
}
?>

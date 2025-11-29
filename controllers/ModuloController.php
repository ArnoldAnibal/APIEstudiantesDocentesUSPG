<?php
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../services/ModuloService.php';
require_once __DIR__ . '/../dto/ModuloRequestDTO.php';

class ModuloController {
    private $service;

    public function __construct($currentUser) {
        $pais = $currentUser['pais'] ?? 'GT';
        $this->service = new ModuloService($pais);
    }

    public function manejar($method, $id = null, $data = null) {
        $auth = auth_require_user();
        $currentUser = $auth['user'];
        $conn = $auth['conn'];


        if (!$data) {
            $data = json_decode(file_get_contents("php://input"), true);
        }

        switch ($method) {
            case 'GET':
                $audit = [
                    'Petición Hecha por' => "{$currentUser['nombres']} {$currentUser['apellidos']} ({$currentUser['username']})"
                ];
                if ($id) {
                    $responseDTO = $this->service->getById((int)$id);
                    if ($responseDTO) {
                        echo json_encode(['audit' => $audit, 'data' => $responseDTO->toArray()]);
                    } else {
                        http_response_code(404);
                        echo json_encode(['audit' => $audit, 'error' => 'Módulo no encontrado']);
                    }
                } else {
                    $responseDTOs = $this->service->getAll();
                    $response = array_map(fn($dto) => $dto->toArray(), $responseDTOs);
                    echo json_encode(['audit' => $audit, 'data' => $response]);
                }
                break;

            case 'POST':
                $dtoRequest = new ModuloRequestDTO($data);
                $responseDTO = $this->service->create($dtoRequest);
                if ($responseDTO) {
                    $res = $responseDTO->toArray();
                    $res['audit'] = "Creado por {$currentUser['nombres']} {$currentUser['apellidos']} ({$currentUser['username']})";
                    echo json_encode($res);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'No se pudo crear el módulo']);
                }
                break;

            case 'PUT':
                $id = $data['id'] ?? null;
                if (!$id) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Se requiere un ID en el body']);
                    exit;
                }
                $dtoRequest = new ModuloRequestDTO($data);
                $responseDTO = $this->service->update($dtoRequest);
                if ($responseDTO) {
                    $res = $responseDTO->toArray();
                    $res['audit'] = "Actualizado por {$currentUser['nombres']} {$currentUser['apellidos']} ({$currentUser['username']})";
                    echo json_encode($res);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Módulo no encontrado o sin cambios']);
                }
                break;

            case 'DELETE':
                $id = $data['id'] ?? null;
                if (!$id) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Se requiere un ID en el body']);
                    exit;
                }
                $deleted = $this->service->delete((int)$id);
                if ($deleted) {
                    echo json_encode(['message' => 'Módulo eliminado', 'audit' => "Eliminado por {$currentUser['nombres']} {$currentUser['apellidos']} ({$currentUser['username']})"]);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Módulo no encontrado']);
                }
                break;

            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
        }
    }
}
?>

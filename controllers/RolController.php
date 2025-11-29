<?php
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../services/RolService.php';
require_once __DIR__ . '/../dto/RolRequestDTO.php';

class RolController {
    private $service;

    public function __construct($currentUser) {
        $pais = $currentUser['pais'] ?? 'GT';
        $this->service = new RolService($pais);
    }

    public function manejar($method, $id = null, $data = null) {
        $auth = auth_require_user();
        $currentUser = $auth['user'];
        $conn = $auth['conn'];

        if (!$data) $data = json_decode(file_get_contents("php://input"), true);

        switch ($method) {
            case 'GET':
                $audit = ['Petición Hecha por' => "{$currentUser['nombres']} {$currentUser['apellidos']} ({$currentUser['username']})"];
                if ($id) {
                    $responseDTO = $this->service->getById((int)$id);
                    if ($responseDTO)
                        echo json_encode(['audit' => $audit, 'data' => $responseDTO->toArray()]);
                    else {
                        http_response_code(404);
                        echo json_encode(['audit' => $audit, 'error' => 'Rol no encontrado']);
                    }
                } else {
                    $responseDTOs = $this->service->getAll();
                    $response = array_map(fn($dto) => $dto->toArray(), $responseDTOs);
                    echo json_encode(['audit' => $audit, 'data' => $response]);
                }
                break;

            case 'POST':
                $dto = new RolRequestDTO($data);
                $responseDTO = $this->service->create($dto, $currentUser['id']);
                $response = $responseDTO->toArray();
                $response['audit'] = "Creado por {$currentUser['nombres']} {$currentUser['apellidos']}";
                echo json_encode($response);
                break;

            case 'PUT':
                $id = $data['id'] ?? null;
                if (!$id) { http_response_code(400); echo json_encode(['error' => 'Se requiere un ID en el body']); exit; }
                $dto = new RolRequestDTO($data);
                $responseDTO = $this->service->update($dto, $currentUser['id']);
                if (!$responseDTO) {
                    http_response_code(404);
                    echo json_encode(['message' => 'Rol no encontrado o sin cambios']);
                } else {
                    $response = $responseDTO->toArray();
                    $response['audit'] = "Actualizado por {$currentUser['nombres']} {$currentUser['apellidos']}";
                    echo json_encode($response);
                }
                break;

            case 'DELETE':
                $id = $data['id'] ?? null;
                if (!$id) { http_response_code(400); echo json_encode(['error' => 'Se requiere un ID en el body']); exit; }
                $deleted = $this->service->delete((int)$id);
                if ($deleted) echo json_encode(['message' => 'Rol eliminado correctamente']);
                else { http_response_code(404); echo json_encode(['error' => "No se encontró el rol con ID $id"]); }
                break;

            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
        }
    }
}
?>

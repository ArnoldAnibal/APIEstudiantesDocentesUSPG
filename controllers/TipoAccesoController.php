<?php
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../services/TipoAccesoService.php';
require_once __DIR__ . '/../dto/TipoAccesoRequestDTO.php';

class TipoAccesoController {
    private $service;

    public function __construct($currentUser) {
        $this->service = new TipoAccesoService();
    }

    public function manejar($method, $id = null, $data = null) {
        $currentUser = auth_require_user();
        if (!$data) $data = json_decode(file_get_contents("php://input"), true);

        switch ($method) {
            case 'GET':
                $audit = ['Petición Hecha por' => "{$currentUser['nombres']} {$currentUser['apellidos']}"];
                if ($id) {
                    $dto = $this->service->getById((int)$id);
                    if ($dto)
                        echo json_encode(['audit' => $audit, 'data' => $dto->toArray()]);
                    else {
                        http_response_code(404);
                        echo json_encode(['error' => 'Tipo de acceso no encontrado']);
                    }
                } else {
                    $responseDTOs = $this->service->getAll();
                    $response = array_map(fn($dto) => $dto->toArray(), $responseDTOs);
                    echo json_encode(['audit' => $audit, 'data' => $response]);
                }
                break;

            case 'POST':
                $dto = new TipoAccesoRequestDTO($data);
                $responseDTO = $this->service->create($dto, $currentUser['id']);
                $response = $responseDTO->toArray();
                $response['audit'] = "Creado por {$currentUser['nombres']} {$currentUser['apellidos']}";
                echo json_encode($response);
                break;

            case 'PUT':
                $id = $data['id'] ?? null;
                if (!$id) { http_response_code(400); echo json_encode(['error' => 'Se requiere un ID en el body']); exit; }
                $dto = new TipoAccesoRequestDTO($data);
                $responseDTO = $this->service->update($dto, $currentUser['id']);
                if (!$responseDTO) {
                    http_response_code(404);
                    echo json_encode(['message' => 'Tipo de acceso no encontrado o sin cambios']);
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
                if ($deleted) echo json_encode(['message' => 'Tipo de acceso eliminado correctamente']);
                else { http_response_code(404); echo json_encode(['error' => "No se encontró el tipo de acceso con ID $id"]); }
                break;

            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
        }
    }
}
?>

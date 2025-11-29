<?php
require_once __DIR__ . '/../services/Authservice.php';
require_once __DIR__ . '/../dto/UsuarioRequestDTO.php';

class AuthController {
    private $service;

    public function __construct() {
        $this->service = new AuthService();
    }

    public function register($inputData) {
        try {
            $dto = new UsuarioRequestDTO($inputData);
            $created = $this->service->register($dto);
            http_response_code(201);
            echo json_encode(['usuario' => $created]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function login($inputData) {
        try {
            $dto = new UsuarioRequestDTO($inputData);
            $userWithToken = $this->service->login($dto);
            http_response_code(200);
            echo json_encode(['usuario' => $userWithToken]);
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

<?php
// incluimos el servicio de usuarios, que contiene la lógica de registro y el DTO para recibir los datos de las solicitudes
require_once __DIR__ . '/../services/UsuarioService.php';
require_once __DIR__ . '/../dto/UsuarioRequestDTO.php';

class UsuarioController {
    private $service;  // propiedad que guarda la instancia del servicio de usuarios
    public function __construct() { $this->service = new UsuarioService(); }  // consturctor que crea la instancia del servicio


    // metodo principal que panela la peticion POST, ID es el id del usuario que puede ser null, y inputDAta son los datos enviados de la peticion.
    public function manejar($method, $id = null, $inputData = null) {
    try {
        switch($method) {
            case 'POST':
                // Registro normal
                $dto = new UsuarioRequestDTO($inputData ?? []);
                $created = $this->service->register((array)$dto);
                http_response_code(201);
                echo json_encode(['usuario' => $created]);
                break;

            case 'CLONAR':
                // Clonar usuario
                $idUsuario = $inputData['id'] ?? null;
                if (!$idUsuario) {
                    throw new Exception("El ID del usuario original es obligatorio para clonar.");
                }
                $clonado = $this->service->clonar($idUsuario, $inputData);
                http_response_code(201);
                echo json_encode(['usuario_clonado' => $clonado]);
                break;

            default:
                http_response_code(405);
                echo json_encode(['error'=>'Método no soportado']);
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
}

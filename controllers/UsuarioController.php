<?php
// incluimos el servicio de usuarios, que contiene la lógica de registro y el DTO para recibir los datos de las solicitudes
require_once __DIR__ . '/../services/UsuarioService.php';
require_once __DIR__ . '/../dto/UsuarioRequestDTO.php';

class UsuarioController {
    private $service;  // propiedad que guarda la instancia del servicio de usuarios
    public function __construct() { $this->service = new UsuarioService(); }  // consturctor que crea la instancia del servicio


    // metodo principal que panela la peticion POST, ID es el id del usuario que puede ser null, y inputDAta son los datos enviados de la peticion.
    public function manejar($method, $id = null, $inputData = null) {
        switch($method) {
            case 'POST':
                $dto = new UsuarioRequestDTO($inputData ?? []);  // cremoas un DTO a partir de los datos recibimos 
                try {
                    $created = $this->service->register((array)$dto);  // llamamos al servicio para registrar al usuario
                    http_response_code(201); // codigo 201 por si fue exitoso
                    echo json_encode(['usuario'=>$created]); // se devuelve la información del usuario creado
                } catch (Exception $e) { // capturamos errores si falla el registro
                    http_response_code(400);
                    echo json_encode(['error'=>$e->getMessage()]);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(['error'=>'Metodo no soportado']);
        }
    }
}

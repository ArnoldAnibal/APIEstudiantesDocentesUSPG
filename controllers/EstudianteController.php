<?php
// Controlador de estudiantes, maneja las peticiones HTTP y se encarga de recibir la peticion, procesar los datos,  delegar la lógica del negocio

// vamos a incluir el DTO y el servicio de estudiante para poder llevar la logica del negocio
require_once __DIR__ . '/../services/EstudianteService.php';  // Servicio que contiene toda la logica del negocio
require_once __DIR__ . '/../dto/EstudianteRequestDTO.php'; // DTO para recibir datos de la solicitud

class EstudianteController { // creamos la clase controlador estudiante
    private $service; // esta es una propiedad que guardará una instancia de EstudianteService

    public function __construct() {
        // al crear el controlador, instanciamos el servicio de estudiante
        $this->service = new EstudianteService();
    }

    // aca manejamos las peticiones get, post, put, delete. el method es el tip de petición
    // method es el tipo de peticion, id, es el id del estudiante pero es opcional, data siendo el array de datos recibido en el metodo, opcional
    public function manejar($method, $id = null, $data = null) {

        // si no se proporcionó $data, leeemos el JSON del body de la peticion
        if (!$data) {
            $data = json_decode(file_get_contents("php://input"), true);
        }

        switch ($method) {  // se revisa que metodo llego
            case 'GET': // en get vemos si enviaron un id o no, si lo hacen hago uso de una clase diferente
                if ($id) {  // si nos dieron un ID, buscamos un docente en especifico
                   $responseDTO = $this->service->getById((int)$id); 
                    if ($responseDTO) {
                        echo json_encode($responseDTO->toArray());  // convertimos a JSON y devolvemos
                    } else {  // si no se encontro el docente
                        http_response_code(404);  // codigo de error
                        echo json_encode(['error' => 'Estudiante no encontrado']);
                    }
                } else {  // si no nos dieron ID, devolvemos a todos los estuidantes
                    $responseDTOs = $this->service->getAll();  // array de DTOs
                    echo json_encode(array_map(fn($dto) => $dto->toArray(), $responseDTOs));
                }
                break;
            case 'POST':  // para crear un nuevo estudiante
                $dtoRequest = new EstudianteRequestDTO($data);  // creamos un DTO a partir del JSON recibido
                $responseDTO = $this->service->create($dtoRequest); // llamamos al servicio para crear un nuevo registro
                echo json_encode($responseDTO->toArray());  // devolvemos la respuesta como JSON
                break;
            case 'PUT':  // actualizar
                $id = $data['id'] ?? null;  // verificamos que se enviá el ID por el body
                if (!$id) {  // si no, damos error
                    http_response_code(400);
                    echo json_encode(['error' => 'Se requiere un ID en el body']);
                    exit;
                }  
                $dtoRequest = new EstudianteRequestDTO($data); // creamos DTO con los datos recibidos
                $responseDTO = $this->service->update($dtoRequest);  // llamamos al servicio para actualizar
                if (!$responseDTO) {  // si no se encontró, damos error
                    http_response_code(404);
                    echo json_encode(['error' => 'Estudiante no encontrado o sin cambios']);
                } else {  // si si, damos el array como respuesta
                    echo json_encode($responseDTO->toArray());
                }
                break;
            case 'DELETE':  // elminiar
                $id = $data['id'] ?? null;  // verificamos que se envie el ID en el body
                // leemos el json del body si data no fue pasado
                if (!$id) {  // si no hay ID, damos error
                    http_response_code(400);
                    echo json_encode(['error' => 'Se requiere un ID en el body']);
                    exit;
                }
                $success = $this->service->delete((int)$id);  // si si hay, llamamos al servicio para eliminar al servicio
                echo json_encode(['success' => 'Estudiante eliminado correctamente']);
                break;
            default:
                http_response_code(405);  // mensaje para metodo no permitido
                echo json_encode(['error' => 'Método no permitido']);
        }
    }
}
?>
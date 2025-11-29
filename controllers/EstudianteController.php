<?php
// Controlador de estudiantes, maneja las peticiones HTTP y se encarga de recibir la peticion, procesar los datos,  delegar la lógica del negocio

// vamos a incluir el DTO y el servicio de estudiante para poder llevar la logica del negocio
// incluimos el middleware de autenticaion
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../services/EstudianteService.php';  // Servicio que contiene toda la logica del negocio
require_once __DIR__ . '/../dto/EstudianteRequestDTO.php'; // DTO para recibir datos de la solicitud

class EstudianteController { // creamos la clase controlador estudiante
    private $service; // esta es una propiedad que guardará una instancia de EstudianteService
    private $currentUser;

public function __construct($currentUser) {
    $this->currentUser = $currentUser;
    $pais = $currentUser['pais'] ?? 'GT';
    $this->service = new EstudianteService();
}


    // aca manejamos las peticiones get, post, put, delete. el method es el tip de petición
    // method es el tipo de peticion, id, es el id del estudiante pero es opcional, data siendo el array de datos recibido en el metodo, opcional
    public function manejar($method, $id = null, $data = null) {

        $currentUser = $this->currentUser;


        // si no se proporcionó $data, leeemos el JSON del body de la peticion
        if (!$data) {
            $data = json_decode(file_get_contents("php://input"), true);
        }

        switch ($method) {  // se revisa que metodo llego
            case 'GET': // en get vemos si enviaron un id o no, si lo hacen hago uso de una clase diferente
                // creamos un array de auditoria que tiene la info del usuario que hizo la petición
                    $audit = [
                        'Petición Hecha por ' => "{$currentUser['nombres']} {$currentUser['apellidos']} con username '{$currentUser['username']}'"
                    ];

                if ($id) {  // si nos dieron un ID, buscamos un docente en especifico
                   $responseDTO = $this->service->getById((int)$id); 
                    if ($responseDTO) {
                        echo json_encode([
                            'audit' => $audit,
                            'data' => $responseDTO->toArray()
                        ]);  // convertimos a JSON y devolvemos
                    } else {  // si no se encontro el docente
                        http_response_code(404);  // codigo de error
                        echo json_encode([
                            'audit' => $audit, // incluimos la auditoria
                            'error' => 'Estudiante no encontrado' // incluimos los datos del estudiante
                        ]);
                    }
                } else {  // si no nos dieron ID, devolvemos a todos los estuidantes
                    $responseDTOs = $this->service->getAll();  // array de DTOs
                    $response = array_map(fn($dto) => $dto->toArray(), $responseDTOs); // convertimos cada DTO en un array
                    
                    echo json_encode([
                                'audit' => $audit,
                                'data' => $response
                    ]);
                }
                break;
            case 'POST':  // para crear un nuevo estudiante
                $dtoRequest = new EstudianteRequestDTO($data);  // creamos un DTO a partir del JSON recibido
                $currentUserId = $currentUser['id']; // obtenemos el ID del usuario actual
                $responseDTO = $this->service->create($dtoRequest, $currentUserId); // llamamos al servicio para crear un nuevo registro
                $response = $responseDTO->toArray(); // convertimos el DTO a array
                $response['audit'] = "Creado por {$currentUser['nombres']} {$currentUser['apellidos']} con username '{$currentUser['username']}'"; //auditoria
                echo json_encode($response);
                break;
            case 'PUT':  // actualizar
                $id = $data['id'] ?? null;  // verificamos que se envió el ID por el body
                if (!$id) {  // si no, damos error
                    http_response_code(400);
                    echo json_encode(['error' => 'Se requiere un ID en el body']);
                    exit;
                }  
                $dtoRequest = new EstudianteRequestDTO($data); // creamos DTO con los datos recibidos
                $responseDTO = $this->service->update($dtoRequest, (int)$id);  // llamamos al servicio para actualizar con el ID del estudiante
                if (!$responseDTO) {  // si no se encontró, damos error
                    http_response_code(404);
                    echo json_encode(['message' => 'Estudiante no encontrado o sin cambios',
                                      'audit' => "Intento de actualización por {$currentUser['nombres']} {$currentUser['apellidos']} con username '{$currentUser['username']}'"]);
                } else {  // si si, damos el array como respuesta
                    $response = $responseDTO->toArray();
                    $response['audit'] = "Actualizado por {$currentUser['nombres']} {$currentUser['apellidos']} con username '{$currentUser['username']}'";
                    echo json_encode($response);
                }
                break;
            case 'DELETE':  // elminiar
                $id = $data['id'] ?? null;  // verificamos que se envie el ID en el body
                // leemos el json del body si data no fue pasado
                if (!$id) {  // si no hay ID, damos error
                    http_response_code(400);
                    echo json_encode(['message' => 'Se requiere un ID en el body']);
                    exit;
                }
                try {
                    $deleted = $this->service->delete((int)$id); // llamamos al servicio
                    if ($deleted) { // si se elimino de manera correcta
                        echo json_encode(['message' => 'Estudiante eliminado correctamente',
                                          'audit' => "Eliminado por {$currentUser['nombres']} {$currentUser['apellidos']} con username '{$currentUser['username']}'"]);
                    } else { // si no se encontró
                        http_response_code(404);
                        echo json_encode(['message' => "No se encontró el estudiante con ID $id",
                                          'audit' => "Intento de eliminación por {$currentUser['nombres']} {$currentUser['apellidos']} con username '{$currentUser['username']}'"]);
                    }
                } catch (Exception $e) { // capturamos errores de la BD
                        http_response_code(500);
                        echo json_encode(['message' => $e->getMessage(),
                                      'audit' => "Error durante la eliminación por {$currentUser['nombres']} {$currentUser['apellidos']} con username '{$currentUser['username']}'"]);
                }
                break;
            default:
                http_response_code(405);  // mensaje para metodo no permitido
                echo json_encode(['error' => 'Método no permitido',
                                  'audit' => "Intento de acceso por {$currentUser['nombres']} {$currentUser['apellidos']} con username '{$currentUser['username']}'"]);
        }
    }
}
?>

<?php
// Controlador de docentes, se encarga de manejar las peticiones HTTP, procesar los datos y delegar la logica al servicio correspodiente

// vamos a incluir el DTO y servicios necesarios para procesar las peticiones
// incluimos el middleware de autenticaion
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../dto/DocenteRequestDTO.php';  //DTO para recibir los datos de la solicitudes
require_once __DIR__ . '/../services/DocenteService.php'; // servicio que contiene la lógica de negocio


class DocenteController {  // creamos la clase controlador de docentes
    private $service;  // esta es una propiedad que guardará una instancia de DocenteService


    public function __construct() {
        // al instanciar el controlador, instanciamos el servicio de docentes
        $this->service = new DocenteService();
    }

    // aca manejamos las peticiones get, post, put, delete. el method es el tip de petición
    // method es el tipo de peticion, id, es el id del docente pero es opcional, data siendo el array de datos recibido en el metodo, opcional
    public function manejar($method, $id = null, $data = null) {

        $currentUser = auth_require_user(); // verificamos el token JWT y obtenemos el usuario actual

        // Leer JSON del body si no se pasó $data al instanciarnos
        if (!$data) {
            $data = json_decode(file_get_contents("php://input"), true);
        }

        switch ($method) {  // se revisa que metodo llego
            case 'GET':  // en get vemos si enviaron un id o no, si lo hacen hago uso de una clase diferente
                // creamos un array de auditoria que tiene la info del usuario que hizo la petición
                $audit = [
                        'Petición Hecha por ' => "{$currentUser['nombres']} {$currentUser['apellidos']} con username '{$currentUser['username']}'"
                    ];
                if ($id) {
                    //si nos enviaron un ID, buscamos un docente en especifico
                     $responseDTO = $this->service->getById((int)$id); // llamada al servicio que develve un DTO
                    if ($responseDTO) { // si se encontró al docente
                        echo json_encode([
                            'audit' => $audit,  // incluimos la auditoria
                            'data' => $responseDTO->toArray() // incluimos los datos del docente
                        ]);
                    } else { 
                        http_response_code(404);  // si no se encontró, me da el codigo e eror
                        echo json_encode([
                            'audit' => $audit,
                            'error' => 'Docente no encontrado'
                        ]);
                    }
                } else {  // si no nos dan ID, devolvemos todos los docents
                    $responseDTOs = $this->service->getAll(); // devuelve array de DTOs
                    $response = array_map(fn($dto) => $dto->toArray(), $responseDTOs); // convertimos cada DTO en un array
                                         
                    echo json_encode([
                                'audit' => $audit,
                                'data' => $response
                    ]);
                }
                break;
            case 'POST':
                // llama al servicio para crear un nuevo docente con los datos obtenidos
                $dtoRequest = new DocenteRequestDTO($data);  //creamos un DTO a partir del JSON recibido
                $currentUserId = $currentUser['id']; // obtenemos el ID del usuario actual
                $responseDTO = $this->service->create($dtoRequest, $currentUserId);  // llamamos al servicio para crear
                $response = $responseDTO->toArray(); // convertimos el DTO a array
                $response['audit'] = "Creado por {$currentUser['nombres']} {$currentUser['apellidos']} con username '{$currentUser['username']}'";  //auditoria
                echo json_encode($response);
                break;
            case 'PUT':
                // verificamos el ID que viene en el body json
                $id = $data['id'] ?? null;
                if (!$id) {  // si no existe, damos un error
                    http_response_code(400);
                    echo json_encode(['error' => 'Se requiere un ID en el body']);
                    exit;
                }
                $dtoRequest = new DocenteRequestDTO($data);  // si lo hay, creamos un DTO con los datos recibidos
                $currentUserId = $currentUser['id'];
                $responseDTO = $this->service->update($dtoRequest, $currentUserId);  // llamamos al servicio para actualizar
                if (!$responseDTO) {  // si no se encontro o no hubo algun cambio
                    http_response_code(404);
                    echo json_encode(['message' => 'Docente no encontrado o sin cambios',
                                      'audit' => "Intento de actualización por {$currentUser['nombres']} {$currentUser['apellidos']} con username '{$currentUser['username']}'"]);
                } else {
                    $response = $responseDTO->toArray();
                    $response['audit'] = "Actualizado por {$currentUser['nombres']} {$currentUser['apellidos']} con username '{$currentUser['username']}'";
                    echo json_encode($response);
                }
                break;
            case 'DELETE':
                // leemos el json del body si data no fue pasado
                $id = $data['id'] ?? null;
                if (!$id) {  //si no hay damos error
                    http_response_code(400);
                    echo json_encode(['message' => 'Se requiere un ID en el body']);
                    exit;
                }
                try {
                    $deleted = $this->service->delete((int)$id); // llamamos al servicio
                    if ($deleted) { // si se elimino de manera correcta
                        echo json_encode(['message' => 'Docente eliminado correctamente',
                                          'audit' => "Eliminado por {$currentUser['nombres']} {$currentUser['apellidos']} con username '{$currentUser['username']}'"]);
                    } else { // si no se encontró
                        http_response_code(404);
                        echo json_encode(['message' => "No se encontró el docente con ID $id",
                                          'audit' => "Intento de eliminación por {$currentUser['nombres']} {$currentUser['apellidos']} con username '{$currentUser['username']}'"]);
                    }
                } catch (Exception $e) { // capturamos errores de la BD
                        http_response_code(500);
                        echo json_encode(['message' => $e->getMessage(),
                                      'audit' => "Error durante la eliminación por {$currentUser['nombres']} {$currentUser['apellidos']} con username '{$currentUser['username']}'"]);
                }
                break;

            default:
                http_response_code(405);  // el metodo no esta permitido
                echo json_encode(['error' => 'Método no permitido',
                                  'audit' => "Intento de acceso por {$currentUser['nombres']} {$currentUser['apellidos']} con username '{$currentUser['username']}'"]);
        }
    }
}
?>
<?php
// Controlador de docentes, se encarga de manejar las peticiones HTTP, procesar los datos y delegar la logica al servicio correspodiente

// vamos a incluir el DTO y servicios necesarios para procesar las peticiones
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

        // Leer JSON del body si no se pasó $data al instanciarnos
        if (!$data) {
            $data = json_decode(file_get_contents("php://input"), true);
        }

        switch ($method) {  // se revisa que metodo llego
            case 'GET':  // en get vemos si enviaron un id o no, si lo hacen hago uso de una clase diferente
                if ($id) {
                    //si nos enviaron un ID, buscamos un docente en especifico
                     $responseDTO = $this->service->getById((int)$id); // llamada al servicio que develve un DTO
                    if ($responseDTO) { // si se encontró al docente
                        echo json_encode($responseDTO->toArray());  // convertimos a JSON y devolvemos
                    } else { 
                        http_response_code(404);  // si no se encontró, me da el codigo e eror
                        echo json_encode(['error' => 'Docente no encontrado']);
                    }
                } else {  // si no nos dan ID, devolvemos todos los docents
                    $responseDTOs = $this->service->getAll(); // devuelve array de DTOs
                    echo json_encode(array_map(fn($dto) => $dto->toArray(), $responseDTOs));
                }
                break;
            case 'POST':
                // llama al servicio para crear un nuevo docente con los datos obtenidos
                $dtoRequest = new DocenteRequestDTO($data);  //creamos un DTO a partir del JSON recibido
                $responseDTO = $this->service->create($dtoRequest);  // llamamos al servicio para crear
                echo json_encode($responseDTO->toArray());  // devolvemos la respuesta como json
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
                $responseDTO = $this->service->update($dtoRequest);  // llamamos al servicio para actualizar
                if (!$responseDTO) {  // si no se encontro o no hubo algun cambio
                    http_response_code(404);
                    echo json_encode(['error' => 'Docente no encontrado o sin cambios']);
                } else {
                    echo json_encode($responseDTO->toArray());  // si si lo hubo, mostramos el array con cambios
                }
                break;
            case 'DELETE':
                // leemos el json del body si data no fue pasado
                $id = $data['id'] ?? null;
                if (!$id) {  //si no hay damos error
                    http_response_code(400);
                    echo json_encode(['error' => 'Se requiere un ID en el body']);
                    exit;
                }
                $success = $this->service->delete((int)$id); // llamamos al servicio para eliminar
                echo json_encode(['success' => 'Docente eliminado correctamente']); // mensaje de confirmacion
                break;
            default:
                http_response_code(405);  // el metodo no esta permitido
                echo json_encode(['error' => 'Método no permitido']);
        }
    }
}
?>
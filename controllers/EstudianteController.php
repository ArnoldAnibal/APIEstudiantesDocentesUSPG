<?php
// Controlador de estudiantes, maneja las peticiones HTTP y se encarga de recibir la peticion, procesar los datos,  delegar la lógica del negocio

// vamos a incluir el servicio de estudiante para poder llevar la logica del negocio
require_once __DIR__ . '/../services/EstudianteService.php';

class EstudianteController { // creamos la clase estudiante
    private $service; // esta es una propiedad que guardará una instancia de EstudianteService

    public function __construct() {
        // al crear el controlador, instanciamos el servicio de estudiante
        $this->service = new EstudianteService();
    }

    // aca manejamos las peticiones get, post, put, delete. el method es el tip de petición
    // method es el tipo de peticion, id, es el id del estudiante pero es opcional, data siendo el array de datos recibido en el metodo, opcional
    public function manejar($method, $id = null, $data = null) {
        switch ($method) {  // se revisa que metodo llego
            case 'GET': // en get vemos si enviaron un id o no, si lo hacen hago uso de una clase diferente
                if ($id) {
                     $estudiante = $this->service->getById($id);
                    if ($estudiante) {
                        echo json_encode($estudiante->toArray());
                    } else {
                        http_response_code(404);
                        echo json_encode(['error' => 'Estudiante no encontrado']);
                    }
                } else {
                    // si no mandan ID, vemos los datos de todos
                    $estudiantes = $this->service->getAll();
                    // convierte cada objeto estudiante a un array y nos da un JSON
                    echo json_encode(array_map(fn($d) => $d->toArray(), $estudiantes));
                }
                break;
            case 'POST':
                // llamamos al servicio para crear un nuevo estudiante con los datos recibidos
                $success = $this->service->create($data);
                echo json_encode(['success' => $success]);
                break;
            case 'PUT':
                // leer el JSON del body si $data aún no se tiene
                if (!$data) {
                $data = json_decode(file_get_contents("php://input"), true);
                }

                // extraemos el ID del body
                $id = $data['id'] ?? null;

                if (!$id) {
                    // si no nos enviaron un ID, retornamos el error 400
                    http_response_code(400);
                    echo json_encode(['error' => 'Se requiere un ID en el body']);
                exit;
            }

                //  llamamos al servicio para actualizar el estudiante
              $success = $this->service->update($id, $data);
                if (!$success) {
                    // si no se actualizó, damos error 404
                    http_response_code(404);
                    echo json_encode(['error' => 'Estudiante no encontrado o sin cambios']);
                } else {
                    echo json_encode(['success' => true]);
                }
                break;
            case 'DELETE':
                // leemos el json del body si data no fue pasado
                if (!$data) {
                    $data = json_decode(file_get_contents("php://input"), true);
                }

                // extraemos el id del array
                $id = $data['id'] ?? null;

                if (!$id) {
                    // si no hay ID, da error 00
                    http_response_code(400);
                    echo json_encode(['error' => 'Se requiere un ID en el body']);
                    exit;
                }

                // llamamos el servicio para elimiar el dato
                $success = $this->service->delete($id);
                if (!$success) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Estudiante no encontrado']);
                } else {
                    echo json_encode(['success' => true]);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
        }
    }
}
?>
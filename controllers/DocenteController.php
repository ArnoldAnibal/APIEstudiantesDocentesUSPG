<?php
// Controlador de docentes, se encarga de manejar las peticiones, procesar los datos y delegar la logica al servicio correspodiente

// vamos a incluir el servicio de docentes para poder llevar la logica del negocio
require_once __DIR__ . '/../services/DocenteService.php';

class DocenteController {  // creamos la clase docente
    private $service;  // esta es una propiedad que guardará una instancia de DocenteService

    public function __construct() {
        // al crear el controlador, instanciamos el servicio de docentes
        $this->service = new DocenteService();
    }

    // aca manejamos las peticiones get, post, put, delete. el method es el tip de petición
    // method es el tipo de peticion, id, es el id del docente pero es opcional, data siendo el array de datos recibido en el metodo, opcional
    public function manejar($method, $id = null, $data = null) {
        switch ($method) {  // se revisa que metodo llego
            case 'GET':  // en get vemos si enviaron un id o no, si lo hacen hago uso de una clase diferente
                if ($id) {
                    //si nos enviaron un ID, buscamos un docente en especifico
                    $docente = $this->service->getById($id);
                    if ($docente) {
                        // devuelve los datos del docente en json
                        echo json_encode($docente->toArray());
                    } else {
                        // si no se encontro el docente por el id, nos da el error 404
                        http_response_code(404);
                        echo json_encode(['error' => 'Docente no encontrado']);
                    }
                } else {
                    // si no se envia id, se obtienen todos los docentes de la tabla
                    $docentes = $this->service->getAll();
                    // se convierte cada objeto docente a un array y devuelve un JSON
                    echo json_encode(array_map(fn($d) => $d->toArray(), $docentes));
                }
                break;
            case 'POST':
                // llama al servicio para crear un nuevo docente con los datos obtenidos
                $success = $this->service->create($data);
                echo json_encode(['success' => $success]);
                break;
            case 'PUT':
                // leer el JSON del body si $data aún no se tiene
                if (!$data) {
                $data = json_decode(file_get_contents("php://input"), true);
                }

                // extraer el ID del body
                $id = $data['id'] ?? null;

                if (!$id) {
                    // si no nos dan ID, retornamos el error 400
                    http_response_code(400);
                    echo json_encode(['error' => 'Se requiere un ID en el body']);
                exit;
            }
            // llamamos al servicio para actualizar el docente
            $success = $this->service->update($id, $data);
            // si no se actualizo retornamos el error 404
                if (!$success) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Docente no encontrado o sin cambios']);
                } else {
                    // se actualizo sin problemas
                    echo json_encode(['success' => true]);
                }
                break;
            case 'DELETE':
                // leemos el json del body si data no fue pasado
                if (!$data) {
                    $data = json_decode(file_get_contents("php://input"), true);
                }

                // se extraeo el ID del body
                $id = $data['id'] ?? null;

                if (!$id) {
                    // si no se envia ID, retornamos el error 400
                    http_response_code(400);
                    echo json_encode(['error' => 'Se requiere un ID en el body']);
                    exit;
                }

                // llamamos al servicio para eliminar el docente
                $success = $this->service->delete($id);
                 if (!$success) {
                    // error por si no se elimino el docente
                    http_response_code(404);
                    echo json_encode(['error' => 'Docente no encontrado']);
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
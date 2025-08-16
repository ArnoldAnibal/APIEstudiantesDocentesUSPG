<?php
// Controlador de docentes

// vamos a incluir el servicio de docentes para poder llevar la logica del negocio
require_once __DIR__ . '/../services/DocenteService.php';

class DocenteController {  // creamos la clase docente
    private $service;  // esta es una propiedad que guardará una instancia de DocenteService

    public function __construct() {
        // al crear el controlador, instanciamos el servicio de docentes
        $this->service = new DocenteService();
    }

    // aca manejamos las peticiones get, post, put, delete. el method es el tip de petición
    public function manejar($method, $id = null, $data = null) {
        switch ($method) {  // se revisa que metodo llego
            case 'GET':  // en get vemos si enviaron un id o no, si lo hacen hago uso de una clase diferente
                echo json_encode($id ? $this->service->obtener($id) : $this->service->listar());
                break;
            case 'POST':
                echo json_encode(["exito" => $this->service->crear($data)]);
                break;
            case 'PUT':
                echo json_encode(["exito" => $this->service->actualizar($id, $data)]);
                break;
            case 'DELETE':
                echo json_encode(["exito" => $this->service->eliminar($id)]);
                break;
        }
    }
}
?>
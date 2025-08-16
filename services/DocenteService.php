<?php
// Service para llevar la Lógica de negocio de docentes, tambien encapsulamos las operaciones y relgas del negocio usando el

// incluimos repositorios
require_once __DIR__ . '/../repositories/DocenteRepository.php';

class DocenteService {
    // creamos una propiedad privada que lamacena la instancia del repositorio
    private $repo;

    // constructor que inicializa el repositorio
    public function __construct() {
        $this->repo = new DocenteRepository();  // nueva instancia del repositorio
    }

    public function listar() {
        return $this->repo->obtenerTodos();  // llama al metodo del repositorio para tener todos los registros
    }

    public function obtener($id) {
        return $this->repo->obtenerPorId($id);  // lama al metodo del repositorio para obtener solo un registro en base a su id
    }

    public function crear($data) {
        $docente = new Docente();  // nueva instancia del modelo
        $docente->setNombres($data['nombres']);  //asignamos los nombres desde los datos recibidos
        $docente->setApellidos($data['apellidos']);  // asignamos los apellidos desde los datos recibios
        return $this->repo->crear($docente);  // llamo al repositorio para insertar el docente en la bd
    }

    public function actualizar($id, $data) {
        $docente = new Docente(); // nueva instancia del modelo
        $docente->setId($id);  // asigna el id que se actualizara
        $docente->setNombres($data['nombres']);  // actualizamos el nombre
        $docente->setApellidos($data['apellidos']);  // actualizamos el apellido
        return $this->repo->actualizar($docente);  // llamamos al repositorio para actualizar el registro
    }

    public function eliminar($id) {
        return $this->repo->eliminar($id);  // lamamos al repositorio para eliminar el registro
    }
}
?>

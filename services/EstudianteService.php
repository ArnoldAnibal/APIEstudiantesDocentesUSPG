<?php
// Service para llevar la Lógica de negocio de docentes, tambien encapsulamos las operaciones y relgas del negocio usando el

// incluimos repositorios
require_once __DIR__ . '/../repositories/EstudianteRepository.php';

class EstudianteService {
    // creamos una propiedad privada que lamacena la instancia del repositorio
    private $repo;

    // constructor que inicializa el repositorio
    public function __construct() {
        $this->repo = new EstudianteRepository(); // nueva instancia del repositorio
    }

    public function listar() {
        return $this->repo->obtenerTodos();  // llama al metodo del repositorio para tener todos los registros
    }

    public function obtener($id) {
        return $this->repo->obtenerPorId($id); // lama al metodo del repositorio para obtener solo un registro en base a su id
    }

    public function crear($data) {
        $estudiante = new Estudiante();  // nueva instancia del modelo
        $estudiante->setNombres($data['nombres']); //asignamos los nombres desde los datos recibidos
        $estudiante->setApellidos($data['apellidos']); // asignamos los apellidos desde los datos recibios
        return $this->repo->crear($estudiante);  // llamo al repositorio para insertar el docente en la bd
    }

    public function actualizar($id, $data) {
        $estudiante = new Estudiante(); // nueva instancia del modelo
        $estudiante->setId($id); // asigna el id que se actualizara
        $estudiante->setNombres($data['nombres']); // actualizamos el nombre
        $estudiante->setApellidos($data['apellidos']); // actualizamos el apellido
        return $this->repo->actualizar($estudiante); // llamamos al repositorio para actualizar el registro
    }

    public function eliminar($id) {
        return $this->repo->eliminar($id);// lamamos al repositorio para eliminar el registro
    } 
}
?>
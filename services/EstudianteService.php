<?php
// Service para llevar la Lógica de negocio de estudiantes, tambien encapsulamos operaciones, reglas, y validaciones antes de ir al repositorio

// incluimos repositorios
require_once __DIR__ . '/../repositories/EstudianteRepository.php';
require_once __DIR__ . '/../models/Estudiante.php';

class EstudianteService {
    // creamos una propiedad privada que lamacena la instancia del repositorio
    private $repo;

    // constructor que inicializa el repositorio
    public function __construct() {
        $this->repo = new EstudianteRepository(); // nueva instancia del repositorio
    }

    public function getAll(): array {
        return $this->repo->findAll();  // llama al metodo del repositorio para tener todos los registros
    }

    public function getById($id): ?Estudiante {
        return $this->repo->findById($id); // lama al metodo del repositorio para obtener solo un registro en base a su id
    }

    public function create(array $data): bool {
        // primero transformatos los datos en un objeto Estudiante
        $estudiante = new Estudiante(null, $data['nombres'], $data['apellidos']);
        return $this->repo->create($estudiante);  // llamo al repositorio para insertar el docente en la bd
    }

    public function update($id,array $data): bool {
        // creamos un nuevo objeto en base a los nuevos datos
        $estudiante = new Estudiante($id, $data['nombres'], $data['apellidos']);
        return $this->repo->update($estudiante); // llamamos al repositorio para actualizar el registro
    }

    public function delete($id): bool {
        return $this->repo->delete($id);// lamamos al repositorio para eliminar el registro
    } 
}
?>
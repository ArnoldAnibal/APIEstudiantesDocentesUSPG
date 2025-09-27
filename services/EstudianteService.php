<?php
// Service para llevar la Lógica de negocio de estudiantes, tambien encapsulamos operaciones, reglas, y validaciones antes de ir al repositorio

// incluimos repositorios, dtos, y mappers
require_once __DIR__ . '/../repositories/EstudianteRepository.php';
require_once __DIR__ . '/../dto/EstudianteRequestDTO.php';
require_once __DIR__ . '/../dto/EstudianteResponseDTO.php';
require_once __DIR__ . '/../mapper/EstudianteMapper.php';

class EstudianteService {
    // creamos una propiedad privada que lamacena la instancia del repositorio
    private EstudianteRepository $repo;

    // constructor que inicializa el repositorio
    public function __construct() {
        $this->repo = new EstudianteRepository(); // nueva instancia del repositorio
    }

    // obtener todos los estudiantes
    public function getAll(): array {
        $estudiantes = $this->repo->findAll();  // llama al metodo del repositorio para tener todos los registros
        // convierte cada entidad Estudiante a ResponseDTO antes de devolverlo
        return array_map(fn($d) => EstudianteMapper::mapEntityToResponseDTO($d), $estudiantes); // el array estudiantes tiene todos los objetos Estudiantes del FindAll. el array map es una funcion de php que aplica una funcion a cada elemento de un array y devuelve un nuevo array con los resultados. el fd($d) es una funcion anonima que recibe un elemento $d del array y lo pasa al mapper para volverlo DTO, y devuelve un array map donce cada objeto Estudiante se convirtió en un dto
    }

    // obtener estudiante por ID
    public function getById($id): ?EstudianteResponseDTO {
        $estudiante = $this->repo->findById($id); // llama al metodo repositorio para obtener un regirstro por ID
        return $estudiante ? EstudianteMapper::mapEntityToResponseDTO($estudiante) : null; // Si existe, lo convierte a un responseDTO si no, devuelve null
    }

    // creamos un nuevo estudiante, dto son los datos recibidos para la peticion
    public function create(EstudianteRequestDTO $dto, int $currentUserId): EstudianteResponseDTO {
        // primero transformatos los datos del DTO a un objeto Estudiante
        $estudiante = EstudianteMapper::mapRequestDTOToEntity($dto, false); // false es creacion
        $estudiante->setUsuarioCreacion($currentUserId);  // seteamos quien creo el registro
        $estudiante->setFechaCreacion(date('Y-m-d H:i:s')); // fecha y hora actual
        return $this->repo->create($estudiante);  // llamo al repositorio para insertar el estudiante en la bd y retornamos el DTO resultante
    }

    // actualizar el estudiante, recibe un DTO con los datos recibidos en la peticion, y retorna un DTO acutalizado o null
    public function update(EstudianteRequestDTO $dto, int $currentUserId): ?EstudianteResponseDTO {
        // creamos un nuevo objeto a partir del DTO y se indica que es actualizacion
        $estudiante = EstudianteMapper::mapRequestDTOToEntity($dto, true); // true es update
        $estudiante->setUsuarioModificacion($currentUserId); // setamos quien actualizo el registro
        $estudiante->setFechaModificacion(date('Y-m-d H:i:s')); // fecha y hora actual
        return $this->repo->update($estudiante); // lamamos al repositorio para actualizar y devolvemos el DTO resultante
    }

    // eliminamos el estudiante por ID, recibimos el ID, y retornamos un bool si se elimino
    public function delete($id): bool {
        $deleted = $this->repo->delete($id); // lamamos al repositorio para eliminar el registro
        return $deleted ? true : false;
    } 
}
?>
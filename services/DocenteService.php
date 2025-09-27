<?php
// Service para llevar la Lógica de negocio de docentes, tambien encapsulamos operaciones, reglas, y validaciones antes de ir al repositorio

// incluimos repositorios, dtos, y mappers
require_once __DIR__ . '/../repositories/DocenteRepository.php';
require_once __DIR__ . '/../dto/DocenteRequestDTO.php';
require_once __DIR__ . '/../dto/DocenteResponseDTO.php';
require_once __DIR__ . '/../mapper/DocenteMapper.php';

class DocenteService {
    // creamos una propiedad privada que lamacena la instancia del repositorio
    private DocenteRepository $repo;

    // constructor que inicializa el repositorio
    public function __construct() {
        $this->repo = new DocenteRepository();  // nueva instancia del repositorio
    }

    // obtener todos los docentes
    public function getAll(): array {
        $docentes = $this->repo->findAll();  // llama al metodo del repositorio para tener todos los registros
        // convierte cada entidad Docente a ResponseDTO antes de devolverlo
        return array_map(fn($d) => DocenteMapper::mapEntityToResponseDTO($d), $docentes);// el array docentes tiene todos los objetos Docentes del FindAll. el array map es una funcion de php que aplica una funcion a cada elemento de un array y devuelve un nuevo array con los resultados. el fd($d) es una funcion anonima que recibe un elemento $d del array y lo pasa al mapper para volverlo DTO, y devuelve un array map donce cada objeto Docente se convirtió en un dto
    }

    // obtener docente por ID
    public function getById(int $id): ?DocenteResponseDTO {
        $docente = $this->repo->findById($id);  // llama al metodo repositorio para obtener un regirstro por ID
        return $docente ? DocenteMapper::mapEntityToResponseDTO($docente) : null;;  // Si existe, lo convierte a un responseDTO si no, devuelve null
    }

    // creamos un nuevo docente, dto son los datos recibidos para la peticion
    public function create(DocenteRequestDTO $dto, int $currentUserId): DocenteResponseDTO {
        // primero transformatos los datos del DTO a un objeto Docente
        $docente = DocenteMapper::mapRequestDTOToEntity($dto, false); // false es creacion
        $docente->setUsuarioCreacion($currentUserId);  // seteamos quien creo el registro
        $docente->setFechaCreacion(date('Y-m-d H:i:s')); // fecha y hora actual
        return $this->repo->create($docente);// llamo al repositorio para insertar el docente en la bd y retornamos el DTO resultante
    }

    // actualizar el docente, recibe un DTO con los datos recibidos en la peticion, y retorna un DTO acutalizado o null
    public function update(DocenteRequestDTO $dto, int $currentUserId): ?DocenteResponseDTO {
        // creamos un nuevo objeto a partir del DTO y se indica que es actualizacion
        $docente = DocenteMapper::mapRequestDTOToEntity($dto, true); // true = update
        $docente->setUsuarioModificacion($currentUserId); // seteamos quien acutalizo el registro
        $docente->setFechaModificacion(date('Y-m-d H:i:s')); // fecha y hora actual
        return $this->repo->update($docente);  // lamamos al repositorio para actualizar y devolvemos el DTO resultante
    }

    // eliminamos el docente por ID, recibimos el ID, y retornamos un bool si se elimino
    public function delete(int $id): bool {
        $deleted = $this->repo->delete($id); // lamamos al repositorio para eliminar el registro
        return $deleted ? true : false;
    }
}
?>

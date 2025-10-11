<?php
require_once __DIR__ . '/../repositories/RolRepository.php';
require_once __DIR__ . '/../dto/RolResponseDTO.php';

class RolService {
    private $repository;

    public function __construct() {
        $this->repository = new RolRepository();
    }

    public function getAll() {
        $roles = $this->repository->findAll();
        return array_map(fn($r) => new RolResponseDTO($r), $roles);
    }

    public function getById($id) {
        $rol = $this->repository->findById($id);
        return $rol ? new RolResponseDTO($rol) : null;
    }

    public function create(RolRequestDTO $dto, $userId) {
        $nuevo = $this->repository->create($dto->toArray(), $userId);
        return new RolResponseDTO($nuevo);
    }

    public function update(RolRequestDTO $dto, int $currentUserId): ?RolResponseDTO {
    $data = [
        'nombre' => $dto->getNombre()
    ];
    $id = $dto->getId();  // obtén el ID desde el DTO
    $rol = $this->repository->update($id, $data, $currentUserId);

    if (!$rol) {
        return null;
    }
    return new RolResponseDTO($rol);
    }

    public function delete($id) {
        return $this->repository->delete($id);
    }
}
?>

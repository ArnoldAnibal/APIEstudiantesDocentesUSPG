<?php
require_once __DIR__ . '/../repositories/ModuloRepository.php';
require_once __DIR__ . '/../mapper/ModuloMapper.php';
require_once __DIR__ . '/../dto/ModuloRequestDTO.php';
require_once __DIR__ . '/../dto/ModuloResponseDTO.php';

class ModuloService {
    private $repository;

    public function __construct($pais) {
        $this->repository = new ModuloRepository($pais);
    }

    public function getAll(): array {
        $modulos = $this->repository->findAll();
        return array_map(fn($modulo) => ModuloMapper::mapEntityToResponseDTO($modulo), $modulos);
    }

    public function getById(int $id): ?ModuloResponseDTO {
        $modulo = $this->repository->findById($id);
        return $modulo ? ModuloMapper::mapEntityToResponseDTO($modulo) : null;
    }

    public function create(ModuloRequestDTO $dto): ?ModuloResponseDTO {
        $modulo = ModuloMapper::mapRequestDTOToEntity($dto);
        return $this->repository->create($modulo);
    }

    public function update(ModuloRequestDTO $dto): ?ModuloResponseDTO {
        $modulo = ModuloMapper::mapRequestDTOToEntity($dto, true);
        return $this->repository->update($modulo);
    }

    public function delete(int $id): bool {
        return $this->repository->delete($id);
    }
}
?>

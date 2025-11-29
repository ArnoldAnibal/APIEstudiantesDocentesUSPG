<?php
require_once __DIR__ . '/../repositories/TipoAccesoRepository.php';
require_once __DIR__ . '/../dto/TipoAccesoResponseDTO.php';

class TipoAccesoService {
    private $repository;

    public function __construct($pais) {
        $this->repository = new TipoAccesoRepository($pais);
    }

    public function getAll() {
        $tipos = $this->repository->findAll();
        return array_map(fn($t) => new TipoAccesoResponseDTO($t), $tipos);
    }

    public function getById($id) {
        $tipo = $this->repository->findById($id);
        return $tipo ? new TipoAccesoResponseDTO($tipo) : null;
    }

    public function create(TipoAccesoRequestDTO $dto, $userId) {
        $nuevo = $this->repository->create($dto->toArray(), $userId);
        return new TipoAccesoResponseDTO($nuevo);
    }

    public function update(TipoAccesoRequestDTO $dto, int $currentUserId): ?TipoAccesoResponseDTO {
        $data = [
            'nombre' => $dto->getNombre()
        ];
        $id = $dto->getId();  // obtén el ID desde el DTO
        $tipoAcceso = $this->repository->update($id, $data, $currentUserId);

        if (!$tipoAcceso) {
            return null;
        }
        return new TipoAccesoResponseDTO($tipoAcceso);
    }

    public function delete($id) {
        return $this->repository->delete($id);
    }
}
?>

<?php
require_once __DIR__ . '/../repositories/UsuarioAccesoRepository.php';
require_once __DIR__ . '/../dto/UsuarioAccesoResponseDTO.php';

class UsuarioAccesoService {
    private $repository;

    public function __construct() {
        $this->repository = new UsuarioAccesoRepository();
    }

    public function getAll() {
    $items = $this->repository->findAll(); // array de DTOs
    return $items; // ya son DTOs
}


    public function getById($id) {
        $item = $this->repository->findById($id);
        return $item ? new UsuarioAccesoResponseDTO($item) : null;
    }

    public function create(UsuarioAccesoRequestDTO $dto, $userId) {
        $nuevo = $this->repository->create($dto->toArray(), $userId);
        return new UsuarioAccesoResponseDTO($nuevo);
    }

    public function update(UsuarioAccesoRequestDTO $dto, $userId) {
    if (!isset($dto->id)) {
        throw new Exception("Se requiere un ID para actualizar el registro");
    }
    $actualizado = $this->repository->update((int)$dto->id, $dto->toArray());
    return $actualizado ? new UsuarioAccesoResponseDTO($actualizado) : null;
}

    public function delete($id) {
        return $this->repository->delete($id);
    }

    public function cloneAccesos(int $usuarioOriginalId, int $nuevoUsuarioId): bool {
    $accesosOriginales = $this->repository->findByUsuarioId($usuarioOriginalId);

    foreach ($accesosOriginales as $acceso) {
        // Normalizar las claves para el RequestDTO
        $dataDTO = [
            'id_usuario' => $nuevoUsuarioId,
            'rol_id' => $acceso['idRol'] ?? $acceso['idrol'] ?? null,
            'modulo_id' => $acceso['idModulo'] ?? $acceso['idmodulo'] ?? null,
            'tipoacceso_id' => $acceso['idAcceso'] ?? $acceso['idacceso'] ?? null
        ];

        $dto = new UsuarioAccesoRequestDTO($dataDTO);

        // Debug opcional
        // var_dump($dto->toArray());

        $this->repository->create($dto->toArray());
    }

    return true;
}

}
?>

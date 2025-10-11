<?php
require_once __DIR__ . '/../entities/Modulo.php';
require_once __DIR__ . '/../dto/ModuloRequestDTO.php';
require_once __DIR__ . '/../dto/ModuloResponseDTO.php';

class ModuloMapper {

    // Convierte DTO de request a entidad
    public static function mapRequestDTOToEntity(ModuloRequestDTO $dto, bool $isUpdate = false): Modulo {
        $modulo = new Modulo();
        if ($isUpdate) {
            $modulo->setId($dto->id ?? null);
        }
        $modulo->setNombre($dto->nombre); // usa el setter de la entidad
        return $modulo;
    }

    // Convierte entidad a DTO de response
    public static function mapEntityToResponseDTO(Modulo $modulo): ModuloResponseDTO {
        return new ModuloResponseDTO(
            $modulo->getId(),
            $modulo->getNombre()
        );
    }

    // Convierte fila de la BD a entidad
    public static function mapRowToEntity(array $row): Modulo {
        $modulo = new Modulo();
        $modulo->setId($row['id']);
        $modulo->setNombre($row['nombre']);
        return $modulo;
    }

    // Convierte entidad a array listo para INSERT o UPDATE
    public static function mapEntityToRow(Modulo $modulo): array {
        return [
            'id' => $modulo->getId(),
            'nombre' => $modulo->getNombre()
        ];
    }
}
?>

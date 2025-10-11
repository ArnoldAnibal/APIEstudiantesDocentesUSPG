<?php
require_once __DIR__ . '/../dto/RolResponseDTO.php';

class RolMapper {
    public static function mapRowToResponseDTO(array $row): RolResponseDTO {
        return new RolResponseDTO([
            'id' => $row['id_rol'] ?? $row['id'] ?? null,
            'nombre' => $row['nombre_rol'] ?? $row['nombre'] ?? null
        ]);
    }
}

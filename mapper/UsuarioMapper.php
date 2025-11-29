<?php

require_once __DIR__ . '/../dto/UsuarioResponseDTO.php';
require_once __DIR__ . '/../entities/Usuario.php';

class UsuarioMapper {

    /**
     * Convierte un array o entidad Usuario en un UsuarioResponseDTO
     */
    public static function mapEntityToResponseDTO($row): UsuarioResponseDTO {
        if (!$row) {
            return new UsuarioResponseDTO(); // retorna DTO vacío si no hay datos
        }

        return new UsuarioResponseDTO([
            'id'       => $row['id'] ?? null,
            'username' => $row['username'] ?? null,
            'nombres'  => $row['nombres'] ?? null,
            'apellidos'=> $row['apellidos'] ?? null,
            'correo'=> $row['correo'] ?? null,
            'pais' => $row['pais'] ?? null
        ]);
    }
}


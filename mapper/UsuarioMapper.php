<?php

// se incluye los DTOs y entidad que se va a mapear
require_once __DIR__ . '/../dto/UsuarioResponseDTO.php';
require_once __DIR__ . '/../entities/Usuario.php';

// clase mapper que convierte entre la entity usuario y los dtos
class UsuarioMapper {
    // función que recibe un array o entity que es una fila de la BD y devuelve un response dto
    public static function mapEntityToResponseDTO($row): UsuarioResponseDTO {
        // creamos un nuevo usuarioresponsedto a partir de la fila obtenida
        return new UsuarioResponseDTO([
            'id'=>$row['id'] ?? null,
            'username'=>$row['username'] ?? null,
            'nombres'=>$row['nombres'] ?? null,
            'apellidos'=>$row['apellidos'] ?? null
        ]);
    }
}

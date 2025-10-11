<?php
require_once __DIR__ . '/../dto/UsuarioAccesoResponseDTO.php';
class UsuarioAccesoMapper {
    public static function mapRowToResponseDTO(array $row): UsuarioAccesoResponseDTO {
        return new UsuarioAccesoResponseDTO([
            'idUsuarioAcceso' => $row['idUsuarioAcceso'] ?? null,
            'idUsuario' => $row['idUsuario'] ?? null,
            'idRol' => $row['idRol'] ?? null,
            'idModulo' => $row['idModulo'] ?? null,
            'idAcceso' => $row['idAcceso'] ?? null
        ]);
    }
}




<?php

// incluimos las calses necesarias como la entidad y ambos DTOs
require_once __DIR__ . '/../entities/Docente.php';
require_once __DIR__ . '/../dto/DocenteRequestDTO.php';
require_once __DIR__ . '/../dto/DocenteResponseDTO.php';

// clase mapper que se encarga de convertir entre DTOs, entity y arrays para la BD
class DocenteMapper {

    // Mapea RequestDTO a Entity, el $dto tiene los datos del docente, is update nos dice si es una operacion de actualizacion, y el Docente es que es un objeto listo para insertar o actualizar a la BD
    public static function mapRequestDTOToEntity(DocenteRequestDTO $dto, bool $isUpdate = false): Docente {
        $docente = new Docente();  // creamos un nuevo objeto docente
        if ($isUpdate) {
            // si es un update, sseteamos la id y los cambios de auditoria 
            $docente->setId($dto->id);
            //$docente->setUsuarioModificacion("system_apiActualizacion");
            //$docente->setFechaModificacion(date('Y-m-d H:i:s')); // esta es la fecha de ahorita
        }

        // asignamos los nombres y apellidos del DTO al objeto entity
        $docente->setNombres($dto->nombres);
        $docente->setApellidos($dto->apellidos);

        return $docente;  // retornamos el objeto Entity
    }

    // Mapea Entity a ResponseDTO es un objeto Docente y un DTO listo para enviar la respuesta
    public static function mapEntityToResponseDTO(Docente $docente): DocenteResponseDTO {
        return new DocenteResponseDTO(
            $docente->getId(),
            $docente->getNombres(),
            $docente->getApellidos(),
            $docente->getUsuarioCreacion(),
            $docente->getFechaCreacion(),
            $docente->getUsuarioModificacion(),
            $docente->getFechaModificacion()
        );
    }

    // mapeo de un registro array de la base de datos a un objeto Docente que es una entity
    public static function mapRowToEntity(array $row): Docente {
    return new Docente(
        $row['id'],
        $row['nombres'],
        $row['apellidos'],
        $row['usuario_creacion_username'] ?? null,
        $row['fechaCreacion'] ?? '',
        $row['usuario_modificacion_username'] ?? null,
        $row['fechaModificacion'] ?? ''
    );
}

// mapeamos o convertimos un objeto Docente a un array listo para interaccion con la bd 
public static function mapDocenteToRow(Docente $docente): array {
    return [
        'id' => $docente->getId(),
        'nombres' => $docente->getNombres(),
        'apellidos' => $docente->getApellidos(),
        'UsuarioCreacion' => $docente->getUsuarioCreacion(),
        'FechaCreacion' => $docente->getFechaCreacion(),
        'UsuarioModificacion' => $docente->getUsuarioModificacion(),
        'FechaModificacion' => $docente->getFechaModificacion()
    ];
}
}
?>

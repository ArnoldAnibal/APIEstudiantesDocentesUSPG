<?php
// incluimos las calses necesarias como la entidad y ambos DTOs
require_once __DIR__ . '/../entities/Estudiante.php';
require_once __DIR__ . '/../dto/EstudianteRequestDTO.php';
require_once __DIR__ . '/../dto/EstudianteResponseDTO.php';

// clase mapper que se encarga de convertir entre DTOs, entity y arrays para la BD
class EstudianteMapper {

    // Mapea RequestDTO a Entity, el $dto tiene los datos del docente, is update nos dice si es una operacion de actualizacion, y el Docente es que es un objeto listo para insertar o actualizar a la BD
    public static function mapRequestDTOToEntity(EstudianteRequestDTO $dto, bool $isUpdate = false): Estudiante {
        $estudiante = new Estudiante(); // creamos un nuevo objeto docente
        if ($isUpdate) {
            // si es un update, sseteamos la id y los cambios de auditoria 
            $estudiante->setId($dto->id);
            $estudiante->setUsuarioModificacion("system_apiActualizacion");
            $estudiante->setFechaModificacion(date('Y-m-d H:i:s')); // esta es la fecha de ahorita
        } else {
            // si es una creacion, seteamos los campos de creacion
            $estudiante->setUsuarioCreacion("system_api");
            $estudiante->setFechaCreacion(date('Y-m-d H:i:s')); // esta es la fecha de ahorita
        }

        // asignamos los nombres y apellidos del DTO al objeto entity
        $estudiante->setNombres($dto->nombres);
        $estudiante->setApellidos($dto->apellidos);

        return $estudiante; // retornamos el objeto Entity
    }

    // Mapea Entity a ResponseDTO es un objeto Docente y un DTO listo para enviar la respuesta
    public static function mapEntityToResponseDTO(Estudiante $estudiante): EstudianteResponseDTO {
        return new EstudianteResponseDTO(
            $estudiante->getId(),
            $estudiante->getNombres(),
            $estudiante->getApellidos(),
            $estudiante->getUsuarioCreacion(),
            $estudiante->getFechaCreacion(),
            $estudiante->getUsuarioModificacion(),
            $estudiante->getFechaModificacion()
        );
    }

    // mapeo de un registro array de la base de datos a un objeto Docente que es una entity
    public static function mapRowToEntity(array $row): Estudiante {
    return new Estudiante(
        $row['id'],
        $row['nombres'],
        $row['apellidos'],
        $row['usuarioCreacion'] ?? '',
        $row['fechaCreacion'] ?? '',
        $row['usuarioModificacion'] ?? '',
        $row['fechaModificacion'] ?? ''
    );
}

// mapeamos o convertimos un objeto Docente a un array listo para interaccion con la bd 
    public static function mapEstudianteToRow(Estudiante $estudiante): array {
        return [
            'id' => $estudiante->getId(),
            'nombres' => $estudiante->getNombres(),
            'apellidos' => $estudiante->getApellidos(),
            'UsuarioCreacion' => $estudiante->getUsuarioCreacion(),
            'FechaCreacion' => $estudiante->getFechaCreacion(),
            'UsuarioModificacion' => $estudiante->getUsuarioModificacion(),
            'FechaModificacion' => $estudiante->getFechaModificacion()
        ];
    }

}
?>

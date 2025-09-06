<?php
// este archivo Mapper nos ayuda a transformar los datos de la BD que son arrays asociativos a objetos de aplicación

require_once __DIR__ . '/../models/Estudiante.php';

class EstudianteMapper {
// Acá vamos a convertir un array asociativo, que es una fila de la BD, en un nuevo objeto
    // array row es la fila obtenida de la bd con claves id, nombre, y apellidos, return objeto estudiante
    public static function mapRowToEstudiante(array $row): Estudiante {
         // acá creamos un objeto Estudiante usando los datos de la fila
        return new Estudiante(
            $row['id'] ?? null, // si no hay id, entonces es null
            $row['nombres'] ?? '', // si no hay nombres, asignamos un string vacio
            $row['apellidos'] ?? '' // si no hay nombres, asignamos un string vacio
        );
    }

    // Acá se convierte un objeto Estudiante en un array asociativo para peticiones SQL como insert o post y update o put
    // Estudiante $estudiante es el objeto que vamos a convertir
    // retornamos un array es el array asociativo conlas claves id, nombres, apellidos
    public static function mapEstudianteToRow(Estudiante $estudiante): array {
        return [
            'id' => $estudiante->getId(), // sacamos el id del objeto
            'nombres' => $estudiante->getNombres(),
            'apellidos' => $estudiante->getApellidos()
        ];
    }
}


?>
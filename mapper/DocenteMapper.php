<?php
// este archivo Mapper nos ayuda a transformar los datos de la BD que son arrays asociativos a objetos de aplicación

require_once __DIR__ .'/../models/Docente.php';

class DocenteMapper {
    // Acá vamos a convertir un array asociativo, que es una fila de la BD, en un nuevo objeto
    // array row es la fila obtenida de la bd con claves id, nombre, y apellidos, return objeto docente
    public static function mapRowToDocente(array $row): Docente{
        // acá creamos un objeto Docente usando los datos de la fila
        return new Docente(
            $row['id'] ?? null,  // si no hay id, entonces es null
            $row['nombres'] ?? '', // si no hay nombres, asignamos un string vacio
            $row['apellidos'] ?? '' // si no hay nombres, asignamos un string vacio
        );
    }


    // Acá se convierte un objeto Docente en un array asociativo para peticiones SQL como insert o post y update o put
    // docente $docente es el objeto que vamos a convertir
    // retornamos un array es el array asociativo conlas claves id, nombres, apellidos
public static function mapDocenteToRow(Docente $docente): array {
    return [
        'id' => $docente->getId(), // sacamos el id del objeto
        'nombres' => $docente->getNombres(),
        'apellidos' => $docente->getApellidos()
    ];
}
}

?>
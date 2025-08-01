## API con PHP y MySQLi

Este proyecto es una API desarrollada en PHP utilizando MySQLi como método de conexión a la base de datos. El propósito es administrar dos tablas: estudiantes y docentes, con un CRUD (crear, leer, actualizar, eliminar).
Fue creada para la clase de programación III en USPG para la carrera de Ingeniera en sistemas

## Estructura del proyecto

apidocente/
    connection/
        db.php                # Clase de conexión a MySQL (usando mysqli)
    controllers/
        estudiantesController.php
        docentesController.php
    models/
        docente.php
        estudiante.php
    routes/
        rutas.php             # Interpretación de URI para enrutar a controladores
    public/
        index.php             # Punto de partida
        .htaccess             # Permite reescritura de URLs


## Queries disponibles

### Estudiantes
    GET /estudiantes -> Lista todos los estudiantes
    POST /estudiantes -> Crea un nuevo estudiante
            {
            "nombres": "nombres",
            "apellidos": "apellidos"
            }
    PUT /estudiantes/ -> Actualiza un estudiante
            {
            "id": id,
            "nombres": "nombres",
            "apellidos": "apellidos"
            }

    DELETE /estudiantes -> Elimina un estudiante
            {
            "id": id
            }


### Docentes
    GET /docentes -> Lista todos los docentes
    POST /docentes -> Crea un nuevo docente
            {
            "nombres": "nombres",
            "apellidos": "apellidos"
            }
    PUT /docentes/ -> Actualiza un docente
            {
            "id": id,
            "nombres": "nombres",
            "apellidos": "apellidos"
            }

    DELETE /docentes?id=id -> Elimina un docente
            {
            "id": id
            }

## Métodos soportados

GET, POST, PUT, DELETE


## Autor

Desarrollado por: Arnold Avila
Fecha: Agosto 2025

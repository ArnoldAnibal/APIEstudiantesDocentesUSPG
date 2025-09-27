## API con PHP y MySQLi

Este proyecto es una API desarrollada en PHP utilizando MySQLi como método de conexión a la base de datos. El propósito es administrar dos tablas: estudiantes y docentes, con un CRUD (crear, leer, actualizar, eliminar).
Fue creada para la clase de programación III en USPG para la carrera de Ingeniera en sistemas

## Estructura del proyecto

APIDocente/
connection/
    db.php
models/
    Docente.php
    Estudiante.php
repositories/
    DocenteRepository.php
    EstudianteRepository.php
services/
    DocenteService.php
    EstudianteService.php
controllers/
    DocenteController.php
    EstudianteController.php
routes/
    rutas.php
public/
    index.php
    .htaccess
README.txt


## Queries disponibles

## Usuarios
    POST -> Crear un nuevo usuario

        http://localhost/APIDocente/public/index.php/auth/register 
        En el body tipo raw
            {
            "username": "",
            "password": "",
            "nombres": "",
            "apellidos": ""
            }

        POST -> Iniciar sesión

        http://localhost/APIDocente/public/index.php/auth/login 
        En el body tipo raw
            {
            "username": "",
            "password": ""
            }

            COPIA EL TOKEN QUE TE DA PORQUE SE USARA PARA LAS RUTAS PROTEGIDAS

### Estudiantes
EN HEADERS DEBES AGREGAR UN NUEVO LLAMADO 'AUTHORIZATION' Y ESTE DEBE SER 'BEARER TUTOKEN'
    GET -> Lista todos los estudiantes

        http://localhost/APIDocente/public/index.php/estudiantes  -> todos los estudiantes
        http://localhost/APIDocente/public/index.php/estudiantes/{id}  -> por ID


    POST -> Crea un nuevo estudiante

    http://localhost/APIDocente/public/index.php/estudiantes
        En el body tipo raw
            {
            "nombres":"nombre",
            "apellidos":"apellido"
            }


    PUT -> Actualiza un estudiante

    http://localhost/APIDocente/public/index.php/estudiantes
        En el body tipo raw
            {
            "id": id,
            "nombres": "nombres",
            "apellidos": "apellidos"
            }

    DELETE -> Elimina un estudiante

    http://localhost/APIDocente/public/index.php/estudiantes
    En el body tipo raw
            {
            "id": id
            }



## Docentes

EN HEADERS DEBES AGREGAR UN NUEVO LLAMADO 'AUTHORIZATION' Y ESTE DEBE SER 'BEARER TUTOKEN'

    GET -> Lista todos los docentes

        http://localhost/APIDocente/public/index.php/docentes  -> todos los docentes
        http://localhost/APIDocente/public/index.php/docentes/{id}  -> por ID


    POST -> Crea un nuevo estudiante

    http://localhost/APIDocente/public/index.php/docentes
        En el body tipo raw
            {
            "nombres":"nombre",
            "apellidos":"apellido"
            }


    PUT -> Actualiza un estudiante

    http://localhost/APIDocente/public/index.php/docentes
        En el body tipo raw
            {
            "id": id,
            "nombres": "nombres",
            "apellidos": "apellidos"
            }

    DELETE -> Elimina un estudiante

    http://localhost/APIDocente/public/index.php/docentes
    En el body tipo raw
            {
            "id": id
            }

## Métodos soportados

GET, POST, PUT, DELETE


## Autor

Desarrollado por: Arnold Avila
Fecha: Septiembre 2025

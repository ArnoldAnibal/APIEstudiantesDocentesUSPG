## API con PHP y MySQLi

Este proyecto es una API desarrollada en PHP utilizando MySQLi como método de conexión a la base de datos. El propósito es administrar varias tablas: estudiantes, docentes, usuarios, rol, modulo, tipo de acceso, y usuario acceso con CRUD (crear, leer, actualizar, eliminar). Se ha agregado el concepto de Propotype para clonar los tipos de acceso configurado a un usuario.
Fue creada para la clase de programación III en USPG para la carrera de Ingeniera en sistemas

## Estructura del proyecto

APIDocente/
auth/
    auth.php
connection/
    db.php
controllers/
    AuthController.php
    DocenteController.php
    EstudianteController.php
    ModuloController.php
    RolController.php
    TipoAccesoController.php
    UsuarioAccesoController.php
    UsuarioController.php
dto/
    DocenteRequestDTO.php
    DocenteResponseDTO.php
    EstudianteRequestDTO.php
    EstudianteResponseDTO.php
    ModuloRequestDTO.php
    ModuloResponseDTO.php
    RolRequestDTO.php
    RolResponseDTO.php
    TipoAccesoRequestDTO.php
    TipoAccesoResponseDTO.php
    UsuarioAccesoRequestDTO.php
    UsuarioAccesoResponseDTO.php
    UsuarioRequestDTO.php
    UsuarioResponseDTO.php
entities/
    Docente.php
    Estudiante.php
    Modulo.php
    Rol.php
    TipoAcceso.php
    Usuario.php
    UsuarioAcceso.php
mapper/
    DocenteMapper.php
    EstudianteMapper.php
    ModuloMapper.php
    RolMapper.php
    TipoAccesoMapper.php
    UsuarioAccesoMapper.php
    UsuarioMapper.php
middlewares/
    AuthMiddleware.php
public/
    .htaccess
    index.php
repositories/
    DocenteRepository.php
    EstudianteRepository.php
    ModuloRepository.php
    RolRepository.php
    TipoAccesoRepository.php
    UsuarioAccesoRepository.php
    UsuarioRepository.php
routes/
    rutas.php
services/
    Authservice.php
    DocenteService.php
    EstudianteService.php
    ModuloService.php
    RolService.php
    TipoAccesoService.php
    UsuarioAccesoService.php
    UsuarioService.php
README.txt

Se hace uso de la libreria Firebase por medio de Composer

## Queries disponibles

## Usuarios
    POST -> Crear un nuevo usuario

        http://localhost/APIDocente/public/index.php/auth/register
        En el body tipo raw
            {
            "username": "",
            "password": "",
            "nombres": "",
            "apellidos": "",
            "correo": "",
            "pais": ""
            }
    SOLO SE ACEPTA COMO PAIS GT, SV o HN

    POST -> Iniciar sesión

        http://localhost/APIDocente/public/index.php/auth/login
        En el body tipo raw
            {
            "username": "",
            "password": "",
            "pais": ""
            }
    SOLO SE ACEPTA COMO PAIS GT, SV o HN
            COPIA EL TOKEN QUE TE DA PORQUE SE USARA PARA LAS RUTAS PROTEGIDAS

    POTS -> Clonar

        http://localhost/APIDocente/public/index.php/usuarios/clonar
        En el body tipo JSON
            {
            "id": idAserClonada,
            "username": "",
            "password": "",
            "nombres": "",
            "apellidos": "",
            "correo": ""
            }

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


    POST -> Crea un nuevo docente

    http://localhost/APIDocente/public/index.php/docentes
        En el body tipo raw
            {
            "nombres":"nombre",
            "apellidos":"apellido"
            }


    PUT -> Actualiza un docente

    http://localhost/APIDocente/public/index.php/docentes
        En el body tipo raw
            {
            "id": id,
            "nombres": "nombres",
            "apellidos": "apellidos"
            }

    DELETE -> Elimina un docente

    http://localhost/APIDocente/public/index.php/docentes
    En el body tipo raw
            {
            "id": id
            }

## Modulos

EN HEADERS DEBES AGREGAR UN NUEVO LLAMADO 'AUTHORIZATION' Y ESTE DEBE SER 'BEARER TUTOKEN'

    GET -> Lista todos los modulos

        http://localhost/APIDocente/public/index.php/modulos  -> todos los modulos
        http://localhost/APIDocente/public/index.php/modulos/{id}  -> por ID


    POST -> Crea un nuevo modulo

    http://localhost/APIDocente/public/index.php/modulos
        En el body tipo raw
            {
            "nombre":"nombre"
            }


    PUT -> Actualiza un modulo

    http://localhost/APIDocente/public/index.php/modulos
        En el body tipo raw
            {
            "id": id,
            "nombre": "nombre"
            }

    DELETE -> Elimina un modulo

    http://localhost/APIDocente/public/index.php/modulos
    En el body tipo raw
            {
            "id": id
            }


## Roles

EN HEADERS DEBES AGREGAR UN NUEVO LLAMADO 'AUTHORIZATION' Y ESTE DEBE SER 'BEARER TUTOKEN'

    GET -> Lista todos los roles

        http://localhost/APIDocente/public/index.php/roles  -> todos los roles
        http://localhost/APIDocente/public/index.php/roles/{id}  -> por ID


    POST -> Crea un nuevo rol

    http://localhost/APIDocente/public/index.php/roles
        En el body tipo raw
            {
            "nombre":"nombre"
            }


    PUT -> Actualiza un rol

    http://localhost/APIDocente/public/index.php/roles
        En el body tipo raw
            {
            "id": id,
            "nombre": "nombre"
            }

    DELETE -> Elimina un rol

    http://localhost/APIDocente/public/index.php/roles
    En el body tipo raw
            {
            "id": id
            }


## Tipo de Acceso

EN HEADERS DEBES AGREGAR UN NUEVO LLAMADO 'AUTHORIZATION' Y ESTE DEBE SER 'BEARER TUTOKEN'

    GET -> Lista todos los tipos de acceso

        http://localhost/APIDocente/public/index.php/tipoacceso  -> todos los tipos de acceso
        http://localhost/APIDocente/public/index.php/tipoacceso/{id}  -> por ID


    POST -> Crea un nuevo tipo de acceso

    http://localhost/APIDocente/public/index.php/tipoacceso
        En el body tipo raw
            {
            "nombre":"nombre"
            }


    PUT -> Actualiza un tipo de acceso

    http://localhost/APIDocente/public/index.php/tipoacceso
        En el body tipo raw
            {
            "id": id,
            "nombre": "nombre"
            }

    DELETE -> Elimina un tipo de acceso

    http://localhost/APIDocente/public/index.php/tipoacceso
    En el body tipo raw
            {
            "id": id
            }

## Lista de links de Usuario Acceso

EN HEADERS DEBES AGREGAR UN NUEVO LLAMADO 'AUTHORIZATION' Y ESTE DEBE SER 'BEARER TUTOKEN'

    GET -> Lista todos los links de usuario acceso

        http://localhost/APIDocente/public/index.php/usuarioacceso  -> todos los links de usuario de acceso
        http://localhost/APIDocente/public/index.php/usuarioacceso/{id}  -> por ID


    POST -> Crea un nuevo tipo de acceso

    http://localhost/APIDocente/public/index.php/usuarioacceso
        En el body tipo raw
            {
            "id_usuario": "3",  // datos que si deben existir
            "id_rol": "1",
            "id_modulo": "4",
            "id_tipo_acceso": "3"
}


    PUT -> Actualiza un tipo de acceso

    http://localhost/APIDocente/public/index.php/usuarioacceso
        En el body tipo raw
            {
            "id_usuario": "3",
            "id_rol": "1",
            "id_modulo": "4",
            "id_tipo_acceso": "3"
            }

    DELETE -> Elimina un tipo de acceso

    http://localhost/APIDocente/public/index.php/usuarioacceso
    En el body tipo raw
            {
            "id": id
            }

## Métodos soportados

GET, POST, PUT, DELETE


## Autor

Desarrollado por: Arnold Avila
Fecha: Septiembre 2025

<?php
//incluimos el archivo de autenticación donde están las funciones JWT
require_once __DIR__ . '/../auth/auth.php';


// función para proteger rutas, se llama antes de procesar cualquier endpoint que requiera autenticacion
function protegerRuta() {
        // Llama a auth_require_user(), que hace:
    // 1. Extrae el token Bearer del header
    // 2. Decodifica el JWT
    // 3. Busca el usuario en la BD según el ID del token
    // 4. Retorna el usuario autenticado o detiene la ejecución si hay error
    $usuario = auth_require_user(); // Esto ya hace todo: token + usuario
    return $usuario;  //retorna la info del usuario logueado
}

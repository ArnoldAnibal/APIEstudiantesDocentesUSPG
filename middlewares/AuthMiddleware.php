<?php
require_once __DIR__ . '/../auth/auth.php';

function protegerRuta() {
    // Devuelve tanto la conexión como el usuario autenticado
    return auth_require_user();
}

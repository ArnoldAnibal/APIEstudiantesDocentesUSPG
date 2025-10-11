<?php

require_once __DIR__ . '/../connection/db.php';                // conexión
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';   // middleware

// incluimos los controladores
require_once __DIR__ . '/../controllers/DocenteController.php';
require_once __DIR__ . '/../controllers/EstudianteController.php';
require_once __DIR__ . '/../controllers/ModuloController.php';
require_once __DIR__ . '/../controllers/RolController.php';
require_once __DIR__ . '/../controllers/TipoAccesoController.php';
require_once __DIR__ . '/../controllers/UsuarioAccesoController.php';

// obtenemos la URI de la petición y el método
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$inputData = json_decode(file_get_contents("php://input"), true) ?? [];

// ===========================================================
// RUTAS PÚBLICAS (sin token): login y register
// ===========================================================

if (preg_match('#^/APIDocente/public/index.php/auth/login$#', $uri)) {
    require_once __DIR__ . '/../controllers/AuthController.php';
    $auth = new AuthController();

    if ($method === 'POST') {
        $auth->login($inputData);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Método no soportado']);
    }
    exit;
}

if (preg_match('#^/APIDocente/public/index.php/auth/register$#', $uri)) {
    require_once __DIR__ . '/../controllers/AuthController.php';
    $auth = new AuthController();

    if ($method === 'POST') {
        $auth->register($inputData);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Método no soportado']);
    }
    exit;
}

// ===========================================================
// RUTAS PROTEGIDAS (requieren token JWT)
// ===========================================================

$conn = Database::getConnection();
$currentUser = protegerRuta($conn); // valida el token JWT y retorna el usuario

// Inicializamos controladores
$docenteController = new DocenteController($currentUser);
$estudianteController = new EstudianteController($currentUser);

// ===========================================================
// DEFINICIÓN DE RUTAS
// ===========================================================

// DOCENTES
if (preg_match('#^/APIDocente/public/index.php/docentes/?([0-9]*)$#', $uri, $matches)) {
    $id = $matches[1] !== '' ? (int)$matches[1] : null;
    $docenteController->manejar($method, $id, $inputData);

// ESTUDIANTES
} elseif (preg_match('#^/APIDocente/public/index.php/estudiantes/?([0-9]*)$#', $uri, $matches)) {
    $id = $matches[1] !== '' ? (int)$matches[1] : null;
    $estudianteController->manejar($method, $id, $inputData);

// USUARIOS - CLONAR
} elseif (preg_match('#^/APIDocente/public/index.php/usuarios/clonar$#', $uri)) {
    require_once __DIR__ . '/../controllers/UsuarioController.php';
    $usuarioController = new UsuarioController();
    $usuarioController->manejar('CLONAR', null, $inputData);

// USUARIOS (REST)
} elseif (preg_match('#^/APIDocente/public/index.php/usuarios/?([0-9]*)$#', $uri, $matches)) {
    require_once __DIR__ . '/../controllers/UsuarioController.php';
    $id = $matches[1] !== '' ? (int)$matches[1] : null;
    $usuarioController = new UsuarioController();
    $usuarioController->manejar($method, $id, $inputData);

// MODULOS (REST)
} elseif (preg_match('#^/APIDocente/public/index.php/modulos/?([0-9]*)$#', $uri, $m)) {
    $id = $m[1] !== '' ? (int)$m[1] : null;
    $ctrl = new ModuloController($currentUser);
    $ctrl->manejar($method, $id, $inputData);

// ROLES (REST)
} elseif (preg_match('#^/APIDocente/public/index.php/roles/?([0-9]*)$#', $uri, $m)) {
    $id = $m[1] !== '' ? (int)$m[1] : null;
    $ctrl = new RolController($currentUser);
    $ctrl->manejar($method, $id, $inputData);

// TIPO ACCESO (REST)
} elseif (preg_match('#^/APIDocente/public/index.php/tipoacceso/?([0-9]*)$#', $uri, $m)) {
    $id = $m[1] !== '' ? (int)$m[1] : null;
    $ctrl = new TipoAccesoController($currentUser);
    $ctrl->manejar($method, $id, $inputData);

// USUARIO ACCESO (REST)
} elseif (preg_match('#^/APIDocente/public/index.php/usuarioacceso/?([0-9]*)$#', $uri, $m)) {
    $id = $m[1] !== '' ? (int)$m[1] : null;
    $ctrl = new UsuarioAccesoController($currentUser);
    $ctrl->manejar($method, $id, $inputData);

// SI NO COINCIDE NINGUNA
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Ruta no encontrada']);
}

?>

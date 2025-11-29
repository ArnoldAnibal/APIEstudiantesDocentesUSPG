<?php

require_once __DIR__ . '/../connection/DatabaseFactory.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';

// Controladores
require_once __DIR__ . '/../controllers/DocenteController.php';
require_once __DIR__ . '/../controllers/EstudianteController.php';
require_once __DIR__ . '/../controllers/ModuloController.php';
require_once __DIR__ . '/../controllers/RolController.php';
require_once __DIR__ . '/../controllers/TipoAccesoController.php';
require_once __DIR__ . '/../controllers/UsuarioAccesoController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$inputData = json_decode(file_get_contents("php://input"), true) ?? [];

/* ===========================================================
   RUTAS PÚBLICAS (login y register)
   =========================================================== */

if (preg_match('#/auth/login$#', $uri)) {
    require_once __DIR__ . '/../controllers/AuthController.php';
    $auth = new AuthController();

    if ($method === 'POST') {
        $auth->login($inputData);
        exit;
    }
    http_response_code(405);
    exit(json_encode(['error' => 'Método no soportado']));
}

if (preg_match('#/auth/register$#', $uri)) {
    require_once __DIR__ . '/../controllers/AuthController.php';
    $auth = new AuthController();

    if ($method === 'POST') {
        $auth->register($inputData);
        exit;
    }
    http_response_code(405);
    exit(json_encode(['error' => 'Método no soportado']));
}

/* ===========================================================
   RUTAS PROTEGIDAS (requieren token)
   =========================================================== */

$conn = DatabaseFactory::getConnection('GT'); 
$currentUser = protegerRuta($conn);
$pais = $currentUser['pais'] ?? 'GT';

/* ===========================================================
   DOCENTES
   =========================================================== */
if (preg_match('#/docentes/?([0-9]*)$#', $uri, $matches)) {
    $id = $matches[1] !== "" ? (int)$matches[1] : null;

    $controller = new DocenteController($currentUser);
    $controller->manejar($method, $id, $inputData);
    exit;
}

/* ===========================================================
   ESTUDIANTES
   =========================================================== */
if (preg_match('#/estudiantes/?([0-9]*)$#', $uri, $matches)) {
    $id = $matches[1] !== "" ? (int)$matches[1] : null;

    $controller = new EstudianteController($currentUser);
    $controller->manejar($method, $id, $inputData);
    exit;
}

/* ===========================================================
   USUARIOS
   =========================================================== */
if (preg_match('#/usuarios/clonar$#', $uri)) {
    require_once __DIR__ . '/../controllers/UsuarioController.php';
    $controller = new UsuarioController();
    $result = $controller->clonar(null, $inputData);
    echo json_encode($result);
    exit;
}


if (preg_match('#/usuarios/?([0-9]*)$#', $uri, $matches)) {
    require_once __DIR__ . '/../controllers/UsuarioController.php';
    $id = $matches[1] !== "" ? (int)$matches[1] : null;
    $controller = new UsuarioController();
    $controller->manejar($method, $id, $inputData);
    exit;
}

/* ===========================================================
   MODULOS
   =========================================================== */
if (preg_match('#/modulos/?([0-9]*)$#', $uri, $m)) {
    $id = $m[1] !== "" ? (int)$m[1] : null;
    $controller = new ModuloController($currentUser);
    $controller->manejar($method, $id, $inputData);
    exit;
}

/* ===========================================================
   ROLES
   =========================================================== */
if (preg_match('#/roles/?([0-9]*)$#', $uri, $m)) {
    $id = $m[1] !== "" ? (int)$m[1] : null;
    $controller = new RolController($currentUser);
    $controller->manejar($method, $id, $inputData);
    exit;
}

/* ===========================================================
   TIPO ACCESO
   =========================================================== */
if (preg_match('#/tipoacceso/?([0-9]*)$#', $uri, $m)) {
    $id = $m[1] !== "" ? (int)$m[1] : null;
    $controller = new TipoAccesoController($currentUser);
    $controller->manejar($method, $id, $inputData);
    exit;
}

/* ===========================================================
   USUARIO ACCESO
   =========================================================== */
if (preg_match('#/usuarioacceso/?([0-9]*)$#', $uri, $m)) {
    $id = $m[1] !== "" ? (int)$m[1] : null;
    $controller = new UsuarioAccesoController($currentUser);
    $controller->manejar($method, $id, $inputData);
    exit;
}

/* ===========================================================
   RUTA NO ENCONTRADA
   =========================================================== */
http_response_code(404);
echo json_encode(["error" => "Ruta no encontrada"]);
exit;

?>

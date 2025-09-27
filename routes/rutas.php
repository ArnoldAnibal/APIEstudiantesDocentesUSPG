<?php

require_once __DIR__ . '/../connection/db.php';                // conexión
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';   // middleware

// incluimos los controladores
require_once __DIR__ . '/../controllers/DocenteController.php';
require_once __DIR__ . '/../controllers/EstudianteController.php';


// obtenemos la URI de la petición ylo dividimos por cada /
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// obtenemos el metodo que quieren usar
$method = $_SERVER['REQUEST_METHOD'];
// se revisa el contenido json que nos enviaron y se convierte en un arreglo
$inputData = json_decode(file_get_contents("php://input"), true) ?? [];


// protegemos rutas que requieren autenticación
// auth/login y auth/register quedan públicas
// ruta de login
if (preg_match('#^/APIDocente/public/index.php/auth/login$#', $uri)) {
    require_once __DIR__ . '/../controllers/AuthController.php';
    $auth = new AuthController();
    if ($method === 'POST') { $auth->login($inputData); } else { http_response_code(405); echo json_encode(['error'=>'Metodo no soportado']); }
    exit;
}
// ruta de registrar
if (preg_match('#^/APIDocente/public/index.php/auth/register$#', $uri)) {
    require_once __DIR__ . '/../controllers/AuthController.php';
    $auth = new AuthController();
    if ($method === 'POST') { $auth->register($inputData); } else { http_response_code(405); echo json_encode(['error'=>'Metodo no soportado']); }
    exit;
}


// Para todas las demás rutas vamos a verificar token
$conn = Database::getConnection();
$currentUser = protegerRuta($conn);  // esto valida el token JWT y retorna la info del usuario

// Inicializamos controladores para poder delegar las peticiones
$docenteController = new DocenteController($currentUser);
$estudianteController = new EstudianteController($currentUser);

// Definición de rutas protegidas
// verificamos si la url es de docentes o docentes/id. Si hay un numero despues de docentes/ lo guarda en $id para que se llame al metodo manejar del controlador pasando todos los datos
if (preg_match('#^/APIDocente/public/index.php/docentes/?([0-9]*)$#', $uri, $matches)) {
    $id = $matches[1] !== '' ? (int)$matches[1] : null;
    $docenteController->manejar($method, $id, $inputData);

} elseif (preg_match('#^/APIDocente/public/index.php/estudiantes/?([0-9]*)$#', $uri, $matches)) {
    $id = $matches[1] !== '' ? (int)$matches[1] : null;
    $estudianteController->manejar($method, $id, $inputData);

} elseif (preg_match('#^/APIDocente/public/index.php/usuarios/?([0-9]*)$#', $uri, $matches)) {
    require_once __DIR__ . '/../controllers/UsuarioController.php';
    $id = $matches[1] !== '' ? (int)$matches[1] : null;
    $usuarioController = new UsuarioController($currentUser);
    $usuarioController->manejar($method, $id, $inputData);

} else {
    http_response_code(404);
    echo json_encode(['error' => 'Ruta no encontrada']);
}

?>
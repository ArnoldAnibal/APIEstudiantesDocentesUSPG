<?php
// Simple autenticador JWT que necesita de firebase/php-jwt que se descargo con Composer (vendor/autoload.php).
require_once __DIR__ . '/../connection/db.php';  // conexión a la bd
require_once __DIR__ . '/../vendor/autoload.php';  // se incluye el autoload de composer para cargar las librerias, en este caso usamos Firebase JWT
// importamos las clases de JWT que usaremos para codificar y decodificar tokens
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// clave secreta que se usa para firmar y verificar los tokends JWT
$JWT_SECRET = 'B8AHgIk26d55';

// Extraemos el token Bearer del header Authorization
function get_bearer_token() {
    $headers = null;  // inicializa la variable que contrendrá el header
    // si exite la variable de servidor HTTP_AUTHORIZATION 
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers = trim($_SERVER['HTTP_AUTHORIZATION']); // le limpiamos los espacios en blanco
    } else if (function_exists('apache_request_headers')) { // si no existe, intentamos usar apache_request_headers() para obtener todos los headers
        $req = apache_request_headers();  // obtiene todos los headers
        if (isset($req['Authorization'])) $headers = trim($req['Authorization']); // tomamos el Authorization token
    }
    // si no se encontro ningun header, retornamos null
    if (!$headers) return null;
    // usamos regex para extraer el token Bearer
    if (preg_match('/Bearer\s+(\S+)/', $headers, $matches)) {
        return $matches[1]; // se retorna el token capturado
    }
    return null;  // si no hay nada, devolvemos null
}

function auth_require_user() {
    global $JWT_SECRET, $mysqli;  // accede a la clave secreta y conexion a la db
    // obtenemos el token del header
    $token = get_bearer_token();
    if (!$token) { // si no hay token, devolvemos error 401
        http_response_code(401);
        echo json_encode(['error' => 'No hay un token de autorización válido.']);
        exit;
    }
    try {
        $decoded = JWT::decode($token, new Key($JWT_SECRET, 'HS256')); // decodifica el token usando la clave secreta y algoritmo HS256
    } catch (Exception $e) {
        http_response_code(401);  // si hubo error como token invalido, expirado, o alterdao, damos error 401
        echo json_encode(['error' => 'Token Invalido', 'details' => $e->getMessage()]);
        exit;
    }
    // extraer el ID de usuario del payload del token
    $userId = $decoded->sub ?? null;
    if (!$userId) {
        http_response_code(401);
        echo json_encode(['error' => 'Payload de token Invalido']);
        exit;
    }
    // conectamos a la base de datos
    $conn = Database::getConnection();
    //preparamos la consulta SQL para obtener los datos del usuario
    $stmt = $conn->prepare('SELECT id, username, nombres, apellidos FROM usuarios WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $userId); // asociamos el parámetro
    $stmt->execute(); // ejecutamos la consulta
    $res = $stmt->get_result(); // obtenemos el resultado
    $user = $res->fetch_assoc(); //convertimos a array asociativo
    if (!$user) { http_response_code(401); echo json_encode(['error'=>'Usuario no encontrado']); exit; } // si no se eoncro usuario, damos error 401
    return $user; // si si se encontró, retornamos los datos del usuario
}

<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/../connection/DatabaseFactory.php';
require_once __DIR__ . '/../connection/db_mysql.php';

$JWT_SECRET = "B8AHgIk26d55";

/**
 * Normaliza el token desde cualquier servidor (Apache/XAMPP)
 */
function get_bearer_token()
{
    $headers = [
        'HTTP_AUTHORIZATION',
        'Authorization',
        'REDIRECT_HTTP_AUTHORIZATION'
    ];

    foreach ($headers as $h) {
        if (isset($_SERVER[$h])) {
            return trim(str_replace("Bearer", "", $_SERVER[$h]));
        }
    }

    return null;
}

/**
 * Ejecuta una consulta SELECT en MySQL con mysqli
 */
function mysqli_select_one($conn, $query, $param)
{
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $param);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc();
}

/**
 * Ejecuta una consulta SELECT para PDO
 */
function pdo_select_one($conn, $query, $param)
{
    $stmt = $conn->prepare($query);
    $stmt->execute([$param]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * MIDDLEWARE DE AUTENTICACIÓN
 */
function auth_require_user()
{
    global $JWT_SECRET;

    // Obtener token del header
    $token = get_bearer_token();
    if (!$token) {
        http_response_code(401);
        echo json_encode(["error" => "Token requerido"]);
        exit;
    }

    // Decodificar token
    try {
        $decoded = JWT::decode($token, new Key($JWT_SECRET, "HS256"));
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["error" => "Token inválido", "details" => $e->getMessage()]);
        exit;
    }

    // Datos del token
    $userId = $decoded->id ?? null;
    $pais   = $decoded->pais ?? null;

    if (!$userId || !$pais) {
        http_response_code(401);
        echo json_encode(["error" => "Payload del token inválido"]);
        exit;
    }

    // Conexión del país según el token (mysql, pgsql o sqlsrv)
    try {
        $conn = DatabaseFactory::getConnection($pais);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
        exit;
    }

    // Query según driver
    if ($conn instanceof mysqli) {
        $sql = "SELECT id, nombres, apellidos, username, correo, pais 
                FROM usuarios 
                WHERE id = ? LIMIT 1";
        $user = mysqli_select_one($conn, $sql, $userId);
    } else { // PDO
        $driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'sqlsrv') {
            $sql = "SELECT TOP 1 id, nombres, apellidos, username, correo, pais 
                    FROM usuarios 
                    WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userId]);
        } else { // pgsql u otros
            $sql = "SELECT id, nombres, apellidos, username, correo, pais 
                    FROM usuarios 
                    WHERE id = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userId]);
        }
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (!$user) {
        http_response_code(401);
        echo json_encode(["error" => "Usuario no existe en {$pais}"]);
        exit;
    }

    return [
    'id'        => $user['id'],
    'nombres'   => $user['nombres'] ?? '',
    'apellidos' => $user['apellidos'] ?? '',
    'username'  => $user['username'] ?? '',
    'pais'      => $user['pais'] ?? $pais
];

}

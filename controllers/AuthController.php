<?php
// Incluimos la conexión a la base de datos, el autoload de composer para usar firebase JWT, y también importamos la clase JWT
require_once __DIR__ . '/../connection/db.php';
require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;

class AuthController {
    private $conn; // propiedad con la conexion a la bd
    private $jwt_secret; // propiedad para almacenar la clase secreta del JWT
    public function __construct() {
        $this->conn = Database::getConnection(); // obtenemos la conexion a la bd
        $this->jwt_secret = 'B8AHgIk26d55';  // definimos la clave secreta para firmar los JWT
    }

    public function register($data) {  // crear un nuevo usuario
        // recibimos y retornamos los avlores enviados para proteger de inyección SQL
        $username = $this->conn->real_escape_string($data['username'] ?? '');
        $password = $data['password'] ?? '';
        $nombres = $this->conn->real_escape_string($data['nombres'] ?? null);
        $apellidos = $this->conn->real_escape_string($data['apellidos'] ?? null);

        // validamos de forma basica, que el username y password son obligatorios
        if (!$username || !$password) { http_response_code(400); echo json_encode(['error'=>'Username y contraseña requeridos']); exit; }
        // hacemos el hash de la contraseañ usando bcrypt
        $hash = password_hash($password, PASSWORD_DEFAULT);
        // preparamos la sentencia SQL para insertar el usuario
        $stmt = $this->conn->prepare('INSERT INTO usuarios (username, password_hash, nombres, apellidos) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $username, $hash, $nombres, $apellidos); // asignamos los parametros
        // ejecutamos y verificamos si falló
        if (!$stmt->execute()) { http_response_code(500); echo json_encode(['error'=>'No se pudo crear el usuario', 'details'=>$stmt->error]); exit; }
        // si se creo sin problema, devolvemos codigo 201 con los datos del usuario
        http_response_code(201); echo json_encode(['id'=>$stmt->insert_id,'username'=>$username,'nombres'=>$nombres,'apellidos'=>$apellidos]);
    }

    public function login($data) {  // autenticamos a un usuario
        // obtenemos los datos de login
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        // validamos que son obligatorios ambos campos
        if (!$username || !$password) { http_response_code(400); echo json_encode(['error'=>'Username y contraseña requeridos']); exit; }
        // buscamos el usuario en la bd
        $stmt = $this->conn->prepare('SELECT id, username, password_hash, nombres, apellidos FROM usuarios WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);  // asignamos el parametro
        $stmt->execute(); 
        $res = $stmt->get_result(); // obtenemos el resultado
        $user = $res->fetch_assoc(); // convertimos a array asociativo
        // verificamos que exista el usuario y que la contraseña sea correcta
        if (!$user || !password_verify($password, $user['password_hash'])) { http_response_code(401); echo json_encode(['error'=>'Credenciales Invalidas']); exit; }
        // creamos el payload del JWT
        $now = time(); // timestampt de ahora mismo
        $payload = ['iat'=>$now,'exp'=>$now + 3600*24,'sub'=>$user['id'],'username'=>$user['username']]; // issued at, momento en el que se creo. expiration que son 24 horas, subject que es el id del usuario y la opcional es username para tenerlo en el token
        // se genera el token firmado con HS256
        $jwt = JWT::encode($payload, $this->jwt_secret, 'HS256');
        // retornamos con el token, y los datos del usuario
        echo json_encode(['token'=>$jwt,'user'=>['id'=>$user['id'],'username'=>$user['username'],'nombres'=>$user['nombres'],'apellidos'=>$user['apellidos']]]);
    }
}

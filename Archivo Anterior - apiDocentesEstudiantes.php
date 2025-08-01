<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "uspg");
if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conexion->connect_error]));
}

// Obtener el endpoint desde la URL
$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$recurso = $uri[count($uri) - 1];
$method = $_SERVER['REQUEST_METHOD'];

// Función auxiliar
function get_json_input() {
    return json_decode(file_get_contents("php://input"), true);
}


// RUTEO
switch ($recurso) {
    case 'docentes':
        if ($method === 'GET') {
            $res = $conexion->query("SELECT * FROM docentes");
            echo json_encode($res->fetch_all(MYSQLI_ASSOC));
        } elseif ($method === 'POST') {
            $datos = get_json_input();
            $stmt = $conexion->prepare("INSERT INTO docentes (nombres, apellidos) VALUES (?, ?)");
            $stmt->bind_param("ss",$datos['nombres'], $datos['apellidos']);
            $stmt->execute();
            echo json_encode(["exitoso" => true, "id" => $stmt->insert_id]);
            // i = integer
            // s = string   
        }
        elseif ($method === 'PUT') {
             $datos = get_json_input();
                $stmt = $conexion->prepare("UPDATE docentes SET nombres = ?, apellidos = ? WHERE id = ?");
                $stmt->bind_param("ssi", $datos['nombres'], $datos['apellidos'], $datos['id']);
                $stmt->execute();
                echo json_encode(["exitoso" => $stmt->affected_rows > 0]);            
        }
        elseif ($method === 'DELETE') {
            $datos = get_json_input();
                $stmt = $conexion->prepare("DELETE FROM docentes WHERE id = ?");
                $stmt->bind_param("i", $datos['id']);
                $stmt->execute();
                echo json_encode(["success" => true, "id" => $stmt->insert_id]);           
            // i = integer
            // s = string   
        }
        break;

    case 'estudiantes':
        if ($method === 'GET') {
            $res = $conexion->query("SELECT * FROM estudiantes");
            echo json_encode($res->fetch_all(MYSQLI_ASSOC));
        } elseif ($method === 'POST') {
            $datos = get_json_input();
            $stmt = $conexion->prepare("INSERT INTO estudiantes (nombres, apellidos) VALUES (?, ?)");
            $stmt->bind_param("ss",$datos['nombres'], $datos['apellidos']);
            $stmt->execute();
            echo json_encode(["success" => true, "id" => $stmt->insert_id]);
            // i = integer
            // s = string   
        }
        elseif ($method === 'PUT') {
             $datos = get_json_input();
                $stmt = $conexion->prepare("UPDATE estudiantes SET nombres = ?, apellidos = ? WHERE id = ?");
                $stmt->bind_param("ssi", $datos['nombres'], $datos['apellidos'], $datos['id']);
                $stmt->execute();
                echo json_encode(["exitoso" => $stmt->affected_rows > 0]);            
        }
        elseif ($method === 'DELETE') {
            $datos = get_json_input();
                $stmt = $conexion->prepare("DELETE FROM estudiantes WHERE id = ?");
                $stmt->bind_param("i", $datos['id']);
                $stmt->execute();
                echo json_encode(["success" => true, "id" => $stmt->insert_id]);           
            // i = integer
            // s = string   
        }
        break;

        default:
        http_response_code(404);
        echo json_encode(["error" => "Recurso no encontrado"]);
        break;
    }
?>

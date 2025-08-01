<?php
// cargamos la informacion de ambos ficheros
require_once __DIR__ . '/../connection/db.php';
require_once __DIR__ . '/../controllers/estudiantesController.php';
require_once __DIR__ . '/../controllers/docentesController.php';

// se hace instancia a la clase DB y se conecta 
$db = (new DB())->connectionDB();

// se extraen las partes de la Url, se separan por el / 
$uri = explode("/", trim($_SERVER['REQUEST_URI'], "/"));

// obtenemos la uri que pueda venir por el URL para saber que controlador usar
$resource = $uri[2] ?? null;

switch ($resource) {
    case 'estudiantes':
        // se hace instancia al controladdr de estudiantes y docentes con la conexión a la bd
        $controller = new estudiantesController($db);
        break;
    case 'docentes':
        // se hace instancia al controladdr de estudiantes y docentes con la conexión a la bd
        $controller = new docentesController($db);
        break;
    default:
        http_response_code(404);
        echo json_encode(["error" => "Recurso no encontrado"]);
        exit;
        
}



// se deteca el metodo HTTP de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

// capturamos el cuerpo del JSON y se convierte en array
$input = json_decode(file_get_contents("php://input"), true);
// obtenemos la id  que pueda venir por el body para usarlo en el delete
$id = $input['id'] ?? null;


// segun el método HTTP se llama al metodo correspondiente del controlador, los datos son enviados en json
switch($method){
    case 'GET':
        $controller->index();
        break;
    case 'POST':
        $controller->guardar($input);
        break;
    case 'PUT':
        $controller->actualizar($id,$input);
        break;
    case 'DELETE':
        $controller->eliminar($id);
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}

?>

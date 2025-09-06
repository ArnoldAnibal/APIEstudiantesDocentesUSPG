<?php

// incluimos los controladores
require_once __DIR__ . '/../controllers/DocenteController.php';
require_once __DIR__ . '/../controllers/EstudianteController.php';


// obtenemos la URI de la petición ylo dividimos por cada /
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// obtenemos el metodo que quieren usar
$method = $_SERVER['REQUEST_METHOD'];
// se revisa el contenido json que nos enviaron y se convierte en un arreglo
$inputData = json_decode(file_get_contents("php://input"), true) ?? [];

// Inicializamos controladores para poder delegar las peticiones
$docenteController = new DocenteController();
$estudianteController = new EstudianteController();

// Definición de rutas
// verificamos si la url es de docentes o docentes/id. Si hay un numero despues de docentes/ lo guarda en $id para que se llame al metodo manejar del controlador pasando todos los datos
if (preg_match('#^/APIDocente/public/index.php/docentes/?([0-9]*)$#', $uri, $matches)) {
    $id = $matches[1] !== '' ? (int)$matches[1] : null;
    $docenteController->manejar($method, $id, $inputData);

} elseif (preg_match('#^/APIDocente/public/index.php/estudiantes/?([0-9]*)$#', $uri, $matches)) {
    $id = $matches[1] !== '' ? (int)$matches[1] : null;
    $estudianteController->manejar($method, $id, $inputData);

} else {
    // Ruta no encontrada
    http_response_code(404);
    echo json_encode(['error' => 'Ruta no encontrada']);
}

?>
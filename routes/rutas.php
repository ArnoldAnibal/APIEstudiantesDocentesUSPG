<?php

// incluimos los controladores
require_once __DIR__ . '/../controllers/DocenteController.php';
require_once __DIR__ . '/../controllers/EstudianteController.php';


// obtenemos la URI de la petición ylo dividimos por cada /
$uri = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
// obtenemos el metodo que quieren usar
$method = $_SERVER['REQUEST_METHOD'];
// se revisa el contenido json que nos enviaron y se convierte en un arreglo
$input = json_decode(file_get_contents('php://input'), true);

// Buscamos el recurso principal (docentes o estudiantes)
if (in_array("docentes", $uri)) {
    $controller = new DocenteController();  // hacemos una instancia del controlador
    // Si hay un número después de 'docentes', lo tomamos como ID
    $pos = array_search("docentes", $uri);  // buscamos el uri
    $id = $uri[$pos + 1] ?? null;  // sacamos el id si lo mandaron
    $controller->manejar($method, $id, $input);  // usamos el metodo manejar del controlador
}
elseif (in_array("estudiantes", $uri)) {
    $controller = new EstudianteController(); // hacemos una instancia del controlador
    // Si hay un número después de 'estudiantes', lo tomamos como ID
    $pos = array_search("estudiantes", $uri); // buscamos el uri
    $id = $uri[$pos + 1] ?? null; // sacamos el id si lo mandaron
    $controller->manejar($method, $id, $input); // usamos el metodo manejar del controlador
}
else {
    echo json_encode(["error" => "Ruta no encontrada"]);  // mensaje de error
}
?>
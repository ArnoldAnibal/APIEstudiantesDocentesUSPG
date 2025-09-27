<?php
// Punto de entrada al sistema

header("Access-Control-Allow-Origin: *");  // permitimos que cualquier origen pueda consumir la api
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");  // metodos permitidos, el options lo usan los navegadores para hacer peticiones con json
header("Access-Control-Allow-Headers: Content-Type");  // indicamos que se permiten los encabezados personalizados
date_default_timezone_set('America/Guatemala');  // establecemos la zona horaria de GT

// maneja la peticion preflight ucando el navegador manda una peticion otions antes de un post o put
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Devolver JSON siempre
header("Content-Type: application/json; charset=UTF-8");

// Incluir las rutas para definir que controlador maneja cada endpoint
require_once __DIR__ . '/../routes/rutas.php';
?>
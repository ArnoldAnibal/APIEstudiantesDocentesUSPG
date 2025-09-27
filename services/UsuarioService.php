<?php
// incluimos el repositorio que maneja el acceso a la tabla de usuarios
require_once __DIR__ . '/../repositories/UsuarioRepository.php';

class UsuarioService {
    private $repo;  // propiedad que almacena la instancia del repositorio
    public function __construct() { $this->repo = new UsuarioRepository(); } // constructor que inicializa el repositorio


    public function register($data) {
        // obtenemos los datos del arreglo recibido
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;
        // validamos que si recibamos username y password
        if (!$username || !$password) { throw new Exception('Nombre de usuario y contraseña son requeridos.'); }
        // verificamos si ya existe el username
        $existing = $this->repo->findByUsername($username);
        if ($existing) throw new Exception('El username ya existe');  // error si ya existe
        $hash = password_hash($password, PASSWORD_DEFAULT);  // hasheamos la contraseña
        return $this->repo->create($username, $hash, $data['nombres'] ?? null, $data['apellidos'] ?? null);
    }
}

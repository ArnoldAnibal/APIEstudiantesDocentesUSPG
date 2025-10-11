<?php
// clase Entity que representa un usuario en la base de datos, la diferencia con los DTOs es que esta clase refleja directamente la estructura de la tabla
require_once __DIR__ . '/../repositories/UsuarioAccesoRepository.php';

class Usuario {
    public $id;
    public $username;
    public $password_hash;
    public $nombres;
    public $apellidos;
    public $correo;
    public $activo;
    public $created_at;
    public $updated_at;
    public $accesos = []; // Colección de accesos relacionados

    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) $this->$key = $value;
        }
    }

 /**
     * Clona el usuario actual (Prototype pattern) con nuevos datos.
     * Permite crear un nuevo usuario basado en otro, copiando sus accesos.
     */
    public function clone(string $nuevoUsername, string $nuevoNombre, string $nuevoApellido, string $nuevoCorreo, string $nuevaContrasena): self {
    $clon = new self([
        'username' => $nuevoUsername,
        'password_hash' => password_hash($nuevaContrasena, PASSWORD_DEFAULT),
        'nombres' => $nuevoNombre,
        'apellidos' => $nuevoApellido,
        'correo' => $nuevoCorreo,
        'activo' => 1
    ]);

    // Clonar accesos sin copiar el ID
    $clon->accesos = [];
    foreach ($this->accesos as $acceso) {
        $clon->accesos[] = new UsuarioAcceso(
            null, // id nuevo
            $acceso->getUsuarioId(), // se asignará después al nuevo usuario
            $acceso->getRolId(),
            $acceso->getModuloId(),
            $acceso->getTipoAccesoId()
        );
    }

    return $clon;
}


}
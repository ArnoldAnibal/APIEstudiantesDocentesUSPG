<?php

//clase DTO para recibir datos de un usuario desde una solicitud HTTP
class UsuarioRequestDTO {
    public $username;
    public $password;  // contraseña en texto plano que se hashea antes de guardar
    public $nombres;
    public $apellidos;
    public $correo;
    public $pais;

    public function __construct($data = []) {  // constructor que recibe un array de datos como lo son el json enviado en el query, si no hay algun dato, lo coloca como null
        $this->username = $data['username'] ?? null;
        $this->password = $data['password'] ?? null;
        $this->nombres = $data['nombres'] ?? null;
        $this->apellidos = $data['apellidos'] ?? null;
        $this->correo = $data['correo'] ?? null;
        $this->pais = strtoupper($data['pais'] ?? '');
    }
}

<?php
// clase DTO para enviar datos de un usuario como respuesta de la API
class UsuarioResponseDTO {
    public $id;
    public $username;
    public $nombres;
    public $apellidos;
    public $correo;
    public $pais;

    public function __construct($data = []) {  // constructor que recibe un array de datos de la base de datos, asigna los valores del array si existen o null si no
        $this->id = $data['id'] ?? null; 
        $this->username = $data['username'] ?? null;
        $this->nombres = $data['nombres'] ?? null;
        $this->apellidos = $data['apellidos'] ?? null;
        $this->correo = $data['correo'] ?? null;
        $this->pais = $data['pais'] ?? null;
    }
    // metodo para convertir el objeto DTO a un array asociativo listo para respuesta en JSON
    public function toArray(): array {
        return ['id'=>$this->id,'username'=>$this->username,'nombres'=>$this->nombres,'apellidos'=>$this->apellidos,'correo'=>$this->correo,'pais'=>$this->pais];
    }
}

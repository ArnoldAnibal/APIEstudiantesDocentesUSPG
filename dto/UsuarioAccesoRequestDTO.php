<?php
class UsuarioAccesoRequestDTO {
    public $id_usuario;
    public $rol_id;
    public $modulo_id;
    public $tipoacceso_id;

    public function __construct($data = []) {
        $this->id_usuario = $data['id_usuario'] ?? null;
        $this->rol_id = $data['rol_id'] ?? null;
        $this->modulo_id = $data['modulo_id'] ?? null;
        $this->tipoacceso_id = $data['tipoacceso_id'] ?? null;
    }

    public function toArray(): array {
        return [
            'idUsuario' => $this->id_usuario,
            'idRol' => $this->rol_id,
            'idModulo' => $this->modulo_id,
            'idAcceso' => $this->tipoacceso_id
        ];
    }
}



?>

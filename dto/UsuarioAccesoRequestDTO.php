<?php
class UsuarioAccesoRequestDTO {
    public $id; // agregado
    public $id_usuario;
    public $rol_id;
    public $modulo_id;
    public $tipoacceso_id;

    public function __construct($data) {
        $this->id = $data['id'] ?? null;
        $this->id_usuario = $data['id_usuario'] ?? $data['idUsuario'] ?? null;
        $this->rol_id = $data['id_rol'] ?? $data['idRol'] ?? null;
        $this->modulo_id = $data['id_modulo'] ?? $data['idModulo'] ?? null;
        $this->tipoacceso_id = $data['id_tipo_acceso'] ?? $data['tipoacceso_id'] ?? $data['idAcceso'] ?? null;
    }

    public function toArray() {
        return [
            'idUsuario' => (int)$this->id_usuario,
            'idRol' => (int)$this->rol_id,
            'idModulo' => (int)$this->modulo_id,
            'idAcceso' => (int)$this->tipoacceso_id
        ];
    }
}


?>

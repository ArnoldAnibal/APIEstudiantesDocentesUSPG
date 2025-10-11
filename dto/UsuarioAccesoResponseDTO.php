<?php
class UsuarioAccesoResponseDTO {
    public $id;
    public $id_usuario;
    public $id_rol;
    public $id_modulo;
    public $id_tipo_acceso;

    public function __construct($entity) {
        $this->id = $entity['idUsuarioAcceso'] ?? null;
        $this->id_usuario = $entity['idUsuario'] ?? null;
        $this->id_rol = $entity['idRol'] ?? null;
        $this->id_modulo = $entity['idModulo'] ?? null;
        $this->id_tipo_acceso = $entity['idAcceso'] ?? null;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'id_usuario' => $this->id_usuario,
            'id_rol' => $this->id_rol,
            'id_modulo' => $this->id_modulo,
            'id_tipo_acceso' => $this->id_tipo_acceso
        ];
    }
}


?>

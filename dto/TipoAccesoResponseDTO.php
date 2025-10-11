<?php
class TipoAccesoResponseDTO {
    public $id;
    public $nombre;

    public function __construct($entity) {
        $this->id = $entity['id'];
        $this->nombre = $entity['nombre'];
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre
        ];
    }
}
?>

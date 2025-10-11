<?php
class TipoAccesoResponseDTO {
    public $id;
    public $nombre;
    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->nombre = $data['nombre'] ?? null;
    }
    public function toArray(): array { return ['id'=>$this->id,'nombre'=>$this->nombre]; }
}

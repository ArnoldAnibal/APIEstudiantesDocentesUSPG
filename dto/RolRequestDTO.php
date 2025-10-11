<?php
class RolRequestDTO {
    public $id;
    public $nombre;

    public function __construct($data) {
        $this->id = $data['id'] ?? null;
        $this->nombre = $data['nombre'] ?? '';
    }

    public function getId(): ?int {
        return $this->id;
    }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre
        ];
    }

    public function getNombre(): string {
        return $this->nombre;
    }
}
?>

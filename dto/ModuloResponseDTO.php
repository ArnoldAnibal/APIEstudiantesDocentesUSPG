<?php
class ModuloResponseDTO {
    public $id;
    public $nombre;

    public function __construct(?int $id, string $nombre) {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre
        ];
    }
}
?>

<?php
class ModuloRequestDTO {
    public ?int $id;
    public string $nombre;

    public function __construct(array $data) {
        $this->id = $data['id'] ?? null;
        $this->nombre = $data['nombre'] ?? '';
    }
}
?>

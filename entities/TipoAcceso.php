<?php
class TipoAcceso {
    private ?int $id;
    private string $nombre;

    public function __construct(
        int $id = null,
        string $nombre = ''
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }

    public function getNombre(): string { return $this->nombre; }
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }
}
?>

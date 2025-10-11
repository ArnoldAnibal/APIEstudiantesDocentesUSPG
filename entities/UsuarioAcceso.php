<?php
class UsuarioAcceso {
    private ?int $id;
    private int $usuario_id;
    private int $rol_id;
    private int $modulo_id;
    private int $tipoacceso_id;

    public function __construct(
        int $id = null,
        int $usuario_id = 0,
        int $rol_id = 0,
        int $modulo_id = 0,
        int $tipoacceso_id = 0
    ) {
        $this->id = $id;
        $this->usuario_id = $usuario_id;
        $this->rol_id = $rol_id;
        $this->modulo_id = $modulo_id;
        $this->tipoacceso_id = $tipoacceso_id;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }

    public function getUsuarioId(): int { return $this->usuario_id; }
    public function setUsuarioId(int $usuario_id): void { $this->usuario_id = $usuario_id; }
    
    public function getRolId(): int { return $this->rol_id; }
    public function setRolId(int $rol_id): void { $this->rol_id = $rol_id; }

    public function getModuloId(): int { return $this->modulo_id; }
    public function setModuloId(int $modulo_id): void { $this->modulo_id = $modulo_id; }

    public function getTipoAccesoId(): int { return $this->tipoacceso_id; }
    public function setTipoAccesoId(int $tipoacceso_id): void { $this->tipoacceso_id = $tipoacceso_id; }
}
?>

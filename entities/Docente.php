<?php

// clase que representa a la entidad Docente en el sistema, acá tenemos todos los campos de la tabla y metodos para acceder y modificar los datos

class Docente {
    // propiedades privadas
    private ?int $id; // ID del docente, puede ser null si aun no se ha insertado  ? puede ser null
    private ?string $nombres;
    private ?string $apellidos;
    private string $usuarioCreacion;
    private string $fechaCreacion;
    private ?string $usuarioModificacion;
    private ?string $fechaModificacion;


    // constructor de la clase que permite inicializar todas las propiedades opcionalmente
    public function __construct(
        int $id = null,
        ?string $nombres = '',
        ?string $apellidos = '',
        ?string $usuarioCreacion = '',
        ?string $fechaCreacion = '',
        ?string $usuarioModificacion = '',
        ?string $fechaModificacion = ''
    ) {
        $this->id = $id;
        $this->nombres = $nombres;
        $this->apellidos = $apellidos;
        $this->usuarioCreacion = $usuarioCreacion;
        $this->fechaCreacion = $fechaCreacion;
        $this->usuarioModificacion = $usuarioModificacion;
        $this->fechaModificacion = $fechaModificacion;
    }

    // Getters y Setters, que son metodos publicos para acceder y modificar las propiedades privadas
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getNombres() { return $this->nombres; }
    public function setNombres($nombres) { $this->nombres = $nombres; }

    public function getApellidos() { return $this->apellidos; }
    public function setApellidos($apellidos) { $this->apellidos = $apellidos; }

    public function getUsuarioCreacion() { return $this->usuarioCreacion; }
    public function setUsuarioCreacion($usuarioCreacion) { $this->usuarioCreacion = $usuarioCreacion; }

    public function getFechaCreacion() { return $this->fechaCreacion; }
    public function setFechaCreacion($fechaCreacion) { $this->fechaCreacion = $fechaCreacion; }

    public function getUsuarioModificacion() { return $this->usuarioModificacion; }
    public function setUsuarioModificacion($usuarioModificacion) { $this->usuarioModificacion = $usuarioModificacion; }

    public function getFechaModificacion() { return $this->fechaModificacion; }
    public function setFechaModificacion($fechaModificacion) { $this->fechaModificacion = $fechaModificacion; }
}
?>

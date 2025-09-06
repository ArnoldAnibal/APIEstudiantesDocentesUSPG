<?php
// Modelo de Docente con getters y setters, representa a un docente en el sistema y encapsula las propiedades y metodos relacionados con un docente en especifico

class Docente {
    // propiedad privda que almacena el ID
    private $id;
    // propiedad privda que almacena el nombre
    private $nombres;
    // propiedad privda que almacena el apellido
    private $apellidos;

    // id puede ser null si aun no está en la bd
    public function __construct($id, $nombres, $apellidos) {
        $this->id = $id;
        $this->nombres = $nombres;
        $this->apellidos = $apellidos;
    }

    // metodo publico que nos devuelve el valor del ID
    public function getId() {
        return $this->id;
    }
    public function getNombres() {
        return $this->nombres;
    }
    public function getApellidos() {
        return $this->apellidos;
    }

    // convierte el objeto Docente en un array asociativo, util para devolver respuestas json

    public function toArray(): array {
        return [
            'id' => $this->id,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos
        ];
    }

}
?>
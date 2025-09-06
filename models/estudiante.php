<?php
// Modelo de Estudiante con getters y setters, representa a un estudiante en el sistema y encapsula las propiedades y metodos relacionados con un estudiante en especifico

class Estudiante {
    // propiedad privda que almacena el ID
    private $id;
    // propiedad privda que almacena el nombre
    private $nombres;
    // propiedad privda que almacena el apellido
    private $apellidos;


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

    public function toArray(): array {
        return [
            'id' => $this->id,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos
        ];
    }

}
?>

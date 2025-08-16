<?php
// Modelo de Estudiante con getters y setters

class Estudiante {
    // propiedad privda que almacena el ID
    private $id;
    // propiedad privda que almacena el nombre
    private $nombres;
    // propiedad privda que almacena el apellido
    private $apellidos;


    // metodo publico que nos devuelve el valor del ID
    public function getId() {
        return $this->id;
    }

    // en este metodo publico asignamos un valor a la propiedad id
    public function setId($id) {
        $this->id = $id;
    }

    public function getNombres() {
        return $this->nombres;
    }
    public function setNombres($nombres) {
        $this->nombres = $nombres;
    }

    public function getApellidos() {
        return $this->apellidos;
    }
    public function setApellidos($apellidos) {
        $this->apellidos = $apellidos;
    }
}
?>

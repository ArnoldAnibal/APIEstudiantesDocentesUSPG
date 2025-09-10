<?php

//DTO Data Transfer Object, para manejar los datos recibidos en las solicitudes relacionadas con los docentes
class DocenteRequestDTO {
    public $id; // ID del docente que puede ser nulo si se creara uno nuevo
    public $nombres;  // string
    public $apellidos;  // string

    // constructor del dto que hace el array $data con los datos recibidos, generalmente desde un JSON que se decodifico
    public function __construct($data) {
        $this->id = $data['id'] ?? null;  // si hay un id, lo usamos, si no, se vuelve null
        $this->nombres = $data['nombres'] ?? '';  // si hay nombres los usamos, si no, mandamos un string vacio
        $this->apellidos = $data['apellidos'] ?? ''; // si hay nombres los apellidos, si no, mandamos un string vacio
    }
}

?>
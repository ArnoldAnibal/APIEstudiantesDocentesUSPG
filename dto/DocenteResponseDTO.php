<?php

// DTO Data Transfer Object para enviar la respuesta de los docentes que contiene unicament los cambios que queremos que el cliente vea como el ID, nombres y apellidos
class DocenteResponseDTO {
    public $id; 
    public $nombres;  
    public $apellidos;

    // constructor del DTO con el id del docente que puede ser nulo si no existe, nombres y apellidos con string
    public function __construct(
        ?int $id,
        string $nombres,
        string $apellidos
    ) {
        $this->id = $id;  // asiganmos el id que recibimos y los otros dos cambios
        $this->nombres = $nombres;
        $this->apellidos = $apellidos;
    }

    // convertimos el DTO en un array asociativo, esto lo usamos para enviar las respuestas en json
    public function toArray(): array {
        return [
            'id' => $this->id,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos
        ];
    }    
}

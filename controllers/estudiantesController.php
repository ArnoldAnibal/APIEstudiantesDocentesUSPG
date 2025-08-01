<?php
//referencia al contendio del modelo estudiante, el once nos ayuda a ver que solo se le haga referencia una vez
require_once __DIR__ . '/../models/estudiante.php';



class EstudiantesController {
    private $estudiante;

    // recibimos la conexión a la bd y usamos el modelo estudiante.php
    public function __construct($db){
        $this->estudiante = new Estudiante($db);
    }

    //obtenemos todos los datos del estudiante, get
    public function index(){
        $result = $this->estudiante->obtenerTodosRegistros();
        $estudiantes = [];

        while ($row = $result->fetch_assoc()) {
            //llenamos el array con todo lo que tenga la tabla
            $estudiantes[] = $row;
        }

        echo json_encode($estudiantes);
    }

    // creamos un nuveo estudiante, post
    public function guardar($data){
        $this->estudiante->nombres = $data['nombres'];
        $this->estudiante->apellidos = $data['apellidos'];

        if ($this->estudiante->crear()){
            echo json_encode(["message" => "Registro de estudiante creado"]);
        } else {
            echo json_encode(["error" => "Fallo al crear un registro de estudiante"]);
        }
    }

    // actualizamos un estudiante, put
    public function actualizar($id,$data){
        $this->estudiante->id = $id;
        $this->estudiante->nombres = $data['nombres'] ?? null;
        $this->estudiante->apellidos = $data['apellidos'] ?? null;

        if ($this->estudiante->actualizar()){
            echo json_encode(["message" => "Registro de estudiante actualizado"]);
        }else {
            echo json_encode(["error" => "Fallo al actualizar el registro de estudiante"]);
        }
    }

    // eliminamos un estudiante
    public function eliminar($id){
        $this->estudiante->id = $id;

        if($this->estudiante->eliminar()){
            echo json_encode(["message" => "Registro de estudiante eliminardo."]);
        } else {
            echo json_encode(["error" => "Fallo al eliminar el registro de estudiante"]);
        }
    }
}

?>
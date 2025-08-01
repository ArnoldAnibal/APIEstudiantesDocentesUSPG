<?php
//referencia al contendio del modelo docente, el once nos ayuda a ver que solo se le haga referencia una vez
require_once __DIR__ . '/../models/docente.php';



class DocentesController {
    private $docente;

    // recibimos la conexión a la bd y usamos el modelo docente.php
    public function __construct($db){
        $this->docente = new Docente($db);
    }

    //obtenemos todos los datos del docente, get
    public function index(){
        $result = $this->docente->obtenerTodosRegistros();
        $docentes = [];

        while ($row = $result->fetch_assoc()) {
            //llenamos el array con todo lo que tenga la tabla
            $docentes[] = $row;
        }

        echo json_encode($docentes);
    }

    // creamos un nuveo estudiante, post
    public function guardar($data){
        $this->docente->nombres = $data['nombres'];
        $this->docente->apellidos = $data['apellidos'];

        if ($this->docente->crear()){
            echo json_encode(["message" => "Registro de docente creado"]);
        } else {
            echo json_encode(["error" => "Fallo al crear un registro de docente"]);
        }
    }

    // actualizamos un docente, put
    public function actualizar($id,$data){
        $this->docente->id = $id;
        $this->docente->nombres = $data['nombres'] ?? null;
        $this->docente->apellidos = $data['apellidos'] ?? null;

        if ($this->docente->actualizar()){
            echo json_encode(["message" => "Registro de docente actualizado"]);
        }else {
            echo json_encode(["error" => "Fallo al actualizar el registro de docente"]);
        }
    }

    // eliminamos un docente
    public function eliminar($id){
        $this->docente->id = $id;

        if($this->docente->eliminar()){
            echo json_encode(["message" => "Registro de docente eliminardo."]);
        } else {
            echo json_encode(["error" => "Fallo al eliminar el registro de docente"]);
        }
    }
}

?>
<?php
// Servicio que maneja la lógica de negocio para usuarios.
// Interactúa con los repositorios y las entidades.

require_once __DIR__ . '/../repositories/UsuarioRepository.php';
require_once __DIR__ . '/../entities/UsuarioAcceso.php';
require_once __DIR__ . '/../repositories/UsuarioAccesoRepository.php';
require_once __DIR__ . '/../entities/Usuario.php';
require_once __DIR__ . '/../dto/UsuarioAccesoRequestDTO.php';

class UsuarioService {
    private $repo;        // Repositorio principal de usuarios
    private $accesoRepo;  // Repositorio de accesos asociados

    public function __construct() {
        $this->repo = new UsuarioRepository();
        $this->accesoRepo = new UsuarioAccesoRepository();
    }

    /**
     * Registra un nuevo usuario con su contraseña hasheada.
     */
    public function register(array $data) {
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$password) {
            throw new Exception('Nombre de usuario y contraseña son requeridos.');
        }

        // Verificamos si ya existe el username
        if ($this->repo->findByUsername($username)) {
            throw new Exception('El nombre de usuario ya existe.');
        }

        // Hasheamos la contraseña
        $hash = password_hash($password, PASSWORD_DEFAULT);

        return $this->repo->create(
            $username,
            $hash,
            $data['nombres'] ?? null,
            $data['apellidos'] ?? null,
            $data['correo'] ?? null
        );
    }

    /**
     * Clona un usuario existente y sus accesos.
     */
    public function clonar(int $idUsuarioOriginal, array $data) {
    // 1. Buscar el usuario original
    $originalData = $this->repo->findById($idUsuarioOriginal);
    if (!$originalData) {
        throw new Exception('Usuario original no encontrado.');
    }

    // 2. Crear entidad Usuario original
    $original = new Usuario($originalData);

    // 3. Traer accesos originales
    $accesosOriginales = [];
    $accesosBD = $this->accesoRepo->findByUsuarioId($idUsuarioOriginal);

    foreach ($accesosBD as $acc) {
        if (!$acc['idRol'] || !$acc['idModulo'] || !$acc['idAcceso']) {
            throw new Exception("El acceso original contiene valores nulos: " . json_encode($acc));
        }
        $accesosOriginales[] = new UsuarioAcceso(
            null,
            $acc['idUsuario'],
            $acc['idRol'],
            $acc['idModulo'],
            $acc['idAcceso']
        );
    }

    $original->accesos = $accesosOriginales;

    // 4. Validar datos del nuevo usuario
    $campos = ['username','nombres','apellidos','correo','password'];
    foreach ($campos as $c) {
        if (empty($data[$c])) {
            throw new Exception("El campo {$c} es requerido para el nuevo usuario.");
        }
    }

    // 5. Crear el clon
    $clon = $original->clone(
        $data['username'],
        $data['nombres'],
        $data['apellidos'],
        $data['correo'],
        $data['password']
    );

    // 6. Guardar nuevo usuario
    $nuevoUsuario = $this->repo->clonarUsuario($clon);
    $nuevoUsuarioId = is_array($nuevoUsuario) ? $nuevoUsuario['id'] : $nuevoUsuario->id;

    if (!$nuevoUsuarioId) {
        throw new Exception("No se pudo obtener ID del nuevo usuario.");
    }

    // 7. Clonar accesos en BD
    foreach ($clon->accesos as $acceso) {
        $dto = new UsuarioAccesoRequestDTO([
            'id_usuario' => $nuevoUsuarioId,
            'rol_id' => $acceso->getRolId(),
            'modulo_id' => $acceso->getModuloId(),
            'tipoacceso_id' => $acceso->getTipoAccesoId()
        ]);

        // Validar DTO antes de insertar
        $arr = $dto->toArray();
        if (!$arr['idUsuario'] || !$arr['idRol'] || !$arr['idModulo'] || !$arr['idAcceso']) {
            throw new Exception("Error: el DTO contiene null antes de insertar: " . json_encode($arr));
        }

        $this->accesoRepo->create($arr);
    }

    return $nuevoUsuario;
}

}
?>

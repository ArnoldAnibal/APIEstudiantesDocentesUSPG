<?php
require_once __DIR__ . '/../repositories/UsuarioRepository.php';
require_once __DIR__ . '/../repositories/UsuarioAccesoRepository.php';
require_once __DIR__ . '/../entities/Usuario.php';
require_once __DIR__ . '/../entities/UsuarioAcceso.php';
require_once __DIR__ . '/../dto/UsuarioRequestDTO.php';
require_once __DIR__ . '/../dto/UsuarioAccesoRequestDTO.php';
require_once __DIR__ . '/../mapper/UsuarioMapper.php';

class UsuarioService {
    private UsuarioRepository $repo;
    private UsuarioAccesoRepository $accesoRepo;
    private string $pais;

    public function __construct(string $pais) {
        $pais = strtoupper($pais);
        if (!in_array($pais, ['GT','SV','HN'])) {
            throw new Exception("País no válido. Debe ser GT, SV o HN.");
        }
        $this->pais = $pais;
        $this->repo = new UsuarioRepository($pais);
        $this->accesoRepo = new UsuarioAccesoRepository($pais); // DB principal
    }

    public function register(UsuarioRequestDTO $dto): array {
        $repoPais = new UsuarioRepository($this->pais);
        if ($repoPais->findByUsername($dto->username)) {
            throw new Exception("El usuario ya existe en la base de datos de {$this->pais}.");
        }

        $hash = password_hash($dto->password, PASSWORD_DEFAULT);

        $usuarioArray = [
            'username' => $dto->username,
            'password_hash' => $hash,
            'nombres' => $dto->nombres ?? null,
            'apellidos' => $dto->apellidos ?? null,
            'correo' => $dto->correo ?? null,
            'pais' => $this->pais
        ];

        return $repoPais->create($usuarioArray);
    }

    public function clonar(int $idUsuarioOriginal, UsuarioRequestDTO $dto) {
    $originalData = $this->repo->findById($idUsuarioOriginal);
    if (!$originalData) throw new Exception("Usuario no existe. ID: $idUsuarioOriginal");

    $original = new Usuario($originalData);

    // Traer accesos del usuario original
    $accesosBD = $this->accesoRepo->findByUsuarioId($idUsuarioOriginal);
    $original->accesos = array_map(fn($a) => new UsuarioAcceso(
        null,
        $a['idUsuario'],
        $a['idRol'],
        $a['idModulo'],
        $a['idAcceso']
    ), $accesosBD);

    // Crear clon del usuario
    $clon = $original->clone(
        $dto->username,
        $dto->nombres,
        $dto->apellidos,
        $dto->correo,
        $dto->password,
        $this->pais
    );

    // Guardar usuario
    $nuevoUsuario = $this->repo->clonarUsuario($clon);
    $nuevoUsuarioId = is_array($nuevoUsuario) ? $nuevoUsuario['id'] : $nuevoUsuario->id;

    // Clonar accesos si existen
    foreach ($clon->accesos as $acceso) {
        $this->accesoRepo->create([
            'idUsuario' => $nuevoUsuarioId,
            'idRol' => $acceso->getRolId(),
            'idModulo' => $acceso->getModuloId(),
            'idAcceso' => $acceso->getTipoAccesoId()
        ]);
    }

    return $nuevoUsuario;
}




    public function update(int $id, UsuarioRequestDTO $dto) {
        $usuarioExistente = $this->repo->findById($id);
        if (!$usuarioExistente) {
            throw new Exception("Usuario no encontrado.");
        }

        $updateData = [
            'username' => $dto->username ?? $usuarioExistente['username'],
            'nombres' => $dto->nombres ?? $usuarioExistente['nombres'],
            'apellidos' => $dto->apellidos ?? $usuarioExistente['apellidos'],
            'correo' => $dto->correo ?? $usuarioExistente['correo'],
        ];

        return $this->repo->update($id, $updateData);
    }

    public function getAll() {
        return $this->repo->getAll();
    }

    public function getById(int $id) {
        $usuario = $this->repo->findById($id);
        if (!$usuario) throw new Exception("Usuario no encontrado");
        return $usuario;
    }

    public function delete(int $id) {
        return $this->repo->delete($id);
    }
}
?>

<?php
require_once __DIR__ . '/../repositories/EstudianteRepository.php';
require_once __DIR__ . '/../dto/EstudianteRequestDTO.php';
require_once __DIR__ . '/../dto/EstudianteResponseDTO.php';
require_once __DIR__ . '/../mapper/EstudianteMapper.php';
require_once __DIR__ . '/../helpers/JWTHelper.php';
require_once __DIR__ . '/../connection/DatabaseFactory.php';

class EstudianteService {
    private EstudianteRepository $repo;
    private string $pais;
    private int $currentUserId;

    public function __construct() {
        // Obtener país y usuario desde el token
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        if (!$authHeader) {
            throw new Exception("Token JWT no encontrado en la cabecera Authorization.");
        }

        $decodedPais = JWTHelper::getPaisFromToken($authHeader);
        if (!$decodedPais) {
            throw new Exception("No se pudo determinar el país desde el token JWT.");
        }

        $decodedUser = JWTHelper::getUserIdFromToken($authHeader);
        if (!$decodedUser) {
            throw new Exception("No se pudo determinar el ID del usuario desde el token JWT.");
        }

        $this->pais = $decodedPais;
        $this->currentUserId = (int)$decodedUser;

        // Log para debug
        error_log("Pais desde token: " . $this->pais);
        error_log("UserId desde token: " . $this->currentUserId);

        // --- VERIFICAR USUARIO EXISTE EN LA DB ---
        try {
            $conexion = DatabaseFactory::getConnection($this->pais);
            if ($conexion instanceof PDO) { // Postgres o SQL Server
                $stmt = $conexion->prepare("SELECT id, username FROM usuarios WHERE id = :id");
                $stmt->execute([':id' => $this->currentUserId]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            } else { // MySQLi
                $stmt = $conexion->prepare("SELECT id, username FROM usuarios WHERE id = ?");
                $stmt->bind_param("i", $this->currentUserId);
                $stmt->execute();
                $usuario = $stmt->get_result()->fetch_assoc();
            }

            if (!$usuario) {
                error_log("Usuario no encontrado en {$this->pais}: ID = " . $this->currentUserId);
                throw new Exception("Usuario no existe en {$this->pais}. Status 401.");
            } else {
                error_log("Usuario encontrado en {$this->pais}: ID = " . $usuario['id'] . ", Username = " . $usuario['username']);
            }
        } catch (Exception $ex) {
            error_log("Error verificando usuario: " . $ex->getMessage());
            throw $ex;
        }
        // --- FIN VERIFICACIÓN ---

        // Crear repositorio
        $this->repo = new EstudianteRepository($this->pais);
    }

    public function getAll(): array {
        $estudiantes = $this->repo->findAll();
        return array_map(fn($e) => EstudianteMapper::mapEntityToResponseDTO($e), $estudiantes);
    }

    public function getById($id): ?EstudianteResponseDTO {
        $estudiante = $this->repo->findById($id);
        return $estudiante ? EstudianteMapper::mapEntityToResponseDTO($estudiante) : null;
    }

    public function create(EstudianteRequestDTO $dto): EstudianteResponseDTO {
        $estudiante = EstudianteMapper::mapRequestDTOToEntity($dto, false);
        $estudiante->setUsuarioCreacion($this->currentUserId);
        $estudiante->setFechaCreacion(date('Y-m-d H:i:s'));

        return $this->repo->create($estudiante);
    }

    public function update(EstudianteRequestDTO $dto, $id): ?EstudianteResponseDTO {
        $estudiante = EstudianteMapper::mapRequestDTOToEntity($dto, true);
        $estudiante->setId($id);
        $estudiante->setUsuarioModificacion($this->currentUserId);
        $estudiante->setFechaModificacion(date('Y-m-d H:i:s'));

        return $this->repo->update($estudiante);
    }

    public function delete($id): bool {
        return $this->repo->delete($id);
    }
}
?>

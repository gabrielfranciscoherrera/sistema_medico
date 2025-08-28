<?php
// private/controllers/EmpleadoController.php
require_once __DIR__ . '/../models/Empleado.php';

class EmpleadoController {
    private $db;
    private $empleado;

    public function __construct($db) {
        $this->db = $db;
        $this->empleado = new Empleado($this->db);
    }

    public function list() {
        try {
            $stmt = $this->empleado->listAll();
            // rowCount no es confiable para SELECT en MySQL; usamos fetchAll y contamos
            $empleados_arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
            echo json_encode($empleados_arr);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'DATABASE_ERROR', 'message' => 'Error al listar empleados.']);
        }
    }

    public function get($id) {
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['message' => 'ID de empleado no proporcionado.']);
            return;
        }
        try {
            $row = $this->empleado->getOne($id);
            if ($row) {
                http_response_code(200);
                echo json_encode($row);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Empleado no encontrado.']);
            }
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'DATABASE_ERROR', 'message' => 'Error al obtener empleado.']);
        }
    }

    public function create($data) {
        // Normalizar a arreglo asociativo
        $payload = is_object($data) ? (array)$data : (array)$data;

        $nombre = isset($payload['nombre']) ? trim((string)$payload['nombre']) : '';
        $usuario = isset($payload['usuario']) ? trim((string)$payload['usuario']) : '';
        $password = isset($payload['password']) ? (string)$payload['password'] : '';
        $id_rol = isset($payload['id_rol']) ? (int)$payload['id_rol'] : 0;

        $missing = [];
        if ($nombre === '') $missing[] = 'nombre';
        if ($usuario === '') $missing[] = 'usuario';
        if ($password === '') $missing[] = 'password';
        if ($id_rol <= 0) $missing[] = 'id_rol';
        if (!empty($missing)) {
            http_response_code(400);
            echo json_encode(['error' => 'MISSING_FIELDS', 'message' => 'Faltan campos requeridos.', 'fields' => $missing]);
            return;
        }

        $valErrors = [];
        if (mb_strlen($usuario) < 3) $valErrors[] = 'usuario:min_length:3';
        if (mb_strlen($password) < 6) $valErrors[] = 'password:min_length:6';
        if (!empty($valErrors)) {
            http_response_code(422);
            echo json_encode(['error' => 'VALIDATION_ERROR', 'message' => 'Datos no cumplen validaciones.', 'errors' => $valErrors]);
            return;
        }

        try {
            // Evitar duplicados de usuario
            $exists = $this->empleado->findAnyStatus($usuario);
            if ($exists) {
                http_response_code(409);
                echo json_encode(['error' => 'USER_EXISTS', 'message' => 'El nombre de usuario ya existe.']);
                return;
            }

            $newId = $this->empleado->create([
                'nombre' => $nombre,
                'usuario' => $usuario,
                'password' => $password,
                'id_rol' => $id_rol
            ]);

            if ($newId) {
                http_response_code(201);
                echo json_encode(['message' => 'Empleado creado exitosamente.', 'id' => $newId]);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'No se pudo crear el empleado.']);
            }
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'DATABASE_ERROR', 'message' => 'Error al crear empleado.']);
        }
    }

    public function update($id, $data) {
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['message' => 'ID de empleado no proporcionado.']);
            return;
        }
        $payload = is_object($data) ? (array)$data : (array)$data;
        try {
            $ok = $this->empleado->updateById($id, $payload);
            if ($ok) {
                http_response_code(200);
                echo json_encode(['message' => 'Empleado actualizado exitosamente.']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'No se aplicaron cambios.']);
            }
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'DATABASE_ERROR', 'message' => 'Error al actualizar empleado.']);
        }
    }

    public function delete($id) {
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['message' => 'ID de empleado no proporcionado.']);
            return;
        }
        try {
            $ok = $this->empleado->deleteById($id);
            if ($ok) {
                http_response_code(200);
                echo json_encode(['message' => 'Empleado eliminado exitosamente.']);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Empleado no encontrado o no eliminado.']);
            }
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'DATABASE_ERROR', 'message' => 'Error al eliminar empleado.']);
        }
    }
}
?>

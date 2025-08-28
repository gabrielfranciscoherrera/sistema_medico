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
        $stmt = $this->empleado->listAll();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $empleados_arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
            echo json_encode($empleados_arr);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron empleados.']);
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

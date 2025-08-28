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
}
?>
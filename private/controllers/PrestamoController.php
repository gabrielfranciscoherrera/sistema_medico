<?php
// private/controllers/PrestamoController.php
require_once __DIR__ . '/../models/Prestamo.php';

class PrestamoController {
    private $db;
    private $prestamo;

    public function __construct($db) {
        $this->db = $db;
        $this->prestamo = new Prestamo($this->db);
    }

    // Listar préstamos
    public function list($filter) {
        $result = $this->prestamo->read($filter);
        $prestamos_arr = array();
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            array_push($prestamos_arr, $row);
        }
        echo json_encode($prestamos_arr);
    }

    // Crear un nuevo préstamo
    public function create($data) {
        // Validación de datos de entrada
        if (empty($data->id_cliente) || empty($data->monto_aprobado) || empty($data->tasa_interes_anual) || empty($data->plazo) || empty($data->frecuencia_pago) || empty($data->fecha_solicitud)) {
            http_response_code(400);
            echo json_encode(['message' => 'Datos incompletos para crear el préstamo.']);
            return;
        }

        // Asignar datos al objeto Préstamo
        $this->prestamo->id_cliente = $data->id_cliente;
        $this->prestamo->monto_aprobado = $data->monto_aprobado;
        $this->prestamo->tasa_interes_anual = $data->tasa_interes_anual;
        $this->prestamo->plazo = $data->plazo;
        $this->prestamo->frecuencia_pago = $data->frecuencia_pago;
        $this->prestamo->fecha_solicitud = $data->fecha_solicitud;
        $this->prestamo->estado = 'Pendiente'; // Estado inicial
        $this->prestamo->id_empleado_registra = $_SESSION['user_id']; // ID del empleado que crea el préstamo

        // Intentar crear el préstamo
        if ($this->prestamo->create()) {
            http_response_code(201);
            echo json_encode([
                'message' => 'Préstamo creado exitosamente.',
                'prestamo_id' => $this->prestamo->id
            ]);
        } else {
            http_response_code(503);
            echo json_encode(['message' => 'No se pudo crear el préstamo.']);
        }
    }

    // Actualizar estado
    public function updateStatus($data) {
        if (!empty($data->id) && !empty($data->estado)) {
            if ($this->prestamo->updateStatus($data->id, $data->estado)) {
                http_response_code(200);
                echo json_encode(['message' => 'Estado del préstamo actualizado.']);
            } else {
                http_response_code(503);
                echo json_encode(['message' => 'No se pudo actualizar el estado.']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Datos incompletos.']);
        }
    }
}
?>

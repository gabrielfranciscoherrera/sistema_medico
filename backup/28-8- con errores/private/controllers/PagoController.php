<?php
require_once __DIR__ . '/../models/Pago.php';

class PagoController {
    private $db;
    private $pago;

    public function __construct($db) {
        $this->db = $db;
        $this->pago = new Pago($this->db);
    }

    // Registrar un nuevo pago
    public function create($data) {
        // Validación de datos de entrada
        if (empty($data->id_amortizacion) || empty($data->monto_pagado) || empty($data->metodo_pago)) {
            http_response_code(400);
            echo json_encode(['message' => 'Datos incompletos para registrar el pago.']);
            return;
        }

        // Asignar datos al objeto Pago
        $this->pago->id_amortizacion = $data->id_amortizacion;
        $this->pago->monto_pagado = $data->monto_pagado;
        $this->pago->metodo_pago = $data->metodo_pago;
        $this->pago->id_cajero = $_SESSION['user_id']; // El cajero es el usuario logueado

        // Intentar registrar el pago
        if ($this->pago->create()) {
            http_response_code(201);
            echo json_encode(['message' => 'Pago registrado exitosamente.']);
        } else {
            http_response_code(503);
            echo json_encode(['message' => 'No se pudo registrar el pago.']);
        }
    }

    // Listar pagos recientes
    public function listRecent($limit = 10) {
        $stmt = $this->pago->listRecent($limit);
        $rows = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }
        http_response_code(200);
        echo json_encode($rows);
    }
}
?>

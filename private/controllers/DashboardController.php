<?php
class DashboardController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getSummary() {
        if (!has_permission(['Admin', 'Gerente'])) {
            deny_access('No tiene permisos para ver el resumen del dashboard.');
        }

        try {
            // 1. Capital Total Prestado (suma de montos de préstamos desembolsados o saldados)
            $query_capital = "SELECT SUM(monto_aprobado) as total_prestado FROM prestamos WHERE estado IN ('Desembolsado', 'Saldado')";
            $stmt_capital = $this->db->prepare($query_capital);
            $stmt_capital->execute();
            $total_prestado = $stmt_capital->fetch(PDO::FETCH_ASSOC)['total_prestado'] ?? 0;

            // 2. Monto Total Cobrado (suma de todos los pagos registrados)
            $query_cobrado = "SELECT SUM(monto_pagado) as total_cobrado FROM pagos";
            $stmt_cobrado = $this->db->prepare($query_cobrado);
            $stmt_cobrado->execute();
            $total_cobrado = $stmt_cobrado->fetch(PDO::FETCH_ASSOC)['total_cobrado'] ?? 0;

            // 3. Préstamos Activos (préstamos que han sido desembolsados y no están saldados)
            $query_activos = "SELECT COUNT(id) as prestamos_activos FROM prestamos WHERE estado = 'Desembolsado'";
            $stmt_activos = $this->db->prepare($query_activos);
            $stmt_activos->execute();
            $prestamos_activos = $stmt_activos->fetch(PDO::FETCH_ASSOC)['prestamos_activos'] ?? 0;

            // 4. Clientes en Mora (clientes con al menos una cuota en estado 'Mora')
            // Esto requiere una consulta más compleja, uniendo clientes, préstamos y amortizaciones.
            $query_mora = "SELECT COUNT(DISTINCT c.id) as clientes_en_mora
                         FROM clientes c
                         JOIN prestamos p ON c.id = p.id_cliente
                         JOIN amortizaciones a ON p.id = a.id_prestamo
                         WHERE a.estado = 'Mora' AND p.estado = 'Desembolsado'";
            $stmt_mora = $this->db->prepare($query_mora);
            $stmt_mora->execute();
            $clientes_en_mora = $stmt_mora->fetch(PDO::FETCH_ASSOC)['clientes_en_mora'] ?? 0;

            // Ensamblar la respuesta
            $summary = [
                'total_prestado' => (float)$total_prestado,
                'total_cobrado' => (float)$total_cobrado,
                'prestamos_activos' => (int)$prestamos_activos,
                'clientes_en_mora' => (int)$clientes_en_mora
            ];

            http_response_code(200);
            echo json_encode($summary);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Error al obtener el resumen del dashboard.', 'error' => $e->getMessage()]);
        }
    }
}
?>
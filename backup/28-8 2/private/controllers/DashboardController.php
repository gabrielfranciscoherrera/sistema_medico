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

    // Devuelve datos agregados reales para las gráficas del dashboard
    public function getCharts() {
        if (!has_permission(['Admin', 'Gerente'])) {
            deny_access('No tiene permisos para ver las gráficas del dashboard.');
        }

        try {
            // Últimos 8 meses (incluido el mes actual)
            $months = [];
            $labels = [];
            $labelsShort = [];
            $now = new DateTime('first day of this month');
            // Generar desde más antiguo al más reciente
            for ($i = 7; $i >= 0; $i--) {
                $d = (clone $now)->modify("-{$i} months");
                $key = $d->format('Y-m');
                $months[] = $key;
                // Etiquetas legibles en español corto: Ene 24, Feb 24, etc.
                $mes = (int)$d->format('n');
                $anioCorto = $d->format('y');
                $nombres = [1=>'Ene',2=>'Feb',3=>'Mar',4=>'Abr',5=>'May',6=>'Jun',7=>'Jul',8=>'Ago',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dic'];
                $labels[] = $nombres[$mes] . ' ' . $d->format('Y');
                $labelsShort[] = $nombres[$mes] . ' ' . $anioCorto;
            }

            $fromDate = $months[0] . '-01';

            // 1) Prestado mensual (sumas de monto_aprobado) usando fecha_solicitud como referencia
            $sqlPrestado = "SELECT DATE_FORMAT(fecha_solicitud, '%Y-%m') AS ym, SUM(monto_aprobado) AS total
                             FROM prestamos
                             WHERE fecha_solicitud >= :from
                               AND estado IN ('Desembolsado','Saldado')
                             GROUP BY ym";
            $stmtPrestado = $this->db->prepare($sqlPrestado);
            $stmtPrestado->bindParam(':from', $fromDate);
            $stmtPrestado->execute();
            $prestadoMap = [];
            while ($row = $stmtPrestado->fetch(PDO::FETCH_ASSOC)) {
                $prestadoMap[$row['ym']] = (float)($row['total'] ?? 0);
            }

            // 2) Cobrado mensual (sumas de monto_pagado) por fecha_pago
            $sqlCobrado = "SELECT DATE_FORMAT(fecha_pago, '%Y-%m') AS ym, SUM(monto_pagado) AS total
                           FROM pagos
                           WHERE fecha_pago >= :from
                           GROUP BY ym";
            $stmtCobrado = $this->db->prepare($sqlCobrado);
            $stmtCobrado->bindParam(':from', $fromDate);
            $stmtCobrado->execute();
            $cobradoMap = [];
            while ($row = $stmtCobrado->fetch(PDO::FETCH_ASSOC)) {
                $cobradoMap[$row['ym']] = (float)($row['total'] ?? 0);
            }

            // 3) Distribución por estado de préstamos
            $sqlEstados = "SELECT estado, COUNT(*) AS cnt FROM prestamos GROUP BY estado";
            $stmtEstados = $this->db->prepare($sqlEstados);
            $stmtEstados->execute();
            $estadoLabels = [];
            $estadoCounts = [];
            while ($row = $stmtEstados->fetch(PDO::FETCH_ASSOC)) {
                $estadoLabels[] = $row['estado'] ?? '—';
                $estadoCounts[] = (int)($row['cnt'] ?? 0);
            }

            // Construir arrays alineados a los 8 meses
            $seriePrestado = [];
            $serieCobrado = [];
            foreach ($months as $ym) {
                $seriePrestado[] = isset($prestadoMap[$ym]) ? (float)$prestadoMap[$ym] : 0.0;
                $serieCobrado[] = isset($cobradoMap[$ym]) ? (float)$cobradoMap[$ym] : 0.0;
            }

            http_response_code(200);
            echo json_encode([
                'monthly' => [
                    'months' => $months,          // 'YYYY-MM'
                    'labels' => $labels,          // 'Ene 2024'
                    'labels_short' => $labelsShort, // 'Ene 24'
                    'prestado' => $seriePrestado,
                    'cobrado' => $serieCobrado,
                ],
                'status_distribution' => [
                    'labels' => $estadoLabels,
                    'counts' => $estadoCounts,
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Error al obtener datos de las gráficas.', 'error' => $e->getMessage()]);
        }
    }
}
?>

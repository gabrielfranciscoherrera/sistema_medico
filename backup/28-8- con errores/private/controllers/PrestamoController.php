<?php
// private/controllers/PrestamoController.php
require_once __DIR__ . '/../models/Prestamo.php';
require_once __DIR__ . '/../models/Amortizacion.php';

class PrestamoController {
    private $db;
    private $prestamo;
    private $amortizacion;

    public function __construct($db) {
        $this->db = $db;
        $this->prestamo = new Prestamo($this->db);
        $this->amortizacion = new Amortizacion($this->db);
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
        // Normalizar y validar datos de entrada
        if ($data === null) {
            http_response_code(400);
            echo json_encode(['message' => 'Cuerpo de la solicitud inválido. Se esperaba JSON.']);
            return;
        }
        if (!is_object($data)) {
            $data = (object)$data;
        }

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

        // Calcular monto de la cuota periódica en base a tasa y frecuencia
        $monto = (float)$this->prestamo->monto_aprobado;
        $tasaAnual = (float)$this->prestamo->tasa_interes_anual; // porcentaje anual (ej. 24 => 24%)
        $plazo = (int)$this->prestamo->plazo; // número de periodos
        $freq = (string)$this->prestamo->frecuencia_pago; // 'diario' | 'semanal' | 'mensual'

        $periodosPorAnio = 12; // por defecto
        if ($freq === 'diario') {
            $periodosPorAnio = 365;
        } elseif ($freq === 'semanal') {
            $periodosPorAnio = 52;
        } elseif ($freq === 'mensual') {
            $periodosPorAnio = 12;
        }
        $tasaPeriodica = ($tasaAnual / 100.0) / $periodosPorAnio;

        if ($plazo <= 0 || $monto <= 0) {
            http_response_code(400);
            echo json_encode(['message' => 'Monto y plazo deben ser mayores a 0.']);
            return;
        }

        if ($tasaPeriodica > 0) {
            $factor = pow(1 + $tasaPeriodica, $plazo);
            $montoCuota = $monto * ($tasaPeriodica * $factor) / ($factor - 1);
        } else {
            // Sin interés
            $montoCuota = $monto / $plazo;
        }

        // Guardar el monto de cuota calculado
        $this->prestamo->monto_cuota = round($montoCuota, 2);

        // Intentar crear el préstamo
        if ($this->prestamo->create()) {
            // Crear tabla de amortización
            $prestamo_id = $this->prestamo->id;
            $fecha = new DateTime($this->prestamo->fecha_solicitud);
            $frecuencia = $freq;
            $saldo_pendiente = $monto;
            
            // Calcular amortización francesa con precisión
            for ($i = 1; $i <= $plazo; $i++) {
                $fecha_pago = clone $fecha;
                if ($frecuencia === 'diario') {
                    $fecha_pago->modify("+$i day");
                } elseif ($frecuencia === 'semanal') {
                    $fecha_pago->modify("+" . ($i * 7) . " days");
                } else { // mensual
                    $fecha_pago->modify("+" . $i . " month");
                }
                
                // Calcular interés y capital para esta cuota
                $interes_cuota = $saldo_pendiente * $tasaPeriodica;
                $capital_cuota = $montoCuota - $interes_cuota;
                $saldo_pendiente -= $capital_cuota;
                
                // Ajustar última cuota para saldo exacto de cero
                if ($i == $plazo) {
                    $capital_cuota += $saldo_pendiente;
                    $saldo_pendiente = 0;
                }
                
                $cuota_data = [
                    'id_prestamo' => $prestamo_id,
                    'numero_cuota' => $i,
                    'monto_cuota' => round($montoCuota, 2),
                    'capital' => round($capital_cuota, 2),
                    'interes' => round($interes_cuota, 2),
                    'saldo_pendiente' => round($saldo_pendiente, 2),
                    'fecha_pago' => $fecha_pago->format('Y-m-d'),
                    'estado' => 'Pendiente'
                ];
                
                $this->amortizacion->create($cuota_data);
            }
            
            http_response_code(201);
            echo json_encode([
                'message' => 'Préstamo y amortización creados exitosamente.',
                'prestamo_id' => $prestamo_id
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

    // Obtener información para pagos (préstamo + próxima cuota)
    public function getForPayment($id) {
        $id = intval($id);
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['message' => 'ID de préstamo inválido']);
            return;
        }

        $prestamo = $this->prestamo->getOne($id);
        if (!$prestamo) {
            http_response_code(404);
            echo json_encode(['message' => 'Préstamo no encontrado']);
            return;
        }

        if (!isset($prestamo['estado']) || $prestamo['estado'] !== 'Desembolsado') {
            http_response_code(409);
            echo json_encode(['message' => 'El préstamo no está desembolsado.']);
            return;
        }

        $next = $this->amortizacion->getNextByPrestamo($id);
        if (!$next) {
            http_response_code(200);
            echo json_encode([
                'prestamo_id' => $prestamo['id'],
                'cliente_nombre' => $prestamo['cliente_nombre'] ?? null,
                'estado' => $prestamo['estado'],
                'sin_cuotas_pendientes' => true
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            'prestamo_id' => $prestamo['id'],
            'cliente_nombre' => $prestamo['cliente_nombre'] ?? null,
            'estado' => $prestamo['estado'],
            'proxima_cuota' => [
                'id_amortizacion' => $next['id'],
                'numero_cuota' => $next['numero_cuota'],
                'fecha_pago' => $next['fecha_pago'],
                'monto_cuota' => (float)$next['monto_cuota'],
            ],
        ]);
    }
}
?>

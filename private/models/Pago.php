<?php
class Pago {
    private $conn;
    private $table = 'pagos';

    // Propiedades del Pago
    public $id;
    public $id_amortizacion;
    public $monto_pagado;
    public $fecha_pago;
    public $id_cajero;
    public $metodo_pago;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Registrar un nuevo pago
    public function create() {
        $this->conn->beginTransaction();

        try {
            // 1. Insertar el pago
            $query_pago = 'INSERT INTO ' . $this->table . ' SET
                id_amortizacion = :id_amortizacion,
                monto_pagado = :monto_pagado,
                id_cajero = :id_cajero,
                metodo_pago = :metodo_pago';

            $stmt_pago = $this->conn->prepare($query_pago);

            // Limpiar datos
            $this->id_amortizacion = htmlspecialchars(strip_tags($this->id_amortizacion));
            $this->monto_pagado = htmlspecialchars(strip_tags($this->monto_pagado));
            $this->id_cajero = htmlspecialchars(strip_tags($this->id_cajero));
            $this->metodo_pago = htmlspecialchars(strip_tags($this->metodo_pago));

            // Vincular parámetros
            $stmt_pago->bindParam(':id_amortizacion', $this->id_amortizacion);
            $stmt_pago->bindParam(':monto_pagado', $this->monto_pagado);
            $stmt_pago->bindParam(':id_cajero', $this->id_cajero);
            $stmt_pago->bindParam(':metodo_pago', $this->metodo_pago);

            if (!$stmt_pago->execute()) {
                throw new Exception('Error al registrar el pago.');
            }

            // 2. Actualizar el estado de la cuota en la tabla de amortizaciones
            $query_amortizacion = 'UPDATE amortizaciones SET estado = "Pagada" WHERE id = :id_amortizacion';
            $stmt_amortizacion = $this->conn->prepare($query_amortizacion);
            $stmt_amortizacion->bindParam(':id_amortizacion', $this->id_amortizacion);

            if (!$stmt_amortizacion->execute()) {
                throw new Exception('Error al actualizar la cuota.');
            }
            
            // 3. Opcional: Verificar si todas las cuotas del préstamo están pagadas para marcar el préstamo como "Saldado"
            // Esta lógica se puede añadir aquí si se desea automatizar el cierre del préstamo.

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            // En un entorno de producción, registrar el error: error_log($e->getMessage());
            return false;
        }
    }
}
?>
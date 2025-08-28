<?php
// private/models/Prestamo.php

class Prestamo {
    private $conn;
    private $table = 'prestamos';

    // Propiedades del objeto
    public $id;
    public $id_cliente;
    public $monto_aprobado;
    public $tasa_interes_anual;
    public $plazo;
    public $frecuencia_pago;
    public $monto_cuota;
    public $fecha_solicitud;
    public $estado;
    public $id_empleado_registra;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener un préstamo por ID con datos de cliente
    public function getOne($id) {
        $query = "SELECT 
                    p.id, p.id_cliente, c.nombre_completo as cliente_nombre, c.cedula as cliente_cedula,
                    p.monto_aprobado, p.estado, p.fecha_solicitud
                  FROM " . $this->table . " p
                  LEFT JOIN clientes c ON p.id_cliente = c.id
                  WHERE p.id = :id
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener todos los préstamos con filtro
    public function read($filter) {
        $query = "SELECT 
                    p.id,
                    p.id_cliente,
                    c.nombre_completo AS cliente_nombre,
                    c.cedula AS cliente_cedula,
                    p.monto_aprobado,
                    p.tasa_interes_anual,
                    p.plazo,
                    p.frecuencia_pago,
                    p.monto_cuota,
                    p.estado,
                    p.fecha_solicitud
                  FROM " . $this->table . " p
                  LEFT JOIN clientes c ON p.id_cliente = c.id
                  WHERE 
                    c.nombre_completo LIKE :filter OR 
                    c.cedula LIKE :filter OR 
                    p.id LIKE :filter
                  ORDER BY p.fecha_solicitud DESC";
        
        $stmt = $this->conn->prepare($query);
        $filter_param = '%' . htmlspecialchars(strip_tags($filter)) . '%';
        $stmt->bindParam(':filter', $filter_param);
        $stmt->execute();
        return $stmt;
    }

    // Crear un nuevo préstamo
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' SET
            id_cliente = :id_cliente,
            monto_aprobado = :monto_aprobado,
            tasa_interes_anual = :tasa_interes_anual,
            plazo = :plazo,
            frecuencia_pago = :frecuencia_pago,
            monto_cuota = :monto_cuota,
            fecha_solicitud = :fecha_solicitud,
            estado = :estado,
            id_empleado_registra = :id_empleado_registra';

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->id_cliente = htmlspecialchars(strip_tags($this->id_cliente));
        $this->monto_aprobado = htmlspecialchars(strip_tags($this->monto_aprobado));
        $this->tasa_interes_anual = htmlspecialchars(strip_tags($this->tasa_interes_anual));
        $this->plazo = htmlspecialchars(strip_tags($this->plazo));
        $this->frecuencia_pago = htmlspecialchars(strip_tags($this->frecuencia_pago));
        $this->monto_cuota = htmlspecialchars(strip_tags($this->monto_cuota));
        $this->fecha_solicitud = htmlspecialchars(strip_tags($this->fecha_solicitud));
        $this->estado = htmlspecialchars(strip_tags($this->estado));
        $this->id_empleado_registra = htmlspecialchars(strip_tags($this->id_empleado_registra));

        // Vincular parámetros
        $stmt->bindParam(':id_cliente', $this->id_cliente);
        $stmt->bindParam(':monto_aprobado', $this->monto_aprobado);
        $stmt->bindParam(':tasa_interes_anual', $this->tasa_interes_anual);
        $stmt->bindParam(':plazo', $this->plazo);
        $stmt->bindParam(':frecuencia_pago', $this->frecuencia_pago);
        $stmt->bindParam(':monto_cuota', $this->monto_cuota);
        $stmt->bindParam(':fecha_solicitud', $this->fecha_solicitud);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':id_empleado_registra', $this->id_empleado_registra);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Actualizar el estado de un préstamo
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET estado = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $status = htmlspecialchars(strip_tags($status));
        $id = htmlspecialchars(strip_tags($id));

        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>

<?php
// private/models/Amortizacion.php

class Amortizacion {
    // Crear una cuota de amortización
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " (id_prestamo, numero_cuota, monto_cuota, capital, interes, saldo_pendiente, fecha_pago, estado) VALUES (:id_prestamo, :numero_cuota, :monto_cuota, :capital, :interes, :saldo_pendiente, :fecha_pago, :estado)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_prestamo', $data['id_prestamo']);
        $stmt->bindParam(':numero_cuota', $data['numero_cuota']);
        $stmt->bindParam(':monto_cuota', $data['monto_cuota']);
        $stmt->bindParam(':capital', $data['capital']);
        $stmt->bindParam(':interes', $data['interes']);
        $stmt->bindParam(':saldo_pendiente', $data['saldo_pendiente']);
        $stmt->bindParam(':fecha_pago', $data['fecha_pago']);
        $stmt->bindParam(':estado', $data['estado']);
        return $stmt->execute();
    }
    private $conn;
    private $table = 'amortizaciones';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Devuelve la próxima cuota pendiente para un préstamo dado
    public function getNextByPrestamo($id_prestamo) {
        $query = "SELECT a.*
                  FROM {$this->table} a
                  WHERE a.id_prestamo = :id_prestamo AND a.estado = 'Pendiente'
                  ORDER BY a.numero_cuota ASC, a.fecha_pago ASC
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $id_prestamo = htmlspecialchars(strip_tags($id_prestamo));
        $stmt->bindParam(':id_prestamo', $id_prestamo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>


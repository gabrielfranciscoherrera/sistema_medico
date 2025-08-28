<?php
// private/models/Amortizacion.php

class Amortizacion {
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


<?php

class Cliente {
    private $conn;
    private $table = 'clientes';

    // Propiedades del Cliente
    public $id;
    public $nombre_completo;
    public $cedula;
    public $telefono;
    public $direccion;
    public $creado_por_empleado_id;
    public $fecha_registro;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Buscar clientes por nombre o cédula
    public function search($term) {
    $query = "SELECT id, nombre_completo, cedula, telefono, fecha_registro FROM " . $this->table . "
          WHERE nombre_completo LIKE :term OR cedula LIKE :term
          LIMIT 10";

        $stmt = $this->conn->prepare($query);
        $term_param = '%' . htmlspecialchars(strip_tags($term)) . '%';
        $stmt->bindParam(':term', $term_param);
        $stmt->execute();
        return $stmt;
    }

    // Obtener un solo cliente por ID
    public function getOne($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo cliente
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' SET
            nombre_completo = :nombre_completo,
            cedula = :cedula,
            telefono = :telefono,
            direccion = :direccion,
            creado_por_empleado_id = :creado_por_empleado_id,
            fecha_registro = :fecha_registro';

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre_completo = htmlspecialchars(strip_tags($this->nombre_completo));
        $this->cedula = htmlspecialchars(strip_tags($this->cedula));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        $this->creado_por_empleado_id = htmlspecialchars(strip_tags($this->creado_por_empleado_id));
        $this->fecha_registro = date('Y-m-d H:i:s'); // Asignar la fecha y hora actual

        // Vincular parámetros
        $stmt->bindParam(':nombre_completo', $this->nombre_completo);
        $stmt->bindParam(':cedula', $this->cedula);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':creado_por_empleado_id', $this->creado_por_empleado_id);
        $stmt->bindParam(':fecha_registro', $this->fecha_registro);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Actualizar un cliente
    public function update() {
        $query = 'UPDATE ' . $this->table . ' SET
            nombre_completo = :nombre_completo,
            cedula = :cedula,
            telefono = :telefono,
            direccion = :direccion
            WHERE id = :id';

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->nombre_completo = htmlspecialchars(strip_tags($this->nombre_completo));
        $this->cedula = htmlspecialchars(strip_tags($this->cedula));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));

        // Vincular parámetros
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':nombre_completo', $this->nombre_completo);
        $stmt->bindParam(':cedula', $this->cedula);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':direccion', $this->direccion);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Eliminar un cliente por ID
    public function delete($id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        return false;
    }
}

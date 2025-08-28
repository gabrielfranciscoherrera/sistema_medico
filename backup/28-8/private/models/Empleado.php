<?php
// private/models/Empleado.php

class Empleado {
    private $conn;
    private $table = 'empleados';

    // Propiedades del Empleado
    public $id;
    public $nombre;
    public $usuario;
    public $password;
    public $id_rol;
    public $activo;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Busca un empleado por su nombre de usuario para el login.
     */
    public function findByUsername($username) {
        $query = 'SELECT e.id, e.nombre, e.usuario, e.password, e.id_rol, r.nombre_rol as nombre_rol
                  FROM ' . $this->table . ' e
                  LEFT JOIN roles r ON e.id_rol = r.id
                  WHERE e.usuario = :usuario AND e.estado = \'Activo\'
                  LIMIT 1';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario', $username);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Lista todos los empleados con sus roles para el módulo de gestión.
     */
    public function listAll() {
        $query = 'SELECT e.id, e.nombre, e.usuario, r.nombre_rol as rol, r.descripcion
                  FROM ' . $this->table . ' e
                  LEFT JOIN roles r ON e.id_rol = r.id
                  ORDER BY e.nombre ASC';
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
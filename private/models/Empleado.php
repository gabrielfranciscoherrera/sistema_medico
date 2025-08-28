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
        $queries = [
            // Opción 1: campo booleano "activo"
            "SELECT e.id, e.nombre, e.usuario, e.password, e.id_rol, r.nombre as nombre_rol
             FROM {$this->table} e
             LEFT JOIN roles r ON e.id_rol = r.id
             WHERE e.usuario = :usuario AND e.activo = 1
             LIMIT 1",
            // Opción 2: campo de texto "estado = 'Activo'"
            "SELECT e.id, e.nombre, e.usuario, e.password, e.id_rol, r.nombre as nombre_rol
             FROM {$this->table} e
             LEFT JOIN roles r ON e.id_rol = r.id
             WHERE e.usuario = :usuario AND e.estado = 'Activo'
             LIMIT 1",
            // Opción 2b: sin JOIN con roles, usando activo
            "SELECT e.id, e.nombre, e.usuario, e.password, e.id_rol
             FROM {$this->table} e
             WHERE e.usuario = :usuario AND e.activo = 1
             LIMIT 1",
            // Opción 2c: sin JOIN con roles, usando estado
            "SELECT e.id, e.nombre, e.usuario, e.password, e.id_rol
             FROM {$this->table} e
             WHERE e.usuario = :usuario AND e.estado = 'Activo'
             LIMIT 1",
            // Opción 3: sin filtro de estado
            "SELECT e.id, e.nombre, e.usuario, e.password, e.id_rol, r.nombre as nombre_rol
             FROM {$this->table} e
             LEFT JOIN roles r ON e.id_rol = r.id
             WHERE e.usuario = :usuario
             LIMIT 1"
        ];

        $lastException = null;
        foreach ($queries as $q) {
            try {
                $stmt = $this->conn->prepare($q);
                $stmt->bindParam(':usuario', $username);
                $stmt->execute();
                return $stmt;
            } catch (PDOException $ex) {
                $lastException = $ex;
                // Intentar siguiente variante si hay error de columna/campo
                continue;
            }
        }
        // Si todas las variantes fallan, relanzar la última excepción
        if ($lastException) throw $lastException;
        // Fallback imposible, pero retornamos un statement vacío para contrato
        $stmt = $this->conn->prepare('SELECT 1');
        $stmt->execute();
        return $stmt;
    }

    /**
     * Obtiene un empleado por usuario sin filtrar por estado y con la mayor
     * cantidad posible de campos (incluyendo 'activo' o 'estado' si existen).
     * Devuelve un arreglo asociativo o null si no existe.
     */
    public function findAnyStatus($username) {
        $queries = [
            // Con JOIN de roles e intentando traer columnas de estado
            "SELECT e.id, e.nombre, e.usuario, e.password, e.id_rol, e.activo, e.estado, r.nombre as nombre_rol
             FROM {$this->table} e
             LEFT JOIN roles r ON e.id_rol = r.id
             WHERE e.usuario = :usuario
             LIMIT 1",
            // Sin JOIN de roles
            "SELECT e.id, e.nombre, e.usuario, e.password, e.id_rol, e.activo, e.estado
             FROM {$this->table} e
             WHERE e.usuario = :usuario
             LIMIT 1"
        ];

        foreach ($queries as $q) {
            try {
                $stmt = $this->conn->prepare($q);
                $stmt->bindParam(':usuario', $username);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) return $row;
            } catch (PDOException $ex) {
                // Intentar siguiente variante en caso de error de columna o tabla
                continue;
            }
        }
        return null;
    }

    /**
     * Lista todos los empleados con sus roles para el módulo de gestión.
     */
    public function listAll() {
        $query = 'SELECT e.id, e.nombre, e.usuario, r.nombre as rol, r.descripcion
                  FROM ' . $this->table . ' e
                  LEFT JOIN roles r ON e.id_rol = r.id
                  ORDER BY e.nombre ASC';
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>

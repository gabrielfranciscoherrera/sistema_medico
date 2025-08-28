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

    // Obtener un empleado por ID
    public function getOne($id) {
        $queries = [
            "SELECT e.id, e.nombre, e.usuario, e.id_rol, e.activo, e.estado, r.nombre as rol
             FROM {$this->table} e LEFT JOIN roles r ON e.id_rol = r.id WHERE e.id = :id LIMIT 1",
            "SELECT e.id, e.nombre, e.usuario, e.id_rol, e.activo, e.estado
             FROM {$this->table} e WHERE e.id = :id LIMIT 1",
        ];
        foreach ($queries as $q) {
            try {
                $stmt = $this->conn->prepare($q);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) return $row;
            } catch (PDOException $ex) { continue; }
        }
        return null;
    }

    // Actualizar empleado por ID (campos opcionales)
    public function updateById($id, $data) {
        $fields = [];
        $params = [':id' => $id];

        if (isset($data['nombre'])) { $fields[] = 'nombre = :nombre'; $params[':nombre'] = htmlspecialchars(strip_tags($data['nombre'])); }
        if (isset($data['usuario'])) { $fields[] = 'usuario = :usuario'; $params[':usuario'] = htmlspecialchars(strip_tags($data['usuario'])); }
        if (isset($data['id_rol'])) { $fields[] = 'id_rol = :id_rol'; $params[':id_rol'] = (int)$data['id_rol']; }
        if (isset($data['password']) && $data['password'] !== '') {
            $fields[] = 'password = :password';
            $params[':password'] = password_hash((string)$data['password'], PASSWORD_BCRYPT);
        }

        // Soporte para activo/estado
        $sqlTried = false; $updated = false; $lastEx = null;
        if (!empty($fields)) {
            $set = implode(', ', $fields);
            try {
                $query = 'UPDATE ' . $this->table . ' SET ' . $set . ' WHERE id = :id';
                $stmt = $this->conn->prepare($query);
                foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
                $sqlTried = true;
                $updated = $stmt->execute();
            } catch (PDOException $ex) { $lastEx = $ex; }
        }

        if (isset($data['activo'])) {
            try {
                $stmt = $this->conn->prepare('UPDATE ' . $this->table . ' SET activo = :activo WHERE id = :id');
                $stmt->bindValue(':activo', (int)$data['activo']);
                $stmt->bindValue(':id', $id);
                $stmt->execute();
                $updated = true;
            } catch (PDOException $ex) { $lastEx = $ex; }
        }
        if (isset($data['estado'])) {
            try {
                $stmt = $this->conn->prepare('UPDATE ' . $this->table . ' SET estado = :estado WHERE id = :id');
                $stmt->bindValue(':estado', htmlspecialchars(strip_tags($data['estado'])));
                $stmt->bindValue(':id', $id);
                $stmt->execute();
                $updated = true;
            } catch (PDOException $ex) { $lastEx = $ex; }
        }

        if (!$updated && $sqlTried && $lastEx) throw $lastEx;
        return $updated;
    }

    // Eliminación lógica preferida; si falla, eliminación física
    public function deleteById($id) {
        try {
            $stmt = $this->conn->prepare('UPDATE ' . $this->table . ' SET activo = 0 WHERE id = :id');
            $stmt->bindValue(':id', $id);
            if ($stmt->execute() && $stmt->rowCount() > 0) return true;
        } catch (PDOException $ex) { /* intentar siguiente */ }
        try {
            $stmt = $this->conn->prepare("UPDATE " . $this->table . " SET estado = 'Inactivo' WHERE id = :id");
            $stmt->bindValue(':id', $id);
            if ($stmt->execute() && $stmt->rowCount() > 0) return true;
        } catch (PDOException $ex) { /* intentar delete */ }
        // Eliminación física (cuidado con FKs)
        $stmt = $this->conn->prepare('DELETE FROM ' . $this->table . ' WHERE id = :id');
        $stmt->bindValue(':id', $id);
        return $stmt->execute() && $stmt->rowCount() > 0;
    }
}
?>

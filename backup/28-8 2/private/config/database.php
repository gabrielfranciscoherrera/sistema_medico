<?php
// private/config/database.php

// Configuración directa en PHP (sin .env)
// Puedes cambiar APP_DEBUG a true temporalmente para ver detalles en respuestas JSON
if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', false);
}

class Database {
    // Parámetros de conexión (ajusta según tu servidor)
    private $host = 'localhost';
    private $db_name = 'prestamos_db';
    private $username = 'root';
    private $password = 'Theboy%88';
    private $port = 3306;
    private $conn;

    // Método para obtener la conexión a la base de datos
    public function connect() {
        $this->conn = null;

        try {
            $dsn = 'mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->db_name . ';charset=utf8';
            $this->conn = new PDO($dsn, $this->username, $this->password);

            // Configurar PDO para que lance excepciones en caso de error
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Configurar para que devuelva resultados como arrays asociativos
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch(PDOException $e) {
            // Propagar el error para manejo centralizado en la API
            throw new Exception('DATABASE_CONNECTION_ERROR', 0, $e);
        }

        return $this->conn;
    }
}
?>

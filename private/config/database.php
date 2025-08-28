<?php
// private/config/database.php

class Database {
    // Parámetros de la base de datos
    private $host = 'localhost';
    private $db_name = 'prestamos_db'; // Cambia esto por el nombre de tu BD
    private $username = 'root'; // Cambia esto por tu usuario de BD
    private $password = 'Theboy%88'; // Cambia esto por tu contraseña de BD
    private $conn;

    // Método para obtener la conexión a la base de datos
    public function connect() {
        $this->conn = null;

        try {
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8';
            $this->conn = new PDO($dsn, $this->username, $this->password);
            
            // Configurar PDO para que lance excepciones en caso de error
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Configurar para que devuelva resultados como arrays asociativos
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch(PDOException $e) {
            // En un entorno de producción, no mostrarías el error detallado
            echo 'Error de Conexión: ' . $e->getMessage();
        }

        return $this->conn;
    }
}
?>

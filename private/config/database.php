<?php
// private/config/database.php

// Cargador simple de variables desde un archivo .env en la raíz del proyecto
function loadDotEnv($path)
{
    if (!file_exists($path) || !is_readable($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        $pos = strpos($line, '=');
        if ($pos === false) continue;
        $name = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));
        // Quitar comillas si existen (compatible con PHP < 8)
        if (strlen($value) >= 2) {
            $first = $value[0];
            $last  = substr($value, -1);
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }
        // No sobreescribir si ya existe en entorno
        if (getenv($name) === false) {
            putenv($name . '=' . $value);
            $_ENV[$name] = $value;
        }
    }
}

class Database {
    // Valores por defecto (se pueden sobreescribir por .env)
    private $host = 'localhost';
    private $db_name = 'prestamos_db';
    private $username = 'root';
    private $password = '';
    private $port = 3306;
    private $conn;

    // Método para obtener la conexión a la base de datos
    public function connect() {
        $this->conn = null;

        // Intentar cargar .env de la raíz del proyecto
        $envPath = dirname(__DIR__, 2) . '/.env';
        if (function_exists('loadDotEnv')) {
            loadDotEnv($envPath);
        }

        // Leer variables desde entorno con fallback a propiedades
        $host = getenv('DB_HOST') ?: $this->host;
        $db   = getenv('DB_DATABASE') ?: $this->db_name;
        $user = getenv('DB_USERNAME') ?: $this->username;
        $pass = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : $this->password;
        $port = getenv('DB_PORT') ?: $this->port;

        try {
            $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $db . ';charset=utf8';
            $this->conn = new PDO($dsn, $user, $pass);

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

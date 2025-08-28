<?php
// private/controllers/AuthController.php
require_once __DIR__ . '/../models/Empleado.php';

class AuthController {
    private $db;
    private $empleado;

    public function __construct($db) {
        $this->db = $db;
        $this->empleado = new Empleado($this->db);
    }

    public function login($data) {
        if (empty($data->usuario) || empty($data->password)) {
            http_response_code(400);
            echo json_encode(['message' => 'Usuario y contraseña son requeridos.']);
            return;
        }

        $this->empleado->usuario = $data->usuario;
        $stmt = $this->empleado->findByUsername($data->usuario);
        
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($data->password, $row['password'])) {
                // La sesión ya fue iniciada en api.php
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_role'] = $row['nombre_rol'];
                $_SESSION['user_name'] = $row['nombre'];

                http_response_code(200);
                echo json_encode([
                    'message' => 'Inicio de sesión exitoso.',
                    'user' => [
                        'id' => $row['id'],
                        'nombre' => $row['nombre'],
                        'rol' => $row['nombre_rol']
                    ]
                ]);
            } else {
                http_response_code(401);
                echo json_encode(['message' => 'Contraseña incorrecta.']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Usuario no encontrado o inactivo.']);
        }
    }

    public function logout() {
        // La sesión ya fue iniciada en api.php
        session_unset();
        session_destroy();
        http_response_code(200);
        echo json_encode(['message' => 'Sesión cerrada exitosamente.']);
    }
}
?>
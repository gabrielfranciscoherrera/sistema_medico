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
        // Normalizar entrada (array u objeto)
        $usuario = null;
        $password = null;
        if (is_array($data)) {
            $usuario = isset($data['usuario']) ? trim((string)$data['usuario']) : '';
            $password = isset($data['password']) ? (string)$data['password'] : '';
        } else if (is_object($data)) {
            $usuario = isset($data->usuario) ? trim((string)$data->usuario) : '';
            $password = isset($data->password) ? (string)$data->password : '';
        }

        // Validación de campos requeridos
        $missing = [];
        if ($usuario === '' || $usuario === null) $missing[] = 'usuario';
        if ($password === '' || $password === null) $missing[] = 'password';
        if (!empty($missing)) {
            http_response_code(400);
            echo json_encode([
                'error' => 'MISSING_FIELDS',
                'message' => 'Faltan campos requeridos.',
                'fields' => $missing
            ]);
            return;
        }

        // Validaciones básicas
        $valErrors = [];
        if (mb_strlen($usuario) < 3) $valErrors[] = 'usuario:min_length:3';
        if (mb_strlen($password) < 6) $valErrors[] = 'password:min_length:6';
        if (!empty($valErrors)) {
            http_response_code(422);
            echo json_encode([
                'error' => 'VALIDATION_ERROR',
                'message' => 'Datos de acceso no cumplen validaciones.',
                'errors' => $valErrors
            ]);
            return;
        }

        try {
            $this->empleado->usuario = $usuario;
            $stmt = $this->empleado->findByUsername($usuario);

            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if (password_verify($password, $row['password'])) {
                    // La sesión ya fue iniciada en api.php
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['user_role'] = $row['nombre_rol'] ?? ($row['rol'] ?? ($row['rol_nombre'] ?? 'Empleado'));
                    $_SESSION['user_name'] = $row['nombre'];

                    http_response_code(200);
                    echo json_encode([
                        'message' => 'Inicio de sesión exitoso.',
                        'user' => [
                            'id' => $row['id'],
                            'nombre' => $row['nombre'],
                            'rol' => ($_SESSION['user_role'])
                        ]
                    ]);
                } else {
                    http_response_code(401);
                    echo json_encode([
                        'error' => 'PASSWORD_INCORRECT',
                        'message' => 'La contraseña es incorrecta.'
                    ]);
                }
            } else {
                // Diferenciar entre usuario inexistente e inactivo
                $userInfo = $this->empleado->findAnyStatus($usuario);
                if ($userInfo) {
                    $inactive = false;
                    if (isset($userInfo['activo'])) {
                        $inactive = (string)$userInfo['activo'] === '0';
                    } elseif (isset($userInfo['estado'])) {
                        $inactive = strtolower((string)$userInfo['estado']) !== 'activo';
                    }
                    if ($inactive) {
                        http_response_code(403);
                        echo json_encode([
                            'error' => 'USER_INACTIVE',
                            'message' => 'El usuario existe pero no está activo.'
                        ]);
                        return;
                    }
                }
                http_response_code(404);
                echo json_encode([
                    'error' => 'USER_NOT_FOUND',
                    'message' => 'Usuario no encontrado.'
                ]);
            }
        } catch (Throwable $e) {
            http_response_code(500);
            $payload = [
                'error' => 'DATABASE_ERROR',
                'message' => 'Error en la base de datos durante el inicio de sesión.'
            ];
            $isDebug = defined('APP_DEBUG') ? (bool)APP_DEBUG : in_array(strtolower((string) getenv('APP_DEBUG')), ['1','true','yes','on']);
            if ($isDebug) {
                $payload['details'] = $e->getMessage();
            }
            echo json_encode($payload);
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

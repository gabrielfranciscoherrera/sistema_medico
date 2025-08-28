<?php
// public_html/api.php

// Iniciar sesión para manejo de autenticación
session_start();

// Encabezados para evitar el caché del navegador
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");

// Encabezados para permitir solicitudes CORS y asegurar respuesta JSON.
header("Access-Control-Allow-Origin: *"); // En producción, debería ser más restrictivo
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Responder a solicitudes OPTIONS (pre-flight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// --- Helpers de configuración y respuesta ---
function env_val($key, $default = null) {
    $v = getenv($key);
    return $v === false ? $default : $v;
}
function app_debug() {
    $v = strtolower((string) env_val('APP_DEBUG', 'false'));
    return in_array($v, ['1','true','yes','on']);
}
function respond($status, $payload) {
    http_response_code($status);
    echo json_encode($payload);
    exit();
}
function error_response($status, $code, $message, $details = null, $extra = []) {
    $resp = array_merge([
        'error' => $code,
        'message' => $message,
    ], $extra);
    if ($details !== null && app_debug()) {
        $resp['details'] = $details;
    }
    respond($status, $resp);
}

// Incluir los archivos necesarios
require_once __DIR__ . '/../private/config/database.php';
require_once __DIR__ . '/../private/controllers/PrestamoController.php';
require_once __DIR__ . '/../private/controllers/ClienteController.php';
require_once __DIR__ . '/../private/controllers/AuthController.php';
require_once __DIR__ . '/../private/controllers/PagoController.php';
require_once __DIR__ . '/../private/controllers/DashboardController.php';
require_once __DIR__ . '/../private/controllers/EmpleadoController.php';

// Instanciar la base de datos y obtener la conexión, con manejo de errores
try {
    $database = new Database();
    $db = $database->connect();
} catch (Throwable $e) {
    $details = $e->getMessage();
    if (method_exists($e, 'getPrevious') && $e->getPrevious()) {
        $details = $e->getPrevious()->getMessage();
    }
    error_response(500, 'DATABASE_ERROR', 'No se pudo conectar a la base de datos.', $details);
}

// Instanciar los controladores
$authController = new AuthController($db);
$clienteController = new ClienteController($db);
$prestamoController = new PrestamoController($db);
$pagoController = new PagoController($db);
$dashboardController = new DashboardController($db);
$empleadoController = new EmpleadoController($db);

// --- Funciones auxiliares de seguridad ---
function is_authenticated() {
    return isset($_SESSION['user_id']);
}

function has_permission($required_roles) {
    if (!is_authenticated()) return false;
    $user_role = $_SESSION['user_role'];
    return in_array($user_role, $required_roles);
}

function deny_access($message = 'Acceso denegado.') {
    http_response_code(403);
    echo json_encode(['message' => $message]);
    exit();
}

// --- Enrutador ---
$action = isset($_GET['action']) ? $_GET['action'] : '';
$rawBody = file_get_contents("php://input");
$data = json_decode($rawBody);

try {
switch ($action) {
    // --- Rutas Públicas ---
    case 'health':
        try {
            $stmt = $db->query('SELECT 1');
            $db_ok = ($stmt && $stmt->fetchColumn() == 1);
            respond(200, [
                'status' => 'ok',
                'db' => $db_ok ? 'up' : 'down'
            ]);
        } catch (Throwable $e) {
            error_response(500, 'HEALTH_DB_ERROR', 'Fallo al verificar la base de datos.', $e->getMessage());
        }
        break;
    case 'login':
        // Método debe ser POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Allow: POST');
            error_response(405, 'METHOD_NOT_ALLOWED', 'El login requiere método POST.');
        }

        // Content-Type debe ser JSON
        $contentType = $_SERVER['CONTENT_TYPE'] ?? ($_SERVER['HTTP_CONTENT_TYPE'] ?? '');
        if (stripos($contentType, 'application/json') === false) {
            error_response(415, 'UNSUPPORTED_MEDIA_TYPE', 'Se requiere Content-Type: application/json.');
        }

        // Validar JSON
        $payloadAssoc = json_decode($rawBody, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_response(400, 'INVALID_JSON', 'JSON inválido en el cuerpo de la solicitud.', json_last_error_msg());
        }

        $authController->login($payloadAssoc);
        break;
    case 'logout':
        $authController->logout();
        break;
    case 'get_session':
        if (is_authenticated()) {
            http_response_code(200);
            echo json_encode([
                'id' => $_SESSION['user_id'],
                'nombre' => $_SESSION['user_name'],
                'rol' => $_SESSION['user_role']
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'No autenticado.']);
        }
        break;
    // --- Rutas de Dashboard ---
    case 'get_dashboard_summary':
        $dashboardController->getSummary();
        break;
    case 'get_cliente':
        if (!has_permission(['Admin', 'Gerente', 'Servicio al Cliente', 'Cajero'])) deny_access();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $clienteController->get($id);
        break;
    case 'create_cliente':
        if (!has_permission(['Admin', 'Servicio al Cliente'])) deny_access();
        $clienteController->create($data);
        break;
    case 'update_cliente':
        if (!has_permission(['Admin', 'Gerente', 'Servicio al Cliente'])) deny_access();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $clienteController->update($id, $data);
        break;

    // --- Rutas de Préstamos ---
    case 'list_prestamos':
        if (!has_permission(['Admin', 'Gerente', 'Servicio al Cliente'])) deny_access();
        $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
        $prestamoController->list($filter);
        break;
    case 'create_prestamo':
        if (!has_permission(['Admin', 'Servicio al Cliente'])) deny_access('No tiene permisos para crear préstamos.');
        $prestamoController->create($data);
        break;
    case 'update_prestamo_status':
        if (!has_permission(['Admin', 'Gerente'])) deny_access('No tiene permisos para cambiar el estado de un préstamo.');
        $prestamoController->updateStatus($data);
        break;

    // --- Rutas de Pagos ---
    case 'create_pago':
        if (!has_permission(['Admin', 'Cajero'])) deny_access('No tiene permisos para registrar pagos.');
        $pagoController->create($data);
        break;

    // --- Rutas de Empleados ---
    case 'list_empleados':
        if (!has_permission(['Admin'])) deny_access();
        $empleadoController->list();
        break;

    default:
        respond(404, ['message' => 'Acción no encontrada.']);
        break;
}
} catch (Throwable $e) {
    // Manejo de errores inesperados (incluidos de base de datos)
    error_response(500, 'INTERNAL_SERVER_ERROR', 'Ha ocurrido un error interno al procesar la solicitud.', $e->getMessage());
}
?>

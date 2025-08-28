<?php
// public_html/auth.php - API dedicada a autenticación

session_start();

// Encabezados comunes
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Helpers
function env_val($key, $default = null) { $v = getenv($key); return $v === false ? $default : $v; }
function app_debug() {
    if (defined('APP_DEBUG')) return (bool) APP_DEBUG;
    $v = strtolower((string) env_val('APP_DEBUG', 'false'));
    return in_array($v, ['1','true','yes','on']);
}
function respond($status, $payload) { http_response_code($status); echo json_encode($payload); exit(); }
function error_response($status, $code, $message, $details = null, $extra = []) {
    $resp = array_merge(['error' => $code, 'message' => $message], $extra);
    if ($details !== null && app_debug()) $resp['details'] = $details;
    respond($status, $resp);
}

require_once __DIR__ . '/../private/config/database.php';
require_once __DIR__ . '/../private/controllers/AuthController.php';

// Conexión DB
try {
    $database = new Database();
    $db = $database->connect();
} catch (Throwable $e) {
    $details = $e->getMessage();
    if (method_exists($e, 'getPrevious') && $e->getPrevious()) $details = $e->getPrevious()->getMessage();
    error_response(500, 'DATABASE_ERROR', 'No se pudo conectar a la base de datos.', $details);
}

$auth = new AuthController($db);

$action = isset($_GET['action']) ? $_GET['action'] : '';
$rawBody = file_get_contents("php://input");

try {
    switch ($action) {
        case 'health':
            try {
                $ok = $db->query('SELECT 1')->fetchColumn() == 1;
                respond(200, ['status' => 'ok', 'db' => $ok ? 'up' : 'down']);
            } catch (Throwable $e) {
                error_response(500, 'HEALTH_DB_ERROR', 'Fallo al verificar la base de datos.', $e->getMessage());
            }
            break;

        case 'login':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Allow: POST');
                error_response(405, 'METHOD_NOT_ALLOWED', 'El login requiere método POST.');
            }
            $ct = $_SERVER['CONTENT_TYPE'] ?? ($_SERVER['HTTP_CONTENT_TYPE'] ?? '');
            if (stripos($ct, 'application/json') === false) {
                error_response(415, 'UNSUPPORTED_MEDIA_TYPE', 'Se requiere Content-Type: application/json.');
            }
            $payloadAssoc = json_decode($rawBody, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_response(400, 'INVALID_JSON', 'JSON inválido en el cuerpo de la solicitud.', json_last_error_msg());
            }
            $auth->login($payloadAssoc);
            break;

        case 'logout':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Allow: POST');
                error_response(405, 'METHOD_NOT_ALLOWED', 'El logout requiere método POST.');
            }
            $auth->logout();
            break;

        case 'get_session':
            if (isset($_SESSION['user_id'])) {
                respond(200, [
                    'id' => $_SESSION['user_id'],
                    'nombre' => $_SESSION['user_name'],
                    'rol' => $_SESSION['user_role']
                ]);
            }
            respond(401, ['message' => 'No autenticado.']);
            break;

        default:
            respond(404, ['message' => 'Acción no encontrada.']);
    }
} catch (Throwable $e) {
    error_response(500, 'INTERNAL_SERVER_ERROR', 'Ha ocurrido un error interno al procesar la solicitud.', $e->getMessage());
}
?>


<?php
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {
    private $db;
    private $cliente;

    public function __construct($db) {
        $this->db = $db;
        $this->cliente = new Cliente($this->db);
    }

    // Buscar clientes
    public function search($term) {
        $result = $this->cliente->search($term);
        $clientes_arr = array();
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            array_push($clientes_arr, $row);
        }
        echo json_encode($clientes_arr);
    }

    // Obtener un cliente por ID
    public function get($id) {
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['message' => 'ID de cliente no proporcionado.']);
            return;
        }
        $cliente_data = $this->cliente->getOne($id);
        if ($cliente_data) {
            http_response_code(200);
            echo json_encode($cliente_data);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Cliente no encontrado.']);
        }
    }

    // Crear un nuevo cliente
    public function create($data) {
        // Asegurar que $data sea un objeto para evitar errores de acceso a propiedades
        if ($data === null) {
            http_response_code(400);
            echo json_encode(['message' => 'Cuerpo de la solicitud inválido. Se esperaba JSON.']);
            return;
        }
        if (!is_object($data)) {
            $data = (object)$data;
        }

        if (empty($data->nombre_completo) || empty($data->cedula)) {
            http_response_code(400);
            echo json_encode(['message' => 'Nombre y cédula son requeridos.']);
            return;
        }

        $this->cliente->nombre_completo = $data->nombre_completo;
        $this->cliente->cedula = $data->cedula;
        $this->cliente->telefono = $data->telefono ?? null;
        $this->cliente->direccion = $data->direccion ?? null;
        $this->cliente->creado_por_empleado_id = $_SESSION['user_id'] ?? null;

        try {
            if ($this->cliente->create()) {
                http_response_code(201);
                echo json_encode([
                    'message' => 'Cliente creado exitosamente.',
                    'cliente_id' => $this->cliente->id
                ]);
                return;
            }
            http_response_code(400);
            echo json_encode(['message' => 'No se pudo crear el cliente.']);
        } catch (PDOException $e) {
            $code = $e->getCode();
            $msg = $e->getMessage();
            if ($code === '23000') { // violación de integridad (duplicado/FK)
                if (stripos($msg, 'cedula') !== false || stripos($msg, 'UNIQUE') !== false || stripos($msg, 'Duplicate') !== false) {
                    http_response_code(409);
                    echo json_encode(['message' => 'Ya existe un cliente con esta cédula.']);
                    return;
                }
                if (stripos($msg, 'creado_por_empleado_id') !== false || stripos($msg, 'foreign key') !== false) {
                    http_response_code(400);
                    echo json_encode(['message' => 'No se pudo asociar el cliente al empleado actual. Inicie sesión nuevamente.']);
                    return;
                }
            }
            http_response_code(500);
            echo json_encode(['message' => 'Error al crear el cliente.']);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Error inesperado al crear el cliente.']);
        }
    }

    // Actualizar un cliente
    public function update($id, $data) {
        if ($data === null) {
            http_response_code(400);
            echo json_encode(['message' => 'Cuerpo de la solicitud inválido. Se esperaba JSON.']);
            return;
        }
        if (!is_object($data)) {
            $data = (object)$data;
        }

        if (empty($id) || empty($data->nombre_completo) || empty($data->cedula)) {
            http_response_code(400);
            echo json_encode(['message' => 'ID, nombre y cédula son requeridos.']);
            return;
        }

        $this->cliente->id = $id;
        $this->cliente->nombre_completo = $data->nombre_completo;
        $this->cliente->cedula = $data->cedula;
        $this->cliente->telefono = $data->telefono ?? null;
        $this->cliente->direccion = $data->direccion ?? null;

        try {
            if ($this->cliente->update()) {
                http_response_code(200);
                echo json_encode(['message' => 'Cliente actualizado exitosamente.']);
                return;
            }
            http_response_code(400);
            echo json_encode(['message' => 'No se pudo actualizar el cliente.']);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $msg = $e->getMessage();
                if (stripos($msg, 'cedula') !== false || stripos($msg, 'UNIQUE') !== false || stripos($msg, 'Duplicate') !== false) {
                    http_response_code(409);
                    echo json_encode(['message' => 'Ya existe otro cliente con esta cédula.']);
                    return;
                }
            }
            http_response_code(500);
            echo json_encode(['message' => 'Error al actualizar el cliente.']);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Error inesperado al actualizar el cliente.']);
        }
    }
}
?>

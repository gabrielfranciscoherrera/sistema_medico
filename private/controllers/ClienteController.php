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
        if (empty($data->nombre_completo) || empty($data->cedula)) {
            http_response_code(400);
            echo json_encode(['message' => 'Nombre y cédula son requeridos.']);
            return;
        }

        $this->cliente->nombre_completo = $data->nombre_completo;
        $this->cliente->cedula = $data->cedula;
        $this->cliente->telefono = $data->telefono ?? null;
        $this->cliente->direccion = $data->direccion ?? null;
        $this->cliente->creado_por_empleado_id = $_SESSION['user_id'];

        if ($this->cliente->create()) {
            http_response_code(201);
            echo json_encode([
                'message' => 'Cliente creado exitosamente.',
                'cliente_id' => $this->cliente->id
            ]);
        } else {
            http_response_code(503);
            echo json_encode(['message' => 'No se pudo crear el cliente. La cédula ya podría existir.']);
        }
    }

    // Actualizar un cliente
    public function update($id, $data) {
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

        if ($this->cliente->update()) {
            http_response_code(200);
            echo json_encode(['message' => 'Cliente actualizado exitosamente.']);
        } else {
            http_response_code(503);
            echo json_encode(['message' => 'No se pudo actualizar el cliente.']);
        }
    }
}
?>
<?php
require_once __DIR__ . "/../Models/ClientesModel.php";

class ClienteController {
    private $model;

    public function __construct() {
        $this->model = new ClientesModel();
    }

    public function index() {
        $productos = $this->model->getAllClientes();
        require_once __DIR__ . "/../Views/ListaClientes.php";
    }

    public function getAllJson() {
        header("Content-Type: application/json");
        $productos = $this->model->getAllClientes();

        if (!empty($productos)) {
            echo json_encode(["status" => "success", "data" => $productos]);
        } else {
            echo json_encode(["status" => "error", "message" => "No hay datos disponibles."]);
        }
        exit();
    }

    public function getAllJsonSearch() {
        header("Content-Type: application/json"); 
    
        $filtros = [
            'nombre' => $_GET['nombre'] ?? '',
            'email' => $_GET['email'] ?? '',
            'empresa' => $_GET['empresa'] ?? '',
            'estado' => $_GET['estado'] ?? ''
        ];
    
        $clientes = $this->model->getClientesFiltrados($filtros);
    
        echo json_encode(["status" => "success", "data" => $clientes]);
        exit();
    }
    
    public function create() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nombre = $_POST["nombre"] ?? null;
            $email = $_POST["email"] ?? null;
            $estado_id = $_POST["estado"] ?? null;
            $empresa = $_POST["empresa"] ?? null;
            $cedula = $_POST["cedula"] ?? null;
            $telefono = $_POST["telefono"] ?? null;
            $direccion = $_POST["direccion"] ?? null;

            if (!$nombre || !$email || !$estado_id) {
                echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios"]);
                exit();
            }

            $result = $this->model->createCliente($nombre, $email, $estado_id, $empresa, $cedula, $telefono, $direccion);

            echo json_encode($result);
        }
    }

    public function updateCliente() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = $_POST["id_cliente"] ?? null;
            $nombre = $_POST["nombre"] ?? null;
            $cedula = $_POST["cedula"] ?? null;
            $email = $_POST["email"] ?? null;
            $estado_id = $_POST["estado"] ?? null;
            $empresa = $_POST["empresa"] ?? null;
            $telefono = $_POST["telefono"] ?? null;
            $direccion = $_POST["direccion"] ?? null;
    
            if (!$id || !$nombre || !$email || !$estado_id || !$cedula) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Faltan datos obligatorios"
                ]);
                exit();
            }
    
            $result = $this->model->updateCliente(
                $id,
                $nombre,
                $email,
                $estado_id,
                $empresa,
                $cedula,
                $telefono,
                $direccion
            );
    
            if (is_array($result)) {
                echo json_encode($result);
            } else if ($result === true) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Cliente actualizado correctamente."
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Error desconocido al actualizar el cliente."
                ]);
            }
        }
    }
    
    public function getClienteById() {
        if (!isset($_GET["id"])) {
            echo json_encode(["status" => "error", "message" => "ID de cliente no proporcionado"]);
            exit();
        }
    
        $clienteID = $_GET["id"];
        $clientes = $this->model->getClienteById($clienteID);
    
        if ($clientes) {
            echo json_encode(["status" => "success", "data" => $clientes]);
        } else {
            echo json_encode(["status" => "error", "message" => "Cliente no encontrado"]);
        }
        exit();
    }
    
    public function deleteCliente() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = $_POST["id"] ?? null;
    
            if (!$id) {
                echo json_encode(["status" => "error", "message" => "ID de cliente no proporcionado."]);
                exit();
            }
    
            $result = $this->model->deleteClienteById($id);
            echo json_encode($result);
            exit();
        }
    }
    
    public function actualizarFoto() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioId = $_POST['usuario_id'] ?? null;
            
            if (!$usuarioId || !isset($_FILES['foto_perfil']) || $_FILES['foto_perfil']['error'] !== 0) {
                echo json_encode(["status" => "error", "message" => "Faltan datos o archivo inválido"]);
                exit();
            }
            
            $nombreArchivo = uniqid() . '_' . basename($_FILES['foto_perfil']['name']);
            $rutaDestino = __DIR__ . '/../../uploads/usuarios/' . $nombreArchivo;

            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $rutaDestino)) {
                $this->model->actualizarFotoUsuario($usuarioId, $nombreArchivo);
                header("Location: /Kima/app/Views/clientes.php?id=$usuarioId");
                exit();
            } else {
                echo json_encode(["status" => "error", "message" => "Error al mover la imagen"]);
                exit();
            }
        }
    }
}

$action = $_GET["action"] ?? "index";
error_log("Acción recibida: $action");

$controller = new ClienteController();

if (method_exists($controller, $action)) {
    $controller->$action();
    exit();
} else {
    echo json_encode(["status" => "error", "message" => "Acción no válida."]);
    exit();
}
?>

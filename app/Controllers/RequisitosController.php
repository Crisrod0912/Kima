<?php
require_once __DIR__ . "/../Models/RequisitosModel.php";

class RequisitosController {
    private $model;

    public function __construct() {
        $this->model = new RequisitosModel();
    }

    public function index() {
        $productos = $this->model->getAll();
        require_once __DIR__ . "/../Views/Requisitos.php";
    }

    public function getAllJson() {
        header("Content-Type: application/json");
        $requisitos = $this->model->getAll();

        if (!empty($requisitos)) {
            echo json_encode(["status" => "success", "data" => $requisitos]);
        } else {
            echo json_encode(["status" => "error", "message" => "No hay datos disponibles."]);
        }
        exit();
    }

    public function create() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            header("Content-Type: application/json");
    
            $descripcion = $_POST["descripcion"] ?? null;
            $categoria = $_POST["categoria"] ?? null;
            $prioridad = $_POST["prioridad"] ?? null;
    
            if (!$descripcion || !$categoria || !$prioridad) {
                echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios."]);
                exit();
            }
    
            if ($this->model->create($descripcion, $categoria, $prioridad)) {
                echo json_encode(["status" => "success", "message" => "Requisito creado correctamente."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al crear el requisito."]);
            }
            exit();
        }
    }
    
    public function update() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            header("Content-Type: application/json");
    
            $id = $_POST["id_requisito"] ?? null;
            $descripcion = $_POST["descripcion"] ?? null;
            $categoria = $_POST["categoria"] ?? null;
            $prioridad = $_POST["prioridad"] ?? null;
    
            if (!$id || !$descripcion || !$categoria || !$prioridad) {
                echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios para actualizar."]);
                exit();
            }
    
            if ($this->model->update($id, $descripcion, $categoria, $prioridad)) {
                echo json_encode(["status" => "success", "message" => "Requisito actualizado correctamente."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al actualizar el requisito."]);
            }
            exit();
        }
    }
    
    public function delete() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = $_POST["id_requisito"] ?? null;
    
            if (!$id) {
                echo json_encode(["status" => "error", "message" => "ID no proporcionado"]);
                exit();
            }
    
            if ($this->model->delete($id)) {
                echo json_encode(["status" => "success", "message" => "Requisito eliminado correctamente."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al eliminar el requisito."]);
            }
            exit();
        }
    }
    
    public function getCategorias() {
        header("Content-Type: application/json");
        $categorias = $this->model->getCategorias();
        echo json_encode(["status" => "success", "data" => $categorias]);
        exit();
    }
    
    public function getPrioridades() {
        header("Content-Type: application/json");
        $prioridades = $this->model->getPrioridades();
        echo json_encode(["status" => "success", "data" => $prioridades]);
        exit();
    }
}

$action = $_GET["action"] ?? "index";
error_log("Acción recibida: $action");

$controller = new RequisitosController();

if (method_exists($controller, $action)) {
    $controller->$action();
    exit();
} else {
    echo json_encode(["status" => "error", "message" => "Acción no válida."]);
    exit();
}
?>

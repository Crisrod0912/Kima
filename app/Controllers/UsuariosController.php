<?php
require_once __DIR__ . "/../Models/UsuarioModel.php";

class UsuarioController {
    private $model;

    public function __construct() {
        $this->model = new UsuarioModel();
    }

    public function getAllJson() {
        header("Content-Type: application/json");
        $productos = $this->model->getAllUsuarios();

        if (!empty($productos)) {
            echo json_encode(["status" => "success", "data" => $productos]);
        } else {
            echo json_encode(["status" => "error", "message" => "No hay datos disponibles."]);
        }
        exit();
    }

    public function create() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nombre = $_POST["nombre"] ?? null;
            $email = $_POST["email"] ?? null;
            $password = $_POST["password"] ?? null;
            $estado_id = $_POST["estado"] ?? null;
            $rol = $_POST["rol"] ?? null;
    
            if (!$nombre || !$email || !$estado_id || !$rol || !$password) {
                echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios"]);
                exit();
            }
    
            $result = $this->model->createUsuario($nombre, $email, $estado_id, $password, $rol);
    
            if (is_array($result)) {
                echo json_encode($result);
            } else if ($result === true) {
                echo json_encode(["status" => "success", "message" => "Usuario agregado correctamente."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al agregar el usuario."]);
            }
        }
    }

    public function getUserById() {
        if (!isset($_GET["id"])) {
            echo json_encode(["status" => "error", "message" => "ID de usuario no proporcionado"]);
            exit();
        }
    
        $usuarioID = $_GET["id"];
        $users = $this->model->getUserById($usuarioID);
    
        if ($users) {
            echo json_encode(["status" => "success", "data" => $users]);
        } else {
            echo json_encode(["status" => "error", "message" => "Usuario no encontrado"]);
        }
        exit();
    }

    public function updateUsuario() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = $_POST["id"] ?? null;
            $nombre = $_POST["nombre"] ?? null;
            $email = $_POST["email"] ?? null;
            $rol = $_POST["rol"] ?? null;
            $estado_id = $_POST["estado_id"] ?? null;
            $password = $_POST["password"] ?? null;
    
            if (!$id || !$nombre || !$email || !$rol || !$estado_id) {
                echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios"]);
                exit();
            }
    
            $this->model->updateUsuario($id, $nombre, $email, $rol, $estado_id, $password);
            echo json_encode(["status" => "success", "message" => "Usuario actualizado correctamente."]);
        }
    }

    public function deleteUser() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = $_POST["id"] ?? null;
    
            if (!$id) {
                echo json_encode(["status" => "error", "message" => "ID de usuario no proporcionado."]);
                exit();
            }
    
            $result = $this->model->deleteUserById($id);
    
            if ($result) {
                echo json_encode(["status" => "success", "message" => "Usuario eliminado correctamente."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al eliminar el usuario."]);
            }
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
                header("Location: /Kima/app/Views/usuarios_perfil.php?id=$usuarioId");
                exit();
            } else {
                echo json_encode(["status" => "error", "message" => "Error al mover la imagen"]);
                exit();
            }
        }
    }

    public function cambiarTema() {
        session_start();

        $darkmode = isset($_POST['darkmode']) ? 1 : 0;
        $usuarioId = $_SESSION['usuario_id'] ?? null;

        if (!$usuarioId) {
            echo json_encode(["status" => "error", "message" => "Usuario no autenticado"]);
            exit();
        }

        $this->model->actualizarTemaUsuario($usuarioId, $darkmode);

        $_SESSION['darkmode'] = $darkmode;

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    public function cambiarPassword() {
        session_start();
        $usuarioId = $_SESSION['usuario_id'] ?? null;
        
        if (!$usuarioId) {
            echo json_encode(["status" => "error", "message" => "Usuario no autenticado"]);
            exit();
        }
        
        $nueva = $_POST["nueva"] ?? null;
        $confirmar = $_POST["confirmar"] ?? null;
        
        if (!$nueva || !$confirmar) {
            echo json_encode(["status" => "error", "message" => "Faltan campos obligatorios"]);
            exit();
        }
        
        if ($nueva !== $confirmar) {
            echo json_encode(["status" => "error", "message" => "Las contraseñas no coinciden"]);
            exit();
        }
        
        $resultado = $this->model->actualizarPassword($usuarioId, $nueva);
        
        if ($resultado) {
            echo json_encode(["status" => "success", "message" => "Contraseña actualizada correctamente"]);
        } else {
            echo json_encode(["status" => "error", "message" => "No se pudo actualizar la contraseña"]);
        }
        exit();
    }
}

$action = $_GET["action"] ?? "index";
error_log("Acción recibida: $action");

$controller = new UsuarioController();

if (method_exists($controller, $action)) {
    $controller->$action();
    exit();
} else {
    echo json_encode(["status" => "error", "message" => "Acción no válida."]);
    exit();
}
?>

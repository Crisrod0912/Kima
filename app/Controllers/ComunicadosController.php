<?php
require_once __DIR__ . "/../Models/ComunicadosModel.php";

class ComunicadosController {
    private $model;

    public function __construct() {
        $this->model = new ComunicadosModel();
    }

    public function obtenerComunicados() {
        $data = $this->model->obtenerComunicados();
        echo json_encode(["status" => "success", "data" => $data]);
        exit();
    }

    public function subirArchivo() {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["archivo"])) {
            $archivo = $_FILES["archivo"];
            $carpeta_id = $_POST["carpeta_id"] ?? null;
    
            $ext = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));
            if ($archivo["type"] !== "application/pdf" || $ext !== 'pdf') {
                echo json_encode(["status" => "error", "message" => "Solo se permiten archivos PDF"]);
                exit();
            }
    
            $uploadBaseDir = __DIR__ . "/../../public";
            $uploadDir = "/uploads_comunicados/";
    
            if ($carpeta_id) {
                $rutaPublicaCarpeta = $this->model->obtenerRutaCarpetaPorID($carpeta_id);
    
                if (!$rutaPublicaCarpeta) {
                    echo json_encode(["status" => "error", "message" => "Carpeta no encontrada"]);
                    exit();
                }
    
                $uploadDir = str_replace("/Kima/public", "", $rutaPublicaCarpeta); 
            }
    
            $uploadPath = $uploadBaseDir . $uploadDir;
    
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
    
            $nombreArchivo = basename($archivo["name"]);
            $rutaDestino = $uploadPath . $nombreArchivo;
    
            if (move_uploaded_file($archivo["tmp_name"], $rutaDestino)) {
                $rutaPublica = "/Kima/public" . $uploadDir . $nombreArchivo;
    
                $this->model->agregarArchivo($nombreArchivo, $rutaPublica, $archivo["size"], $carpeta_id);
                echo json_encode(["status" => "success", "message" => "Archivo subido correctamente"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al mover el archivo"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "No se recibió archivo"]);
        }
        exit();
    }
    
    public function crearCarpeta() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nombre = $_POST["nombre"] ?? null;
            if ($nombre) {
                $this->model->crearCarpeta($nombre);
                echo json_encode(["status" => "success", "message" => "Carpeta creada"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Nombre no proporcionado"]);
            }
        }
        exit();
    }

    public function eliminarComunicado() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = isset($_POST["id"]) ? $_POST["id"] : null;
            $tipo = isset($_POST["tipo"]) ? $_POST["tipo"] : "archivo"; 
    
            if ($id && $tipo) {
                if ($this->model->eliminarComunicado($id, $tipo)) {
                    echo json_encode(["status" => "success", "message" => "Eliminado correctamente"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "No se pudo eliminar el registro"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Datos inválidos"]);
            }
        }
        exit();
    }

    public function obtenerInfoArchivo() {
        $id = $_GET["id"] ?? null;
    
        if ($id) {
            $info = $this->model->obtenerArchivoConCarpeta($id);
            if ($info) {
                echo json_encode(["status" => "success", "data" => $info]);
            } else {
                echo json_encode(["status" => "error", "message" => "Archivo no encontrado"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "ID no proporcionado"]);
        }
        exit();
    }

    public function renombrarComunicado() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = $_POST["id"] ?? null;
            $nuevo_nombre = $_POST["nombre"] ?? null;
            $tipo = $_POST["tipo"] ?? "archivo";
    
            if ($id && $nuevo_nombre && $tipo) {
                if ($this->model->renombrarComunicado($id, $nuevo_nombre, $tipo)) {
                    echo json_encode(["status" => "success", "message" => "Nombre actualizado"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "No se pudo actualizar"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Datos inválidos"]);
            }
        }
        exit();
    }

    public function crearCarpetaConArchivos() {
        $nombre = $_POST["nombre"] ?? null;
        $archivosSeleccionados = $_POST["archivos"] ?? [];
    
        if (!$nombre) {
            echo json_encode(["status" => "error", "message" => "Nombre no proporcionado"]);
            return;
        }
    
        $carpetaId = $this->model->crearCarpetaYObtenerID($nombre);
    
        if (!$carpetaId) {
            echo json_encode(["status" => "error", "message" => "No se pudo crear la carpeta"]);
            return;
        }
    
        if (!empty($archivosSeleccionados)) {
            $this->model->moverYAsociarArchivosACarpeta($archivosSeleccionados, $carpetaId);

        }
    
        echo json_encode(["status" => "success", "message" => "Carpeta creada y archivos movidos", "carpeta_id" => $carpetaId]);
    }
    
    public function listarArchivos() {
        $archivos = $this->model->obtenerTodosLosArchivos();
        echo json_encode(["status" => "success", "data" => $archivos]);
        exit();
    }

    public function obtenerCarpetasConArchivos() {
        $data = $this->model->obtenerCarpetasConArchivos();
        echo json_encode(["status" => "success", "data" => $data]);
    }

    public function relacionarArchivoACarpeta() {
        $archivoId = $_POST['archivo_id'] ?? null;
        $carpetaId = $_POST['carpeta_id'] ?? null;
    
        if ($archivoId && $carpetaId) {
            $this->model->moverYAsociarArchivosACarpeta([$archivoId], $carpetaId);
            echo json_encode(["status" => "success", "message" => "Archivo relacionado"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
        }
        exit();
    }   
}

$action = $_GET["action"] ?? "obtenerComunicados";
$controller = new ComunicadosController();

if (method_exists($controller, $action)) {
    $controller->$action();
    exit();
} else {
    echo json_encode(["status" => "error", "message" => "Acción no válida."]);
    exit();
}
?>

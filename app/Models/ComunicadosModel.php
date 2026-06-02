<?php
require_once __DIR__ . "/../../config/database.php";

class ComunicadosModel {
    private $conn;

    public function __construct() {
        global $conn; 
        $this->conn = $conn;
    }

    public function obtenerComunicados() {
        $sql = "SELECT id, nombre, tamaño, ruta, carpeta_id, FORMAT(fecha_modificacion, 'dd MMM yyyy, hh:mm tt') AS fecha_modificacion 
                FROM dbo.archivos 
                ORDER BY fecha_modificacion DESC;
               ";
                
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregarArchivo($nombre, $ruta, $tamaño, $carpeta_id = null) {
        $sql = "INSERT INTO dbo.archivos (nombre, ruta, tamaño, carpeta_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$nombre, $ruta, $tamaño, $carpeta_id]);
    }

    public function crearCarpeta($nombre) {
        $sql = "INSERT INTO dbo.carpetas (nombre) VALUES (?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$nombre]);
    }

    public function eliminarComunicado($id, $tipo) {
        if ($tipo === "archivo") {
            $stmt = $this->conn->prepare("SELECT ruta FROM dbo.archivos WHERE id = ?");
            $stmt->execute([$id]);
            $archivo = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($archivo && isset($archivo['ruta'])) {
                $rutaFisica = $_SERVER['DOCUMENT_ROOT'] . $archivo['ruta'];
    
                if (file_exists($rutaFisica)) {
                    unlink($rutaFisica);
                }
            }
    
            $stmtDelete = $this->conn->prepare("DELETE FROM dbo.archivos WHERE id = ?");
            return $stmtDelete->execute([$id]);

        } else if ($tipo === "carpeta") {
            try {
                $this->conn->beginTransaction();
    
                $stmtRuta = $this->conn->prepare("SELECT ruta FROM dbo.carpetas WHERE id = ?");
                $stmtRuta->execute([$id]);
                $carpeta = $stmtRuta->fetch(PDO::FETCH_ASSOC);
    
                if ($carpeta && isset($carpeta['ruta'])) {
                    $rutaFisicaCarpeta = $_SERVER['DOCUMENT_ROOT'] . $carpeta['ruta'];
                    if (is_dir($rutaFisicaCarpeta)) {
                        $archivos = glob($rutaFisicaCarpeta . '*');
                        foreach ($archivos as $archivo) {
                            if (is_file($archivo)) {
                                unlink($archivo);
                            }
                        }
                        rmdir($rutaFisicaCarpeta);
                    }
                }
    
                $stmtArchivos = $this->conn->prepare("DELETE FROM dbo.archivos WHERE carpeta_id = ?");
                $stmtArchivos->execute([$id]);
    
                $stmtCarpeta = $this->conn->prepare("DELETE FROM dbo.carpetas WHERE id = ?");
                $stmtCarpeta->execute([$id]);
    
                $this->conn->commit();
                return true;
    
            } catch (PDOException $e) {
                $this->conn->rollBack();
                return false;
            }
        }
    
        return false;
    }

    public function obtenerArchivoConCarpeta($idArchivo) {
        $sql = "SELECT a.nombre AS archivo_nombre, c.nombre AS carpeta_nombre
                FROM dbo.archivos a
                LEFT JOIN dbo.carpetas c ON a.carpeta_id = c.id
                WHERE a.id = ?
               ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idArchivo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function renombrarComunicado($id, $nuevo_nombre, $tipo) {
        if ($tipo === "archivo") {
            $sql = "UPDATE dbo.archivos SET nombre = ? WHERE id = ?";
        } else {
            $sql = "UPDATE dbo.carpetas SET nombre = ? WHERE id = ?";
        }
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$nuevo_nombre, $id]);
    }

    public function crearCarpetaYObtenerID($nombre) {
        $nombreCarpeta = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $nombre);
        $rutaFisica = __DIR__ . "/../../public/uploads_comunicados/{$nombreCarpeta}";
        $rutaPublica = "/Kima/public/uploads_comunicados/{$nombreCarpeta}/";
    
        if (!is_dir($rutaFisica)) {
            mkdir($rutaFisica, 0777, true);
        }
    
        $sql = "INSERT INTO dbo.carpetas (nombre, ruta) OUTPUT INSERTED.ID VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);

        if ($stmt->execute([$nombre, $rutaPublica])) {
            return $stmt->fetchColumn();
        }
    
        return false;
    }

    public function asociarArchivosACarpeta($archivoIds, $carpetaId) {
        $stmtRuta = $this->conn->prepare("SELECT ruta FROM dbo.carpetas WHERE id = ?");
        $stmtRuta->execute([$carpetaId]);
        $carpeta = $stmtRuta->fetch(PDO::FETCH_ASSOC);
        $nuevaRuta = $carpeta["ruta"];
        
        $stmtArchivos = $this->conn->prepare("SELECT id, nombre, ruta FROM dbo.archivos WHERE id = ?");
        $stmtUpdate = $this->conn->prepare("UPDATE dbo.archivos SET ruta = ?, carpeta_id = ? WHERE id = ?");
        
        foreach ($archivoIds as $id) {
            $stmtArchivos->execute([$id]);
            $archivo = $stmtArchivos->fetch(PDO::FETCH_ASSOC);
            
            $rutaActual = $_SERVER['DOCUMENT_ROOT'] . $archivo["ruta"];
            $nuevaRutaFisica = $_SERVER['DOCUMENT_ROOT'] . $nuevaRuta . "/" . $archivo["nombre"];
            $nuevaRutaWeb = $nuevaRuta . "/" . $archivo["nombre"];
            
            if (file_exists($rutaActual)) {
                rename($rutaActual, $nuevaRutaFisica);
            }
            
            $stmtUpdate->execute([$nuevaRutaWeb, $carpetaId, $id]);
        }
    }
    
    public function moverYAsociarArchivosACarpeta($archivoIds, $carpetaId) {
        $stmtRuta = $this->conn->prepare("SELECT ruta FROM dbo.carpetas WHERE id = ?");
        $stmtRuta->execute([$carpetaId]);
        $carpeta = $stmtRuta->fetch(PDO::FETCH_ASSOC);
        
        if (!$carpeta) return;
        
        $rutaPublicaNueva = $carpeta["ruta"];
        $rutaFisicaBase = realpath(__DIR__ . "/../../public");
        
        foreach ($archivoIds as $id) {
            $stmtArchivo = $this->conn->prepare("SELECT nombre, ruta FROM dbo.archivos WHERE id = ?");
            $stmtArchivo->execute([$id]);
            $archivo = $stmtArchivo->fetch(PDO::FETCH_ASSOC);
            
            if ($archivo) {
                $nombreArchivo = $archivo["nombre"];
                
                $rutaActualFisica = $rutaFisicaBase . str_replace("/Kima/public", "", $archivo["ruta"]);
                
                $rutaNuevaFisica = $rutaFisicaBase . str_replace("/Kima/public", "", $rutaPublicaNueva) . $nombreArchivo;
                
                $directorioDestino = dirname($rutaNuevaFisica);
                if (!is_dir($directorioDestino)) {
                    mkdir($directorioDestino, 0777, true);
                }
                
                if (file_exists($rutaActualFisica)) {
                    rename($rutaActualFisica, $rutaNuevaFisica);
                }
                
                $nuevaRutaDB = $rutaPublicaNueva . $nombreArchivo;
                $stmtUpdate = $this->conn->prepare("UPDATE dbo.archivos SET carpeta_id = ?, ruta = ? WHERE id = ?");
                $stmtUpdate->execute([$carpetaId, $nuevaRutaDB, $id]);
            }
        }
    }
    
    public function obtenerRutaCarpetaPorID($carpetaId) {
        $stmt = $this->conn->prepare("SELECT ruta FROM dbo.carpetas WHERE id = ?");
        $stmt->execute([$carpetaId]);
        $carpeta = $stmt->fetch(PDO::FETCH_ASSOC);
        return $carpeta ? $carpeta["ruta"] : false;
    }
    
    public function obtenerTodosLosArchivos() {
        $sql = "SELECT id, nombre FROM dbo.archivos WHERE carpeta_id IS NULL ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerCarpetasConArchivos() {
        $carpetas = $this->conn->query("SELECT id, nombre, fecha_creacion, ruta FROM dbo.carpetas ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($carpetas as &$carpeta) {
            $stmt = $this->conn->prepare("SELECT nombre, ruta FROM dbo.archivos WHERE carpeta_id = ?");
            $stmt->execute([$carpeta['id']]);
            $carpeta['archivos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    
        return $carpetas;
    }
}
?>

<?php
require_once __DIR__ . "/../../config/database.php";

class CotizacionesModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
   
    public function buscarClientes($busqueda) {
        $sql = "SELECT * FROM dbo.clientes WHERE nombre LIKE ? OR empresa LIKE ?";
        $query = $this->conn->prepare($sql);
        $param = "%$busqueda%";
        $query->execute([$param, $param]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarProductos($busqueda) {
        $sql = "SELECT * FROM TiposProductos WHERE nombre LIKE ?";
        $query = $this->conn->prepare($sql);
        $param = "%$busqueda%";
        $query->execute([$param]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function guardarCotizacion($cliente_id, $subtotal, $iva, $total) {
        try {
            $sql = "INSERT INTO dbo.cotizaciones (cliente_id, subtotal, iva, total, fecha_creacion) VALUES (?, ?, ?, ?, GETDATE())";
            $query = $this->conn->prepare($sql);
            $query->execute([$cliente_id, $subtotal, $iva, $total]);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function guardarDetalleCotizacion($cotizacion_id, $producto_id, $cantidad, $precio, $subtotal) {
        try {
            $sql = "INSERT INTO dbo.detalle_cotizacion (cotizacion_id, producto_id, cantidad, precio, subtotal) VALUES (?, ?, ?, ?, ?)";
            $query = $this->conn->prepare($sql);
            $query->execute([$cotizacion_id, $producto_id, $cantidad, $precio, $subtotal]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerCotizaciones() {
        $sql = "SELECT c.id, CONCAT(cl.nombre, ' - ', cl.empresa) AS cliente, cl.id as cliente_id, cl.email AS cliente_email, c.subtotal, c.iva, c.total,FORMAT(c.total, 'N2') AS total, FORMAT(c.fecha_creacion, 'yyyy-MM-dd') AS fecha_creacion
                FROM dbo.cotizaciones c
                INNER JOIN dbo.clientes cl ON c.cliente_id = cl.id
                ORDER BY c.fecha_creacion DESC
                ";

        $query = $this->conn->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerCotizacionPorId($id) {
        $sql = "SELECT c.*, cl.nombre AS cliente_nombre, cl.empresa AS cliente_empresa, cl.email AS cliente_email
                FROM dbo.cotizaciones c
                INNER JOIN dbo.clientes cl ON c.cliente_id = cl.id
                WHERE c.id = ?
                ORDER BY c.fecha_creacion DESC
                ";
                
        $query = $this->conn->prepare($sql);
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    
    public function obtenerDetalleCotizacion($cotizacion_id) {
        $sql = "SELECT dc.*, tp.nombre AS nombre_producto 
                FROM detalle_cotizacion dc
                INNER JOIN TiposProductos tp ON dc.producto_id = tp.ID
                WHERE dc.cotizacion_id = ?
                ";

        $query = $this->conn->prepare($sql);
        $query->execute([$cotizacion_id]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarCotizacionConDetalles($cotizacion_id, $cliente_id, $subtotal, $iva, $total, $productos) {
        try {
            $this->conn->beginTransaction();
    
            $sql = "UPDATE dbo.cotizaciones SET cliente_id = ?, subtotal = ?, iva = ?, total = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$cliente_id, $subtotal, $iva, $total, $cotizacion_id]);
    
            $sqlDelete = "DELETE FROM dbo.detalle_cotizacion WHERE cotizacion_id = ?";
            $this->conn->prepare($sqlDelete)->execute([$cotizacion_id]);
    
            foreach ($productos as $p) {
                $this->guardarDetalleCotizacion(
                    $cotizacion_id,
                    $p["id"],
                    $p["cantidad"],
                    $p["precio"],
                    $p["subtotal"]
                );
            }
    
            $this->conn->commit();
            return true;
    
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al actualizar cotización: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarCotizacionPorId($id) {
        try {
            $this->conn->beginTransaction();
    
            $sql1 = "DELETE FROM dbo.detalle_cotizacion WHERE cotizacion_id = ?";
            $this->conn->prepare($sql1)->execute([$id]);
    
            $sql2 = "DELETE FROM dbo.cotizaciones WHERE id = ?";
            $this->conn->prepare($sql2)->execute([$id]);
    
            $this->conn->commit();
            return true;

        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al eliminar cotización: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPorCliente($clienteId) {
        $stmt = $this->conn->prepare("SELECT id, fecha_creacion, total FROM dbo.cotizaciones WHERE cliente_id = ?");
        $stmt->execute([$clienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

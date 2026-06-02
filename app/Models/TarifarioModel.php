<?php
require_once __DIR__ . "/../../config/database.php";

class TarifarioModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT TP.ID, TP.Nombre, TP.Costo, TP.Descripcion, TP.id_categoria_serv, CS.Nombre AS CategoriaNombre
                                      FROM dbo.TiposProductos TP
                                      LEFT JOIN dbo.Categoria_Serv CS ON TP.id_categoria_serv = CS.ID
                                      ORDER BY TP.ID DESC"
                                    );

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function guardarHistorial($tipoProductoID, $accion, $nombre = null, $costo = null, $descripcion = null, $categoria = null, $usuario = null, $mensaje = null) {
        $stmt = $this->conn->prepare("INSERT INTO dbo.Historial_TiposProductos (TipoProductoID, Accion, NombreAnterior, CostoAnterior, DescripcionAnterior, CategoriaAnterior, Usuario, Mensaje) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$tipoProductoID, $accion, $nombre, $costo, $descripcion, $categoria, $usuario, $mensaje]);
    }

    public function estadisticasCategorias() {
        $stmt = $this->conn->prepare("SELECT cs.Nombre AS categoria, COUNT(tp.id_categoria_serv) AS cantidad
                                      FROM dbo.TiposProductos tp
                                      JOIN dbo.Categoria_Serv cs ON cs.ID = tp.id_categoria_serv
                                      GROUP BY cs.Nombre
                                      ORDER BY cantidad DESC"
                                    );
                                      
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function estadisticasPorFecha($inicio, $fin) {
        $stmt = $this->conn->prepare("SELECT cs.Nombre AS categoria, COUNT(tp.id_categoria_serv) AS cantidad
                                      FROM dbo.TiposProductos tp
                                      JOIN dbo.Categoria_Serv cs ON cs.ID = tp.id_categoria_serv
                                      WHERE tp.FechaCreacion BETWEEN ? AND ?
                                      GROUP BY cs.Nombre
                                      ORDER BY cantidad DESC"
                                    );

        $stmt->execute([$inicio, $fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($nombre, $costo, $descripcion, $categoria_serv) {
        $stmt = $this->conn->prepare("INSERT INTO dbo.TiposProductos (Nombre, Costo, FechaCreacion, Descripcion, id_categoria_serv) VALUES (?, ?, GETDATE(), ?, ?)");
        $stmt->execute([$nombre, $costo, $descripcion,  $categoria_serv]);

        $tipoProductoID = $this->conn->lastInsertId();
        $this->guardarHistorial($tipoProductoID, 'INSERTAR');
        return true;
    }
    
    public function update($id, $nombre, $costo, $descripcion, $categoria_serv, $usuario) {
        $stmt_old = $this->conn->prepare("SELECT Nombre, Costo, Descripcion, id_categoria_serv FROM dbo.TiposProductos WHERE ID = ?");
        $stmt_old->execute([$id]);
        $old = $stmt_old->fetch(PDO::FETCH_ASSOC);
    
        $stmt = $this->conn->prepare("UPDATE dbo.TiposProductos SET Nombre = ?, Costo = ?, Descripcion = ?, id_categoria_serv = ? WHERE ID = ?");
        $stmt->execute([$nombre, $costo, $descripcion, $categoria_serv, $id]);
    
        if ($old && $old['Costo'] != $costo) {
            $mensaje = "Cambio de precio: ₡{$old['Costo']} → ₡{$costo}";
            $this->guardarHistorial($id, 'CAMBIO_PRECIO', $old['Nombre'], $old['Costo'], $old['Descripcion'], $old['id_categoria_serv'], $usuario, $mensaje);
        }

        $this->guardarHistorial($id, 'ACTUALIZAR', $old['Nombre'], $old['Costo'], $old['Descripcion'], $old['id_categoria_serv'], $usuario, 'Se actualizo el servicio.');
    
        return true;
    }
    
    public function delete($id, $usuario) {
        $stmtCheckTickets = $this->conn->prepare("SELECT COUNT(*) FROM dbo.Tickets WHERE TipoProductoID = ?");
        $stmtCheckTickets->execute([$id]);
        $ticketsCount = $stmtCheckTickets->fetchColumn();
    
        $stmtCheckCoti = $this->conn->prepare("SELECT COUNT(*) FROM dbo.detalle_cotizacion WHERE producto_id = ?");
        $stmtCheckCoti->execute([$id]);
        $cotizacionesCount = $stmtCheckCoti->fetchColumn();
    
        if ($ticketsCount > 0 || $cotizacionesCount > 0) {
            return [
                'status' => 'error',
                'message' => 'No se puede eliminar este producto porque está asociado a uno o más Tickets o Cotizaciones.'
            ];
        }
    
        $stmt_old = $this->conn->prepare("SELECT Nombre, Costo, Descripcion, id_categoria_serv FROM dbo.TiposProductos WHERE ID = ?");
        $stmt_old->execute([$id]);
        $old = $stmt_old->fetch(PDO::FETCH_ASSOC);
    
        $stmt = $this->conn->prepare("DELETE FROM dbo.TiposProductos WHERE ID = ?");
        $stmt->execute([$id]);
    
        $this->guardarHistorial($id, 'ELIMINAR', $old['Nombre'], $old['Costo'], $old['Descripcion'], $old['id_categoria_serv'], $usuario);
    
        return [
            'status' => 'success',
            'message' => 'Producto eliminado correctamente.'
        ];
    }
    
    public function obtenerHistorialAgrupado() {
        $stmt = $this->conn->prepare("SELECT h.TipoProductoID, h.Accion, h.NombreAnterior, h.CostoAnterior, h.DescripcionAnterior, h.CategoriaAnterior, c.Nombre AS CategoriaAnteriorNombre, h.Usuario, h.FechaAccion
                                      FROM dbo.Historial_TiposProductos h
                                      LEFT JOIN dbo.Categoria_Serv c ON h.CategoriaAnterior = c.ID
                                      ORDER BY h.TipoProductoID, h.FechaAccion DESC"
                                    );

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function exists($nombre) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM dbo.Categoria_Serv WHERE LOWER(Nombre) = LOWER(?)");
        $stmt->execute([$nombre]);
        return $stmt->fetchColumn() > 0;
    }
    
    public function insertCategoria($nombre) {
        if ($this->exists($nombre)) {
            return "exists";
        }
    
        try {
            $stmt = $this->conn->prepare("INSERT INTO dbo.Categoria_Serv (Nombre, FechaCreacion) VALUES (?, GETDATE())");
            return $stmt->execute([$nombre]) ? "success" : false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerNotificaciones() {
        $stmt = $this->conn->prepare("SELECT TOP 5 * FROM (
                                      SELECT ID, 'PRODUCTO' AS Tipo, TipoProductoID AS ReferenciaID, NombreAnterior, CostoAnterior, FechaAccion, Mensaje
                                      FROM dbo.Historial_TiposProductos
                                      WHERE Accion = 'CAMBIO_PRECIO'
                                      
                                      UNION ALL
                                      
                                      SELECT ID, 'CONTACTO' AS Tipo, ContactoID AS ReferenciaID, NombreAnterior, NULL AS CostoAnterior, FechaAccion, Mensaje
                                      FROM dbo.Historial_Contactos
                                      WHERE Accion = 'ACTUALIZACIÓN'
                                      ) AS NotificacionesCombinadas
                                      ORDER BY FechaAccion DESC"
                                    );
                                      
        $stmt->execute();
        $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countStmt = $this->conn->query("SELECT 
                                         (SELECT COUNT(*) FROM Historial_TiposProductos WHERE notificaciones_check = 0 AND Accion = 'CAMBIO_PRECIO') +
                                         (SELECT COUNT(*) FROM Historial_Contactos WHERE notificaciones_check = 0 ) AS total"
                                        );

        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
        return ['total' => $total, 'data' => $notificaciones];
    }
    
    public function marcarNotificacionesComoLeidas() {
        try {
            $stmt1 = $this->conn->prepare("UPDATE dbo.Historial_TiposProductos SET notificaciones_check = 1 WHERE notificaciones_check = 0");
            $stmt1->execute();
    
            $stmt2 = $this->conn->prepare("UPDATE dbo.Historial_Contactos SET notificaciones_check = 1 WHERE notificaciones_check = 0");
            $stmt2->execute();
    
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function obtenerHistorialNotificaciones() {
        $stmt = $this->conn->prepare("SELECT TOP 5 ID, TipoProductoID, NombreAnterior, CostoAnterior, FechaAccion, Mensaje
                                      FROM Historial_TiposProductos
                                      WHERE Accion = 'CAMBIO_PRECIO'
                                      ORDER BY FechaAccion DESC"
                                    );

    $stmt->execute();
    $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return ['data' => $notificaciones];
    }
}
?>

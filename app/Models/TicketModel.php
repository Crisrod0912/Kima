<?php
require_once __DIR__ . '/../../config/database.php';

class Ticket {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        if (!$this->conn) {
            die("Error: No hay conexión con la base de datos.");
        }
    }

    public function crearTicket($tipoProductoID, $responsableID, $clienteID, $prioridadId, $descripcion, $documentos) {
        try {
            $verificarQuery = "SELECT COUNT(*) FROM dbo.Tickets 
                               WHERE TipoProductoID = :tipoProductoID 
                               AND ResponsableID = :responsableID 
                               AND ClienteID = :clienteID 
                               AND LOWER(CAST(Descripcion AS VARCHAR(MAX))) = :descripcion"
                               ;
    
            $verificarStmt = $this->conn->prepare($verificarQuery);
            $verificarStmt->execute([
                ":tipoProductoID" => $tipoProductoID,
                ":responsableID" => $responsableID,
                ":clienteID" => $clienteID,
                ":descripcion" => strtolower($descripcion)
            ]);
    
            $existe = $verificarStmt->fetchColumn();
            if ($existe > 0) {
                return ["status" => "error", "message" => "Ya existe un ticket con los mismos datos."];
            }
    
            date_default_timezone_set('America/Costa_Rica');
            $FechaCreacion = date("Y-m-d H:i:s");
            $estadoID = 5;
    
            $query = "INSERT INTO dbo.Tickets (TipoProductoID, ResponsableID, ClienteID, EstadoID, FechaCreacion, Descripcion, CategoriaID) VALUES (:tipoProductoID, :responsableID, :clienteID, :estadoID, :FechaCreacion, :descripcion, :prioridadId)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ":tipoProductoID" => $tipoProductoID,
                ":responsableID" => $responsableID,
                ":clienteID" => $clienteID,
                ":estadoID" => $estadoID,
                ":FechaCreacion" => $FechaCreacion,
                ":descripcion" => $descripcion,
                ":prioridadId" => $prioridadId
            ]);

            $ticketId = $this->conn->lastInsertId();
    
            if ($documentos && is_array($documentos)) {
                foreach ($documentos as $archivo) {
                    $ruta = "/uploads/" . $archivo;
    
                    $insertDoc = $this->conn->prepare("INSERT INTO dbo.documentos_tickets (TicketID, NombreArchivo, RutaArchivo) VALUES (?, ?, ?)");
                    $insertDoc->execute([$ticketId, $archivo, $ruta]);
                }
            }
    
            return ["status" => "success", "message" => "Ticket creado correctamente."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Error SQL: " . $e->getMessage()];
        }
    }

    public function eliminarDocumentoPorId($id) {
        try {
            $stmt = $this->conn->prepare("SELECT dbo.RutaArchivo FROM documentos_tickets WHERE ID = ?");
            $stmt->execute([$id]);
            $archivo = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$archivo) {
                return ["status" => "error", "message" => "Documento no encontrado."];
            }
    
            $rutaArchivo = __DIR__ . '/../../public' . $archivo['RutaArchivo'];
    
            $stmtDelete = $this->conn->prepare("DELETE FROM dbo.documentos_tickets WHERE ID = ?");
            $stmtDelete->execute([$id]);
    
            if (file_exists($rutaArchivo)) {
                unlink($rutaArchivo);
            }
    
            return ["status" => "success", "message" => "Documento eliminado correctamente."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Error al eliminar documento: " . $e->getMessage()];
        }
    }

    public function obtenerTickets() {
        try {
            $query = "SELECT t.ID, tp.Nombre AS TipoProducto, u.Nombre AS Responsable, c.Nombre AS Cliente, c.empresa As Empresa, e.ID AS EstadoID, ct.Nombre AS Prioridad, e.Estado, t.FechaCreacion, t.Descripcion, t.Documento
                      FROM dbo.Tickets t
                      JOIN dbo.TiposProductos tp ON t.TipoProductoID = tp.ID
                      JOIN dbo.Usuarios u ON t.ResponsableID = u.ID
                      JOIN dbo.clientes c ON t.ClienteID = c.ID
                      JOIN dbo.EstadosTickets e ON t.EstadoID = e.ID
                      JOIN dbo.Categorias ct on t.CategoriaID = ct.ID
                      ORDER BY t.ID DESC"
                      ;
    
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function buscarTickets($query) {
        try {
            $sql = "SELECT t.ID, tp.Nombre AS TipoProducto, u.Nombre AS Responsable, c.Nombre AS Cliente, c.empresa As Empresa, ct.Nombre AS Prioridad, e.Estado, t.EstadoID, t.FechaCreacion, t.Descripcion
                    FROM dbo.Tickets t
                    INNER JOIN dbo.TiposProductos tp ON t.TipoProductoID = tp.ID
                    INNER JOIN dbo.Usuarios u ON t.ResponsableID = u.ID
                    INNER JOIN dbo.clientes c ON t.ClienteID = c.ID
                    INNER JOIN dbo.EstadosTickets e ON t.EstadoID = e.ID
                    INNER JOIN dbo.Categorias ct on t.CategoriaID = ct.ID
                    WHERE t.ID LIKE :query1
                       OR tp.Nombre LIKE :query2 
                       OR u.Nombre LIKE :query3 
                       OR c.Nombre LIKE :query4 
                       OR c.empresa LIKE :query5
                       OR e.Estado LIKE :query6 
                       OR t.Descripcion LIKE :query7
                       OR ct.Nombre LIKE :query8
                       ORDER BY t.ID DESC"
                    ;
    
            $stmt = $this->conn->prepare($sql);
    
            $searchQuery = "%$query%";
            $stmt->bindValue(':query1', $searchQuery, PDO::PARAM_STR);
            $stmt->bindValue(':query2', $searchQuery, PDO::PARAM_STR);
            $stmt->bindValue(':query3', $searchQuery, PDO::PARAM_STR);
            $stmt->bindValue(':query4', $searchQuery, PDO::PARAM_STR);
            $stmt->bindValue(':query5', $searchQuery, PDO::PARAM_STR);
            $stmt->bindValue(':query6', $searchQuery, PDO::PARAM_STR);
            $stmt->bindValue(':query7', $searchQuery, PDO::PARAM_STR);
            $stmt->bindValue(':query8', $searchQuery, PDO::PARAM_STR);
    
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en SQL: " . $e->getMessage());
            return [];
        }
    }

    public function buscarClientes($search) {
        try {
            $query = "SELECT id, nombre, empresa FROM dbo.clientes WHERE nombre LIKE :search OR empresa LIKE :search LIMIT 10";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['search' => "%$search%"]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerTicketPorId($id) {
        try {
            $query = "SELECT * FROM dbo.Tickets WHERE ID = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function obtenerTicketPorIdShow($id) {
        try {
            $query = "SELECT t.ID, t.FechaCreacion, t.FechaFin, t.Descripcion, tp.Nombre AS TipoProducto, u.Nombre AS Responsable, c.nombre AS Cliente, et.ID AS EstadoID, et.Estado AS Estado, cat.Nombre AS Categoria
                      FROM dbo.Tickets t
                      LEFT JOIN dbo.TiposProductos tp ON t.TipoProductoID = tp.ID
                      LEFT JOIN dbo.Usuarios u ON t.ResponsableID = u.ID
                      LEFT JOIN dbo.clientes c ON t.ClienteID = c.id
                      LEFT JOIN dbo.EstadosTickets et ON t.EstadoID = et.ID
                      LEFT JOIN dbo.Categorias cat ON t.CategoriaID = cat.id
                      WHERE t.ID = :id"
                      ;
    
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function obtenerTicketsPorCliente($clienteID) {
        try {
            $query = "SELECT t.ID, e.Estado, t.FechaCreacion, t.EstadoID
                      FROM dbo.Tickets t
                      JOIN dbo.EstadosTickets e ON t.EstadoID = e.ID
                      WHERE t.ClienteID = :clienteID
                      ORDER BY t.ID DESC"
                      ;
    
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':clienteID', $clienteID, PDO::PARAM_INT);
            $stmt->execute();
    
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function actualizarTicket($data, $files) {
        try {
            date_default_timezone_set('America/Costa_Rica');
            $fechaFin = ($data['estado'] == '3') ? date("Y-m-d H:i:s") : null;
    
            $query = "UPDATE dbo.Tickets SET ClienteID = :cliente, TipoProductoID = :producto, ResponsableID = :responsable, EstadoID = :estado, Descripcion = :descripcion, FechaFin = :fecha_fin, CategoriaID = :categoria WHERE ID = :id";
    
            $stmt = $this->conn->prepare($query);
            $success = $stmt->execute([
                ":id" => $data['id'],
                ":cliente" => $data['cliente'],
                ":producto" => $data['product'],
                ":responsable" => $data['user'],
                ":estado" => $data['estado'],
                ":descripcion" => $data['description'],
                ":fecha_fin" => $fechaFin, 
                ":categoria" => $data['categoria']
            ]);
    
            if (!$success) {
                print_r($stmt->errorInfo()); 
            }
    
            return $success;
        } catch (PDOException $e) {
            error_log("Error SQL: " . $e->getMessage());
            return false;
        }
    }
    
    public function obtenerClientes() {
        $query = "SELECT id, nombre, empresa FROM dbo.clientes";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerProductos() {
        $query = "SELECT ID, Nombre FROM dbo.TiposProductos";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerUsuarios() {
        $query = "SELECT ID, Nombre FROM dbo.Usuarios";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerEstados() {
        $query = "SELECT ID, Estado FROM dbo.EstadosTickets";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerCategorias() {
        $query = "SELECT id, Nombre FROM dbo.Categorias";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function eliminarTicket($id) {
        try {
            $stmtComentarios = $this->conn->prepare("SELECT COUNT(*) FROM dbo.comentarios_tickets WHERE TicketID = :id");
            $stmtComentarios->execute([":id" => $id]);
            $cantidadComentarios = $stmtComentarios->fetchColumn();
    
            $stmtEstado = $this->conn->prepare("SELECT EstadoID FROM dbo.Tickets WHERE ID = :id");
            $stmtEstado->execute([":id" => $id]);
            $estado = $stmtEstado->fetchColumn();
    
            if (!($estado == 3 && $cantidadComentarios > 0)) {
                return [
                    "status" => "error",
                    "message" => "Solo se pueden eliminar tickets cerrados que tengan al menos un comentario."
                ];
            }
    
            $stmtDeleteComentarios = $this->conn->prepare("DELETE FROM dbo.comentarios_tickets WHERE TicketID = :id");
            $stmtDeleteComentarios->execute([":id" => $id]);
    
            $stmtDeleteTicket = $this->conn->prepare("DELETE FROM dbo.Tickets WHERE ID = :id");
            $stmtDeleteTicket->execute([":id" => $id]);
    
            return ["status" => "success"];
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Error al eliminar el ticket: " . $e->getMessage()
            ];
        }
    }

    public function eliminarMultiplesTickets($ids) {
        try {
            $errores = [];
    
            foreach ($ids as $id) {
                $stmt = $this->conn->prepare("SELECT dbo.EstadoID FROM Tickets WHERE ID = ?");
                $stmt->execute([$id]);
                $estado = $stmt->fetchColumn();
    
                $stmtComentarios = $this->conn->prepare("SELECT COUNT(*) FROM dbo.comentarios_tickets WHERE TicketID = ?");
                $stmtComentarios->execute([$id]);
                $cantidadComentarios = $stmtComentarios->fetchColumn();
    
                if (!($estado == 3 && $cantidadComentarios > 0)) {
                    $errores[] = "Ticket #$id no se puede eliminar (debe estar cerrado y tener comentarios)";
                    continue;
                }
    
                $stmtDeleteComentarios = $this->conn->prepare("DELETE FROM dbo.comentarios_tickets WHERE TicketID = ?");
                $stmtDeleteComentarios->execute([$id]);

                $stmtDelete = $this->conn->prepare("DELETE FROM dbo.Tickets WHERE ID = ?");
                $stmtDelete->execute([$id]);
            }
    
            if (!empty($errores)) {
                return [
                    "status" => "partial",
                    "message" => "Algunos tickets no se eliminaron.",
                    "errores" => $errores
                ];
            }
    
            return ["status" => "success", "message" => "Tickets eliminados correctamente."];
    
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Error al eliminar múltiples tickets: " . $e->getMessage()
            ];
        }
    }
}
?>

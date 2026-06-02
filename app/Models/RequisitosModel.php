<?php
require_once __DIR__ . "/../../config/database.php";

class RequisitosModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT r.ID, r.Descripcion, r.CategoriaID, c.Nombre AS Categoria, r.PrioridadID, p.Nombre AS Prioridad, r.Fecha
                                      FROM dbo.Requisitos r
                                      INNER JOIN dbo.CategoriasRequisitos c ON r.CategoriaID = c.ID
                                      INNER JOIN dbo.Categorias p ON r.PrioridadID = p.ID
                                      ORDER BY r.Fecha DESC"
                                    );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($descripcion, $categoria, $prioridad) {
        $stmt = $this->conn->prepare("INSERT INTO dbo.Requisitos (Descripcion, CategoriaID, PrioridadID, Fecha) VALUES (?, ?, ?, GETDATE())");
        return $stmt->execute([$descripcion, $categoria, $prioridad]);
    }
    
    public function update($id, $descripcion, $categoria, $prioridad) {
        $stmt = $this->conn->prepare("UPDATE dbo.Requisitos SET Descripcion = ?, CategoriaID = ?, PrioridadID = ? WHERE ID = ?");
        return $stmt->execute([$descripcion, $categoria, $prioridad, $id]);
    }
    
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM dbo.Requisitos WHERE ID = ?");
        return $stmt->execute([$id]);
    }

    public function getCategorias() {
        $stmt = $this->conn->prepare("SELECT ID, Nombre FROM dbo.CategoriasRequisitos ORDER BY Nombre ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPrioridades() {
        $stmt = $this->conn->prepare("SELECT ID, Nombre FROM dbo.Categorias ORDER BY Nombre ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

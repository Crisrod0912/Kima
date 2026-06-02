<?php
require_once __DIR__ . "/../../config/database.php";

class NotasModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM dbo.Notas ORDER BY fecha DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save($titulo, $descripcion, $usuarioId) {
        $stmt = $this->conn->prepare("INSERT INTO dbo.Notas (titulo, descripcion, usuario_id) VALUES (?, ?, ?)");
        return $stmt->execute([$titulo, $descripcion, $usuarioId]);
    }
    
    public function update($id, $titulo, $descripcion) {
        $stmt = $this->conn->prepare("UPDATE dbo.Notas SET titulo = ?, descripcion = ? WHERE id = ?");
        return $stmt->execute([$titulo, $descripcion, $id]);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM dbo.Notas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function obtenerUltimasNotas($usuarioId, $limite = 3) {
        $sql = "SELECT TOP (?) * FROM dbo.Notas WHERE usuario_id = ? ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->bindValue(2, $usuarioId, PDO::PARAM_INT);
        $stmt->execute();        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM dbo.Notas WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>

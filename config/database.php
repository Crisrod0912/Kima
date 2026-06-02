<?php
$serverName = "DESKTOP-60THOT0\SQLEXPRESS";
$database = "Kima";
$username = "kima";
$password = "Inicio01";

try {
    $conn = new PDO("sqlsrv:server=$serverName;Database=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>

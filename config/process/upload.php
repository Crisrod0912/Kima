<?php
$uploadDir = "uploads/";

if (!empty($_FILES['file'])) {
    $fileName = basename($_FILES['file']['name']);
    $targetFilePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath)) {
        echo $targetFilePath;
    } else {
        echo "Error al subir el archivo.";
    }
}
?>

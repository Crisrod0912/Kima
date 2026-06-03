<?php
$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID no proporcionado.");
}

require_once __DIR__ . '/../../Controllers/CotizacionesController.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

$controller = new CotizacionesController();
$cotizacion = $controller->model->obtenerCotizacionPorId($id);
$productos = $controller->model->obtenerDetalleCotizacion($id);

ob_start();
include 'cotizacion_pdf_template.php';
$html = ob_get_clean();

$dompdf = new Dompdf\Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$pdfOutput = $dompdf->output();
$tempFile = "Cotizacion_{$id}.pdf";
file_put_contents(__DIR__ . "/../../../public/tmp/$tempFile", $pdfOutput);
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Visualizar y Descargar Cotización</title>
</head>

<body>
    <iframe src="/Kima/public/tmp/<?= $tempFile ?>" style="width:100%; height:100vh;" frameborder="0"></iframe>

    <script>
        const link = document.createElement("a");
        link.href = "/Kima/public/tmp/<?= $tempFile ?>";
        link.download = "<?= $tempFile ?>";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    </script>

</body>

</html>

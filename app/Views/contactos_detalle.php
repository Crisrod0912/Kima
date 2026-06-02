<?php
$title = "Contactos";
include '../../layout.php';
require_once "../../config/database.php";

$idCliente = $_GET['id'] ?? null;

if (!$idCliente) {
    die("ID del contacto no proporcionado.");
}

try {
    $stmt = $conn->prepare("SELECT * FROM dbo.lista_contactos WHERE id = ?");
    $stmt->execute([$idCliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        die("Contacto no encontrado.");
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<html lang="en">

<body>
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main" style="margin-top: 50px !important">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="d-flex flex-column flex-xl-row">
                    <div class="w-100 mb-10 d-flex justify-content-center">
                        <div class="card mb-5 mb-xl-8" style="max-width: 700px; width: 100%;">
                            <div class="card-body pt-15">
                                <div class="d-flex justify-content-end mb-3">
                                    <a href="/app/Views/contactos.php" class="btn btn-light-primary fw-bold">
                                        <i class="fas fa-arrow-left me-2"></i> Volver
                                    </a>
                                </div>
                                <div class="d-flex flex-center flex-column mb-5">
                                    <div class="symbol symbol-150px symbol-circle mb-7">
                                        <img src="/public/assets/media/avatars/blank.png" alt="image">
                                    </div>
                                    <a class="fs-3 text-gray-800 text-hover-primary fw-bold mb-1">
                                        <?= htmlspecialchars($cliente['nombre']) ?>
                                    </a>
                                    <a class="fs-5 fw-semibold text-muted text-hover-primary mb-6">
                                        <?= htmlspecialchars($cliente['empresa']) ?>
                                    </a>
                                </div>
                                <div class="d-flex flex-stack fs-4 py-3">
                                    <div class="fw-bold">Perfil del Contacto</div>
                                </div>
                                <div class="separator separator-dashed my-3"></div>
                                <div class="pb-5 fs-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="fw-bold mt-5">Cédula</div>
                                            <div class="text-gray-600">
                                                <?= htmlspecialchars($cliente['cedula']) ?>
                                            </div>
                                            <div class="fw-bold mt-5">Teléfono</div>
                                            <div class="text-gray-600">
                                                <?= htmlspecialchars($cliente['telefono']) ?>
                                            </div>
                                            <div class="fw-bold mt-5">Servicio</div>
                                            <div class="text-gray-600">
                                                <?= htmlspecialchars($cliente['servicio']) ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="fw-bold mt-5">Correo de Contacto</div>
                                            <div class="text-gray-600">
                                                <a href="#" class="text-gray-600 text-hover-primary">
                                                    <?= htmlspecialchars($cliente['email']) ?>
                                                </a>
                                            </div>
                                            <div class="fw-bold mt-5">Dirección</div>
                                            <div class="text-gray-600">
                                                <?= htmlspecialchars($cliente['direccion']) ?>
                                            </div>
                                            <div class="fw-bold mt-5">Especialidad</div>
                                            <div class="text-gray-600">
                                                <?= htmlspecialchars($cliente['especialidad']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        var hostUrl = "/public/assets/";
    </script>

    <script>
        const icono = document.querySelector('#menu_clientes').previousElementSibling.querySelector('i');
        const span = document.getElementById('menu_clientes');

        icono.style.color = 'white';
        span.style.color = 'white';
    </script>

    <script src="/public/assets/plugins/global/plugins.bundle.js"></script>
    <script src="/public/assets/js/scripts.bundle.js"></script>
    <script src="/public/assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <script src="/public/assets/js/custom/apps/file-manager/list.js"></script>
    <script src="/public/assets/js/widgets.bundle.js"></script>
    <script src="/public/assets/js/custom/widgets.js"></script>
    <script src="/public/assets/js/custom/apps/chat/chat.js"></script>
    <script src="/public/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
    <script src="/public/assets/js/custom/utilities/modals/create-app.js"></script>
    <script src="/public/assets/js/custom/utilities/modals/users-search.js"></script>
</body>

</html>

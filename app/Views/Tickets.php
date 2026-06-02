<?php
$title = "Tickets";
include '../../layout.php';
require_once "../../config/database.php";

try {
    $query = "SELECT ID, Nombre FROM dbo.Usuarios";
    $stmt = $conn->query($query);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener usuarios: " . $e->getMessage());
}

try {
    $query = "SELECT ID, Nombre FROM dbo.TiposProductos";
    $stmt = $conn->query($query);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los productos: " . $e->getMessage());
}

try {
    $query = "SELECT ID, Estado FROM dbo.EstadosTickets";
    $stmt = $conn->query($query);
    $estados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los estados: " . $e->getMessage());
}

try {
    $query = "SELECT id, nombre, empresa FROM dbo.clientes";
    $stmt = $conn->query($query);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los clientes: " . $e->getMessage());
}

try {
    $query = "SELECT id, Nombre FROM dbo.Categorias";
    $stmt = $conn->query($query);
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener las categorias: " . $e->getMessage());
}

try {
    $query = "SELECT MAX(ID) AS UltimoID FROM dbo.Tickets";
    $stmt = $conn->query($query);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $proximoID = ($result['UltimoID'] !== null) ? $result['UltimoID'] + 1 : 1;
} catch (PDOException $e) {
    die("Error al obtener el próximo ID: " . $e->getMessage());
}
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="/public/assets/css/style.bundle.css" rel="stylesheet">
</head>

<body>
    <div class="modal fade" id="modalViewTicket" tabindex="-1" aria-labelledby="modalViewTicketLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalViewTicketLabel">Detalles del Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>ID:</strong> <span id="view-ticket-id"></span></p>
                    <p><strong>Cliente:</strong> <span id="view-ticket-cliente"></span></p>
                    <p><strong>Producto:</strong> <span id="view-ticket-producto"></span></p>
                    <p><strong>Responsable:</strong> <span id="view-ticket-responsable"></span></p>
                    <p><strong>Estado:</strong> <span id="view-ticket-estado"></span></p>
                    <p><strong>Fecha de Creación:</strong> <span id="view-ticket-fecha-creacion"></span></p>
                    <p><strong>Fecha de Finalización:</strong> <span id="view-ticket-fecha-fin"></span></p>
                    <p><strong>Descripción:</strong> <span id="view-ticket-descripcion"></span></p>
                    <p><strong>Documentos:</strong></p>
                    <ul id="view-ticket-documentos" class="list-group mb-3"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Volver..</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-labelledby="modalConfirmDeleteLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalConfirmDeleteLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar este ticket? Esta acción no se puede deshacer.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalSuccess" tabindex="-1" aria-labelledby="modalSuccessLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSuccessLabel">Éxito</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    El ticket se actualizó correctamente.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="kt_modal_edit_ticket" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-1000px">
            <div class="modal-content rounded">
                <div class="modal-header pb-0 border-0 justify-content-end">
                    <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                    <form id="kt_modal_edit_ticket_form" class="form">
                        <h1 class="text-center mb-3">Editar Ticket</h1>
                        <input type="hidden" name="id" id="edit-ticket-id">
                        <p><strong>Fecha de Creación:</strong> <span id="label-fecha-creacion"></span></p>
                        <p><strong>Tiempo Transcurrido:</strong> <span id="label-tiempo-transcurrido"></span></p>
                        <div class="mb-8">
                            <label class="fs-6 fw-semibold mb-2 required">Cliente</label>
                            <select class="form-select form-select-solid" id="edit-cliente" name="cliente"></select>
                        </div>
                        <div class="row g-9 mb-8">
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold mb-2 required">Tipo de Producto</label>
                                <select class="form-select form-select-solid" id="edit-producto"
                                    name="product"></select>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold mb-2 required">Responsable</label>
                                <select class="form-select form-select-solid" id="edit-responsable"
                                    name="user"></select>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold mb-2 required">Estado</label>
                                <select class="form-select form-select-solid" id="edit-estado" name="estado"></select>
                            </div>
                        </div>
                        <div class="mb-8">
                            <label class="fs-6 fw-semibold mb-2 required">Descripción</label>
                            <textarea class="form-control form-control-solid" id="edit-descripcion"
                                name="description"></textarea>
                        </div>
                        <div class="mb-8">
                            <label class="fs-6 fw-semibold mb-2 required">Categoría</label>
                            <select class="form-select form-select-solid" id="edit-categoria" name="categoria"></select>
                        </div>
                        <div class="mb-8">
                            <label class="fs-6 fw-semibold mb-2">Subir nuevos documentos</label>
                            <div class="dropzone dz-clickable" id="kt_modal_edit_ticket_attachments">
                                <div class="dz-message needsclick">
                                    <i class="ki-duotone ki-file-up fs-3hx text-primary"></i>
                                    <div class="ms-4">
                                        <h3 class="fs-5 fw-bold text-gray-900 mb-1">Haz clic o arrastra archivos</h3>
                                        <span class="fw-semibold fs-7 text-gray-500">Puedes subir hasta 10
                                            archivos</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-8">
                            <label class="fs-6 fw-semibold mb-2">Documentos actuales</label>
                            <ul id="edit-ticket-documentos" class="list-group mb-3">                                
                            </ul>
                        </div>
                        <h3 class="mt-10 mb-3">Comentarios</h3>
                        <div class="mb-4">
                            <table class="table table-striped" id="tabla_comentarios_ticket">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Observación</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="mb-8">
                            <label class="form-label">Nueva Comentario</label>
                            <textarea class="form-control" id="nuevo_comentario_ticket" rows="3"></textarea>
                            <button type="button" class="btn btn-sm btn-success mt-2" id="btn_agregar_comentario">
                                <i class="fa fa-plus"></i> Agregar Comentario
                            </button>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3"
                        style="margin-top: 0; padding-top: 0;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-ticket-alt fs-2 me-2"></i>
                            <h1 class="page-heading fw-bold fs-3 my-0">Tickets</h1>
                        </div>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="index.html" class="text-muted text-hover-primary">Inicio</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Tickets</li>
                        </ul>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="#" class="btn btn-sm fw-bold btn-primary" data-bs-toggle="modal"
                            data-bs-target="#kt_modal_new_ticket">
                            <i class="fa-solid fa-plus"></i>Crear Ticket
                        </a>
                        <a id="delete-multiple" class="btn btn-sm fw-bold btn-danger">
                            <i class="fa-solid fa-trash"></i>Eliminar Seleccionados
                        </a>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card card-flush">
                        <div class="card-header pt-8">
                            <div class="card-title w-100">
                                <div class="row w-100">
                                    <div class="col-md-3 mb-2">
                                        <div class="d-flex align-items-center position-relative">
                                            <i class="fa-solid fa-magnifying-glass position-absolute ms-3"></i>
                                            <input type="text" id="searchTicket"
                                                class="form-control form-control-solid ps-10"
                                                placeholder="Buscar ticket ó cliente" />
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <select id="filtro-estado" class="form-select form-select-solid">
                                            <option value="">Todos los estados</option>
                                            <?php foreach ($estados as $estado): ?>
                                            <option value="<?= htmlspecialchars($estado['Estado']); ?>">
                                                <?= htmlspecialchars($estado['Estado']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <select id="filtro-categoria" class="form-select form-select-solid">
                                            <option value="">Todas las categorías</option>
                                            <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= htmlspecialchars($categoria['Nombre']); ?>">
                                                <?= htmlspecialchars($categoria['Nombre']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="kt_ticket_table" class="table align-middle table-row-dashed fs-6 gy-5">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th><input type="checkbox" id="select-all-tickets" /></th>
                                        <th>ID</th>
                                        <th>Producto</th>
                                        <th>Responsable</th>
                                        <th>Cliente</th>
                                        <th>Prioridad</th>
                                        <th>Estado</th>
                                        <th>Fecha Inicio</th>
                                        <th>Descripción</th>
                                        <th style="min-width: 120px; white-space: nowrap;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="ticketTableBody" class="fw-semibold text-gray-600">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalMessage" tabindex="-1" aria-labelledby="modalMessageLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMessageLabel">Información</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="kt_modal_new_ticket" tabindex="-1" style="display: none;"
        data-select2-id="select2-data-kt_modal_new_ticket" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-1000px">
            <div class="modal-content rounded">
                <div class="modal-header pb-0 border-0 justify-content-end">
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15" data-select2-id="select2-data-78-17mw">
                    <form id="kt_modal_new_ticket_form" class="form" method="POST" enctype="multipart/form-data">
                        <div class="mb-13 text-center">
                            <h1 class="mb-3">Crear Ticket</h1>
                        </div>
                        <div id="alert-message" class="alert d-none"></div>
                        <div class="mb-8">
                            <label class="fs-6 fw-semibold mb-2 required">ID Ticket</label>
                            <input type="text" class="form-control form-control-solid"
                                placeholder="Introduce el ID del Ticket" name="subject"
                                value="<?= htmlspecialchars($proximoID); ?>" readonly required>
                        </div>
                        <div class="mb-8">
                            <label class="fs-6 fw-semibold mb-2 required">Cliente</label>
                            <select class="form-select form-select-solid" data-control="select2"
                                data-placeholder="Selecciona un cliente" data-hide-search="true" name="cliente">
                                <option value="">Selecciona un cliente...</option>
                                <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= htmlspecialchars($cliente['id']); ?>">
                                    <?= htmlspecialchars($cliente['nombre']); ?> -
                                    <?= htmlspecialchars($cliente['empresa']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row g-9 mb-8">
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold mb-2 required">Tipo de Producto</label>
                                <select class="form-select form-select-solid" data-control="select2"
                                    data-placeholder="Selección del Producto" name="product" required>
                                    <option value="">Selecciona un producto...</option>
                                    <?php foreach ($productos as $producto): ?>
                                    <option value="<?= htmlspecialchars($producto['ID']); ?>">
                                        <?= htmlspecialchars($producto['Nombre']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold mb-2 required">Responsable</label>
                                <select class="form-select form-select-solid" data-control="select2"
                                    data-placeholder="Selecciona un usuario" name="user" required>
                                    <option value="">Selecciona un usuario...</option>
                                    <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?= htmlspecialchars($usuario['ID']); ?>">
                                        <?= htmlspecialchars($usuario['Nombre']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-6 fw-semibold mb-2 required">Prioridad</label>
                                <select class="form-select form-select-solid" data-control="select2"
                                    data-placeholder="Selecciona un usuario" name="prioridad" required>
                                    <option value="">Selecciona una prioridad...</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= htmlspecialchars($categoria['id']); ?>">
                                        <?= htmlspecialchars($categoria['Nombre']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <!--<div class="mb-8">
                            <label class="fs-6 fw-semibold mb-2 required">Estado del Ticket</label>
                            <select class="form-select form-select-solid" data-control="select2"
                                data-placeholder="Selecciona un estado" data-hide-search="true" name="status">
                                <option value="">Selecciona un estado...</option>
                                <?php foreach ($estados as $estado): ?>
                                <option value="<?= htmlspecialchars($estado['ID']); ?>">
                                    <?= htmlspecialchars($estado['Estado']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>--->
                        <!-- Campo Fecha de realización 
                        <div class="mb-8">
                            <label class="fs-6 fw-semibold mb-2 required">Fecha de realización</label>
                            <input class="form-control form-control-solid flatpickr-input"
                                placeholder="Seleccionar Fecha" name="due_date" type="text">
                        </div>-->
                        <div class="mb-8">
                            <label class="fs-6 fw-semibold mb-2 required">Descripción del Ticket</label>
                            <textarea class="form-control form-control-solid" rows="4" name="description"
                                placeholder="Realiza una descripción del Ticket" required></textarea>
                        </div>
                        <div class="mb-8">
                            <label class="fs-6 fw-semibold mb-2">Subir documento</label>
                            <div class="dropzone dz-clickable" id="kt_modal_create_ticket_attachments">
                                <div class="dz-message needsclick">
                                    <i class="ki-duotone ki-file-up fs-3hx text-primary"></i>
                                    <div class="ms-4">
                                        <h3 class="fs-5 fw-bold text-gray-900 mb-1">Drop files here or click to upload.
                                        </h3>
                                        <span class="fw-semibold fs-7 text-gray-500">Upload up to 10 files</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="submit-ticket">
                                <span class="indicator-label">Confirmar</span>
                            </button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        var hostUrl = "/public/assets/";
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/css/select2.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {

            var table = $("#kt_ticket_table").DataTable({
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0
                } 
                ],
                "paging": true,
                "lengthMenu": [5, 10, 25, 50],
                "pageLength": 10,
                "ordering": true,
                "order": [
                    [7, "desc"]
                ],
                "info": true,
                "searching": true,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ tickets por página",
                    "zeroRecords": "No se encontraron productos, responsables, clientes, prioridad o descripción relacionado a lo escrito.",
                    "info": "Mostrando página _PAGE_ de _PAGES_",
                    "infoEmpty": "No hay registros disponibles",
                    "infoFiltered": "(filtrado de _MAX_ registros en total)",
                    "search": "Buscar:",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                }
            });

            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {

                let estado = $('#filtro-estado').val().toLowerCase();
                let categoria = $('#filtro-categoria').val().toLowerCase();

                let estadoData = $('<div>' + data[6] + '</div>').text().toLowerCase();
                let categoriaData = data[5].toLowerCase(); 

                function normalizarTexto(str) {
                    return str.toLowerCase().trim().replace(/\s+/g, ' ');
                }

                let coincideEstado = estado === "" || normalizarTexto(estadoData).includes(normalizarTexto(
                    estado));

                let coincideCategoria = categoria === "" || categoriaData.includes(categoria);

                return coincideEstado && coincideCategoria;
            });

            $('#filtro-cliente, #filtro-estado, #filtro-categoria').on('change', function () {
                table.draw();
            });

            inicializarSelect2();
            inicializarFlatpickr();

            $('#select-all-tickets').on('change', function () {
                $('.ticket-checkbox').prop('checked', this.checked);
            });

            $("#delete-multiple").on("click", function () {
                let seleccionados = $(".ticket-checkbox:checked")
                    .map(function () {
                        return $(this).val();
                    })
                    .get();

                if (seleccionados.length === 0) {
                    return mostrarModalAdvertencia(
                        "Debes seleccionar al menos un ticket para eliminar.");
                }

                if (!confirm("¿Estás seguro de que deseas eliminar los tickets seleccionados?")) {
                    return;
                }

                $.ajax({
                    url: "/app/Controllers/TicketController.php?action=eliminarMultiplesTickets",
                    type: "POST",
                    data: JSON.stringify({
                        ids: seleccionados
                    }),
                    contentType: "application/json",
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "success") {
                            $("#modalMessage .modal-body").html(
                                `<div class="alert alert-success">${response.message}</div>`
                            );
                            cargarTickets();
                        } else if (response.status === "partial") {
                            let erroresHTML = response.errores.map(err => `<li>${err}</li>`)
                                .join("");
                            $("#modalMessage .modal-body").html(`
                            <div class="alert alert-primary">
                                ${response.message}
                                <ul style="margin-top: 10px;">${erroresHTML}</ul>
                            </div>
                            `);
                            cargarTickets();
                        } else {
                            $("#modalMessage .modal-body").html(
                                `<div class="alert alert-danger">${response.message}</div>`
                            );
                            cargarTickets();
                        }

                        $("#modalMessage").modal("show");
                    },
                    error: function () {
                        alert("Error en la eliminación masiva.");
                    }
                });
            });

            $('#kt_modal_new_ticket').on('shown.bs.modal', function () {
                inicializarSelect2(); 
                inicializarFlatpickr(); 
            });

            $('#modalMessage').on('hidden.bs.modal', function () {
                inicializarSelect2();
            });

            console.log($.fn.select2);
            console.log("jQuery versión:", $.fn.jquery);
            console.log("Select2 versión:", $.fn.select2);
            console.log("Inicializando Select2...");
            console.log("jQuery versión:", $.fn.jquery);
            console.log("Select2 versión:", $.fn.select2 ? "Cargado correctamente" : "No cargado");

            function validarFormulario() {
                let valido = true;
                let mensajeError = '';

                $('#kt_modal_new_ticket_form [required]').each(function () {
                    if (!$(this).val()) {
                        valido = false;
                        mensajeError = 'Todos los campos requeridos deben completarse.';
                    }
                });

                if (!valido) {
                    mostrarMensaje(mensajeError, "alert-warning");
                }

                return valido;
            }

            function mostrarModalAdvertencia(mensaje) {
                $("#modalMessage .modal-body").html(`<div class="alert alert-primary">${mensaje}</div>`);
                $("#modalMessage").modal("show").appendTo("body");
            }

            var dropzoneEdit = new Dropzone("#kt_modal_edit_ticket_attachments", {
                url: "/app/Controllers/UploadController.php",
                paramName: "file",
                maxFiles: 10,
                acceptedFiles: ".pdf,.doc,.docx,.jpg,.png",
                autoProcessQueue: false,
                parallelUploads: 10,
                init: function () {
                    var form = document.getElementById("kt_modal_edit_ticket_form");

                    this.on("sending", function (file, xhr, formData) {
                        const ticketID = $("#edit-ticket-id").val();
                        formData.append("ticket_id", ticketID);
                    });

                    this.on("success", function (file, response) {
                        const data = typeof response === "string" ? JSON.parse(response) :
                            response;
                        let hiddenInput = document.createElement("input");
                        hiddenInput.type = "hidden";
                        hiddenInput.name = "documentos[]";
                        hiddenInput.value = data.filename;
                        form.appendChild(hiddenInput);
                    });

                    this.on("queuecomplete", function () {
                        enviarFormularioEdicion();
                    });

                    this.on("error", function (file, response) {
                        console.error("Error al subir archivo:", response);
                        $("#submit-edit-ticket").prop("disabled", false);
                    });
                }
            });

            var dropzone = new Dropzone("#kt_modal_create_ticket_attachments", {
                url: "/app/Controllers/UploadController.php",
                paramName: "file",
                maxFiles: 10,
                acceptedFiles: ".pdf,.doc,.docx,.jpg,.png",
                autoProcessQueue: false,
                parallelUploads: 10,
                init: function () {
                    var submitButton = document.querySelector("#submit-ticket");
                    var form = document.getElementById("kt_modal_new_ticket_form");

                    submitButton.addEventListener("click", function (e) {
                        e.preventDefault();

                        if (!validarFormulario()) {
                            return;
                        }

                        submitButton.disabled = true;

                        if (dropzone.getQueuedFiles().length > 0) {
                            dropzone.processQueue(); 
                        } else {
                            enviarFormulario(); 
                        }
                    });

                    this.on("sending", function (file, xhr, formData) {
                        const ticketID = $("#edit-ticket-id").val() || $(
                            "#kt_modal_new_ticket_form input[name='subject']").val();
                        formData.append("ticket_id", ticketID);
                    });

                    this.on("success", function (file, response) {
                        try {
                            const data = typeof response === "string" ? JSON.parse(response) :
                                response;

                            let hiddenInput = document.createElement("input");
                            hiddenInput.type = "hidden";
                            hiddenInput.name = "documentos[]";
                            hiddenInput.value = data.filename;
                            form.appendChild(hiddenInput);
                        } catch (err) {
                            console.error("Error al procesar la respuesta del archivo:",
                                response);
                        }
                    });

                    this.on("queuecomplete", function () {
                        enviarFormulario();
                    });

                    this.on("error", function (file, response) {
                        console.error("Error al subir archivo:", response);
                        $("#submit-ticket").prop("disabled", false);
                    });
                }
            });

            function cargarDatosEdicion(ticketId) {
                cargarOpcionesSelects();

                $.ajax({
                    url: `/app/Controllers/TicketController.php?action=obtenerTicket&id=${ticketId}`,
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "success") {
                            let ticket = response.data;

                            $("#edit-ticket-id").val(ticket.ID);

                            console.log('ticket', ticket);

                            $("#label-fecha-creacion").text(ticket.FechaCreacion);

                            calcularTiempoTranscurrido(ticket.FechaCreacion);

                            setTimeout(() => {
                                $("#edit-cliente").val(ticket.ClienteID).trigger("change");
                                $("#edit-producto").val(ticket.TipoProductoID).trigger(
                                    "change");
                                $("#edit-responsable").val(ticket.ResponsableID).trigger(
                                    "change");
                                $("#edit-estado").val(ticket.EstadoID).trigger("change");
                                $("#edit-categoria").val(ticket.CategoriaID).trigger("change");
                                $("#edit-fecha-fin").val(ticket.FechaFin);
                                $("#edit-descripcion").val(ticket.Descripcion);
                            }, 500);

                            cargarComentarios(ticketId);

                            $.ajax({
                                url: `/app/Controllers/TicketController.php?action=obtenerDocumentosPorTicket&ticket_id=${ticket.ID}`,
                                type: "GET",
                                dataType: "json",
                                success: function (docResponse) {
                                    console.log('Docs', docResponse);

                                    let documentosHTML = "";
                                    if (docResponse.status === "success" && docResponse.data
                                        .length > 0) {
                                        docResponse.data.forEach(doc => {
                                            documentosHTML += `
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a href="${doc.RutaArchivo}" target="_blank">
                                                    <i class="fa fa-file me-2"></i>${doc.NombreArchivo}
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger eliminar-documento"
                                                data-id="${doc.ID}" data-ticket="${ticket.ID}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                        </li>`;
                                        });
                                    } else {
                                        documentosHTML =
                                            `<li class="list-group-item text-muted">Sin documentos adjuntos</li>`;
                                    }

                                    $("#edit-ticket-documentos").html(documentosHTML);
                                },
                                error: function () {
                                    $("#edit-ticket-documentos").html(
                                        `<li class="list-group-item text-danger">Error al cargar documentos</li>`
                                    );
                                }
                            });

                            $("#kt_modal_edit_ticket").modal("show");
                        }
                    },
                    error: function () {
                        console.error("Error al cargar los datos del ticket.");
                    }
                });
            }

            function cargarComentarios(ticketId) {
                $.getJSON(
                    `/app/Controllers/TicketController.php?action=obtenerComentarios&ticket_id=${ticketId}`,
                    function (response) {
                        let rows = "";
                        if (response.status === "success") {
                            response.data.forEach(c => {
                                rows +=
                                    `<tr><td>${c.Usuario}</td><td>${c.Comentario}</td><td>${c.FechaCreacion}</td></tr>`;
                            });
                        } else {
                            rows = "<tr><td colspan='2'>Sin comentarios</td></tr>";
                        }
                        $("#tabla_comentarios_ticket tbody").html(rows);
                    });
            }

            $(document).on("click", ".eliminar-documento", function () {
                let docId = $(this).data("id");
                let ticketId = $(this).data("ticket");

                if (!confirm("¿Deseas eliminar este documento adjunto?")) return;

                $.ajax({
                    url: `/app/Controllers/TicketController.php?action=eliminarDocumento`,
                    type: "POST",
                    data: {
                        id: docId
                    },
                    success: function (response) {
                        if (response.status === "success") {
                            cargarDatosEdicion(ticketId);
                        } else {
                            alert("No se pudo eliminar el documento.");
                        }
                    },
                    error: function () {
                        alert("Error al eliminar documento.");
                    }
                });
            });

            $("#btn_agregar_comentario").on("click", function () {
                const comentario = $("#nuevo_comentario_ticket").val().trim();
                const ticketID = $("#edit-ticket-id").val();

                if (comentario.length === 0) return alert("Debes ingresar un comentario.");

                $.post("/app/Controllers/TicketController.php?action=guardarComentario", {
                    ticket_id: ticketID,
                    comentario: comentario
                }, function (response) {
                    if (response.status === "success") {
                        $("#nuevo_comentario_ticket").val("");
                        cargarComentarios(ticketID);
                    } else {
                        alert("No se pudo guardar el comentario.");
                    }
                }, "json");
            });

            function calcularTiempoTranscurrido(fechaCreacion) {
                let fechaCreacionUTC = new Date(fechaCreacion);
                let fechaActual = new Date();
                let offsetCR = -6 * 60; 
                let fechaActualCR = new Date(fechaActual.getTime() + (fechaActual.getTimezoneOffset() + offsetCR) * 60000);
                let diferenciaMS = fechaActualCR - fechaCreacionUTC;
                let dias = Math.floor(diferenciaMS / (1000 * 60 * 60 * 24));
                let horas = Math.floor((diferenciaMS % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                let minutos = Math.floor((diferenciaMS % (1000 * 60 * 60)) / (1000 * 60));
                let textoTiempo = `${dias} días, ${horas} horas y ${minutos} minutos`;
                $("#label-tiempo-transcurrido").text(textoTiempo);
            }

            function mostrarMensaje(mensaje, tipo) {
                var alertDiv = $("#alert-message");

                alertDiv.removeClass("d-none alert-success alert-danger alert-warning")
                    .addClass(tipo)
                    .html(mensaje)
                    .fadeIn();

                setTimeout(function () {
                    alertDiv.fadeOut("slow", function () {
                        $(this).addClass("d-none").html("");
                    });
                }, 10000);
            }

            $("#submit-ticket").on("click", function (e) {
                e.preventDefault();

                if (validarFormulario()) {
                    enviarFormulario();
                }
            });

            $("#kt_modal_new_ticket_form input, #kt_modal_new_ticket_form textarea, #kt_modal_new_ticket_form select")
                .on("input", function () {
                    $("#alert-message").fadeOut();
                });

            function enviarFormulario() {
                var formData = new FormData($("#kt_modal_new_ticket_form")[0]);

                $.ajax({
                    url: "/app/Controllers/TicketController.php?action=procesarTicket",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function (response) {
                        console.log('Ticket', response);
                        if (response.status === "success") {
                            $("#error-message").addClass(
                                "d-none"); 
                            $("#modalMessage .modal-body").html(
                                '<div class="alert alert-success">' + response.message + '</div>'
                            );
                            $("#modalMessage").modal("show").appendTo("body");

                            $("#modalMessage").on("hidden.bs.modal", function () {
                                location.reload();
                            });
                        } else {
                            mostrarMensaje("" + response.message, "alert-danger");
                        }
                    },
                    error: function () {
                        mostrarMensaje("Error en la petición AJAX.", "alert-danger");
                    }
                });

                $("#submit-ticket").prop("disabled", false);
            }

            function inicializarSelect2() {
                $('[data-control="select2"]').select2({
                    placeholder: function () {
                        return $(this).data("placeholder");
                    },
                    minimumResultsForSearch: Infinity,
                    allowClear: true
                });
            }

            function inicializarFlatpickr() {
                flatpickr('input[name="due_date"]', {
                    dateFormat: 'Y-m-d',
                    allowInput: true
                });
            }

            function cargarTickets() {
                $.ajax({
                    url: "/app/Controllers/TicketController.php?action=obtenerTickets",
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        table.clear(); 
                        if (response.status === "success") {
                            response.data.forEach(function (ticket) {
                                let estadoColor = obtenerColorEstado(ticket.EstadoID);
                                table.row.add([
                                    `<input type="checkbox" class="ticket-checkbox" value="${ticket.ID}">`,
                                    ticket.ID,
                                    ticket.TipoProducto,
                                    ticket.Responsable,
                                    `${ticket.Cliente} - ${ticket.Empresa}`,
                                    ticket.Prioridad,
                                    `<span style="color: #fff;" class="badge ${estadoColor}">${ticket.Estado}</span>`,
                                    ticket.FechaCreacion,
                                    ticket.Descripcion,
                                    `<button class="btn btn-icon btn-light-primary edit-ticket" data-id="${ticket.ID}">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <button class="btn btn-icon btn-light-danger delete-ticket" data-id="${ticket.ID}">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            <a href="/app/Views/TicketDetalle.php?id=${ticket.ID}" class="btn btn-icon btn-light-info"><i class="fa fa-eye"></i></a>`
                                ]).draw(false); 
                            });

                            $(".edit-ticket").on("click", function () {
                                let ticketId = $(this).data("id");
                                cargarDatosEdicion(ticketId);
                            });
                        }
                    },
                    error: function (xhr) {
                        console.error("Error en la petición AJAX:", xhr.responseText);
                    }
                });
            }

            function obtenerColorEstado(estadoID) {
                switch (parseInt(estadoID)) {
                    case 3:
                        return "bg-primary"; 
                    case 2:
                        return "bg-warning"; 
                    case 5:
                        return "bg-success"; 
                    case 4:
                        return "bg-info"; 
                    default:
                        return "bg-dark"; 
                }
            }

            $("#searchTicket").on("keyup", function () {
                table.search(this.value).draw();
            });

            cargarTickets();

            function cargarOpcionesSelects() {
                $.ajax({
                    url: "/app/Controllers/TicketController.php?action=obtenerClientes",
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        let select = $("#edit-cliente");
                        select.empty().append('<option value="">Selecciona un cliente...</option>');
                        response.data.forEach(cliente => {
                            select.append(
                                `<option value="${cliente.id}">${cliente.nombre} - ${cliente.empresa}</option>`
                            );
                        });
                    }
                });

                $.ajax({
                    url: "/app/Controllers/TicketController.php?action=obtenerProductos",
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        let select = $("#edit-producto");
                        select.empty().append('<option value="">Selecciona un producto...</option>');
                        response.data.forEach(producto => {
                            select.append(
                                `<option value="${producto.ID}">${producto.Nombre}</option>`
                            );
                        });
                    }
                });

                $.ajax({
                    url: "/app/Controllers/TicketController.php?action=obtenerUsuarios",
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        let select = $("#edit-responsable");
                        select.empty().append('<option value="">Selecciona un responsable...</option>');
                        response.data.forEach(usuario => {
                            select.append(
                                `<option value="${usuario.ID}">${usuario.Nombre}</option>`);
                        });
                    }
                });

                $.ajax({
                    url: "/app/Controllers/TicketController.php?action=obtenerEstados",
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        let select = $("#edit-estado");
                        select.empty().append('<option value="">Selecciona un estado...</option>');
                        response.data.forEach(estado => {
                            select.append(
                                `<option value="${estado.ID}">${estado.Estado}</option>`);
                        });
                    }
                });

                $.ajax({
                    url: "/app/Controllers/TicketController.php?action=obtenerCategorias",
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        let select = $("#edit-categoria");
                        select.empty().append('<option value="">Selecciona una categoría...</option>');
                        response.data.forEach(categoria => {
                            select.append(
                                `<option value="${categoria.id}">${categoria.Nombre}</option>`
                            );
                        });
                    }
                });
            }

            $("#kt_modal_edit_ticket_form").on("submit", function (e) {
                e.preventDefault();

                if (dropzoneEdit.getQueuedFiles().length > 0) {
                    dropzoneEdit.processQueue(); 
                } else {
                    enviarFormularioEdicion(); 
                }
            });

            function enviarFormularioEdicion() {
                let formData = new FormData($("#kt_modal_edit_ticket_form")[0]);

                $.ajax({
                    url: "/app/Controllers/TicketController.php?action=actualizarTicket",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function (response) {
                        console.log("Respuesta del servidor:", response);

                        if (response.status === "success") {
                            $("#kt_modal_edit_ticket").modal("hide");

                            setTimeout(() => {
                                $("#modalSuccess").modal("show");
                                cargarTickets();
                            }, 500);
                        } else {
                            alert("Error al actualizar el ticket.");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error AJAX:", xhr.responseText);
                    }
                });

                $("#submit-edit-ticket").prop("disabled", false);
            }

            let ticketIdToDelete = null;

            $(document).on("click", ".delete-ticket", function () {
                ticketIdToDelete = $(this).data("id");
                $("#modalConfirmDelete").modal("show");
            });

            $("#confirmDelete").on("click", function () {
                if (ticketIdToDelete) {
                    $.ajax({
                        url: `/app/Controllers/TicketController.php?action=eliminarTicket&id=${ticketIdToDelete}`,
                        type: "POST",
                        dataType: "json",
                        success: function (response) {
                            if (response.status === "success") {
                                $("#modalConfirmDelete").modal("hide");
                                cargarTickets(); 
                            } else {
                                $("#modalConfirmDelete").on("hidden.bs.modal", function () {
                                    $("#modalMessage .modal-body").html(
                                        `<div class="alert alert-primary">${response.message}</div>`
                                    );
                                    $("#modalMessage").modal("show");

                                    $(this).off("hidden.bs.modal");
                                });

                                $("#modalConfirmDelete").modal("hide");
                            }
                        },
                        error: function () {
                            alert("Error en la petición AJAX.");
                        }
                    });
                }
            });

            $(document).on("click", ".view-ticket", function () {
                let ticketId = $(this).data("id");

                $.ajax({
                    url: `/app/Controllers/TicketController.php?action=obtenerTicketShow&id=${ticketId}`,
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "success") {
                            let ticket = response.data;

                            console.log('View Ticket', ticket);

                            $("#view-ticket-id").text(ticket.ID);
                            $("#view-ticket-cliente").text(ticket.Cliente);
                            $("#view-ticket-producto").text(ticket.TipoProducto);
                            $("#view-ticket-responsable").text(ticket.Responsable);
                            let estadoColor = obtenerColorEstado(ticket.EstadoID);
                            $("#view-ticket-estado").html(
                                `<span style="color: #fff;" class="badge ${estadoColor}">${ticket.Estado}</span>`
                            );
                            $("#view-ticket-fecha-creacion").text(ticket.FechaCreacion);
                            $("#view-ticket-fecha-fin").text(ticket.FechaFin ? ticket.FechaFin :
                                "No finalizado");
                            $("#view-ticket-descripcion").text(ticket.Descripcion);

                            $.ajax({
                                url: `/app/Controllers/TicketController.php?action=obtenerDocumentosPorTicket&ticket_id=${ticket.ID}`,
                                type: "GET",
                                dataType: "json",
                                success: function (docResponse) {
                                    console.log('documents', docResponse);
                                    let documentosHTML = "";

                                    if (docResponse.status === "success" &&
                                        docResponse.data.length > 0) {
                                        docResponse.data.forEach(doc => {
                                            documentosHTML += `
                                    <li class="list-group-item">
                                        <a href="${doc.RutaArchivo}" target="_blank">
                                            <i class="fa fa-file me-2"></i>${doc.NombreArchivo}
                                        </a>
                                    </li>`;
                                        });
                                    } else {
                                        documentosHTML =
                                            `<li class="list-group-item text-muted">Sin documentos adjuntos</li>`;
                                    }

                                    $("#view-ticket-documentos").html(
                                        documentosHTML);
                                },
                                error: function () {
                                    $("#view-ticket-documentos").html(
                                        `<li class="list-group-item text-danger">Error al cargar documentos</li>`
                                    );
                                }
                            });
                            $("#modalViewTicket").modal("show");
                        } else {
                            alert("Error al obtener los datos del ticket.");
                        }
                    },
                    error: function () {
                        alert("Error en la petición AJAX.");
                    }
                });
            });

        });
    </script>

    <script>
        const icono = document.querySelector('#menu_tickets').previousElementSibling.querySelector('i');
        const span = document.getElementById('menu_tickets');

        icono.style.color = 'white';
        span.style.color = 'white';

        const flexRootElements = document.querySelectorAll('.flex-root');

        flexRootElements.forEach(element => {
            element.setAttribute('style', 'flex: 0 !important;');
        });

        const fkt_app_main = document.querySelectorAll('.app-main');

        fkt_app_main.forEach(element => {
            element.setAttribute('style', 'margin-top: 50px !important;');
        });

        flatpickr('input[name="due_date"]', {
            dateFormat: 'Y-m-d', 
            allowInput: true 
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#kt_modal_new_ticket').on('shown.bs.modal', function () {
                $('[data-control="select2"]').select2({
                    placeholder: function () {
                        return $(this).data('placeholder');
                    },
                    minimumResultsForSearch: Infinity,
                    allowClear: true
                });

                flatpickr('input[name="due_date"]', {
                    dateFormat: 'Y-m-d',
                    allowInput: true
                });
            });

            $('#kt_modal_new_ticket_form').on('submit', function (e) {

                if (myDropzone.getQueuedFiles().length > 0) {
                    e.preventDefault(); 
                    myDropzone.processQueue(); 
                } else {
                    $(this).unbind('submit').submit(); 
                }
                const formData = $(this).serializeArray();
                console.log(formData); 

                $('#kt_modal_new_ticket').modal('hide');
                this.reset();
            });

        });
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

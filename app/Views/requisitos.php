<?php
$title = "Requisitos";
include '../../layout.php';
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $title; ?>
    </title>
    <link href="/public/assets/css/style.bundle.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <style>
        div.dataTables_filter {
            display: none !important;
        }
    </style>
</head>

<body>
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" style="margin-top: 25px;"
                    class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3"
                        style="margin-top: 0; padding-top: 0;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-list-alt fs-2 me-2"></i>
                            <h1 class="page-heading fw-bold fs-3 my-0">Requisito</h1>
                        </div>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="index.html" class="text-muted text-hover-primary">Inicio</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Requisito</li>
                        </ul>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <a href="#" class="btn btn-sm fw-bold btn-primary" data-bs-toggle="modal"
                            data-bs-target="#kt_modal_add_requisito">
                            <i class="fa-solid fa-plus"></i> Agregar Requisito
                        </a>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card card-flush">
                        <div class="card-header pt-8">
                            <div class="card-title">
                                <div class="d-flex align-items-center position-relative my-1">
                                    <i class="fa-solid fa-magnifying-glass position-absolute ms-3"></i>
                                    <input type="text" id="buscador_custom"
                                        class="form-control form-control-solid w-250px ps-10"
                                        placeholder="Buscar requisito" />
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="kt_tarifario_table" class="table align-middle table-row-dashed fs-6 gy-5">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th>ID</th>
                                        <th>Descripción</th>
                                        <th>Categoría</th>
                                        <th>Prioridad</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600" id="kt_tarifario_table_body">
                                    <tr>
                                        <td>1</td>
                                        <td>Servicio de Prueba</td>
                                        <td>Consultoría</td>
                                        <td>$500</td>
                                        <td>
                                            <button class="btn btn-icon btn-light-primary me-2" data-bs-toggle="modal"
                                                data-bs-target="#kt_modal_edit_service">
                                                <i class='fa fa-pencil' aria-hidden='true'></i>
                                            </button>
                                            <button class="btn btn-icon btn-light-danger" data-bs-toggle="modal"
                                                data-bs-target="#kt_modal_delete_service">
                                                <i class='fa-solid fa-trash'></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="kt_modal_add_service" tabindex="-1" style="display: none;"
                data-select2-id="select2-data-kt_modal_add_service" aria-hidden="true">
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
                    </div>
                </div>
            </div>
            <div class="modal fade" id="kt_modal_add_requisito" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="fw-bold"> Agregar Nuevo Requisito</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="form_requisito">
                            <div class="modal-body">
                                <input type="hidden" id="id_requisito" name="id_requisito">
                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion"
                                        required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Categoría</label>
                                    <select class="form-control" id="categoria" name="categoria" required></select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Prioridad</label>
                                    <select class="form-control" id="prioridad" name="prioridad" required></select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary">Guardar Requisito</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                $(document).ready(function () {
                    function loadCategorias(selectedCategoria = null) {
                        return new Promise((resolve, reject) => {
                            $.ajax({
                                url: "/app/Controllers/RequisitosController.php?action=getCategorias",
                                type: "GET",
                                dataType: "json",
                                success: function (response) {
                                    let options =
                                        '<option value="">Seleccione una categoría</option>';
                                    response.data.forEach(categoria => {
                                        options +=
                                            `<option value="${categoria.ID}" ${selectedCategoria == categoria.ID ? "selected" : ""}>${categoria.Nombre}</option>`;
                                    });
                                    $("#categoria").html(options);
                                    resolve();
                                },
                                error: function () {
                                    reject("Error en AJAX");
                                }
                            });
                        });
                    }

                    function loadPrioridades(selectedPrioridad = null) {
                        return new Promise((resolve, reject) => {
                            $.ajax({
                                url: "/app/Controllers/RequisitosController.php?action=getPrioridades",
                                type: "GET",
                                dataType: "json",
                                success: function (response) {
                                    let options =
                                        '<option value="">Seleccione una prioridad</option>';
                                    response.data.forEach(prioridad => {
                                        options +=
                                            `<option value="${prioridad.ID}" ${selectedPrioridad == prioridad.ID ? "selected" : ""}>${prioridad.Nombre}</option>`;
                                    });
                                    $("#prioridad").html(options);
                                    resolve();
                                },
                                error: function () {
                                    reject("Error en AJAX");
                                }
                            });
                        });
                    }

                    $(document).on("click", ".btn-edit", function () {
                        let id = $(this).data("id");
                        let descripcion = $(this).data("descripcion");
                        let categoria = $(this).data("categoria");
                        let prioridad = $(this).data("prioridad");

                        $("#id_requisito").val(id);
                        $("#descripcion").val(descripcion);

                        Promise.all([
                            loadCategorias(categoria),
                            loadPrioridades(prioridad)
                        ]).then(() => $("#kt_modal_add_requisito").modal("show"));

                        $("#btn_submit").text("Actualizar Requisito");
                    });

                    $("#kt_modal_add_requisito").on("show.bs.modal", function (event) {
                        let button = $(event.relatedTarget);
                        if (!button.hasClass("btn-edit")) {
                            $("#form_requisito")[0].reset();
                            loadCategorias();
                            loadPrioridades();
                        }
                    });

                    $("#form_requisito").submit(function (event) {
                        event.preventDefault();
                        let formData = $(this).serialize();
                        let action = $("#id_requisito").val() ? "update" : "create";
                        $.post(`/app/Controllers/RequisitosController.php?action=${action}`, formData,
                            function (response) {
                                alert(response.message);
                                if (response.status === "success") {
                                    $("#kt_modal_add_requisito").modal("hide");
                                    location.reload();
                                }
                            }, "json");
                    });

                    function loadRequisitos() {
                        $.getJSON("/app/Controllers/RequisitosController.php?action=getAllJson", function (
                            response) {
                            let rows = response.data.map(req => `
                            <tr>
                                <td>${req.ID}</td>
                                <td>${req.Descripcion}</td>
                                <td>${req.Categoria}</td>
                                <td>${req.Prioridad}</td>
                                <td>${req.Fecha}</td>
                                <td>
                                    <button class="btn btn-icon btn-light-primary me-2 btn-edit" 
                                        data-id="${req.ID}" data-descripcion="${req.Descripcion}" 
                                        data-categoria="${req.CategoriaID}" data-prioridad="${req.PrioridadID}" 
                                        data-bs-toggle="modal" data-bs-target="#kt_modal_add_requisito">
                                        <i class='fa fa-pencil' aria-hidden='true'></i>
                                    </button>
                                    <button class="btn btn-icon btn-light-danger btn-delete" data-id="${req.ID}">
                                        <i class='fa-solid fa-trash'></i>
                                    </button>
                                </td>
                            </tr>
                            `);

                            $("#kt_tarifario_table tbody").html(rows);

                            if ($.fn.DataTable.isDataTable('#kt_tarifario_table')) {
                                $('#kt_tarifario_table').DataTable().destroy();
                            }

                            let table = $('#kt_tarifario_table').DataTable({
                                paging: true,
                                searching: true,
                                info: true,
                                order: [
                                    [0, 'desc']
                                ],
                                language: {
                                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                                }
                            });

                            $('#buscador_custom').off('keyup').on('keyup', function () {
                                table.search(this.value).draw();
                            });
                        });
                    }

                    $(document).on("click", ".btn-delete", function () {
                        let id = $(this).data("id");
                        if (confirm("¿Estás seguro de que deseas eliminar este requisito?")) {
                            $.post(`/app/Controllers/RequisitosController.php?action=delete`, {
                                id_requisito: id
                            }, function (response) {
                                alert(response.message);
                                if (response.status === "success") {
                                    loadRequisitos();
                                }
                            }, "json");
                        }
                    });

                    loadRequisitos();
                });

                const icono = document.querySelector('#menu_requisitos').previousElementSibling.querySelector('i');
                const span = document.getElementById('menu_requisitos');

                icono.style.color = 'white';
                span.style.color = 'white';

                const flexRootElements = document.querySelectorAll('.flex-root');

                flexRootElements.forEach(element => {
                    element.setAttribute('style', 'flex: 0 !important;');
                });
            </script>

</body>

</html>

<?php
$title = "Comunicados";
include '../../layout.php';
require_once "../../config/database.php";

try {
    $query = "SELECT COUNT(id) AS total_archivos, SUM(tamaño) AS total_tamano FROM dbo.archivos";
    $stmt = $conn->query($query);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalArchivos = $resultado['total_archivos'];
    $totalTamanoGB = round(($resultado['total_tamano'] / (1024 * 1024)), 2);
} catch (PDOException $e) {
    die("Error al obtener datos: " . $e->getMessage());
}
?>

<html lang="en">
    
<style>
    .dataTables_filter {
        display: none;
    }
</style>

<body>
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-bell fs-2 me-2"></i>
                            <h1 class="page-heading text-gray-900 fw-bold fs-3 my-0">Comunicados Emitidos</h1>
                        </div>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a class="text-muted text-hover-primary">Inicio - Comunicados</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card card-flush pb-0 bgi-position-y-center bgi-no-repeat mb-10"
                        style="background-size: auto calc(100% + 10rem); background-position-x: 100%; background-image: url('/assets/media/illustrations/sketchy-1/4.png')">
                        <div class="card-header pt-10">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-circle me-5">
                                    <div
                                        class="symbol-label bg-transparent text-primary border border-secondary border-dashed">
                                        <i class="ki-duotone ki-abstract-47 fs-2x text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h2 class="mb-1">Gestión de Archivos</h2>
                                    <div class="text-muted fw-bold">
                                        <span class="mx-3"></span>
                                        <?= $totalTamanoGB ?> GB
                                        <span class="mx-3">|</span>
                                        <?= $totalArchivos ?> items
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pb-0">
                            <div class="d-flex overflow-auto h-55px">
                                <ul
                                    class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-semibold flex-nowrap">
                                    <li class="nav-item">
                                        <a class="nav-link text-active-primary me-6 active"
                                            href="apps/file-manager/folders.html">Archivos</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-active-primary me-6"
                                            href="Views/ComunicadosFolders.php">Carpetas</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card card-flush">
                        <div id="vistaCarpetas" style="display:none;">
                            <div class="accordion" id="accordionCarpetas">
                            </div>
                        </div>

                        <div class="card-header pt-8">
                            <div class="card-title">
                                <div class="d-flex align-items-center position-relative my-1">
                                    <i class="ki-duotone ki-magnifier fs-1 position-absolute ms-6">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <input type="text" data-kt-filemanager-table-filter="search"
                                        class="form-control form-control-solid w-300px ps-15"
                                        placeholder="Buscar Archivos" id="customSearch" />
                                </div>
                            </div>
                            <div class="card-toolbar">
                                <div class="d-flex justify-content-end" data-kt-filemanager-table-toolbar="base">
                                    <button class="btn btn-flex btn-light-primary me-3" data-bs-toggle="modal"
                                        data-bs-target="#modalCrearCarpeta">
                                        <i class="ki-duotone ki-add-folder fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>Nueva Carpeta
                                    </button>
                                    <button type="button" class="btn btn-flex btn-primary" id="btnSubirArchivo">
                                        <i class="ki-duotone ki-folder-up fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>Subir Archivo
                                    </button>
                                </div>
                                <div class="d-flex justify-content-end align-items-center d-none"
                                    data-kt-filemanager-table-toolbar="selected">
                                    <div class="fw-bold me-5">
                                        <span class="me-2"
                                            data-kt-filemanager-table-select="selected_count"></span>Selected
                                    </div>
                                    <button type="button" class="btn btn-danger"
                                        data-kt-filemanager-table-select="delete_selected">Delete Selected</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="kt_file_manager_list" data-kt-filemanager-table="files"
                                class="table align-middle table-row-dashed fs-6 gy-5">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="w-10px pe-2">
                                            <div
                                                class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                    data-kt-check-target="#kt_file_manager_list .form-check-input"
                                                    value="1" />
                                            </div>
                                        </th>
                                        <th class="min-w-250px">Nombre</th>
                                        <th class="min-w-10px">Tamaño</th>
                                        <th class="min-w-125px">Ultima vez modificado</th>
                                        <th class="w-125px">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="listaComunicadosNew" class="listaComunicados fw-semibold text-gray-600">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <table class="d-none">
                    <tr id="kt_file_manager_new_folder_row" data-kt-filemanager-template="upload">
                        <td></td>
                        <td id="kt_file_manager_add_folder_form" class="fv-row">
                            <div class="d-flex align-items-center">
                                <span id="kt_file_manager_folder_icon">
                                    <i class="ki-duotone ki-folder fs-2x text-primary me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <input type="text" name="new_folder_name" placeholder="Enter the folder name"
                                    class="form-control mw-250px me-3" />
                                <button class="btn btn-icon btn-light-primary me-3" id="kt_file_manager_add_folder">
                                    <span class="indicator-label">
                                        <i class="ki-duotone ki-check fs-1"></i>
                                    </span>
                                    <span class="indicator-progress">
                                        <span class="spinner-border spinner-border-sm align-middle"></span>
                                    </span>
                                </button>
                                <button class="btn btn-icon btn-light-danger" id="kt_file_manager_cancel_folder">
                                    <span class="indicator-label">
                                        <i class="ki-duotone ki-cross fs-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span class="indicator-progress">
                                        <span class="spinner-border spinner-border-sm align-middle"></span>
                                    </span>
                                </button>
                            </div>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
                <div class="d-none" data-kt-filemanager-template="rename">
                    <div class="fv-row">
                        <div class="d-flex align-items-center">
                            <span id="kt_file_manager_rename_folder_icon"></span>
                            <input type="text" id="kt_file_manager_rename_input" name="rename_folder_name"
                                placeholder="Enter the new folder name" class="form-control mw-250px me-3" value="" />
                            <button class="btn btn-icon btn-light-primary me-3" id="kt_file_manager_rename_folder">
                                <i class="ki-duotone ki-check fs-1"></i>
                            </button>
                            <button class="btn btn-icon btn-light-danger" id="kt_file_manager_rename_folder_cancel">
                                <span class="indicator-label">
                                    <i class="ki-duotone ki-cross fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <span class="indicator-progress">
                                    <span class="spinner-border spinner-border-sm align-middle"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="d-none" data-kt-filemanager-template="action">
                    <div class="d-flex justify-content-end">
                        <div class="ms-2" data-kt-filemanger-table="copy_link">
                            <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary"
                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-fasten fs-5 m-0">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </button>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-300px"
                                data-kt-menu="true">
                                <div class="card card-flush">
                                    <div class="card-body p-5">
                                        <div class="d-flex" data-kt-filemanger-table="copy_link_generator">
                                            <div class="me-5" data-kt-indicator="on">
                                                <span class="indicator-progress">
                                                    <span
                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                            </div>
                                            <div class="fs-6 text-gray-900">Generating Share Link...</div>
                                        </div>
                                        <div class="d-flex flex-column text-start d-none"
                                            data-kt-filemanger-table="copy_link_result">
                                            <div class="d-flex mb-3">
                                                <i class="ki-duotone ki-check fs-2 text-success me-3"></i>
                                                <div class="fs-6 text-gray-900">Share Link Generated</div>
                                            </div>
                                            <input type="text" class="form-control form-control-sm"
                                                value="https://path/to/file/or/folder/" />
                                            <div class="text-muted fw-normal mt-2 fs-8 px-3">Read only.
                                                <a href="apps/file-manager/settings/.html" class="ms-2">Change
                                                    permissions</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ms-2">
                            <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary"
                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-dots-square fs-5 m-0">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </button>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4"
                                data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3">Download File</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3" data-kt-filemanager-table="rename">Rename</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3" data-kt-filemanager-table-filter="move_row"
                                        data-bs-toggle="modal" data-bs-target="#kt_modal_move_to_folder">Move to
                                        folder</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link text-danger px-3"
                                        data-kt-filemanager-table-filter="delete_row">Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-none" data-kt-filemanager-template="checkbox">
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" value="1" />
                    </div>
                </div>
                <div class="modal fade" id="kt_modal_upload" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered mw-650px">
                        <div class="modal-content">
                            <form class="form" action="none" id="kt_modal_upload_form">
                                <div class="modal-header">
                                    <h2 class="fw-bold">Upload files</h2>
                                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                                        <i class="ki-duotone ki-cross fs-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <div class="modal-body pt-10 pb-15 px-lg-17">
                                    <div class="form-group">
                                        <div class="dropzone dropzone-queue mb-2" id="kt_modal_upload_dropzone">
                                            <div class="dropzone-panel mb-4">
                                                <a class="dropzone-select btn btn-sm btn-primary me-2">Attach
                                                    files</a>
                                                <a class="dropzone-upload btn btn-sm btn-light-primary me-2">Upload
                                                    All</a>
                                                <a class="dropzone-remove-all btn btn-sm btn-light-primary">Remove
                                                    All</a>
                                            </div>
                                            <div class="dropzone-items wm-200px">
                                                <div class="dropzone-item p-5" style="display:none">
                                                    <div class="dropzone-file">
                                                        <div class="dropzone-filename text-gray-900"
                                                            title="some_image_file_name.jpg">
                                                            <span data-dz-name="">some_image_file_name.jpg</span>
                                                            <strong>(
                                                                <span data-dz-size="">340kb</span>)</strong>
                                                        </div>
                                                        <div class="dropzone-error mt-0" data-dz-errormessage="">
                                                        </div>
                                                    </div>
                                                    <div class="dropzone-progress">
                                                        <div class="progress bg-gray-300">
                                                            <div class="progress-bar bg-primary" role="progressbar"
                                                                aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"
                                                                data-dz-uploadprogress=""></div>
                                                        </div>
                                                    </div>
                                                    <div class="dropzone-toolbar">
                                                        <span class="dropzone-start">
                                                            <i class="ki-duotone ki-to-right fs-1"></i>
                                                        </span>
                                                        <span class="dropzone-cancel" data-dz-remove=""
                                                            style="display: none;">
                                                            <i class="ki-duotone ki-cross fs-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        </span>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="file" id="archivoInput" class="form-control w-50">
                                        <button id="btnSubirArchivo" class="btn btn-primary">📤 Subir Archivo</button>
                                        <span class="form-text fs-6 text-muted">Max file size is 1MB per
                                            file.</span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="kt_modal_move_to_folder" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered mw-650px">
                        <div class="modal-content">
                            <form class="form" action="#" id="kt_modal_move_to_folder_form">
                                <div class="modal-header">
                                    <h2 class="fw-bold">Move to folder</h2>
                                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                                        <i class="ki-duotone ki-cross fs-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <div class="modal-body pt-10 pb-15 px-lg-17">
                                    <div class="form-group fv-row">
                                        <div class="d-flex">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input me-3" name="move_to_folder" type="radio"
                                                    value="0" id="kt_modal_move_to_folder_0" />
                                                <label class="form-check-label" for="kt_modal_move_to_folder_0">
                                                    <div class="fw-bold">
                                                        <i class="ki-duotone ki-folder fs-2 text-primary me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>account
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class='separator separator-dashed my-5'></div>
                                        <div class="d-flex">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input me-3" name="move_to_folder" type="radio"
                                                    value="1" id="kt_modal_move_to_folder_1" />
                                                <label class="form-check-label" for="kt_modal_move_to_folder_1">
                                                    <div class="fw-bold">
                                                        <i class="ki-duotone ki-folder fs-2 text-primary me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>apps
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class='separator separator-dashed my-5'></div>
                                        <div class="d-flex">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input me-3" name="move_to_folder" type="radio"
                                                    value="2" id="kt_modal_move_to_folder_2" />
                                                <label class="form-check-label" for="kt_modal_move_to_folder_2">
                                                    <div class="fw-bold">
                                                        <i class="ki-duotone ki-folder fs-2 text-primary me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>widgets
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class='separator separator-dashed my-5'></div>
                                        <div class="d-flex">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input me-3" name="move_to_folder" type="radio"
                                                    value="3" id="kt_modal_move_to_folder_3" />
                                                <label class="form-check-label" for="kt_modal_move_to_folder_3">
                                                    <div class="fw-bold">
                                                        <i class="ki-duotone ki-folder fs-2 text-primary me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>assets
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class='separator separator-dashed my-5'></div>
                                        <div class="d-flex">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input me-3" name="move_to_folder" type="radio"
                                                    value="4" id="kt_modal_move_to_folder_4" />
                                                <label class="form-check-label" for="kt_modal_move_to_folder_4">
                                                    <div class="fw-bold">
                                                        <i class="ki-duotone ki-folder fs-2 text-primary me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>documentation
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class='separator separator-dashed my-5'></div>
                                        <div class="d-flex">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input me-3" name="move_to_folder" type="radio"
                                                    value="5" id="kt_modal_move_to_folder_5" />
                                                <label class="form-check-label" for="kt_modal_move_to_folder_5">
                                                    <div class="fw-bold">
                                                        <i class="ki-duotone ki-folder fs-2 text-primary me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>layouts
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class='separator separator-dashed my-5'></div>
                                        <div class="d-flex">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input me-3" name="move_to_folder" type="radio"
                                                    value="6" id="kt_modal_move_to_folder_6" />
                                                <label class="form-check-label" for="kt_modal_move_to_folder_6">
                                                    <div class="fw-bold">
                                                        <i class="ki-duotone ki-folder fs-2 text-primary me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>modals
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class='separator separator-dashed my-5'></div>
                                        <div class="d-flex">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input me-3" name="move_to_folder" type="radio"
                                                    value="7" id="kt_modal_move_to_folder_7" />
                                                <label class="form-check-label" for="kt_modal_move_to_folder_7">
                                                    <div class="fw-bold">
                                                        <i class="ki-duotone ki-folder fs-2 text-primary me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>authentication
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class='separator separator-dashed my-5'></div>
                                        <div class="d-flex">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input me-3" name="move_to_folder" type="radio"
                                                    value="8" id="kt_modal_move_to_folder_8" />
                                                <label class="form-check-label" for="kt_modal_move_to_folder_8">
                                                    <div class="fw-bold">
                                                        <i class="ki-duotone ki-folder fs-2 text-primary me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>dashboards
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class='separator separator-dashed my-5'></div>
                                        <div class="d-flex">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input me-3" name="move_to_folder" type="radio"
                                                    value="9" id="kt_modal_move_to_folder_9" />
                                                <label class="form-check-label" for="kt_modal_move_to_folder_9">
                                                    <div class="fw-bold">
                                                        <i class="ki-duotone ki-folder fs-2 text-primary me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>pages
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-center mt-12">
                                        <button type="button" class="btn btn-primary"
                                            id="kt_modal_move_to_folder_submit">
                                            <span class="indicator-label">Save</span>
                                            <span class="indicator-progress">Please wait...
                                                <span
                                                    class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        </button>
                                    </div>
                                </div>
                            </form>
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
    <div class="modal fade" id="modalSubidaArchivo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Subir Archivos (Solo formato PDF)</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form action="/app/Controllers/ComunicadosController.php?action=subirArchivo" class="dropzone"
                        id="formDropzone">
                        <div class="dz-message">
                            <span>Arrastra archivos aquí o haz clic para subir archivos pdf</span>
                        </div>
                    </form>
                    <button id="btnSubirDropzone" class="btn btn-primary mt-3">📤 Subir Archivos</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalCrearCarpeta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formCrearCarpetaConArchivos" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Carpeta con Archivos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control mb-3" name="nombre" placeholder="Nombre de la carpeta"
                        required>
                    <input type="hidden" id="carpetaCreadaId" name="carpeta_id">
                    <label for="archivosExistentes">Seleccionar archivos existentes:</label>
                    <select id="archivosExistentes" name="archivos[]" multiple class="form-select"
                        style="height: 150px;">
                    </select>
                    <small class="form-text text-muted">Usa Ctrl (o Cmd en Mac) para seleccionar varios
                        archivos.</small>
                    
                    <br>

                    <label class="mt-4">Subir nuevos archivos (opcional):</label>
                    <input type="file" name="archivo[]" multiple class="form-control" accept=".pdf">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Crear Carpeta y Asociar Archivos</button>
                </div>
            </form>
        </div>
    </div>
    <div id="toastEnlaceCopiado" style="
        display: none;
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #198754;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        z-index: 9999;
    ">
        📋 Link copiado al portapapeles
    </div>
    <div class="modal fade" id="modalRelacionarCarpeta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formRelacionarArchivoCarpeta" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Relacionar archivo a carpeta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="archivo_id" id="archivoIdRelacionar">
                    <label>Selecciona una carpeta:</label>
                    <select name="carpeta_id" id="selectCarpetasRelacionar" class="form-select" required>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Relacionar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        var hostUrl = "/public/assets/";
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

    <script>
        $(document).ready(function () {
            console.log("jQuery:", typeof jQuery);
            console.log("Select2:", typeof $.fn.select2);

            $(document).on("click", ".btn-relacionar-carpeta", function () {
                const archivoId = $(this).data("id");
                $("#archivoIdRelacionar").val(archivoId);

                $.ajax({
                    url: "/app/Controllers/ComunicadosController.php?action=obtenerCarpetasConArchivos",
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "success") {
                            const $select = $("#selectCarpetasRelacionar");
                            $select.empty();
                            response.data.forEach(carpeta => {
                                $select.append(
                                    `<option value="${carpeta.id}">${carpeta.nombre}</option>`
                                );
                            });
                            $("#modalRelacionarCarpeta").modal("show");
                        }
                    }
                });
            });


            $("#formRelacionarArchivoCarpeta").on("submit", function (e) {
                e.preventDefault();
                const formData = $(this).serialize();

                $.post("/app/Controllers/ComunicadosController.php?action=relacionarArchivoACarpeta",
                    formData,
                    function (res) {
                        const response = JSON.parse(res);
                        if (response.status === "success") {
                            alert("Archivo relacionado correctamente");
                            $("#modalRelacionarCarpeta").modal("hide");
                            cargarComunicadosNew();
                        } else {
                            alert(response.message);
                        }
                    });
            });

            $(document).on("click", ".btn-copiar-enlace", function () {
                const url = window.location.origin + $(this).data("enlace");

                navigator.clipboard.writeText(url).then(() => {
                    $("#toastEnlaceCopiado").fadeIn(200);

                    setTimeout(() => {
                        $("#toastEnlaceCopiado").fadeOut(300);
                    }, 2000);
                }).catch(err => {
                    alert("Error al copiar el enlace");
                    console.error(err);
                });
            });

            $('#modalCrearCarpeta').on('shown.bs.modal', function () {
                const $select = $('#archivosExistentes');
                $select.empty();

                $.ajax({
                    url: '/app/Controllers/ComunicadosController.php?action=listarArchivos',
                    method: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if (res.status === 'success') {
                            res.data.forEach(archivo => {
                                $select.append(
                                    `<option value="${archivo.id}">${archivo.nombre}</option>`
                                );
                            });
                        } else {
                            alert("No se pudieron cargar los archivos");
                        }
                    },
                    error: function (err) {
                        console.error("Error AJAX:", err);
                    }
                });
            });
        });
    </script>

    <script>
        $('#formCrearCarpetaConArchivos').on('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            $.ajax({
                url: '/app/Controllers/ComunicadosController.php?action=crearCarpetaConArchivos',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (res) {
                    const response = JSON.parse(res);
                    if (response.status === 'success') {
                        alert('Carpeta creada con éxito');

                        $('#carpetaCreadaId').val(response.carpeta_id);

                        let archivosNuevos = $('input[name="archivo[]"]')[0].files;
                        if (archivosNuevos.length > 0) {
                            for (let i = 0; i < archivosNuevos.length; i++) {
                                const archivo = archivosNuevos[i];
                                const formData = new FormData();
                                formData.append("archivo", archivo);
                                formData.append("carpeta_id", response.carpeta_id);

                                let archivosInvalidos = Array.from(archivosNuevos).filter(file => !file
                                    .name.endsWith('.pdf'));
                                if (archivosInvalidos.length > 0) {
                                    alert("Solo se permiten archivos PDF. Archivo inválido: " +
                                        archivosInvalidos[0].name);
                                    return;
                                }

                                $.ajax({
                                    url: "/app/Controllers/ComunicadosController.php?action=subirArchivo",
                                    type: "POST",
                                    data: formData,
                                    contentType: false,
                                    processData: false,
                                    success: function (subidaRes) {
                                        const res = JSON.parse(subidaRes);
                                        if (res.status === "success") {
                                            console.log("Archivo subido con carpeta:",
                                                archivo.name);
                                        } else {
                                            console.error("Archivo no subido:", archivo
                                                .name);
                                        }
                                    },
                                    error: function () {
                                        console.error("Error subiendo archivo:", archivo
                                            .name);
                                    }
                                });
                            }
                        }

                        $('#modalCrearCarpeta').modal('hide');
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        });

        $(document).ready(function () {
            var table;

            function cargarComunicadosNew() {
                $.ajax({
                    url: "/app/Controllers/ComunicadosController.php?action=obtenerComunicados",
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        let html = "";
                        response.data.forEach(c => {
                            html += `
                <tr>
                    <td>
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" value="${c.id}" />
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-files fs-2x text-primary me-4"></i>
                            <a href="${c.ruta}" class="text-gray-800 text-hover-primary" target="_blank">
                                ${c.nombre}
                            </a>
                        </div>
                    </td>
                    <td>${c.tamaño ? c.tamaño + ' KB' : '-'}</td>
                    <td>${c.fecha_modificacion}</td>
                    <td class="text-end align-middle">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-icon btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i> 
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="${c.ruta}" download>
                                    <i class="fa-solid fa-file-arrow-down me-2 text-primary"></i> Descargar
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="${c.ruta}" target="_blank">
                                    <i class="fa-solid fa-eye me-2 text-info"></i> Ver
                                </a>
                            </li>
                            <li>
                                <button class="dropdown-item btn-renombrar" data-id="${c.id}" data-nombre="${c.nombre}">
                                    <i class="fa fa-pencil me-2 text-warning"></i> Renombrar
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item btn-eliminar text-danger" data-id="${c.id}">
                                    <i class="fa-solid fa-trash me-2"></i> Eliminar
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item btn-copiar-enlace" data-enlace="${c.ruta}">
                                    <i class="fa-solid fa-copy me-2 text-success"></i> Copiar enlace
                                </button>
                            </li>
                            <li>
                                <a class="dropdown-item text-primary btn-relacionar-carpeta"
                                    data-id="${c.id}" 
                                    data-nombre="${c.nombre}">
                                    <i class="fas fa-folder"></i> Agregar o mover a carpeta
                                </a>
                            </li>
                        </ul>
                    </div>
                    </div>
                    </td>

                </tr>`;
                        });

                        if ($.fn.DataTable.isDataTable("#kt_file_manager_list")) {
                            table.destroy();
                        }

                        $("#listaComunicadosNew").html(html);

                        setTimeout(() => {
                            table = $("#kt_file_manager_list").DataTable({
                                "paging": true,
                                "lengthMenu": [10, 25, 50],
                                "pageLength": 10,
                                "ordering": true,
                                "order": [
                                    [3, "desc"]
                                ],
                                "info": true,
                                "searching": true,
                                "language": {
                                    "lengthMenu": "Mostrar _MENU_ registros por página",
                                    "zeroRecords": "No se encontraron resultados",
                                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                                    "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                                    "infoFiltered": "(filtrado de _MAX_ registros totales)",
                                    "search": "Buscar:",
                                    "paginate": {
                                        "first": "Primero",
                                        "last": "Último",
                                        "next": "Siguiente",
                                        "previous": "Anterior"
                                    }
                                }
                            });
                        }, 500); 
                    }
                });
            }

            cargarComunicadosNew();

            $(document).on("keyup", "#customSearch", function () {
                if (table) {
                    table.search(this.value).draw();
                }
            });

            $("#btnSubirArchivo").click(function () {
                $("#modalSubidaArchivo").modal("show");
            });

            Dropzone.autoDiscover = false;

            let dropzoneInstance = new Dropzone("#formDropzone", {
                url: "/app/Controllers/ComunicadosController.php?action=subirArchivo",
                paramName: "archivo",
                maxFilesize: 5,
                acceptedFiles: ".pdf",
                autoProcessQueue: false,
                addRemoveLinks: true,
                dictDefaultMessage: "Arrastra archivos aquí o haz clic para subir",
                dictRemoveFile: "Eliminar",
                init: function () {
                    let myDropzone = this;

                    this.on("success", function (file, response) {
                        console.log("Archivo subido:", file.name);

                        setTimeout(function () {
                            alert("Archivo(s) subido(s) correctamente.");
                            $("#modalSubidaArchivo").modal("hide");
                            myDropzone.removeAllFiles(true);
                            cargarComunicadosNew();
                        }, 1000);
                    });

                    this.on("error", function (file, response) {
                        console.error("Error al subir archivo:", response);
                        alert("Error al subir el archivo.");
                    });

                    $("#btnSubirDropzone").click(function () {
                        if (myDropzone.files.length === 0) {
                            alert("No hay archivos seleccionados.");
                            return;
                        }
                        myDropzone.processQueue();
                    });
                }
            });

            $(document).on("click", ".btn-eliminar", function () {
                let id = $(this).data("id");
                let tipo = "archivo";

                $.ajax({
                    url: "/app/Controllers/ComunicadosController.php?action=obtenerInfoArchivo&id=" +
                        id,
                    type: "GET",
                    dataType: "json",
                    success: function (res) {
                        if (res.status === "success") {
                            let nombreArchivo = res.data.archivo_nombre;
                            let carpeta = res.data.carpeta_nombre;
                            let mensaje =
                                `¿Estás seguro que deseas eliminar el archivo: "${nombreArchivo}"?`;

                            if (carpeta) {
                                mensaje +=
                                    `\nEste archivo pertenece a la carpeta: "${carpeta}".`;
                            }

                            if (confirm(mensaje)) {
                                $.ajax({
                                    url: "/app/Controllers/ComunicadosController.php?action=eliminarComunicado",
                                    type: "POST",
                                    data: {
                                        id,
                                        tipo
                                    },
                                    dataType: "json",
                                    success: function (response) {
                                        if (response.status === "success") {
                                            alert(
                                                "Archivo eliminado correctamente."
                                            );
                                            cargarComunicadosNew
                                                ();
                                        } else {
                                            alert("Error: " + response.message);
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        alert(
                                            "Ocurrió un error en la eliminación."
                                        );
                                    }
                                });
                            }
                        } else {
                            alert("No se pudo obtener la información del archivo.");
                        }
                    },
                    error: function () {
                        alert("Error al obtener información del archivo.");
                    }
                });
            });

            $(document).on("click", ".btn-renombrar", function () {
                let id = $(this).data("id");
                let nombreActual = $(this).data("nombre");
                let nuevoNombre = prompt("Ingrese el nuevo nombre del archivo:", nombreActual);

                if (nuevoNombre && nuevoNombre !== nombreActual) {
                    $.ajax({
                        url: "/app/Controllers/ComunicadosController.php?action=renombrarComunicado",
                        type: "POST",
                        data: {
                            id,
                            nombre: nuevoNombre,
                            tipo: "archivo"
                        },
                        dataType: "json",
                        success: function (response) {
                            if (response.status === "success") {
                                alert("Archivo renombrado correctamente.");
                                cargarComunicadosNew
                                    ();
                            } else {
                                alert("Error al renombrar el archivo: " + response.message);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error en la solicitud AJAX:", error);
                            alert("Ocurrió un error al procesar la solicitud.");
                        }
                    });
                }
            });
            $("#btnCrearCarpeta").click(function () {
                let nombreCarpeta = prompt("Ingrese el nombre de la carpeta:");
                if (!nombreCarpeta) return;

                $.post("/app/Controllers/ComunicadosController.php?action=crearCarpeta", {
                    nombre: nombreCarpeta
                }, function () {
                    cargarComunicados();
                });
            });

        });
    </script>

    <script>
        var hostUrl = "/assets/";
    </script>

    <script>
        Dropzone.autoDiscover = false;

        let myDropzone = new Dropzone("#kt_modal_upload_dropzone", {
            url: "/app/Controllers/ComunicadosController.php?action=subirArchivo",
            paramName: "archivo",
            maxFilesize: 1, 
            acceptedFiles: ".pdf",
            addRemoveLinks: true,
            success: function (file, response) {
                alert("Archivo subido correctamente");
                myDropzone.removeFile(file);
            },
            error: function (file, response) {
                alert("Solo se permiten archivos PDF.");
                this.removeFile(file);
            }

        });

        const icono = document.querySelector('#menu_comunicados').previousElementSibling.querySelector('i');
        const span = document.getElementById('menu_comunicados');

        icono.style.color = 'white';
        span.style.color = 'white';
    </script>

</body>

</html>

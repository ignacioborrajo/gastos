<?php
session_start();

if (isset($_SESSION['usuario'])) {
    include 'assets/bbdd/conectar.php';
    $miembros = $db->select("usuarios", ["id", "nombre"]);

    if (isset($_POST['guardar_categoria']) && !$db->has("viajes_familias", ["nombre" => $_POST['nombre_categoria']])) {
        $db->insert("viajes_familias", ["nombre" => $_POST['nombre_categoria']]);
    }

    if (isset($_POST['guardar_familia']) && !$db->has("viajes_familias", ["nombre" => $_POST['nombre_familia']]) && $_POST['categoria'] != '') {
        if (isset($_POST['tiene_ticket']) && $_POST['tiene_ticket'] == 'S')
            $tiene_ticket = 'S';
        else
            $tiene_ticket = 'N';
        $db->insert("viajes_familias", ["nombre" => $_POST['nombre_familia'], "padre" => $_POST['categoria'], "ticket" => $tiene_ticket]);
    }

    $familias = array();
    $categorias = $db->select("viajes_familias", ["id", "nombre", "icono", "padre", "ticket"], ["padre" => 0, "ORDER" => ["nombre" => "ASC"]]);
    foreach ($categorias as $categoria) {
        $familias[] = $categoria;
        $familia = $db->select("viajes_familias", ["id", "nombre", "icono", "padre", "ticket"], ["padre" => $categoria['id'], "ORDER" => ["nombre" => "ASC"]]);
        foreach ($familia as $item) {
            $familias[] = $item;
        }
    }
} else {
    header('Location: index.php', true, 301);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <!-- begin::Head -->
    <head>
        <meta charset="utf-8" />
        <title>
            Gastos Comunes
        </title>
        <meta name="description" content="Latest updates and statistic charts">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!--begin::Web font -->
        <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
        <script>
            WebFont.load({
                google: {"families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]},
                active: function () {
                    sessionStorage.fonts = true;
                }
            });
        </script>
        <!--end::Web font -->
        <!--begin::Base Styles -->  
        <!--begin::Page Vendors -->
        <link href="assets/vendors/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
        <!--end::Page Vendors -->
        <link href="assets/vendors/base/vendors.bundle.css" rel="stylesheet" type="text/css" />
        <link href="assets/demo/default/base/style.bundle.css" rel="stylesheet" type="text/css" />
        <!--end::Base Styles -->
        <link rel="shortcut icon" href="assets/demo/default/media/img/logo/favicon.ico" />
        <script type="text/javascript">
            var viaje = <?php echo $_GET['id']; ?>;
        </script>
    </head>
    <!-- end::Head -->
    <!-- end::Body -->
    <body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default"  >
        <!-- begin:: Page -->
        <div class="m-grid m-grid--hor m-grid--root m-page">
            <!-- BEGIN: Header -->
            <?php include 'header.php'; ?>
            <!-- END: Header -->		
            <!-- begin::Body -->
            <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">
                <!-- BEGIN: Left Aside -->
                <button class="m-aside-left-close  m-aside-left-close--skin-dark " id="m_aside_left_close_btn">
                    <i class="la la-close"></i>
                </button>
                <div id="m_aside_left" class="m-grid__item	m-aside-left  m-aside-left--skin-dark ">
                    <!-- BEGIN: Aside Menu -->
                    <?php include 'menu.php'; ?>
                    <!-- END: Aside Menu -->
                </div>
                <!-- END: Left Aside -->
                <div class="m-grid__item m-grid__item--fluid m-wrapper">
                    <div class="m-content">
                        <!--Begin::Main Portlet-->
                        <div class="m-portlet m--bg-accent m-portlet--bordered-semi m-portlet--space m-portlet--full-height ">
                            <div class="m-portlet__body">
                                <div class="text-center">
                                    <h1 class="text-white" id="nombre_viaje"></h1>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <!--begin:: Widgets/Announcements 2-->
                                <div class="m-portlet m-portlet--bordered-semi m-portlet--space m-portlet--skin-light m-portlet--full-height ">
                                    <div class="m-portlet__body">
                                        <!--begin::Widget 7-->
                                        <div class="m-widget7 m-widget7--skin-dark">
                                            <h2 class="text-center" style="margin-top: 20px;"><i class="fa fa-sign-out" style="font-size: 2rem"></i> <span id="fecha_inicio"></span></h2>
                                            <h2 class="text-center"><i class="fa fa-sign-in" style="font-size: 2rem"></i> <span id="fecha_fin"></span></h2>
                                        </div>
                                        <!--end::Widget 7-->
                                    </div>
                                </div>
                                <!--end:: Widgets/Announcements 2-->
                            </div>
                            <div class="col-md-8">
                                <!--begin:: Widgets/Product Sales-->
                                <div class="m-portlet m--bg-danger m-portlet--bordered-semi m-portlet--space m-portlet--skin-dark m-portlet--full-height ">
                                    <div class="m-portlet__body">
                                        <div class="m-widget25 text-center">
                                            <span id="total_viaje" class="m-widget25__price text-white"></span>
                                        </div>
                                    </div>
                                </div>
                                <!--end:: Widgets/Product Sales-->
                            </div>
                        </div>

                        <!--begin::Form-->
                        <form id="gastos_form" class="m-form m-form--state mb-5 mt-4">
                            <div class="m-portlet__body">
                                <p>Nuevo gasto</p>
                                <div class="m-form__content">
                                    <div class="m-alert m-alert--icon alert alert-danger m--hide" role="alert" id="m_form_1_msg">
                                        <div class="m-alert__icon">
                                            <i class="la la-warning"></i>
                                        </div>
                                        <div class="m-alert__text">
                                            Faltan datos. Cubre todos los campos.
                                        </div>
                                        <div class="m-alert__close">
                                            <button type="button" class="close" data-close="alert" aria-label="Close"></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-form__section m-form__section--first">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <select id="usuario_picker" name="usuario" class="form-control form-control-lg m-input m-bootstrap-select--pill m-bootstrap-select m_selectpicker" data-style="btn-primary btn-lg">
                                                <?php
                                                foreach ($miembros as $miembro) {
                                                    echo '<option value="' . $miembro['id'] . '" ' . ($miembro['id'] == $usuario['id'] ? 'selected' : '') . '>' . $miembro['nombre'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-3">
                                            <select id="gastos_picker" name="familia" class="form-control form-control-lg m-input m-bootstrap-select--pill m-bootstrap-select m_selectpicker" data-style="btn-danger btn-lg" title="Escoge un gasto">

                                            </select>
                                            <a href="#" id="mostrar_familias" data-toggle="modal" data-target="#familias_modal">Añadir Familia</a>
                                            <a href="#" id="mostrar_ticket" data-toggle="modal" data-target="#ticket_modal" style="visibility: hidden"> | Mostrar Ticket</a>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="m-input-icon m-input-icon--right">
                                                        <input type="text" name="fecha" class="form-control form-control-lg m-input m-input--pill" id="m_datetimepicker" value="<?= date("d/m/Y") ?>"/>
                                                        <span class="m-input-icon__icon m-input-icon__icon--right">
                                                            <span>
                                                                <i class="la la-calendar-check-o"></i>
                                                            </span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="m-input-icon m-input-icon--right">
                                                        <input type="number" id="importe_total" name="importe" class="form-control form-control-lg m-input m-input--pill" placeholder="Importe">
                                                        <span class="m-input-icon__icon m-input-icon__icon--right">
                                                            <span>
                                                                <i class="fa fa-euro"></i>
                                                            </span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="m--margin-top-20 m--visible-tablet-and-mobile"></div>
                                                    <button type="button" id="enviar_gasto" class="btn btn-lg btn-brand btn-block m-btn m-btn--icon m-btn--pill">
                                                        <i class="fa fa-save"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </form>
                        <!--end::Form-->
                        <!--begin::Modal-->
                        <div class="modal fade" id="ticket_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            Ticket
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">
                                                &times;
                                            </span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="ticket_form">
                                            <div id="m_repeater_1">
                                                <div class="form-group  m-form__group row" id="m_repeater_1">
                                                    <div id="linea-repetida" data-repeater-list="ticket" class="col-lg-12">
                                                        <div data-repeater-item class="form-group m-form__group row align-items-center">
                                                            <div class="col-md-5">
                                                                <div class="">
                                                                    <input type="text" class="form-control m-input m-input--pill t_producto" name="producto" placeholder="Producto">
                                                                </div>
                                                                <div class="d-md-none m--margin-bottom-10"></div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div class="">
                                                                    <input type="number" class="form-control m-input m-input--pill importe_ticket" name="t_importe" placeholder="Importe">
                                                                </div>
                                                                <div class="d-md-none m--margin-bottom-10"></div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div data-repeater-delete="" class="btn-sm btn-block btn btn-danger m-btn m-btn--icon m-btn--pill">
                                                                    <span>
                                                                        <i class="la la-trash-o"></i>
                                                                        <span>
                                                                            Borrar
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="m-form__group form-group row">
                                                    <label class="col-lg-10 col-form-label text-right pt-1 pb-1">
                                                        <h4 class="m-0" id="temp_total_ticket">0€</h4>
                                                    </label>
                                                    <div class="col-lg-2">
                                                        <div data-repeater-create="" class="btn btn-block btn btn-sm btn-brand m-btn m-btn--icon m-btn--pill m-btn--wide pull-right">
                                                            <span>
                                                                <i class="la la-plus"></i>
                                                                <span>
                                                                    Añadir
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger m-btn--pill" data-dismiss="modal" id="cancel_modal">
                                            Eliminar
                                        </button>
                                        <button type="button" class="btn btn-success m-btn--pill" data-dismiss="modal" id="guardar_ticket">
                                            Guardar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="familias_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">
                                            Familias de gastos
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">
                                                &times;
                                            </span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5>Familias</h5>
                                                <div class="m-demo" data-code-preview="true" data-code-html="true" data-code-js="false">
                                                    <div class="m-demo__preview">
                                                        <ul id="lista_familias" class="m-nav">

                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <form id="categoria_form" class="m-form m-form--fit m-form--label-align-right">
                                                    <input type="hidden" id="id_familia" name="id_categoria" value="">
                                                    <div class="m-portlet__body">
                                                        <div class="form-group m-form__group m--margin-top-10">
                                                            <h5>Nueva Categoria</h5>
                                                        </div>
                                                        <div class="form-group m-form__group">
                                                            <label for="exampleInputEmail1">
                                                                Nombre
                                                            </label>
                                                            <input type="hidden" name="tabla" value="viajes_familias">
                                                            <input type="text" id="nombre_categoria" name="nombre_categoria" class="form-control m-input m-input--pill" id="exampleInputEmail1" placeholder="Escribe un nombre para la categoría">
                                                        </div>
                                                        <div class="m-form__actions">
                                                            <button type="submit" id="guardar_categoria" class="btn btn-brand pull-right">
                                                                Guardar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <hr>
                                                <form id="familia_form" class="m-form m-form--fit m-form--label-align-right">
                                                    <input type="hidden" id="id_familia" name="id_familia" value="">
                                                    <div class="m-portlet__body">
                                                        <div class="form-group m-form__group m--margin-top-10">
                                                            <h5>Nueva Familia</h5>
                                                        </div>
                                                        <div class="form-group m-form__group">
                                                            <label for="exampleInputEmail1">
                                                                Categoría
                                                            </label>
                                                            <input type="hidden" name="tabla" value="viajes_familias">
                                                            <select id="categoria" name="categoria" class="form-control m-input">

                                                            </select>
                                                        </div>
                                                        <div class="form-group m-form__group">
                                                            <label for="exampleInputEmail1">
                                                                Nombre
                                                            </label>
                                                            <input type="text" id="nombre_familia" name="nombre_familia" class="form-control m-input m-input--pill" id="exampleInputEmail1" placeholder="Escribe un nombre para la familia">
                                                        </div>
                                                        <div class="form-group m-form__group row">
                                                            <div class="col-lg--4">
                                                                <label for="exampleInputEmail1">
                                                                    ¿Tiene tickets?
                                                                </label>
                                                            </div>
                                                            <div class="col-lg-8">
                                                                <span class="m-switch m-switch--lg m-switch--icon">
                                                                    <label>
                                                                        <input type="checkbox" name="tiene_ticket" value="S">
                                                                        <span></span>
                                                                    </label>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="m-form__actions">
                                                            <button type="submit" name="guardar_familia" class="btn btn-brand pull-right">
                                                                Guardar
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
                        <div class="modal fade" id="ticket_resumen" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">
                                            Ticket
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">
                                                &times;
                                            </span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <table id="ticket_table" class="table table-striped table-sm table-info">
                                            <thead>
                                                <tr>
                                                    <th>Producto</th>
                                                    <th class="pull-right">Precio</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="ticket_editar" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 95%">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">
                                            Editar Gasto
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">
                                                &times;
                                            </span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <!--begin::Form-->
                                        <form id="modal_gastos_form" class="m-form m-form--state">
                                            <input type="hidden" name="gasto_id" id="form_gasto_id">
                                            <div class="m-portlet__body">
                                                <div class="m-form__content">
                                                    <div class="m-alert m-alert--icon alert alert-danger m--hide" role="alert" id="modal_m_form_1_msg">
                                                        <div class="m-alert__icon">
                                                            <i class="la la-warning"></i>
                                                        </div>
                                                        <div class="m-alert__text">
                                                            Faltan datos. Cubre todos los campos.
                                                        </div>
                                                        <div class="m-alert__close">
                                                            <button type="button" class="close" data-close="alert" aria-label="Close"></button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="m-form__section m-form__section--first">
                                                    <div class="row">
                                                        <div class="col-lg-3">
                                                            <select id="modal_usuario_picker" name="usuario" class="form-control form-control-lg m-input m-bootstrap-select--pill m-bootstrap-select modal_selectpicker" data-style="btn-primary btn-lg">
                                                                <?php
                                                                foreach ($miembros as $miembro) {
                                                                    echo '<option value="' . $miembro['id'] . '" ' . ($miembro['id'] == $usuario['id'] ? 'selected' : '') . '>' . $miembro['nombre'] . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <select id="modal_gastos_picker" name="familia" class="form-control form-control-lg m-input m-bootstrap-select--pill m-bootstrap-select modal_selectpicker" data-style="btn-danger btn-lg" title="Escoge un gasto">
                                                                <?php
                                                                $padre_actual = -1;
                                                                foreach ($familias as $familia) {
                                                                    if ($familia['padre'] == 0) {
                                                                        if ($familia['padre'] == $padre_actual) {
                                                                            echo '</optgroup>';
                                                                        } else {
                                                                            echo '<optgroup label="' . $familia['nombre'] . '" data-max-options="2">';
                                                                        }
                                                                        $padre_actual = $familia['id'];
                                                                    } else {
                                                                        echo '<option value="' . $familia['id'] . '" data-icon="' . $familia['icono'] . '" data-tokens="' . $familia['ticket'] . '">' . $familia['nombre'] . '</option>';
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div id="modal_repeater" style="display: none">
                                                                <div class="form-group  m-form__group row" id="modal_repeater">
                                                                    <h6  class="col-12 col-form-label">
                                                                        Ticket
                                                                    </h6>
                                                                    <div id="linea-repetida" data-repeater-list="ticket" class="col-lg-12">
                                                                        <div data-repeater-item class="form-group m-form__group row align-items-center">
                                                                            <div class="col-md-5">
                                                                                <div class="">
                                                                                    <input type="text" class="form-control m-input m-input--pill modal_t_producto" name="producto" placeholder="Producto">
                                                                                </div>
                                                                                <div class="d-md-none m--margin-bottom-10"></div>
                                                                            </div>
                                                                            <div class="col-md-5">
                                                                                <div class="">
                                                                                    <input type="text" class="form-control m-input m-input--pill modal_importe_ticket" name="t_importe" placeholder="Importe">
                                                                                </div>
                                                                                <div class="d-md-none m--margin-bottom-10"></div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <div data-repeater-delete="" class="btn-sm btn-block btn btn-danger m-btn m-btn--icon m-btn--pill">
                                                                                    <span>
                                                                                        <i class="la la-trash-o"></i>
                                                                                        <span>
                                                                                            Borrar
                                                                                        </span>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="m-form__group form-group row">
                                                                    <label class="col-lg-10 col-form-label"></label>
                                                                    <div class="col-lg-2">
                                                                        <div data-repeater-create="" class="btn btn-block btn btn-sm btn-brand m-btn m-btn--icon m-btn--pill m-btn--wide pull-right">
                                                                            <span>
                                                                                <i class="la la-plus"></i>
                                                                                <span>
                                                                                    Añadir
                                                                                </span>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="m-form__seperator m-form__seperator--dashed  m-form__seperator--space m--margin-bottom-40"></div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-5">
                                                                    <div class="m-input-icon m-input-icon--right">
                                                                        <input type="text" name="fecha" class="form-control form-control-lg m-input m-input--pill" id="modal_datetimepicker" value="<?= date("d/m/Y") ?>"/>
                                                                        <span class="m-input-icon__icon m-input-icon__icon--right">
                                                                            <span>
                                                                                <i class="la la-calendar-check-o"></i>
                                                                            </span>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <div class="m-input-icon m-input-icon--right">
                                                                        <input type="number" id="modal_importe_total" name="importe" class="form-control form-control-lg m-input m-input--pill" placeholder="Importe">
                                                                        <span class="m-input-icon__icon m-input-icon__icon--right">
                                                                            <span>
                                                                                <i class="fa fa-euro"></i>
                                                                            </span>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="m--margin-top-20 m--visible-tablet-and-mobile"></div>
                                                                    <button type="button" id="modal_enviar_gasto" class="btn btn-lg btn-brand btn-block m-btn m-btn--icon m-btn--pill">
                                                                        <i class="fa fa-save"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </form>
                                        <!--end::Form-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Modal-->

                        <div class="row">
                            <div class="col-sm-5">
                                <div id="grafico-pie-gastos" style="min-width: 310px; height: 405px; width: 100%; margin: 0 auto"></div>
                            </div>
                            <div class="col-sm-7">
                                <!--begin: Datatable -->
                                <div class="m_datatable" id="local_data"></div>
                                <!--end: Datatable -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end:: Body -->
            <!-- begin::Footer -->
            <?php include 'footer.php'; ?>
            <!-- end::Footer -->
        </div>
        <!-- end:: Page -->		    
        <!-- begin::Scroll Top -->
        <div class="m-scroll-top m-scroll-top--skin-top" data-toggle="m-scroll-top" data-scroll-offset="500" data-scroll-speed="300">
            <i class="la la-arrow-up"></i>
        </div>
        <!-- end::Scroll Top -->	
        <!--begin::Base Scripts -->
        <script src="assets/vendors/base/vendors.bundle.js" type="text/javascript"></script>
        <script src="assets/demo/default/base/scripts.bundle.js" type="text/javascript"></script>
        <!--end::Base Scripts -->   
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/data.js"></script>
        <script src="https://code.highcharts.com/modules/drilldown.js"></script>
        <!--begin::Page Snippets -->
        <script src="assets/snippets/pages/user/funciones.js" type="text/javascript"></script>
        <script src="assets/snippets/pages/user/familias.js" type="text/javascript"></script>
        <script src="assets/snippets/pages/user/drilldown.js" type="text/javascript"></script>
        <script src="assets/snippets/pages/user/tabladatos.js" type="text/javascript"></script>
        <script src="assets/snippets/pages/user/viaje.js" type="text/javascript"></script>
        <!--end::Page Snippets -->
    </body>
    <!-- end::Body -->
</html>
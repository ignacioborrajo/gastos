<?php
session_start();

if (isset($_SESSION['usuario'])) {
    include 'assets/bbdd/conectar.php';
    $miembros = $db->select("usuarios", ["id", "nombre"]);
    $familias = $db->select("familias", ["id", "nombre", "icono", "padre", "ticket"], ["ORDER" => ["id" => "ASC"]]);
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
            Metronic | Dashboard
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
                    <!-- BEGIN: Subheader -->
                    <div class="m-subheader ">
                        <div class="d-flex align-items-center">
                            <div class="mr-auto">
                                <h3 class="m-subheader__title ">
                                    Todos los gastos comunes
                                </h3>
                            </div>
                        </div>
                    </div>
                    <!-- END: Subheader -->
                    <div class="m-content">
                        <div class="row">
                            <div class="col-md-12">
                                <!--begin::Portlet-->
                                <div class="m-portlet m-portlet--mobile">
                                    <div class="m-portlet__body">
                                        <!--begin: Search Form -->
                                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                                            <div class="row align-items-center">
                                                <div class="col-xl-8 order-2 order-xl-1">
                                                    <div class="form-group m-form__group row align-items-center">
                                                        <div class="col-md-4">
                                                            <div class="m-input-icon m-input-icon--left">
                                                                <input type="text" class="form-control m-input m-input--solid" placeholder="Buscar..." id="generalSearch">
                                                                <span class="m-input-icon__icon m-input-icon__icon--left">
                                                                    <span>
                                                                        <i class="la la-search"></i>
                                                                    </span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                                                    <a href="resumen_gastos_comunes.php" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
                                                        <span>
                                                            <i class="la la-plus-square-o"></i>
                                                            <span>
                                                                Nuevo Gasto
                                                            </span>
                                                        </span>
                                                    </a>
                                                    <div class="m-separator m-separator--dashed d-xl-none"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end: Search Form -->
                                        <!--begin: Datatable -->
                                        <div class="m_datatable" id="local_data"></div>
                                        <!--end: Datatable -->
                                    </div>
                                </div>
                                <!--end::Portlet-->
                            </div>
                        </div>
                        <div class="modal fade" id="m_modal_6" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
                        <div class="modal fade" id="m_modal_5" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
                                        <form id="gastos_form" class="m-form m-form--state">
                                            <input type="hidden" name="gasto_id" id="form_gasto_id">
                                            <div class="m-portlet__body">
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
                                                            <div id="m_repeater_1" style="display: none">
                                                                <div class="form-group  m-form__group row" id="m_repeater_1">
                                                                    <h6  class="col-12 col-form-label">
                                                                        Ticket
                                                                    </h6>
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
                                                                                    <input type="text" class="form-control m-input m-input--pill importe_ticket" name="t_importe" placeholder="Importe">
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
                                    </div>
                                </div>
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
        <!--begin::Page Snippets -->
        <script src="assets/snippets/pages/user/todos_gastos.js" type="text/javascript"></script>
        <!--end::Page Snippets -->
    </body>
    <!-- end::Body -->
</html>
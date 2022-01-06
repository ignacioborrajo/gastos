<?php
session_start();

if (isset($_SESSION['usuario'])) {
    include 'assets/bbdd/conectar.php';
    
    $familias = $db->select("familias_ingresos", ["id", "nombre", "icono"], ["usuario"=>$_SESSION['usuario'], "ORDER" => ["id" => "ASC"]]);
    
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
                                    Todos los ingresos
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
                        <div class="modal fade" id="m_modal_5" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">
                                            Editar Ingreso
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">
                                                &times;
                                            </span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <!--begin::Form-->
                                        <form id="ingreso_form" class="m-form m-form--state">
                                            <input type="hidden" name="ingreso_id" id="form_ingreso_id" value="">
                                            <input type="hidden" name="usuario" value="<?= $_SESSION['usuario'] ?>">
                                            <div class="m-form__content">
                                                <div class="m-alert m-alert--icon alert alert-danger m--hide" role="alert" id="m_form_2_msg">
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
                                            <div class="form-group m-form__group">
                                                <a id="crear_familia" class="btn btn-link pull-right" data-toggle="modal" data-target="#m_modal_6">
                                                    <i class="fa fa-plus"></i> Nueva familia
                                                </a>
                                                <select id="ingresos_picker" name="familia" class="form-control form-control-lg m-input m-bootstrap-select--pill m-bootstrap-select m_selectpicker" data-style="btn-danger btn-lg" title="Escoge un ingreso">
                                                    <?php
                                                    $padre_actual = -1;
                                                    foreach ($familias as $familia) {
                                                        echo '<option value="' . $familia['id'] . '" data-icon="' . $familia['icono'] . '" data-tokens="' . $familia['ticket'] . '">' . $familia['nombre'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group m-form__group">
                                                <div class="m-input-icon m-input-icon--right">
                                                    <input type="text" name="fecha" class="form-control form-control-lg m-input m-input--pill" id="m_datetimepicker" value="<?= date("d/m/Y") ?>"/>
                                                    <span class="m-input-icon__icon m-input-icon__icon--right">
                                                        <span>
                                                            <i class="la la-calendar-check-o"></i>
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group m-form__group">
                                                <div class="m-input-icon m-input-icon--right">
                                                    <input type="number" name="importe" id="importe_total" class="form-control form-control-lg m-input m-input--pill" placeholder="Importe">
                                                    <span class="m-input-icon__icon m-input-icon__icon--right">
                                                        <span>
                                                            <i class="fa fa-euro"></i>
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group m-form__group">
                                                <div class="m--margin-top-20 m--visible-tablet-and-mobile"></div>
                                                <button type="button" id="enviar_ingreso" class="btn btn-lg btn-brand m-btn m-btn--icon m-btn--pill pull-right">
                                                    <i class="fa fa-save"></i> Guardar
                                                </button>
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
        <script src="assets/snippets/pages/user/todos_ingresos.js" type="text/javascript"></script>
        <!--end::Page Snippets -->
    </body>
    <!-- end::Body -->
</html>
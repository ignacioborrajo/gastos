<?php
session_start();

if (isset($_SESSION['usuario'])) {
    include 'assets/bbdd/conectar.php';
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
                                    Viajes Comunes
                                </h3>
                            </div>
                        </div>
                    </div>
                    <!-- END: Subheader -->
                    <div class="m-content">
                        <!--Begin::Main Portlet-->
                        <div class="row">
                            <div class="col-md-4">
                                <!--begin:: Widgets/Announcements 2-->
                                <div class="m-portlet m--bg-danger m-portlet--bordered-semi m-portlet--skin-dark m-portlet--full-height ">
                                    <div class="m-portlet__body">
                                        <!--begin::Widget 7-->
                                        <div class="m-widget7 m-widget7--skin-dark">
                                            <h2 class="text-white text-center" style="margin-top: 65px;">
                                                <a class="text-white" id="nuevo_viaje" href="#" data-toggle="modal" data-target="#viaje_modal">Nuevo Viaje</a>
                                            </h2>
                                        </div>
                                        <!--end::Widget 7-->
                                        <!--begin::Modal-->
                                        <div class="modal fade" id="viaje_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">
                                                            Nuevo Vieaje
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">
                                                                &times;
                                                            </span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form id="viaje_form">
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
                                                            <div class="form-group">
                                                                <label for="nombre" class="form-control-label">
                                                                    Nombre:
                                                                </label>
                                                                <input type="text" class="form-control form-control-lg m-input m-input--pill" name="nombre" id="nombre">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="fecha_inicio" class="form-control-label">
                                                                    Fecha de inicio:
                                                                </label>
                                                                <div class="m-input-icon m-input-icon--right">
                                                                    <input type="text" class="form-control form-control-lg m-input m-input--pill" name="fecha_inicio" id="fecha_inicio" value="<?= date("d/m/Y") ?>"/>
                                                                    <span class="m-input-icon__icon m-input-icon__icon--right">
                                                                        <span>
                                                                            <i class="la la-calendar-check-o"></i>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="fecha_fin" class="form-control-label">
                                                                    Fecha de fin:
                                                                </label>
                                                                <div class="m-input-icon m-input-icon--right">
                                                                    <input type="text" class="form-control form-control-lg m-input m-input--pill" name="fecha_fin" id="fecha_fin" value="<?= date("d/m/Y") ?>"/>
                                                                    <span class="m-input-icon__icon m-input-icon__icon--right">
                                                                        <span>
                                                                            <i class="la la-calendar-check-o"></i>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                            Cerrar
                                                        </button>
                                                        <button type="button" class="btn btn-primary" id="guardar_viaje">
                                                            Guardar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Modal-->
                                    </div>
                                </div>
                                <!--end:: Widgets/Announcements 2-->
                            </div>
                            <div class="col-md-8">
                                <!--begin:: Widgets/Product Sales-->
                                <div class="m-portlet m-portlet--bordered-semi m-portlet--space m-portlet--full-height ">
                                    <div class="m-portlet__body">
                                        <div class="m-widget25 text-center">
                                            <span id="total_viajes" class="m-widget25__price m--font-brand">
                                                $237,650
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!--end:: Widgets/Product Sales-->
                            </div>
                        </div>
                        <div class="row">
                            <div class="container-fluid p-0">
                                <!-- add extra container element for Masonry -->
                                <div class="grid">
                                    <!-- add sizing element for columnWidth -->
                                    <div class="grid-sizer col-xs-12 col-md-4"></div>
                                    <!-- items use Bootstrap .col- classes -->
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
        <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
        <!--begin::Page Snippets -->
        <script src="assets/snippets/pages/user/viajes_comunes.js" type="text/javascript"></script>
        <!--end::Page Snippets -->
    </body>
    <!-- end::Body -->
</html>
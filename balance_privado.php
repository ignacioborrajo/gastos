<?php
session_start();

if (isset($_SESSION['usuario'])) {
    include 'assets/bbdd/conectar.php';

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
                                    Balance Privado
                                </h3>
                            </div>
                            <div>
                                <span class="m-subheader__daterange" id="m_dashboard_daterangepicker">
                                    <span class="m-subheader__daterange-label">
                                        <span class="m-subheader__daterange-title"></span>
                                        <span class="m-subheader__daterange-date m--font-brand"></span>
                                    </span>
                                    <a href="#" class="btn btn-sm btn-brand m-btn m-btn--icon m-btn--icon-only m-btn--custom m-btn--pill">
                                        <i class="la la-angle-down"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- END: Subheader -->
                    <div class="m-content">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="m-portlet m-portlet--creative m-portlet--bordered-semi">
                                    <div class="m-portlet__head">
                                        <div class="m-portlet__head-caption">
                                            <div class="m-portlet__head-title">
                                                <span class="m-portlet__head-icon m--hide">
                                                    <i class="la la-gear"></i>
                                                </span>
                                                <h3 class="m-portlet__head-text">
                                                    Introduce un nuevo gasto
                                                </h3>
                                            </div>
                                            <h2 class="m-portlet__head-label m-portlet__head-label--primary">
                                                <span>
                                                    Nuevo Gasto
                                                </span>
                                            </h2>
                                        </div>
                                    </div>
                                    <!--begin::Form-->
                                    <form id="gastos_form" class="m-form m-form--state">
                                        <input type="hidden" name="usuario" value="<?= $_SESSION['usuario'] ?>">
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
                                                    <div class="col-lg-4">
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
                                                    <div class="col-lg-8">
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
                            <div class="col-md-6">
                                <!--begin::Portlet-->
                                <div class="m-portlet m-portlet--creative m-portlet--bordered-semi">
                                    <div class="m-portlet__head">
                                        <div class="m-portlet__head-caption">
                                            <div class="m-portlet__head-title">
                                                <span class="m-portlet__head-icon m--hide">
                                                    <i class="la la-gear"></i>
                                                </span>
                                                <h3 class="m-portlet__head-text">
                                                    Introduce un nuevo ingreso
                                                </h3>
                                            </div>
                                            <h2 class="m-portlet__head-label m-portlet__head-label--primary">
                                                <span>
                                                    Nuevo Ingreso
                                                </span>
                                            </h2>
                                        </div>
                                    </div>
                                    <!--begin::Form-->
                                    <form id="ingreso_form" class="m-form m-form--state">
                                        <div class="m-portlet__body">
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
                                            <div class="m-form__section m-form__section--first">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <select id="ingresos_picker" name="familia" class="form-control form-control-lg m-input m-bootstrap-select--pill m-bootstrap-select m_selectpicker" data-style="btn-danger btn-lg" title="Escoge un ingreso">
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
                                                    <div class="col-lg-8">
                                                        <div class="row">
                                                            <div class="col-md-5">
                                                                <div class="m-input-icon m-input-icon--right">
                                                                    <input type="text" name="fecha" class="form-control form-control-lg m-input m-input--pill" id="m_datetimepicker_2" value="<?= date("d/m/Y") ?>"/>
                                                                    <span class="m-input-icon__icon m-input-icon__icon--right">
                                                                        <span>
                                                                            <i class="la la-calendar-check-o"></i>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div class="m-input-icon m-input-icon--right">
                                                                    <input type="number" name="importe" class="form-control form-control-lg m-input m-input--pill" placeholder="Importe">
                                                                    <span class="m-input-icon__icon m-input-icon__icon--right">
                                                                        <span>
                                                                            <i class="fa fa-euro"></i>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="m--margin-top-20 m--visible-tablet-and-mobile"></div>
                                                                <button type="button" id="enviar_ingreso" class="btn btn-lg btn-brand btn-block m-btn m-btn--icon m-btn--pill">
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
                        <div class="m-portlet">
                            <div class="m-portlet__body  m-portlet__body--no-padding">
                                <div class="row m-row--no-padding m-row--col-separator-xl">
                                    <div class="col-xl-4">
                                        <!--begin:: Widgets/Daily Sales-->
                                        <div class="m-widget14">
                                            <div class="m-widget14__header m--margin-bottom-30">
                                                <h3 class="m-widget14__title">
                                                    Daily Sales
                                                </h3>
                                                <span class="m-widget14__desc">
                                                    Check out each collumn for more details
                                                </span>
                                            </div>
                                            <div class="m-widget14__chart" style="height:120px;">
                                                <canvas  id="m_chart_daily_sales"></canvas>
                                            </div>
                                        </div>
                                        <!--end:: Widgets/Daily Sales-->
                                    </div>
                                    <div class="col-xl-4">
                                        <!--begin:: Widgets/Profit Share-->
                                        <div class="m-widget14">
                                            <div class="m-widget14__header">
                                                <h3 class="m-widget14__title">
                                                    Profit Share
                                                </h3>
                                                <span class="m-widget14__desc">
                                                    Profit Share between customers
                                                </span>
                                            </div>
                                            <div class="row  align-items-center">
                                                <div class="col">
                                                    <div id="m_chart_profit_share" class="m-widget14__chart" style="height: 160px">
                                                        <div class="m-widget14__stat">
                                                            45
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="m-widget14__legends">
                                                        <div class="m-widget14__legend">
                                                            <span class="m-widget14__legend-bullet m--bg-accent"></span>
                                                            <span class="m-widget14__legend-text">
                                                                37% Sport Tickets
                                                            </span>
                                                        </div>
                                                        <div class="m-widget14__legend">
                                                            <span class="m-widget14__legend-bullet m--bg-warning"></span>
                                                            <span class="m-widget14__legend-text">
                                                                47% Business Events
                                                            </span>
                                                        </div>
                                                        <div class="m-widget14__legend">
                                                            <span class="m-widget14__legend-bullet m--bg-brand"></span>
                                                            <span class="m-widget14__legend-text">
                                                                19% Others
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end:: Widgets/Profit Share-->
                                    </div>
                                    <div class="col-xl-4">
                                        <!--begin:: Widgets/Stats2-1 -->
                                        <div id="ultimos_gastos" class="m-widget1">

                                        </div>
                                        <!--end:: Widgets/Stats2-1 -->
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
        <script src="assets/snippets/pages/user/resumen_gastos_comunes.js" type="text/javascript"></script>
        <!--end::Page Snippets -->
    </body>
    <!-- end::Body -->
</html>
<?php
session_start();

function imprime($output)
{
    echo "<pre>";
    var_dump($output);
    echo "</pre>";
}

if (isset($_SESSION['usuario'])) {
    include 'assets/bbdd/conectar.php';

    if (isset($_POST['guardar_categoria']) && !$db->has("familias_privadas", ["AND" => ["nombre" => $_POST['nombre_categoria'], "usuario" => $_SESSION['usuario']]])) {
        $db->insert("familias_privadas", ["nombre" => $_POST['nombre_categoria'], "usuario" => $_SESSION['usuario']]);
    }

    if (isset($_POST['guardar_familia']) && !$db->has("familias_privadas", ["AND" => ["nombre" => $_POST['nombre_familia'], "usuario" => $_SESSION['usuario']]]) && $_POST['categoria'] != '') {
        if (isset($_POST['tiene_ticket']) && $_POST['tiene_ticket'] == 'S')
            $tiene_ticket = 'S';
        else
            $tiene_ticket = 'N';
        $db->insert("familias_privadas", ["nombre" => $_POST['nombre_familia'], "usuario" => $_SESSION['usuario'], "padre" => $_POST['categoria'], "ticket" => $tiene_ticket]);
    }

    $familias = array();
    $categorias = $db->select("familias_privadas", ["id", "nombre", "icono", "padre", "ticket"], ["usuario" => $_SESSION['usuario'], "padre" => 0, "ORDER" => ["id" => "ASC"]]);
    foreach ($categorias as $categoria) {
        $familias[] = $categoria;
        $familia = $db->select("familias_privadas", ["id", "nombre", "icono", "padre", "ticket"], ["padre" => $categoria['id'], "ORDER" => ["id" => "ASC"]]);
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
        Metronic | Dashboard
    </title>
    <meta name="description" content="Latest updates and statistic charts">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>
        WebFont.load({
            google: {
                "families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]
            },
            active: function() {
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

<body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">
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
                                Gastos Privados
                            </h3>
                        </div>
                    </div>
                </div>
                <!-- END: Subheader -->
                <div class="m-content">
                    <div class="row">
                        <div class="col-md-12">
                            <!--begin::Portlet-->
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
                                <div class="m-portlet__body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!--begin::Form-->
                                            <form id="gasto_form" class="m-form m-form--state">
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
                                                                <!--
                                                                <a id="crear_familia" class="btn btn-link pull-right" data-toggle="modal" data-target="#m_modal_6">
                                                                    <i class="fa fa-plus"></i> Nueva familia
                                                                </a>-->
                                                                <a class="btn btn-link pull-right" href="familias_privadas.php">
                                                                    <i class="fa fa-plus"></i> Nueva familia
                                                                </a>
                                                            </div>
                                                        </div>
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
                                                                        <h6 class="col-12 col-form-label">
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
                                                                            <input type="text" name="fecha" class="form-control form-control-lg m-input m-input--pill" id="m_datetimepicker" value="<?= date("d/m/Y") ?>" />
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
                                <!--end::Form-->
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <span class="m-portlet__head-icon">
                                        <i class="flaticon-line-graph"></i>
                                    </span>
                                    <h3 class="m-portlet__head-text">
                                        Últimos gastos
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="m-portlet__body  m-portlet__body--no-padding">
                            <div id="ultimos_gastos" class="row m-row--no-padding m-row--col-separator-xl">

                            </div>
                        </div>
                    </div>
                    <!--Begin::Section-->
                    <div class="m-portlet">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <span class="m-portlet__head-icon">
                                        <i class="flaticon-line-graph"></i>
                                    </span>
                                    <h3 class="m-portlet__head-text">
                                        Balance de Gastos
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools">
                                <ul class="m-portlet__nav">
                                    <li class="m-portlet__nav-item">
                                        <label>Incluir gastos comunes</label>
                                        <span class="m-bootstrap-switch m-bootstrap-switch--pill">
                                            <input id="switch_gastos_comunes" data-switch="true" type="checkbox" data-on-text="Sí" data-off-text="No" data-on-color="success">
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="m-portlet__body  m-portlet__body--no-padding">
                            <div class="row m-row--no-padding m-row--col-separator-xl">
                                <div class="col-xl-6">
                                    <!--begin:: Widgets/Profit Share-->
                                    <div class="m-widget14">
                                        <div class="row  align-items-center">
                                            <div id="grafico-pie-gastos" style="min-width: 310px; height: 405px; width: 100%; margin: 0 auto"></div>
                                        </div>
                                    </div>
                                    <!--end:: Widgets/Profit Share-->
                                </div>
                                <div class="col-xl-6">
                                    <!--begin:: Widgets/Daily Sales-->
                                    <div class="m-widget14">
                                        <div class="m-widget14__header m--margin-bottom-30">
                                            <h3 class="m-widget14__title">
                                                Gastos Totales
                                            </h3>
                                            <span class="m-widget14__desc">
                                                Todos los gastos acumulados
                                            </span>
                                        </div>
                                        <div class="m-widget25 text-center">
                                            <p class="m-widget25__desc" style="margin-bottom: 0px">Gastos este mes</p>
                                            <span id="gasto_este_mes" class="m-widget25__price m--font-brand"></span>
                                            <hr class="m--margin-bottom-30">
                                            <p class="m-widget25__desc" style="margin-bottom: 0px">Gastos totales</p>
                                            <span id="gasto_total" class="m-widget25__price m--font-danger"></span>
                                        </div>
                                    </div>
                                    <!--end:: Widgets/Daily Sales-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--End::Section-->
                    <div class="m-portlet m-portlet--brand m-portlet--head-solid-bg">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <span class="m-portlet__head-icon">
                                        <i class="flaticon-line-graph"></i>
                                    </span>
                                    <h3 class="m-portlet__head-text">
                                        Histórico de Gastos
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools">

                                <ul class="m-portlet__nav">
                                    <li class="m-portlet__nav-item">
                                        <label>Detallado</label>
                                        <span class="m-bootstrap-switch m-bootstrap-switch--pill">
                                            <input id="switch_detallado" data-switch="true" type="checkbox" data-on-text="Sí" data-off-text="No" data-on-color="success">
                                        </span>
                                    </li>
                                    <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
                                        <a href="#" class="m-portlet__nav-link m-dropdown__toggle dropdown-toggle btn btn-sm  btn-light m-btn m-btn--pill">
                                            Período
                                        </a>

                                        <div class="m-dropdown__wrapper">
                                            <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 32px;"></span>
                                            <div class="m-dropdown__inner">
                                                <div class="m-dropdown__body">
                                                    <div class="m-dropdown__content">
                                                        <ul class="m-nav">
                                                            <li class="m-nav__section m-nav__section--first">
                                                                <span class="m-nav__section-text">
                                                                    Selecciona un período
                                                                </span>
                                                            </li>
                                                            <li class="m-nav__item">
                                                                <a href="#" class="m-nav__link cambio_rango" data-rango="anual">
                                                                    <i class="m-nav__link-icon flaticon-share"></i>
                                                                    <span class="m-nav__link-text">
                                                                        Anual
                                                                    </span>
                                                                </a>
                                                            </li>
                                                            <li class="m-nav__item">
                                                                <a href="" class="m-nav__link cambio_rango" data-rango="mensual">
                                                                    <i class="m-nav__link-icon flaticon-chat-1"></i>
                                                                    <span class="m-nav__link-text">
                                                                        Mensual
                                                                    </span>
                                                                </a>
                                                            </li>
                                                            <li class="m-nav__item">
                                                                <a href="" class="m-nav__link cambio_rango" data-rango="diario">
                                                                    <i class="m-nav__link-icon flaticon-info"></i>
                                                                    <span class="m-nav__link-text">
                                                                        Diario
                                                                    </span>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="m-portlet__body">
                            <div id="grafico-historico"></div>
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
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <!--begin::Page Snippets -->
    <script src="assets/snippets/pages/user/gastos.js?<?= time() ?>" type="text/javascript"></script>
    <!--end::Page Snippets -->
</body>
<!-- end::Body -->

</html>
<?php
session_start();

if (isset($_SESSION['usuario'])) {
    include 'assets/bbdd/conectar.php';

    if (isset($_POST['guardar_categoria']) && !$db->has("familias_ingresos", ["AND" => ["nombre" => $_POST['nombre_categoria'], "usuario" => $_SESSION['usuario']]])) {
        $db->insert("familias_ingresos", ["nombre" => $_POST['nombre_categoria'], "usuario" => $_SESSION['usuario']]);
    }

    if (isset($_POST['guardar_familia']) && !$db->has("familias_ingresos", ["AND" => ["nombre"=>$_POST['nombre_familia'], "usuario" => $_SESSION['usuario']]]) && $_POST['categoria'] != '') {
        if (isset($_POST['tiene_ticket']) && $_POST['tiene_ticket'] == 'S')
            $tiene_ticket = 'S';
        else
            $tiene_ticket = 'N';
        $db->insert("familias_ingresos", ["nombre" => $_POST['nombre_familia'], "usuario" => $_SESSION['usuario'], "padre" => $_POST['categoria'], "ticket" => $tiene_ticket]);
    }

    $familias = array();
    $familias = $db->select("familias_ingresos", ["id", "nombre", "icono", "padre", "ticket"], ["usuario" => $_SESSION['usuario'], "padre" => 0, "ORDER" => ["id" => "ASC"]]);
    
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
            Ingresos
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
                                    Ingresos
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
                                    <div class="m-portlet__body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <form id="ingreso_form" class="m-form m-form--state">
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
                                                            foreach ($familias as $f) {
                                                                $hijas = $db->select("familias_ingresos", ["id", "nombre", "icono", "padre", "ticket"], ["padre" => $f['id'], "ORDER" => ["nombre" => "ASC"]]);
                                                                if(count($hijas) > 0) {
                                                                    echo '<optgroup label="' . $f['nombre'] . '" data-max-options="2">';
                                                                    foreach ($hijas as $key => $h) {
                                                                        echo '<option value="' . $h['id'] . '" data-icon="' . $h['icono'] . '" data-tokens="' . $h['ticket'] . '">' . $h['nombre'] . '</option>';
                                                                    }
                                                                    echo '</optgroup>';
                                                                } else {
                                                                    echo '<option value="' . $f['id'] . '" data-icon="' . $f['icono'] . '" data-tokens="' . $f['ticket'] . '">' . $f['nombre'] . '</option>';
                                                                }
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
                                                            <input type="number" name="importe" class="form-control form-control-lg m-input m-input--pill" placeholder="Importe">
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
                                            </div>
                                            <div class="col-md-6">
                                                <div id="ultimos_ingresos" class="m-widget1">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="m_modal_6" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">
                                                        Familias de ingresos
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
                                                                    <ul class="m-nav">
                                                                        <?php
                                                                        $padre_actual = -1;
                                                                        foreach ($familias as $familia) {
                                                                            if ($familia['padre'] == 0) {
                                                                                if ($padre_actual != -1) {
                                                                                    echo '</ul></li>';
                                                                                }
                                                                                echo '<li class="m-nav__item m-nav__item--active">
                                                                                        <a href="" class="m-nav__link">
                                                                                            <i class="m-nav__link-icon fa fa-trash-o"></i>
                                                                                            <span class="m-nav__link-title">
                                                                                                <span class="m-nav__link-wrap">
                                                                                                    <span class="m-nav__link-text">
                                                                                                        '.$familia['nombre'].'
                                                                                                    </span>
                                                                                                </span>
                                                                                            </span>
                                                                                        </a>
                                                                                        <ul class="m-nav__sub">';
                                                                                
                                                                                $padre_actual = $familia['id'];
                                                                            } else {
                                                                                echo '<li class="m-nav__item">
                                                                                            <a href="#" class="m-nav__link eliminar-familia" data-id="' . $familia['id'] . '" data-usuario="' . $_SESSION['usuario'] . '">
                                                                                                <span class="m-nav__link-bullet m-nav__link-bullet--line">
                                                                                                    <span></span>
                                                                                                </span>
                                                                                                <span class="m-nav__link-text">
                                                                                                    '.$familia['nombre'].'
                                                                                                </span>
                                                                                            </a>
                                                                                        </li>';
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <form method="POST" class="m-form m-form--fit m-form--label-align-right">
                                                                <input type="hidden" id="id_familia" name="id_categoria" value="">
                                                                <div class="m-portlet__body">
                                                                    <div class="form-group m-form__group m--margin-top-10">
                                                                        <h5>Nueva Categoria</h5>
                                                                    </div>
                                                                    <div class="form-group m-form__group">
                                                                        <label for="exampleInputEmail1">
                                                                            Nombre
                                                                        </label>
                                                                        <input type="text" id="nombre_categoria" name="nombre_categoria" class="form-control m-input m-input--pill" id="exampleInputEmail1" placeholder="Escribe un nombre para la categoría">
                                                                    </div>
                                                                    <div class="m-form__actions">
                                                                        <button type="submit" name="guardar_categoria" class="btn btn-brand pull-right">
                                                                            Guardar
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            <hr>
                                                            <form method="POST" class="m-form m-form--fit m-form--label-align-right">
                                                                <input type="hidden" id="id_familia" name="id_familia" value="">
                                                                <div class="m-portlet__body">
                                                                    <div class="form-group m-form__group m--margin-top-10">
                                                                        <h5>Nueva Familia</h5>
                                                                    </div>
                                                                    <div class="form-group m-form__group">
                                                                        <label for="exampleInputEmail1">
                                                                            Categoría
                                                                        </label>
                                                                        <select name="categoria" class="form-control m-input">
                                                                            <?php
                                                                            foreach ($categorias as $categoria) {
                                                                                echo '<option value="' . $categoria['id'] . '">' . $categoria['nombre'] . '</option>';
                                                                            }
                                                                            ?>
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
                                        <div id="ultimos_ingresos" class="">

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
        <script src="assets/snippets/pages/user/ingresos.js?<?=time()?>" type="text/javascript"></script>
        <!--end::Page Snippets -->
    </body>
    <!-- end::Body -->
</html>
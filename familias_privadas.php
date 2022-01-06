<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
session_start();

if (isset($_SESSION['usuario'])) {
    include 'assets/bbdd/conectar.php';

    include_once 'funciones_familias.php';

    if (isset($_POST['guardar_categoria'])) {
        if (isset($_POST['tiene_ticket']) && $_POST['tiene_ticket'] == 'S') {
            $tiene_ticket = 'S';
        } else {
            $tiene_ticket = 'N';
        }
        if ($_POST['id_familia'] == '') {
            $db->insert("familias_privadas", ["nombre" => $_POST['nombre_categoria'], "padre" => $_POST['familia_categoria'], "usuario" => $_SESSION['usuario'], "ticket" => $tiene_ticket]);
        } else {
            $db->update("familias_privadas", ["nombre" => $_POST['nombre_categoria'], "padre" => $_POST['familia_categoria'], "ticket" => $tiene_ticket], ["id" => $_POST['id_familia'], "usuario" => $_SESSION['usuario']]);
        }
    }

    $familias = familias_privadas($db);
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
        Familias Privadas
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

<script>

</script>

<body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default m-brand--minimize m-aside-left--minimize">
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
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="m-portlet m-portlet--full-height ">
                                <div class="m-portlet__head">
                                    <div class="m-portlet__head-caption">
                                        <div class="m-portlet__head-title">
                                            <h3 class="m-portlet__head-text">
                                                Familias Privadas
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-portlet__body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Familias</h5>
                                            <div class="m-demo" data-code-preview="true" data-code-html="true" data-code-js="false">
                                                <div class="m-demo__preview">
                                                    <ul class="m-nav">
                                                        <?php
                                                        echo implode("", lista_familias_privadas($familias, 0, false));
                                                        ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <form method="POST" class="m-form m-form--fit m-form--label-align-right">
                                                <input type="hidden" id="id_familia" name="id_familia" value="">
                                                <div class="m-portlet__body">
                                                    <div class="form-group m-form__group m--margin-top-10">
                                                        <h5>Nueva Familia</h5>
                                                    </div>
                                                    <div class="form-group m-form__group">
                                                        <label for="exampleInputEmail1">
                                                            Nombre
                                                        </label>
                                                        <input type="text" id="nombre_categoria" name="nombre_categoria" class="form-control m-input m-input--pill" id="exampleInputEmail1" placeholder="Escribe un nombre para la categoría">
                                                    </div>
                                                    <div class="form-group m-form__group">
                                                        <label for="exampleInputEmail1">
                                                            Familia padre
                                                        </label>
                                                        <select id="familia_categoria" name="familia_categoria" class="form-control m-input">
                                                            <option value="">Sin familia padre</option>
                                                            <?php
                                                            echo implode("", opciones_familias_privadas($familias, 0, true));
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group m-form__group">
                                                        <div class="col-lg--4">
                                                            <label for="exampleInputEmail1">
                                                                ¿Tiene tickets?
                                                            </label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <span class="m-switch m-switch--lg m-switch--icon">
                                                                <label>
                                                                    <input type="checkbox" id="tiene_ticket" name="tiene_ticket" value="S">
                                                                    <span></span>
                                                                </label>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="m-form__actions pb-0">
                                                        <button type="submit" name="guardar_categoria" class="btn btn-brand pull-right">
                                                            Guardar
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                            <form method="POST" id="form_borrar" style="display:none;" class="m-form m-form--fit m-form--label-align-right">
                                                <input type="hidden" id="borrar_familia" name="id_familia" value="">
                                                <div class="m-form__actions pt-0">
                                                    <button type="submit" name="borrar_categoria" class="btn btn-danger pull-right">
                                                        Borrar
                                                    </button>
                                                    <button type="submit" id="cancelar_categoria" class="btn pull-right">
                                                        Cancelar
                                                    </button>
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
    <script src="assets/demo/default/custom/components/forms/widgets/dropzone.js" type="text/javascript"></script>
    <!--end::Base Scripts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <!--begin::Page Snippets -->
    <script src="assets/snippets/pages/user/familias_privadas.js?t=<?= time() ?>" type="text/javascript"></script>
    <!--end::Page Snippets -->
</body>
<!-- end::Body -->

</html>
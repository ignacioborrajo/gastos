<?php
session_start();

if (isset($_SESSION['usuario'])) {

    if (isset($_POST['fecha_inicio']) && isset($_POST['fecha_fin'])) {
        $aux = explode("/", $_POST['fecha_inicio']);
        $fecha_inicio = $aux[2] . "-" . $aux[1] . "-" . $aux[0];
        $aux = explode("/", $_POST['fecha_fin']);
        $fecha_fin = $aux[2] . "-" . $aux[1] . "-" . $aux[0];
    } else {
        $fecha_inicio = date('Y-m-01');
        $fecha_fin = date('Y-m-t');
    }

    $aux = explode("-", $fecha_inicio);
    $finicio = $aux[2] . "/" . $aux[1] . "/" . $aux[0];
    $aux = explode("-", $fecha_fin);
    $ffin = $aux[2] . "/" . $aux[1] . "/" . $aux[0];

    include 'assets/bbdd/conectar.php';

    $gastos_privados = $db->sum("gastos_privados", "importe", ["usuario" => $_SESSION['usuario'], "fecha[>=]" => $fecha_inicio, "fecha[<=]" => $fecha_fin]);
    $gastos_comunes = $db->sum("gastos", "importe", ["usuario" => $_SESSION['usuario'], "fecha[>=]" => $fecha_inicio, "fecha[<=]" => $fecha_fin]);
    $ingresos = $db->sum("ingresos", "importe", ["usuario" => $_SESSION['usuario'], "fecha[>=]" => $fecha_inicio, "fecha[<=]" => $fecha_fin]);

    $nombre_meses = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];

    $ano_inicio = intval(date('Y'));
    $mes_inicio = intval(date('m')) - 1;
    if ($mes_inicio < 1) $mes_inicio = 12;
    for ($i = 0; $i < 12; $i++) {

        $fi = date($ano_inicio . '-' . $mes_inicio . '-01');
        $ff = date($ano_inicio . '-' . $mes_inicio . '-t');
        //echo $fi . " | " . $ff . "<br>";
        $gastos_priv = $db->sum("gastos_privados", "importe", ["usuario" => $_SESSION['usuario'], "fecha[>=]" => $fi, "fecha[<=]" => $ff]);
        $gastos_com = $db->sum("gastos", "importe", ["usuario" => $_SESSION['usuario'], "fecha[>=]" => $fi, "fecha[<=]" => $ff]);
        $ingr = $db->sum("ingresos", "importe", ["usuario" => $_SESSION['usuario'], "fecha[>=]" => $fi, "fecha[<=]" => $ff]);

        $arr_gpriv[] = round($gastos_priv, 2, PHP_ROUND_HALF_UP);
        $arr_gcom[] = round($gastos_com, 2, PHP_ROUND_HALF_UP);
        $arr_ingr[] = round($ingr, 2, PHP_ROUND_HALF_UP);
        $arr_bene[] = round($ingr - ($gastos_priv + $gastos_com), 2, PHP_ROUND_HALF_UP);
        $arr_gtot[] = round(($gastos_priv + $gastos_com), 2, PHP_ROUND_HALF_UP);
        $arr_cero[] = 0;

        $meses[] = $nombre_meses[intval($mes_inicio - 1)];

        $mes_inicio--;
        if ($mes_inicio < 1) {
            $mes_inicio = 12;
            $ano_inicio--;
        }
    }
    //var_dump($arr_ingr);
    //die;
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
    var gastos_privados = new Array();
    var gastos_comunes = new Array();
    var ingresos = new Array();
    var beneficios = new Array();
    var gastos_totales = new Array();
    var ceros = new Array();
    var categorias = new Array();

    <?php
    foreach ($arr_gpriv as $aux) {
        echo 'gastos_privados.push(' . $aux . ');';
    }
    foreach ($arr_gcom as $aux) {
        echo 'gastos_comunes.push(' . $aux . ');';
    }
    foreach ($arr_ingr as $aux) {
        echo 'ingresos.push(' . $aux . ');';
    }
    foreach ($arr_bene as $aux) {
        echo 'beneficios.push(' . $aux . ');';
    }
    foreach ($arr_gtot as $aux) {
        echo 'gastos_totales.push(' . $aux . ');';
    }
    foreach ($meses as $aux) {
        echo 'categorias.push("' . $aux . '");';
    }
    foreach ($arr_cero as $aux) {
        echo 'ceros.push("' . $aux . '");';
    }
    ?>

    var data_pie = new Array();
    data_pie.push({
        name: "Gastos Privados",
        color: "#f56464",
        y: <?= round($gastos_privados, 2, PHP_ROUND_HALF_UP) ?>,
    });
    data_pie.push({
        name: "Gastos Comunes",
        color: "#f5a864",
        y: <?= round($gastos_comunes, 2, PHP_ROUND_HALF_UP) ?>,
    });
    data_pie.push({
        name: "Ingresos",
        color: "#90ed7c",
        y: <?= round($ingresos, 2, PHP_ROUND_HALF_UP) ?>,
    });
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
                        <div class="col-lg-12">
                            <div class="m-portlet m-portlet--bordered-semi m-portlet--space m-portlet--full-height  m-portlet--rounded">
                                <div class="m-portlet__head">
                                    <div class="m-portlet__head-caption">
                                        <div class="m-portlet__head-title">
                                            <h3 class="m-portlet__head-text">
                                                Resumen
                                                <span class="m-portlet__head-desc"><?= date('d-m-Y') ?></span>
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="m-portlet__head-tools">
                                        <form id="gastos_form" class="m-form m-form--state col-8 pull-right" method="POST">
                                            <div class="m-form__section m-form__section--first">
                                                <div class="row">
                                                    <div class="col-lg-5">
                                                        <div class="m-input-icon m-input-icon--right">
                                                            <input type="text" name="fecha_inicio" class="form-control form-control-sm m-input m-input--pill m_datetimepicker" value="<?= $finicio ?>" />
                                                            <span class="m-input-icon__icon m-input-icon__icon--right">
                                                                <span>
                                                                    <i class="la la-calendar-check-o"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-5">
                                                        <div class="m-input-icon m-input-icon--right">
                                                            <input type="text" name="fecha_fin" class="form-control form-control-sm m-input m-input--pill m_datetimepicker" value="<?= $ffin ?>" />
                                                            <span class="m-input-icon__icon m-input-icon__icon--right">
                                                                <span>
                                                                    <i class="la la-calendar-check-o"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="m--margin-top-20 m--visible-tablet-and-mobile"></div>
                                                        <button type="submit" id="enviar_gasto" class="btn btn-sm btn-brand btn-block m-btn m-btn--icon m-btn--pill">
                                                            <i class="fa fa-search"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="m-portlet__body">
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="m-widget25" style="text-align: center;">
                                                <span class="m-widget25__price m--font-brand" style="display: block;"><?= round($ingresos - ($gastos_privados + $gastos_comunes), 2, PHP_ROUND_HALF_UP) ?></span>
                                                <span class="m-widget25__desc">Beneficios</span>
                                                <div class="m-widget25--progress">
                                                    <div class="m-widget25__progress">
                                                        <span class="m-widget25__progress-number">
                                                            <?= round($gastos_privados, 2, PHP_ROUND_HALF_UP) ?>
                                                        </span>
                                                        <div class="m--space-10"></div>
                                                        <div class="progress m-progress--sm">
                                                            <div class="progress-bar m--bg-danger" role="progressbar" style="width: 100%;background-color: #f56464 !important;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span class="m-widget25__progress-sub">
                                                            Gastos Privados
                                                        </span>
                                                    </div>
                                                    <div class="m-widget25__progress">
                                                        <span class="m-widget25__progress-number">
                                                            <?= round($gastos_comunes, 2, PHP_ROUND_HALF_UP) ?>
                                                        </span>
                                                        <div class="m--space-10"></div>
                                                        <div class="progress m-progress--sm">
                                                            <div class="progress-bar m--bg-accent" role="progressbar" style="width: 100%;background-color: #f5a864 !important;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span class="m-widget25__progress-sub">
                                                            Gastos Comunes
                                                        </span>
                                                    </div>
                                                    <div class="m-widget25__progress">
                                                        <span class="m-widget25__progress-number">
                                                            <?= round(($gastos_privados + $gastos_comunes), 2, PHP_ROUND_HALF_UP) ?>
                                                        </span>
                                                        <div class="m--space-10"></div>
                                                        <div class="progress m-progress--sm">
                                                            <div class="progress-bar m--bg-accent" role="progressbar" style="width: 100%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span class="m-widget25__progress-sub">
                                                            Total Gastos
                                                        </span>
                                                    </div>
                                                    <div class="m-widget25__progress">
                                                        <span class="m-widget25__progress-number">
                                                            <?= round($ingresos, 2, PHP_ROUND_HALF_UP) ?>
                                                        </span>
                                                        <div class="m--space-10"></div>
                                                        <div class="progress m-progress--sm">
                                                            <div class="progress-bar m--bg-warning" role="progressbar" style="width: 100%;background-color: #90ed7c !important;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <span class="m-widget25__progress-sub">
                                                            Ingresos
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div id="gastos_ingresos"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="m-portlet m-portlet--bordered-semi m-portlet--space m-portlet--full-height  m-portlet--rounded">
                                <div class="m-portlet__body">
                                    <div id="historico"></div>
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
    <!--end::Base Scripts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <!--begin::Page Snippets -->
    <script src="assets/snippets/pages/user/dashboard.js?t=<?= time() ?>" type="text/javascript"></script>
    <!--end::Page Snippets -->
</body>
<!-- end::Body -->

</html>
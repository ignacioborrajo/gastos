<?php
session_start();

if (isset($_SESSION['usuario'])) {
    include 'assets/bbdd/conectar.php';

    if (isset($_POST['desimportar']) && $_POST['desimportar'] != '') {

        $seleccionada = $db->get("importar", ["id", "gasto", "gasto_priv", "ingreso"], ["usuario" => $_SESSION['usuario'], "id" => $_POST['desimportar']]);

        if ($seleccionada['gasto'] != '') {
            $db->delete("tickets", [
                "gasto" => $seleccionada['gasto']
            ]);

            $db->delete("gastos", [
                "usuario" => $_SESSION['usuario'],
                "id" => $seleccionada['gasto']
            ]);
        } else if ($seleccionada['gasto_priv'] != '') {
            $db->delete("tickets_privados", [
                "gasto" => $seleccionada['gasto_priv']
            ]);

            $db->delete("gastos_privados", [
                "usuario" => $_SESSION['usuario'],
                "id" => $seleccionada['gasto_priv']
            ]);
        } else if ($seleccionada['ingreso'] != '') {
            $db->delete("ingresos", [
                "usuario" => $_SESSION['usuario'],
                "id" => $seleccionada['ingreso']
            ]);
        } else if (isset($_POST['descartar'])) {
            $importado = true;
        }

        $db->update("importar", ["importado" => 'N', "gasto" => NULL, "gasto_priv" => NULL, "ingreso" => NULL], ["id" => $seleccionada['id']]);
    }

    $importadas = $db->select("importar", ["id", "usuario", "fecha", "concepto", "importe", "gasto", "gasto_priv", "ingreso"], ["usuario" => $_SESSION['usuario'], "importado" => 'S', "ORDER" => ["fimportacion" => "DESC"]]);
    $datos = array();
    foreach ($importadas as $imp) {
        if ($imp['gasto'] != '') {
            $gasto_com = $db->get("gastos", ["familia"], ["id" => $imp['gasto']]);
            $fam = $db->get("familias", ["nombre"], ["id" => $gasto_com['familia']]);
            $datos[$imp['id']]['gasto'] =  $fam['nombre'];
        }
        if ($imp['gasto_priv'] != '') {
            $gasto_priv = $db->get("gastos_privados", ["familia"], ["id" => $imp['gasto_priv']]);
            $fam = $db->get("familias_privadas", ["nombre"], ["id" => $gasto_priv['familia']]);
            $datos[$imp['id']]['gasto_priv'] =  $fam['nombre'];
        }
        if ($imp['ingreso'] != '') {
            $ingreso = $db->get("ingresos", ["familia"], ["id" => $imp['ingreso']]);
            $fam = $db->get("familias_ingresos", ["nombre"], ["id" => $ingreso['familia']]);
            $datos[$imp['id']]['ingreso'] =  $fam['nombre'];
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
        Histórico Importaciones
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
                                Histórico Importaciones
                            </h3>
                        </div>
                    </div>
                </div>
                <!-- END: Subheader -->
                <div class="m-content">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Concepto</th>
                                        <th>Importe</th>
                                        <th>Gasto</th>
                                        <th>Gasto Privado</th>
                                        <th>Ingreso</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($importadas as $imp) {
                                        echo '<form method="POST">';
                                        echo '<input type="hidden" name="desimportar" value="' . $imp['id'] . '">';
                                        echo '<input type="hidden" name="usuario" value="' . $_SESSION['usuario'] . '">';
                                        echo '<tr>';
                                        echo '<td>' . $imp['fecha'] . '</td>';
                                        echo '<td>' . $imp['concepto'] . '</td>';
                                        echo '<td>' . $imp['importe'] . '</td>';
                                        echo '<td>' . $datos[$imp['id']]['gasto'] . '</td>';
                                        echo '<td>' . $datos[$imp['id']]['gasto_priv'] . '</td>';
                                        echo '<td>' . $datos[$imp['id']]['ingreso'] . '</td>';
                                        echo '<td>';
                                        if ($imp['gasto'] > 0 || $imp['gasto_priv'] > 0 || $imp['ingreso'] > 0) {
                                            echo '<button class="btn-danger btn-sm" type="submit">Deshacer</button>';
                                        }
                                        echo '</td>';
                                        echo '</tr>';
                                        echo '</form>';
                                    }
                                    ?>
                                </tbody>
                            </table>
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
    <script src="assets/snippets/pages/user/importar.js?<?= time() ?>" type="text/javascript"></script>
    <!--end::Page Snippets -->
</body>
<!-- end::Body -->

</html>
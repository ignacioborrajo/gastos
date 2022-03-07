<?php
session_start();

$tabla_importacion = array();

if (isset($_SESSION['usuario'])) {
    include 'assets/bbdd/conectar.php';

    if (isset($_POST['importar']) && $_POST['importar'] != '') {

        $importado = false;
        $gasto_com = null;
        $gasto_priv = null;
        $ingreso = null;
        
        if ($_POST['gasto'] != '') {
            $db->insert("gastos", [
                "usuario" => $_POST['usuario'],
                "familia" => $_POST['gasto'],
                "fecha" => $_POST['fecha'],
                "importe" => $_POST['importe'],
                "tiene_ticket" => (isset($_POST['descripcion']) && $_POST['descripcion'] != '' ? 'S' : 'N')
            ]);

            $last_id = $db->id();
            $gasto_com = $last_id;

            if ($last_id > 0) {
                if ($db->has("productos", ["nombre" => $_POST['descripcion']])) {
                    $id_producto = $db->get("productos", "id", ["nombre" => $_POST['descripcion']]);
                } else {
                    $db->insert("productos", ["nombre" => $_POST['descripcion']]);
                    $id_producto = $db->id();
                }

                $db->insert("tickets", [
                    "fecha" => $_POST['fecha'],
                    "producto" => $_POST['descripcion'],
                    "id_producto" => $id_producto,
                    "importe" => $_POST['importe'],
                    "gasto" => $last_id
                ]);
            }

            $importado = true;
        } else if ($_POST['gasto_priv'] != '') {
            $db->insert("gastos_privados", [
                "usuario" => $_POST['usuario'],
                "familia" => $_POST['gasto_priv'],
                "fecha" => $_POST['fecha'],
                "importe" => $_POST['importe'],
                "tiene_ticket" => (isset($_POST['descripcion']) && $_POST['descripcion'] != '' ? 'S' : 'N')
            ]);

            $last_id = $db->id();
            $gasto_priv = $last_id;

            if ($last_id > 0 && isset($_POST['descripcion']) && $_POST['descripcion']) {
                if ($db->has("productos", ["nombre" => $_POST['descripcion']])) {
                    $id_producto = $db->get("productos", "id", ["nombre" => $_POST['descripcion']]);
                } else {
                    $db->insert("productos", ["nombre" => $_POST['descripcion']]);
                    $id_producto = $db->id();
                }

                $db->insert("tickets_privados", [
                    "fecha" => $_POST['fecha'],
                    "producto" => $_POST['descripcion'],
                    "id_producto" => $id_producto,
                    "importe" => $_POST['importe'],
                    "gasto" => $last_id
                ]);
            }

            $importado = true;
        } else if ($_POST['ingreso'] != '') {
            $db->insert("ingresos", [
                "usuario" => $_POST['usuario'],
                "familia" => $_POST['ingreso'],
                "fecha" => $_POST['fecha'],
                "importe" => $_POST['importe']
            ]);
            $ingreso = $db->id();

            $importado = true;
        } else if (isset($_POST['descartar'])) {
            $importado = true;
        }

        if ($importado) {
            $time = date("Y-m-d H:i:s");
            $db->update("importar", ["importado" => 'S', "gasto" => $gasto_com, "gasto_priv" => $gasto_priv, "ingreso" => $ingreso, "fimportacion" => $time], ["id" => $_POST['importar']]);
        }
    }

    $no_importadas = $db->select("importar", ["id", "usuario", "fecha", "concepto", "importe", "gasto", "gasto_priv", "ingreso"], ["usuario" => $_SESSION['usuario'], "importado" => 'N', "ORDER" => ["fecha" => "DESC"], "LIMIT" => 25]);
    $datos = array();
    foreach ($no_importadas as $imp) {
        $gastos_com = $db->select("gastos", ["fecha", "importe", "familia"], ["fecha" => $imp['fecha']]);
        foreach ($gastos_com as $g) {
            $fam = $db->get("familias", ["nombre"], ["id" => $g['familia']]);
            if(isset($datos[$imp['id']])) {
                $datos[$imp['id']] .=  $fam['nombre'] . " - " . $g['importe'] . "\n";
            }
        }

        $gastos_priv = $db->select("gastos_privados", ["fecha", "importe", "familia"], ["usuario" => $_SESSION['usuario'], "fecha" => $imp['fecha']]);
        foreach ($gastos_priv as $g) {
            $fam = $db->get("familias_privadas", ["nombre"], ["id" => $g['familia']]);
            if(isset($datos[$imp['id']])) {
                $datos[$imp['id']] .=  $fam['nombre'] . " - " . $g['importe'] . "\n";
            }
        }

        $ingresos = $db->select("ingresos", ["fecha", "importe", "familia"], ["usuario" => $_SESSION['usuario'], "fecha" => $imp['fecha']]);
        foreach ($ingresos as $g) {
            $fam = $db->get("familias_ingresos", ["nombre"], ["id" => $g['familia']]);
            if(isset($datos[$imp['id']])) {
                $datos[$imp['id']] .=  $fam['nombre'] . " - " . $g['importe'] . "\n";
            }
        }
    }

    $familias_com = array();
    $familias_com = $db->select("familias", ["id", "nombre", "icono", "padre", "ticket"], ["padre" => 0, "ORDER" => ["nombre" => "ASC"]]);
    
    $familias_priv = array();
    $familias_priv = $db->select("familias_privadas", ["id", "nombre", "icono", "padre", "ticket"], ["usuario" => $_SESSION['usuario'], "padre" => 0, "ORDER" => ["nombre" => "ASC"]]);

    $familias = array();
    $familias = $db->select("familias_ingresos", ["id", "nombre", "icono", "padre", "ticket"], ["usuario" => $_SESSION['usuario'], "padre" => 0, "ORDER" => ["nombre" => "ASC"]]);

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
        Importar
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
                                Importar
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
                                        <th>Descripción</th>
                                        <th>Gasto</th>
                                        <th>Gasto Privado</th>
                                        <th>Ingreso</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($no_importadas as $imp) {
                                        if (floatval($imp['importe']) < 0) {
                                            $importe = floatval($imp['importe']) * (-1);
                                        } else {
                                            $importe = floatval($imp['importe']);
                                        }
                                        echo '<form method="POST">';
                                        echo '<input type="hidden" name="importar" value="' . $imp['id'] . '">';
                                        echo '<input type="hidden" name="fecha" value="' . $imp['fecha'] . '">';
                                        echo '<input type="hidden" name="importe" value="' . $importe . '">';
                                        echo '<input type="hidden" name="usuario" value="' . $_SESSION['usuario'] . '">';
                                        echo '<tr>';
                                        if (isset($datos[$imp['id']]) && $datos[$imp['id']] != '') {
                                            echo '<td><button type="button" class="btn btn-secondary" data-container="body" data-toggle="popover" data-placement="top" data-content="' . $datos[$imp['id']] . '">' . $imp['fecha'] . '</button></td>';
                                        } else {
                                            echo '<td>' . $imp['fecha'] . '</td>';
                                        }

                                        echo '<td>' . $imp['concepto'] . '</td>';
                                        echo '<td>' . $imp['importe'] . '</td>';
                                        echo '<td><input type="text" name="descripcion"></td>';
                                        echo '<td>';
                                        echo '<select id="ingresos_picker" name="gasto" class="form-control form-control-sm">';
                                        echo '<option value="">Escoger</option>';
                                        foreach ($familias_com as $f) {
                                            $hijas = $db->select("familias", ["id", "nombre", "icono", "padre", "ticket"], ["padre" => $f['id'], "ORDER" => ["id" => "ASC"]]);
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
                                        echo '</td>';
                                        echo '<td>';
                                        echo '<select id="ingresos_picker" name="gasto_priv" class="form-control form-control-sm">';
                                        echo '<option value="">Escoger</option>';
                                        foreach ($familias_priv as $f) {
                                            $hijas = $db->select("familias_privadas", ["id", "nombre", "icono", "padre", "ticket"], ["padre" => $f['id'], "ORDER" => ["id" => "ASC"]]);
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
                                        echo '</select>';
                                        echo '</td>';
                                        echo '<td>';
                                        echo '<select id="ingresos_picker" name="ingreso" class="form-control form-control-sm">';
                                        echo '<option value="">Escoger</option>';
                                        foreach ($familias as $f) {
                                            $hijas = $db->select("familias_ingresos", ["id", "nombre", "icono", "padre", "ticket"], ["padre" => $f['id'], "ORDER" => ["id" => "ASC"]]);
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
                                        echo '</select>';
                                        echo '</td>';
                                        echo '<td><button class="btn-success btn-sm" type="submit">Guardar</button></td>';
                                        echo '<td><button class="btn-danger btn-sm" type="submit" name="descartar">Descartar</button></td>';
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
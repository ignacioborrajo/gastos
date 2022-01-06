<?php
session_start();

if (isset($_SESSION['usuario'])) {
    include 'assets/bbdd/conectar.php';

    if (isset($_POST['importar_fichero'])) {
        move_uploaded_file($_FILES['fichero']['tmp_name'], "importar_" . $_SESSION['usuario'] . ".csv");
        $row = 1;
        if (($handle = fopen("importar_" . $_SESSION['usuario'] . ".csv", "r")) !== FALSE) {

            $tabla_importacion = array();
            $tipo_importacion = $_POST['tipo'];

            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                if ($tipo_importacion == 'ABANCA' && $row == 1) {
                    $row++;
                    continue;
                }

                $num = count($data);

                $row++;
                for ($c = 0; $c < $num; $c++) {
                    if ($tipo_importacion == 'ABANCA') {
                        if ($c == 1) {
                            $aux = explode("-", $data[$c]);
                            $tabla_importacion[($row - 1)]['fecha'] = $aux[2] . "-" . $aux[1] . "-" . $aux[0];
                        } else if ($c == 2) {
                            $tabla_importacion[($row - 1)]['concepto'] = $data[$c];
                        } else if ($c == 3) {
                            $tabla_importacion[($row - 1)]['importe'] = str_replace(",", ".", $data[$c]);
                        }
                    }
                }
            }
            fclose($handle);
            $aux = $tabla_importacion;
            foreach ($aux as $key => $fila) {
                if ($db->has("importar", ["AND" => ["fecha" => $fila['fecha'], "concepto" => $fila['concepto']]])) {
                    unset($tabla_importacion[$key]);
                } else {
                    $db->insert("importar", [
                        "usuario" => $_SESSION['usuario'],
                        "fecha" => $fila['fecha'],
                        "concepto" => $fila['concepto'],
                        "importe" => $fila['importe'],
                        "importado" => 'N'
                    ]);
                }
            }
        }
    }


    if (isset($_POST['importar']) && $_POST['importar'] != '') {

        $importado = false;

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
            $datos[$imp['id']] .=  $fam['nombre'] . " - " . $g['importe'] . "\n";
        }

        $gastos_priv = $db->select("gastos_privados", ["fecha", "importe", "familia"], ["usuario" => $_SESSION['usuario'], "fecha" => $imp['fecha']]);
        foreach ($gastos_priv as $g) {
            $fam = $db->get("familias_privadas", ["nombre"], ["id" => $g['familia']]);
            $datos[$imp['id']] .=  $fam['nombre'] . " - " . $g['importe'] . "\n";
        }

        $ingresos = $db->select("ingresos", ["fecha", "importe", "familia"], ["usuario" => $_SESSION['usuario'], "fecha" => $imp['fecha']]);
        foreach ($ingresos as $g) {
            $fam = $db->get("familias_ingresos", ["nombre"], ["id" => $g['familia']]);
            $datos[$imp['id']] .=  $fam['nombre'] . " - " . $g['importe'] . "\n";
        }
    }

    $familias_com = array();
    $categorias = $db->select("familias", ["id", "nombre", "icono", "padre", "ticket"], ["padre" => 0, "ORDER" => ["id" => "ASC"]]);
    foreach ($categorias as $categoria) {
        $familias_com[] = $categoria;
        $familia = $db->select("familias", ["id", "nombre", "icono", "padre", "ticket"], ["padre" => $categoria['id'], "ORDER" => ["id" => "ASC"]]);
        foreach ($familia as $item) {
            $familias_com[] = $item;
        }
    }
    $familias_priv = array();
    $categorias = $db->select("familias_privadas", ["id", "nombre", "icono", "padre", "ticket"], ["usuario" => $_SESSION['usuario'], "padre" => 0, "ORDER" => ["id" => "ASC"]]);
    foreach ($categorias as $categoria) {
        $familias_priv[] = $categoria;
        $familia = $db->select("familias_privadas", ["id", "nombre", "icono", "padre", "ticket"], ["padre" => $categoria['id'], "ORDER" => ["id" => "ASC"]]);
        foreach ($familia as $item) {
            $familias_priv[] = $item;
        }
    }

    $familias = array();
    $categorias = $db->select("familias_ingresos", ["id", "nombre", "icono", "padre", "ticket"], ["usuario" => $_SESSION['usuario'], "padre" => 0, "ORDER" => ["id" => "ASC"]]);
    foreach ($categorias as $categoria) {
        $familias[] = $categoria;
        $familia = $db->select("familias_ingresos", ["id", "nombre", "icono", "padre", "ticket"], ["padre" => $categoria['id'], "ORDER" => ["id" => "ASC"]]);
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
                        <div>
                            <ul class="nav nav-tabs  m-tabs-line m-tabs-line--primary" role="tablist">
                                <li class="nav-item m-tabs__item">
                                    <a class="nav-link m-tabs__link <?= (!isset($_POST['importar_fichero']) ? 'active' : '') ?>" data-toggle="tab" href="#m_tabs_3_1" role="tab">Pendientes</a>
                                </li>
                                <li class="nav-item m-tabs__item">
                                    <a class="nav-link m-tabs__link <?= (isset($_POST['importar_fichero']) ? 'active' : '') ?>" data-toggle="tab" href="#m_tabs_3_3" role="tab">Importar</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- END: Subheader -->
                <div class="m-content">
                    <div class="tab-content">
                        <div class="tab-pane <?= (!isset($_POST['importar_fichero']) ? 'active' : '') ?>" id="m_tabs_3_1" role="tabpanel">

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
                                                if ($datos[$imp['id']] != '') {
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
                                                $padre_actual = -1;
                                                foreach ($familias_com as $familia) {
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
                                                echo '</td>';
                                                echo '<td>';
                                                echo '<select id="ingresos_picker" name="gasto_priv" class="form-control form-control-sm">';
                                                echo '<option value="">Escoger</option>';
                                                $padre_actual = -1;
                                                foreach ($familias_priv as $familia) {
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
                                                echo '</select>';
                                                echo '</td>';
                                                echo '<td>';
                                                echo '<select id="ingresos_picker" name="ingreso" class="form-control form-control-sm">';
                                                echo '<option value="">Escoger</option>';
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
                        <div class="tab-pane <?= (isset($_POST['importar_fichero']) ? 'active' : '') ?>" id="m_tabs_3_3" role="tabpanel">
                            <?php if (count($tabla_importacion) == 0) { ?>
                                <form class="m-form m-form--fit m-form--label-align-right" action="importar.php" method="POST" enctype="multipart/form-data">
                                    <div class="form-group m-form__group row">
                                        <div class="col-lg-5">
                                            <label for="exampleSelect1">Tipo</label>
                                            <select class="form-control m-input" name="tipo" id="exampleSelect1" required>
                                                <option value="ABANCA">Abanca</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-5">
                                            <label for="exampleSelect1">Fichero</label>
                                            <input class="form-control m-input" type="file" name="fichero">
                                        </div>
                                        <div class="col-lg-2">
                                            <label for="exampleSelect1">&nbsp;</label>
                                            <button type="submit" name="importar_fichero" class="btn btn-success"><i class="fa fa-save"></i> Importar</button>
                                        </div>
                                    </div>
                                </form>
                                <?php if (isset($_POST['importar_fichero'])) { ?>
                                    <h3 class="text-center mt-3">No se encontraron datos para importar</h3>
                                <?php } ?>
                            <?php } else { ?>
                                <h3>Entradas importadas: <?= count($tabla_importacion) ?></h3>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Concepto</th>
                                            <th>Importe</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tabla_importacion as $fila) { ?>
                                            <tr>
                                                <td><?= $fila['fecha'] ?></td>
                                                <td><?= $fila['concepto'] ?></td>
                                                <td><?= $fila['importe'] ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } ?>
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
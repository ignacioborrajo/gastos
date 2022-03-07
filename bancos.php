<?php
session_start();

if (isset($_SESSION['usuario'])) {

    include 'assets/bbdd/conectar.php';

    if (isset($_POST['nueva'])) {
        $db->insert("cuentas_bancarias", ["usuario" => $_SESSION['usuario'], "nombre" => $_POST['nombre'], "descripcion" => $_POST['descripcion'], "color" => $_POST['color']]);
    }

    if (isset($_POST['actualizar'])) {
        $db->insert("cuentas_bancarias_det", ["cuenta" => $_POST['cuenta'], "importe" => $_POST["importe"], "fecha" => date('Y-m-d')]);
    }

    if (isset($_POST['eliminar'])) {
        $db->delete("cuentas_bancarias_det", ["cuenta" => $_POST['cuenta']]);
        $db->delete("cuentas_bancarias", ["usuario" => $_SESSION['usuario'], "id" => $_POST['cuenta']]);
    }

    if (isset($_POST['importar'])) {
        move_uploaded_file($_FILES['fichero']['tmp_name'], "importar_" . $_SESSION['usuario'] . ".csv");
        $row = 0;
        if (($handle = fopen("importar_" . $_SESSION['usuario'] . ".csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                if ($row > 0 && !$db->has("cuentas_bancarias_det", ["AND" => ["cuenta" => $_POST['cuenta'], "fecha" => $data[0]]])) {
                    $db->insert("cuentas_bancarias_det", ["cuenta" => $_POST['cuenta'], "importe" => str_replace(",", ".", $data[5]), "fecha" => $data[0]]);
                }
                $row++;
            }
            fclose($handle);
        }
        $row = 1;
        if (($handle = fopen("importar_" . $_SESSION['usuario'] . ".csv", "r")) !== FALSE) {

            $tipo_importacion = 'ABANCA';

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

    $cuentas = $db->select("cuentas_bancarias", ["id", "nombre", "descripcion", "color"], ["usuario" => $_SESSION['usuario']]);
    $importe_total = 0;
    foreach ($cuentas as $key => $cuenta) {
        $importe = $db->select("cuentas_bancarias_det", ["importe"], ["cuenta" => $cuenta['id'], "ORDER" => ["fecha" => "DESC", "importe" => "DESC"], "LIMIT" => 1]);
        $historico = $db->select("cuentas_bancarias_det", ["fecha", "importe"], ["cuenta" => $cuenta['id'], "ORDER" => ["fecha" => "ASC"]]);
        if(count($importe) == 0) {
            $cuentas[$key]["importe"] = 0;
            $importe_total += 0;
        } else {
            $cuentas[$key]["importe"] = intval($importe[0]['importe']);
            $importe_total += intval($importe[0]['importe']);
        }
        $cuentas[$key]["historico"] = $historico;
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
    var series = new Array();
    <?php foreach ($cuentas as $cuenta) { ?>
        var datos = new Array();
        <?php foreach ($cuenta['historico'] as $datos) {
            $aux = explode("-", $datos['fecha']); ?>
            datos.push([Date.UTC(<?= $aux[0] ?>, <?= intval($aux[1] - 1) ?>, <?= intval($aux[2]) ?>), <?= $datos['importe'] ?>]);
        <?php } ?>
        series.push({
            name: "<?= $cuenta['nombre'] ?>",
            data: datos,
            color: "<?= $cuenta['color'] ?>",
            marker: {
                enabled: false,
            },
        });
    <?php } ?>
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
                        <div class="col-xl-8">
                            <div class="m-portlet m-portlet--full-height ">
                                <div class="m-portlet__head">
                                    <div class="m-portlet__head-caption">
                                        <div class="m-portlet__head-title">
                                            <h3 class="m-portlet__head-text">
                                                Cuentas Bancarias
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="m-portlet__head-tools">
                                        <ul class="nav nav-pills nav-pills--brand m-nav-pills--align-right m-nav-pills--btn-pill m-nav-pills--btn-sm" role="tablist">
                                            <li class="nav-item m-tabs__item">
                                                <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_widget4_tab1_content" role="tab">
                                                    Mis Cuentas
                                                </a>
                                            </li>
                                            <li class="nav-item m-tabs__item">
                                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_widget4_tab2_content" role="tab">
                                                    Nueva
                                                </a>
                                            </li>
                                            <li class="nav-item m-tabs__item">
                                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_widget4_tab3_content" role="tab">
                                                    Importar
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="m-portlet__body">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="m_widget4_tab1_content">
                                            <div class="m-widget4 m-widget4--progress">
                                                <?php foreach ($cuentas as $cuenta) {
                                                    $porcentaje = $importe_total != 0 ? (($cuenta['importe'] / $importe_total) * 100) : 0; ?>
                                                    <div class="m-widget4__item">
                                                        <div class="m-widget4__info">
                                                            <span class="m-widget4__title">
                                                                <?= $cuenta['nombre'] ?>
                                                            </span><br>
                                                            <span class="m-widget4__sub">
                                                                <?= $cuenta['descripcion'] ?>
                                                            </span>
                                                        </div>
                                                        <div class="m-widget4__progress">
                                                            <div class="m-widget4__progress-wrapper">
                                                                <span id="importe-<?= $cuenta['id'] ?>" class="m-widget17__progress-number"><?= $cuenta['importe'] ?></span>
                                                                <span class="m-widget17__progress-label">
                                                                    <form method="POST" id="form-<?= $cuenta['id'] ?>" style="display: none;">
                                                                        <input type="hidden" name="cuenta" value="<?= $cuenta['id'] ?>">
                                                                        <input type="text" name="importe" value="<?= $cuenta['importe'] ?>" class="mr-2">
                                                                        <button type="submit" name="actualizar" class="btn btn-sm btn-link p-0 txt-danger mr-2"><i class="fa fa-save"></i></button>
                                                                        <button type="button" id="cancelar-<?= $cuenta['id'] ?>" data-cuenta="<?= $cuenta['id'] ?>" class="cancelar-edicion btn btn-sm btn-link p-0 txt-primary"><i class="fa fa-times"></i></button>
                                                                    </form>
                                                                    <a href="#" id="editar-<?= $cuenta['id'] ?>" class="editar-cuenta txt-primary" data-cuenta="<?= $cuenta['id'] ?>"><i class=" fa fa-pencil"></i></a>
                                                                </span>
                                                                <div id="progress-<?= $cuenta['id'] ?>" class="progress m-progress--sm" style="display: flex;">
                                                                    <div class="progress-bar m--bg-danger" role="progressbar" style="width: <?= $porcentaje ?>%;background-color:<?= $cuenta['color'] ?> !important;" aria-valuenow="<?= $porcentaje ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="m-widget4__ext">
                                                            <form id="eliminar-form-<?= $cuenta['id'] ?>" method="POST">
                                                                <input type="hidden" name="cuenta" value="<?= $cuenta['id'] ?>">
                                                                <input type="hidden" name="eliminar" value="<?= $cuenta['id'] ?>">
                                                                <button type="submit" data-cuenta="<?= $cuenta['id'] ?>" name="eliminar" class="eliminar-cuenta m-btn m-btn--hover-danger m-btn--pill btn btn-sm btn-secondary"><i class="fa fa-trash"></i> Eliminar</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="m_widget4_tab2_content">
                                            <form method="POST" class="m-form m-form--fit m-form--label-align-right">
                                                <div class="m-portlet__body">
                                                    <div class="form-group m-form__group">
                                                        <label for="exampleInputEmail1">Nombre</label>
                                                        <input type="text" name="nombre" class="form-control m-input m-input--square" placeholder="Nombre de la cuenta">
                                                        <span class="m-form__help">Nombre con el que se mostrará esta cuenta.</span>
                                                    </div>
                                                    <div class="form-group m-form__group">
                                                        <label for="exampleInputEmail1">Descripción</label>
                                                        <input type="text" name="descripcion" maxlength="200" class="form-control m-input m-input--square" placeholder="Pequeña descripción de la cuenta">
                                                        <span class="m-form__help">Puedes introducir una pequeña descripción de la cuenta.</span>
                                                    </div>
                                                    <div class="form-group m-form__group">
                                                        <label for="exampleInputEmail1">Color</label>
                                                        <input class="form-control m-input" type="color" name="color" value="" style="padding: 5px 10px;height: 35px;">
                                                        <span class="m-form__help">Nombre con el que se mostrará esta cuenta.</span>
                                                    </div>
                                                </div>
                                                <div class="m-portlet__foot m-portlet__foot--fit">
                                                    <div class="m-form__actions">
                                                        <button type="submit" name="nueva" class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="tab-pane" id="m_widget4_tab3_content">
                                            <div class="m-portlet__body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <form class="m-form m-form--fit m-form--label-align-right" action="bancos.php" method="POST" enctype="multipart/form-data">
                                                            <div class="form-group m-form__group">
                                                                <label for="exampleSelect1">Cuenta</label>
                                                                <select class="form-control m-input" name="cuenta" id="exampleSelect1" required>
                                                                    <?php foreach ($cuentas as $cuenta) { ?>
                                                                        <option value="<?= $cuenta['id'] ?>"><?= $cuenta['nombre'] ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="form-group m-form__group row">
                                                                <label for="exampleSelect1">Fichero</label>
                                                                <input class="form-control m-input" type="file" name="fichero">
                                                            </div>
                                                            <div class="form-group m-form__group">
                                                                <button type="submit" name="importar" class="btn btn-success"><i class="fa fa-save"></i> Importar</button>
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
                        <div class="col-xl-4">
                            <div class="m-portlet m--bg-accent m-portlet--bordered-semi m-portlet--skin-dark m-portlet--full-height ">
                                <div class="m-portlet__head">
                                    <div class="m-portlet__head-caption">
                                        <div class="m-portlet__head-title">
                                            <h3 class="m-portlet__head-text">
                                                Total
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-portlet__body">
                                    <div class="m-widget7 m-widget7--skin-dark" style="height: 100%;">
                                        <div class="m-widget7__desc" style="display: flex;margin: 0;height: 100%;justify-content: center;flex-direction: column;font-weight: bold;font-size: 3rem;">
                                            <?= $importe_total ?>€
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
    <script src="assets/demo/default/custom/components/forms/widgets/dropzone.js" type="text/javascript"></script>
    <!--end::Base Scripts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <!--begin::Page Snippets -->
    <script src="assets/snippets/pages/user/bancos.js?t=<?= time() ?>" type="text/javascript"></script>
    <!--end::Page Snippets -->
</body>
<!-- end::Body -->

</html>
<?php

function subfamilias_privadas($db, $categoria)
{
    if ($categoria != '') {
        $out = array();
        $subfamilias = $db->select("familias_privadas", ["id", "nombre", "icono", "padre", "ticket"], ["padre" => $categoria['id'], "ORDER" => ["id" => "ASC"]]);
        foreach ($subfamilias as $item) {
            $out[] = [
                'familia' => $item,
                'subfamilias' => subfamilias_privadas($db, $item)
            ];
        }
        return $out;
    }
    return array();
}

function familias_privadas($db)
{
    $familias = array();
    $categorias = $db->select("familias_privadas", ["id", "nombre", "icono", "padre", "ticket"], ["usuario" => $_SESSION['usuario'], "padre" => 0, "ORDER" => ["id" => "ASC"]]);
    foreach ($categorias as $categoria) {
        $familias[] = [
            'familia' => $categoria,
            'subfamilias' => subfamilias_privadas($db, $categoria)
        ];
    }
    return $familias;
}

function opciones_familias_privadas($familias, $nivel = 0, $sel_todas = false)
{
    $out = array();
    foreach ($familias as $familia) {
        $espacios = '';
        for ($i = 0; $i < $nivel; $i++) {
            $espacios .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        if (count($familia['subfamilias']) > 0) {
            if ($sel_todas) {
                $out[] = '<option value="' . $familia['familia']['id'] . '" data-icon="' . $familia['familia']['icono'] . '" data-tokens="' . $familia['familia']['ticket'] . '">' . $espacios . $familia['familia']['nombre'] . '</option>';
            } else {
                $out[] = '<optgroup label="' . $espacios . $familia['familia']['nombre'] . '" data-max-options="2">';
            }
            $out[] = implode("", opciones_familias_privadas($familia['subfamilias'], ($nivel + 1), $sel_todas));
            if (!$sel_todas) {
                $out[] = '</optgroup>';
            }
        } else {
            $out[] = '<option value="' . $familia['familia']['id'] . '" data-icon="' . $familia['familia']['icono'] . '" data-tokens="' . $familia['familia']['ticket'] . '">' . $espacios . $familia['familia']['nombre'] . '</option>';
        }
    }
    return $out;
}

function lista_familias_privadas($familias, $nivel = 0, $sel_todas = false)
{
    $out = array();
    foreach ($familias as $familia) {
        $espacios = '';
        for ($i = 0; $i < $nivel; $i++) {
            $espacios .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        if (count($familia['subfamilias']) > 0) {
            $out[] = '<li class="m-nav__item m-nav__item--active">
                        <a href="#" class="m-nav__link  editar-familia" data-id="' . $familia['familia']['id'] . '" data-nombre="' . $familia['familia']['nombre'] . '" data-padre="' . $familia['familia']['padre'] . '" data-ticket="' . $familia['familia']['ticket'] . '">
                            <i class="m-nav__link-icon fa fa-trash-o"></i>
                            <span class="m-nav__link-title">
                                <span class="m-nav__link-wrap">
                                    <span class="m-nav__link-text">
                                        ' . $familia['familia']['nombre'] . '
                                    </span>
                                </span>
                            </span>
                        </a>
                        <ul class="m-nav__sub">';
            $out[] = implode("", lista_familias_privadas($familia['subfamilias'], ($nivel + 1), $sel_todas));
            $out[] = '</ul></li>';
        } else {
            $out[] = '<li class="m-nav__item">
                        <a href="#" class="m-nav__link editar-familia" data-id="' . $familia['familia']['id'] . '" data-nombre="' . $familia['familia']['nombre'] . '" data-padre="' . $familia['familia']['padre'] . '" data-ticket="' . $familia['familia']['ticket'] . '">
                            <span class="m-nav__link-bullet m-nav__link-bullet--line">
                                <span></span>
                            </span>
                            <span class="m-nav__link-text">
                                ' . $familia['familia']['nombre'] . '
                            </span>
                        </a>
                    </li>';
        }
    }
    return $out;
}

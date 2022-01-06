<?php

function transformar_fecha($entrada,$caracter) {
    $aux = explode($caracter,$entrada);
    $fecha = $aux[2]."-".$aux[1]."-".$aux[0];
    return $fecha;
}

function getTablas($tipo, $usuario, $familia, $fecha, $importe, $ticket, $viaje) {
    $salida = array();
    switch ($tipo) {
        case 'viaje':
            $salida['gastos'] = 'viajes_gastos';
            $salida['ticket'] = 'viajes_tickets';
            $salida['productos'] = 'viajes_productos';
            $salida['columnas'] = [
                
                "viaje"=>$viaje,
                "usuario"=>$usuario,
                "familia"=>$familia,
                "fecha"=>transformar_fecha($fecha,"/"),
                "importe"=>$importe,
                "tiene_ticket"=>$ticket

            ];
            break;
    }
    
    return $salida;
    
}

function getFamilias ($tabla) {
    
    include 'bbdd/conectar.php';
    
    $familias = array();
    $categorias = $gastos_db->select($tabla, ["id", "nombre", "icono", "padre", "ticket"], ["padre" => 0, "ORDER" => ["nombre" => "ASC"]]);
    foreach ($categorias as $categoria) {
        $familias[] = $categoria;
        $familia = $gastos_db->select($tabla, ["id", "nombre", "icono", "padre", "ticket"], ["padre" => $categoria['id'], "ORDER" => ["nombre" => "ASC"]]);
        foreach ($familia as $item) {
            $familias[] = $item;
        }
    }
    
    return $familias;
    
}

function getTicket ($productos) {
    
    include 'bbdd/conectar.php';
    
    $total = 0;
    $lineas = array();
    foreach($productos as $producto) {
        $lineas[] = "<tr>";
        $lineas[] = "<td>".$producto['producto']."</td>";
        $lineas[] = "<td class='pull-right'>".$producto['importe']."</td>";
        $lineas[] = "</tr>";
        $total += $producto['importe'];
    }   
    
    $lineas[] = "<tr>";
    $lineas[] = "<td style='text-align: right;font-weight: bold;'>Total</td>";
    $lineas[] = "<td style='text-align: right;font-weight: bold;'>".$total."</td>";
    $lineas[] = "</tr>";
    
    return implode("", $lineas);
    
}
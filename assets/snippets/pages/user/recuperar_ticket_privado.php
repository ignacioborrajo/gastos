<?php
session_start();

include '../../../bbdd/conectar.php';

if(isset($_POST['ticket'])) {
    
    $productos = $db->select("tickets_privados",["producto","importe"],["gasto"=>$_POST['ticket']]);
    
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
    
    echo implode("", $lineas);
    
} else {
    echo "";
}

?>
<?php

include '../../../bbdd/conectar.php';

if(isset($_POST['gasto_id'])) {
    
    $aux = explode("/",$_POST['fecha']);
    $fecha = $aux[2]."-".$aux[1]."-".$aux[0];
    
    $db->update("gastos",[
        
        "usuario"=>$_POST['usuario'],
        "familia"=>$_POST['familia'],
        "fecha"=>$fecha,
        "importe"=>$_POST['importe'],
        "tiene_ticket"=>(isset($_POST['ticket'][0]) && $_POST['ticket'][0]['t_importe'] > 0 ? 'S':'N')
        
    ],["id"=>$_POST['gasto_id']]);
    
    if(count($_POST['ticket']) > 0) {
        foreach ($_POST['ticket'] as $key=>$linea) {
            if($linea['t_importe'] > 0) {
                
                if($key == 0) $db->delete("tickets",["gasto"=>$_POST['gasto_id']]);
                
                if($db->has("productos",["nombre"=>$linea['producto']])) {
                    $id_producto = $db->get("productos","id",["nombre"=>$linea['producto']]);
                } else {
                    $db->insert("productos",["nombre"=>$linea['producto']]);
                    $id_producto = $db->id();
                }
                
                $db->insert("tickets",[

                    "fecha"=>$fecha,
                    "producto"=>$linea['producto'],
                    "id_producto"=>$id_producto,
                    "importe"=>$linea['t_importe'],
                    "gasto"=>$_POST['gasto_id']

                ]);
            }
        }
    }
    
    echo $_POST['gasto_id'];
    
} else {
    echo 0;
}

?>
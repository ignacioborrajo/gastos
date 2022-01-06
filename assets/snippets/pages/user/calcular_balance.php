<?php

include '../../../bbdd/conectar.php';

$salida = "";

$nacho_total = $db->sum("gastos","importe",["usuario"=>'1']);
$nelly_total = $db->sum("gastos","importe",["usuario"=>'2']);

$total = $nacho_total + $nelly_total;

$nacho_pr = ($nacho_total/$total)*100;
$nelly_pr = ($nelly_total/$total)*100;

$time = time();
$inicio = mktime("00","00","00",date("n",$time),"1",date("Y",$time));
$fin = mktime("23","59","59",date("n",$time),date("t",$time),date("Y",$time));
$total_este_mes = $db->sum("gastos","importe",["fecha[<>]"=>[date("Y-m-d",$inicio),date("Y-m-d",$fin)]]);

$salida = array("nacho"=>$nacho_pr, "nelly"=>$nelly_pr, "nacho_total"=>$nacho_total, "nelly_total"=>$nelly_total, "total"=>$total, "total_este_mes"=>$total_este_mes);

echo json_encode($salida);

?>
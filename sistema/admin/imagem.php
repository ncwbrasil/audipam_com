<?php
include('../mod_includes/php/m2brimagem.class.php');
$arquivo	= $_GET['arquivo'];
$largura	= $_GET['largura'];
$altura		= $_GET['altura'];
$marca		= $_GET['marca'];

$oImg = new m2brimagem($arquivo);
$valida = $oImg->valida();
if ($valida == 'OK') {
	$oImg->redimensiona($largura,$altura,'crop');
    $oImg->grava();
} else {
	die($valida);
}
exit;
?>
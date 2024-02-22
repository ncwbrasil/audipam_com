<?php
include('connect.php');
$tam = $_POST['tam'];
$tamanhoMaximo = 1024000;
if($tam > $tamanhoMaximo) 
{
	echo "true";
} 
?>
<?php

// Include the qrlib file 
include '../../core/mod_includes/php/funcoes.php'; 
sec_session_start(); 
include '../../core/mod_includes/php/phpqrcode/qrlib.php'; 

$id = $_GET['id'];
$pagina = $_GET['pagina'];
// Gera key
$chave = generatePassword();
$text = base64_encode(encriptar($id,base64_encode($chave))).$chave; 

$url_parts = explode("/", $_SERVER['HTTP_REFERER']);

$url = $url_parts[0]."/".$url_parts[1]."/".$url_parts[2]."/".$url_parts[3]."/".$url_parts[4]."/$pagina/".$id;

QRcode::png($url);



//http://192.168.0.11:8082/audipam_sistemas/proclegis/materias/26213
//http://192.168.0.11:8082/audipam_sistemas/proclegis/materia_legislativa/26343
//http://192.168.0.11:8082/audipam_sistemas/proclegis/materia_legislativa/26265

?>




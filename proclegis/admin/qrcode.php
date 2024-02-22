<?php

// Include the qrlib file 
include '../../core/mod_includes/php/funcoes.php'; 
sec_session_start(); 
include '../../core/mod_includes/php/phpqrcode/qrlib.php'; 

$login_id = $_GET['login_id'];
$cli_url = $_GET['cli_url'];
$sis_url = $_GET['sis_url'];
// Gera key
$chave = generatePassword();
$text = base64_encode(encriptar($login_id."|".$sis_url."|".$cli_url,base64_encode($chave))).$chave; 

$url_parts = explode("/", $_SERVER['HTTP_REFERER']);
                            
//localhost
//$url = $url_parts[0]."/".$url_parts[1]."/".$url_parts[2]."/".$url_parts[3]."/".$url_parts[4]."/";

//Cloud
$url = $url_parts[0]."/".$url_parts[1]."/".$url_parts[2]."/".$url_parts[3]."/".$url_parts[4]."/qrCode_libera_login";

// $path variable store the location where to  
// store image and $file creates directory name 
// of the QR code file by using 'uniqid' 
// uniqid creates unique id based on microtime 
// $path = 'uploads/facial_qrcodes/'.$dis_id.'/'; 
// if(!file_exists($path)){mkdir($path, 0755, true);}

// $file = $path.uniqid().".png"; 

// $ecc stores error correction capability('L') 


// Generates QR Code and Stores it in directory given 
QRcode::png($url."/?hash=".$text);

?>
<?php
$cidade = str_replace("amp;","",$cidade);
$regiao = str_replace("amp;","",$regiao);

$caracter_a = array ('ã','á','à','ä','â');
$limpo_a = array ('&atilde;','&aacute;','&agrave;','&auml;','&acirc;');
$cidade = str_replace($limpo_a, $caracter_a, $cidade);
$regiao = str_replace($limpo_a, $caracter_a, $regiao);

$caracter_e = array ('é','è','ë','ê');
$limpo_e = array ('&eacute;','&egrave;','&euml;','&ecirc;');
$cidade = str_replace($limpo_e,$caracter_e,$cidade);	
$regiao = str_replace($limpo_e,$caracter_e,$regiao);	

$caracter_i = array ('í','ì','ï','î');
$limpo_i = array ('&iacute;','&igrave;','&iuml;','&icirc;');
$cidade = str_replace($limpo_i,$caracter_i,$cidade);	
$regiao = str_replace($limpo_i,$caracter_i,$regiao);	

$caracter_o = array ('õ','ó','ò','ö','ô');
$limpo_o = array ('&otilde;','&oacute;','&ograve;','&ouml;','&ocirc;');
$cidade = str_replace($limpo_o,$caracter_o,$cidade);	
$regiao = str_replace($limpo_o,$caracter_o,$regiao);	

$caracter_u = array ('ú','ù','ü','û');
$limpo_u = array ('&uacute;','&ugrave;','&uuml;','&ucirc;');
$cidade = str_replace($limpo_u,$caracter_u,$cidade);	
$regiao = str_replace($limpo_u,$caracter_u,$regiao);	

$caracter_c = array ('ç');
$limpo_c = array ('&ccedil;');
$cidade = str_replace($limpo_c,$caracter_c,$cidade);	
$regiao = str_replace($limpo_c,$caracter_c,$regiao);	


?>
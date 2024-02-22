<?php
include('connect.php');
$ext = $_POST['ext'];
$extensoes = array(".pdf",".PDF");
	
if(!in_array(strrchr($ext, "."), $extensoes)) 
{
	echo "true";
} 
?>
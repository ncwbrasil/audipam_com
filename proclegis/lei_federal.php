<?php
$pagina_link = 'cadastro_normas_juridicas';

include_once("../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("mod_includes_portal/php/connect.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php
    //include("header.php"); 
    include_once('url.php');
    ?> 
    
</head>
<body>
	<main class="cd-main-content">    
    <?php 
    
        $id = $_GET['id'];
        $sql = "SELECT div_conteudo
                    FROM leisdiv 
                WHERE div_id = :id	
                ";
        $stmt = $PDO_PROCLEGIS->prepare($sql);  
        $stmt->bindParam(':id', 	$id);
        $stmt->execute();
        $rows = $stmt->rowCount();
        if ($rows > 0)
        {
            
            while($result = $stmt->fetch())
            {
                
                echo $result['div_conteudo'];
            }

        }

    ?>
	</main> <!-- .cd-main-content -->   
</body>
</html>
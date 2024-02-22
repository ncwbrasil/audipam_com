<?php 
$pagina_link = 'aux_mesa_diretora';
include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include('header.php'); ?>
</head>
<body>	
	<main class="cd-main-content">
    	<!--MENU-->
		<?php include("../mod_menu/menu.php"); ?>
        
        <!--CONTEUDO CENTRO-->
		<div class="content-wrapper">
            <div class='mensagem'></div>
            <?php
            $page = "Auxiliares &raquo; <a href='aux_mesa_diretora/view'>Módulo Mesa Diretora</a>";
            
            if($pagina == "view")
            {
                echo "
                <div class='titulo'> $page  </div>  
                
                <a href='aux_mesa_diretora_cargos/view'>
                    <div class='modulos'>
                        Cargos da Mesa
                    </div>
                </a>
                <a href='aux_mesa_diretora_sessoes/view'>
                    <div class='modulos'>
                        Sessões Legislativas
                    </div>
                </a>               
                ";     
            }               
            ?>
       	</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
</body>
</html>
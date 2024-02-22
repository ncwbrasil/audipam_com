<?php 
$pagina_link = 'aux_sessoes_plenarias';
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
            $page = "Auxiliares &raquo; <a href='aux_sessoes_plenarias/view'>Módulo Sessões Plenárias</a>";
            
            if($pagina == "view")
            {
                echo "
                <div class='titulo'> $page  </div>  
                
                <a href='aux_sessoes_plenarias_tipos/view'>
                    <div class='modulos'>
                        Tipo de Sessão
                    </div>
                </a>
                <a href='aux_sessoes_plenarias_tipo_resultado/view'>
                    <div class='modulos'>
                        Tipo de Resultado
                    </div>
                </a>  
                <a href='aux_sessoes_plenarias_tipo_expediente/view'>
                    <div class='modulos'>
                        Tipo de Expediente
                    </div>
                </a>
                <a href='aux_sessoes_plenarias_tipo_retirada_pauta/view'>
                    <div class='modulos'>
                        Tipo de Retirada de Pauta
                    </div>
                </a> 
                <a href='aux_sessoes_plenarias_tipo_justificativa/view'>
                    <div class='modulos'>
                        Tipo de Justificativa
                    </div>
                </a>                   
                           
                ";     
            }               
            ?>
       	</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
</body>
</html>
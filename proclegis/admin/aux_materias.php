<?php 
$pagina_link = 'aux_materias';
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
            $page = "Auxiliares &raquo; <a href='aux_materias/view'>Módulo Matérias Legislativas</a>";
            
            if($pagina == "view")
            {
                echo "
                <div class='titulo'> $page  </div>  
                
                <a href='aux_materias_tipos/view'>
                    <div class='modulos'>
                        Tipo de Matéria Legislativa
                    </div>
                </a>
                <a href='aux_materias_documentos/view'>
                    <div class='modulos'>
                        Tipo de Documentos
                    </div>
                </a>  
                <a href='aux_materias_assuntos/view'>
                    <div class='modulos'>
                        Assuntos de Matéria
                    </div>
                </a>
                <a href='aux_materias_regime_tramitacao/view'>
                    <div class='modulos'>
                        Regime de Tramitação
                    </div>
                </a> 
                <a href='aux_materias_status_tramitacao/view'>
                    <div class='modulos'>
                        Status de Tramitação
                    </div>
                </a>   
                <a href='aux_materias_tipo_fim_relatoria/view'>
                    <div class='modulos'>
                        Tipo de Fim de Relatoria
                    </div>
                </a>
                <a href='aux_materias_origem/view'>
                    <div class='modulos'>
                        Origens
                    </div>
                </a>
                <a href='aux_materias_orgaos/view'>
                    <div class='modulos'>
                        Órgãos
                    </div>
                </a>  
                <a href='aux_materias_unidade_tramitacao/view'>
                    <div class='modulos'>
                        Unidades de Tramitação
                    </div>
                </a> 
                           
                ";     
            }               
            ?>
       	</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
</body>
</html>
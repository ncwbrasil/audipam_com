<?php 
$pagina_link = 'aux_parlamentares';
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
            $page = "Auxiliares &raquo; <a href='aux_parlamentares/view'>Módulo Parlamentares</a>";
            
            if($pagina == "view")
            {
                echo "
                <div class='titulo'> $page  </div>  
                
                <a href='aux_parlamentares_legislaturas/view'>
                    <div class='modulos'>
                    Legislaturas
                    </div>
                </a>
                <a href='aux_parlamentares_tipo_dependentes/view'>
                    <div class='modulos'>
                        Tipo de dependentes
                    </div>
                </a>
                <a href='aux_parlamentares_tipo_afastamento/view'>
                    <div class='modulos'>
                        Tipo de afastamento
                    </div>
                </a>
                <a href='aux_parlamentares_tipo_situacao_militar/view'>
                    <div class='modulos'>
                        Tipo de situação militar
                    </div>
                </a>
                <a href='aux_parlamentares_nivel_instrucao/view'>
                    <div class='modulos'>
                        Nível de instrução
                    </div>
                </a>
                <a href='aux_parlamentares_partidos/view'>
                    <div class='modulos'>
                        Partidos
                    </div>
                </a>
                <a href='aux_parlamentares_coligacoes/view'>
                    <div class='modulos'>
                        Coligações
                    </div>
                </a>
                ";     
            }               
            ?>
       	</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
</body>
</html>
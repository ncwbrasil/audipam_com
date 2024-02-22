<?php
$pagina_link = 'cadastro_sessoes_plenarias';
include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include("header.php");?> 
    <!-- TINY -->
    <script src="../../core/mod_includes/js/tinymce/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: 'textarea',
            plugins: "image code jbimages imagetools advlist link table textcolor media",
            toolbar: "undo redo format bold italic forecolor backcolor alignleft aligncenter alignright alignjustify bullist numlist outdent indent table link media image jbimages",
            imagetools_toolbar: "rotateleft rotateright | flipv fliph | editimage imageoptions",
            paste_data_images: true,
            media_live_embeds: true,
            relative_urls: false,
        });
    </script>
    <!-- TINY -->
</head>
<body>
	<main class="cd-main-content">    
    	<!--MENU-->
		<?php include("../mod_menu/menu.php"); ?>
        
        <!--CONTEUDO CENTRO-->
		<div class="content-wrapper">
            <div class='mensagem'></div>
            <?php    
            if(isset($_GET['id'])){$id = $_GET['id'];}
                     
            $sql = "SELECT *, cadastro_sessoes_plenarias.id as id
                            , cadastro_sessoes_plenarias.numero as numero
                            , aux_parlamentares_legislaturas.numero as numero_legislatura
                            , YEAR(aux_parlamentares_legislaturas.data_inicio) as data_inicio_legislatura
                            , YEAR(aux_parlamentares_legislaturas.data_fim) as data_fim_legislatura
                            , aux_mesa_diretora_sessoes.numero as numero_sessao
                            , YEAR(aux_mesa_diretora_sessoes.data_inicio) as data_inicio_sessao
                            , YEAR(aux_mesa_diretora_sessoes.data_fim) as data_fim_sessao
                            , aux_parlamentares_legislaturas.id as id_legislatura
                    FROM cadastro_sessoes_plenarias 
                    LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_sessoes_plenarias.legislatura                     
                    LEFT JOIN aux_mesa_diretora_sessoes ON aux_mesa_diretora_sessoes.id = cadastro_sessoes_plenarias.sessao                     
                    LEFT JOIN aux_sessoes_plenarias_tipos ON aux_sessoes_plenarias_tipos.id = cadastro_sessoes_plenarias.tipo_sessao                     
                    WHERE cadastro_sessoes_plenarias.id = :id 			
                    ORDER BY cadastro_sessoes_plenarias.id DESC  ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);    
                
            $stmt->bindParam(':id', 	$id);
           
            $stmt->execute();
            $rows = $stmt->rowCount();
            if($rows > 0)
            {
                $result = $stmt->fetch();
                $id_legislatura = $result['id_legislatura'];
                $n = $result['numero'];
                $d = $result['descricao'];
                $n_s = $result['numero_sessao'];
                $n_l = $result['numero_legislatura'];
            }
            
            $page = "Cadastro &raquo; <a href='cadastro_sessoes_plenarias/view'>Sessões Plenárias</a> &raquo; <a href='cadastro_sessoes_plenarias/exib/$id'>Exibir</a>";
                    
            if($pagina == "view")
            {
                echo "
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_sessoes_plenarias/exib/$id/editar_presenca/#abertura'>                            
                    <div class='titulo'> $page   &raquo; Abertura: Presença</div>                
                    <div class='conteudo'>
                            ".$n."ª Sessão Plenária ".$d." da ".$n_s." Sessão Legislativa da ".$n_l." Legislatura                            
								<p><br><span class='bold'>Presença:</span>";
                              
                                $sql = "SELECT *, cadastro_parlamentares.id as id
                                        FROM cadastro_parlamentares
                                        LEFT JOIN cadastro_parlamentares_mandatos ON cadastro_parlamentares_mandatos.parlamentar = cadastro_parlamentares.id
                                        WHERE cadastro_parlamentares_mandatos.legislatura = :legislatura
                                        AND   status = :status	
                                        ORDER BY nome ASC
                                        ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);                                    
                                
                                $stmt->bindParam(':legislatura', 	$id_legislatura);                                                                                                        
                                $stmt->bindValue(':status', 	1);
                                $stmt->execute();
                                $rows = $stmt->rowCount();                     
                                if($rows > 0)
                                {
                                    while($result = $stmt->fetch())
                                    {
                                        $sql = "SELECT *, cadastro_sessoes_plenarias_presenca.id as id                            
                                                FROM cadastro_sessoes_plenarias_presenca
                                                LEFT JOIN cadastro_sessoes_plenarias ON cadastro_sessoes_plenarias.id = cadastro_sessoes_plenarias_presenca.sessao_plenaria                     
                                                WHERE parlamentar = :parlamentar AND
                                                      sessao_plenaria = :sessao_plenaria	
                                                ORDER BY cadastro_sessoes_plenarias_presenca.id ASC
                                                ";
                                        $stmt_int = $PDO_PROCLEGIS->prepare($sql);                                            
                                            
                                        $stmt_int->bindParam(':parlamentar', 	$result['id']);
                                        $stmt_int->bindParam(':sessao_plenaria', 	$id);
                                        $stmt_int->execute();
                                        $rows_int = $stmt_int->rowCount();
                                        if($rows_int > 0)
                                        {
                                            echo "<br><input class='marcar' checked type='checkbox' name='presenca[]' value='".$result['id']."' > ".$result['nome']."";
                                        }
                                        else
                                        {
                                            echo "<br><input class='marcar' type='checkbox' name='presenca[]' value='".$result['id']."' > ".$result['nome']."";
                                        }
                                    }
                                }                                                 														
								echo "							                                                                                                             
                        </div>                                                                                                 				
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_sessoes_plenarias/view'; value='Cancelar'/></center>
                        </center>                    
                </form>";     
            }            
            ?>
    	</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
    <script>                
        //CALENDÁRIOinput
        jQuery("input[name*='data'], #fil_ext_de, #fil_ext_ate").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior'
        });
    
    </script> 
    <!-- MODAL -->
	<script type="text/javascript" src="../../core/mod_includes/js/mdbootstrap/js/jquery-3.4.1.min.js"></script>
	<!-- Bootstrap tooltips -->
	<script type="text/javascript" src="../../core/mod_includes/js/mdbootstrap/js/popper.min.js"></script>
	<!-- Bootstrap core JavaScript -->
	<script type="text/javascript" src="../../core/mod_includes/js/mdbootstrap/js/bootstrap.min.js"></script>
	<!-- MDB core JavaScript -->
    <script type="text/javascript" src="../../core/mod_includes/js/mdbootstrap/js/mdb.min.js"></script>
  
</body>
</html>
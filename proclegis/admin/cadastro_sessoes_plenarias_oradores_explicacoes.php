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
            $page = "Cadastro  &raquo; <a href='cadastro_sessoes_plenarias/view'>Sessões Plenárias</a> &raquo;  <a href='cadastro_sessoes_plenarias/exib/$id'>Exibir</a> ";
             
            
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

            if($action == "adicionar_oradores_explicacoes")
            {            
                           
                $parlamentar   = $_POST['parlamentar'];
                $ordem   = $_POST['ordem'];
                $url_video   = $_POST['url_video'];if($url_video == ""){$url_video = null;}
                $observacao   = $_POST['observacao'];if($observacao == ""){$observacao = null;}

                $dados = array(
                    'sessao_plenaria' 		=> $id,
                    'parlamentar' 		    => $parlamentar,                    
                    'ordem' 		    => $ordem,
                    'url_video' 		        => $url_video,
                    'observacao' 		        => $observacao
                    );
                $sql = "INSERT INTO cadastro_sessoes_plenarias_oradores_explicacoes SET ".bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                if($stmt->execute($dados))
                {	
                    
                    log_operacao($id, $PDO_PROCLEGIS); 	

                    ?>
                    <script>
                        mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                    </script>
                    <?php                  
                }
                else
                {
                    ?>
                    <script>
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php 
                }	
            }
            if($action == "editar_oradores_explicacoes")
            {                       
                $id_oradores_explicacoes   = $_POST['id_oradores_explicacoes'];
                $parlamentar   = $_POST['parlamentar'];
                $ordem   = $_POST['ordem'];
                $url_video   = $_POST['url_video'];if($url_video == ""){$url_video = null;}
                $observacao   = $_POST['observacao'];if($observacao == ""){$observacao = null;}

                $dados = array(
                    'sessao_plenaria' 		=> $id,
                    'parlamentar' 		    => $parlamentar,                    
                    'ordem' 		    => $ordem,
                    'url_video' 		        => $url_video,
                    'observacao' 		        => $observacao
                    );
                    
                $sql = "UPDATE cadastro_sessoes_plenarias_oradores_explicacoes SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id_oradores_explicacoes;
                if($stmt->execute($dados))
                {		
                    log_operacao($id, $PDO_PROCLEGIS); 	
                    ?>
                    <script>
                        mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                    </script>
                    <?php                  
                }
                else
                {
                    ?>
                    <script>
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php 
                }	
            }
            if($action == 'excluir_oradores_explicacoes')
            {
                $id_sub = $_GET['id_sub'];

                $sql = "UPDATE cadastro_sessoes_plenarias_oradores_explicacoes set ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id_sub);
                $stmt->bindValue (':ativo',0);
                if($stmt->execute())
                {
                    log_operacao($id, $PDO_PROCLEGIS); 
                    ?>
                    <script>
                        mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                    </script>
                    <?php
                }
                else
                {
                    ?>
                    <script>
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php
                }            
            }            

            $num_por_pagina = 10;
            if(!$pag){$primeiro_registro = 0; $pag = 1;}
            else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
            $fil_nome = $_REQUEST['fil_nome'];
            if($fil_nome == '')
            {
                $nome_query = " 1 = 1 ";
            }
            else
            {
                $fil_nome1 = $fil_nome2 = $fil_nome3 = "%".$fil_nome."%";
                $nome_query = " (cadastro_parlamentares.nome LIKE :fil_nome1 ) ";
            }            
            $sql = "SELECT *, cadastro_sessoes_plenarias_oradores_explicacoes.id as id
                            , cadastro_sessoes_plenarias_oradores_explicacoes.url_video as url_video    
                    FROM cadastro_sessoes_plenarias_oradores_explicacoes 
                    LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = cadastro_sessoes_plenarias_oradores_explicacoes.parlamentar                     
                    LEFT JOIN cadastro_sessoes_plenarias ON cadastro_sessoes_plenarias.id = cadastro_sessoes_plenarias_oradores_explicacoes.sessao_plenaria                     
                    WHERE cadastro_sessoes_plenarias_oradores_explicacoes.ativo = :ativo and ".$nome_query." AND cadastro_sessoes_plenarias_oradores_explicacoes.sessao_plenaria = :sessao_plenaria	
                    ORDER BY cadastro_sessoes_plenarias_oradores_explicacoes.id DESC
                   ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);    
            $stmt->bindParam(':fil_nome1', 	$fil_nome1);
                
            $stmt->bindParam(':sessao_plenaria', 	$id); 
            $stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
            $stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
            $stmt->bindValue(':ativo', 	1);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if($pagina == "view")
            {
                include("../mod_includes/modal/oradores_explicacoesAdd.php");
                echo "
                <div class='titulo'> $page  &raquo; Orador das Explicações Pessoais </div>
                <div id='botoes'>
                    <div class='g_adicionar' title='Adicionar' data-toggle='modal' data-target='#oradores_explicacoesAdd'><i class='fas fa-plus'></i></div>
                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_sessoes_plenarias_oradores_explicacoes/$id/view'>
                        <input name='fil_nome' id='fil_nome' value='$fil_nome' placeholder='Nome'>
                        <input type='submit' value='Filtrar'> 
                        </form>            
                    </div>    
                </div>
                ";
                if ($rows > 0)
                {
                   

                    echo "
                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                         <tr>
                            <td class='titulo_tabela'>Parlamentar</td>
                            <td class='titulo_tabela'>Ordem pronunciamento</td>                                            
                            <td class='titulo_tabela'>URL vídeo</td>
                            <td class='titulo_tabela'>Observação</td>
                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                        </tr>
                        ";
                        $c=0;
                        while($result = $stmt->fetch())
                        {
                            $id_oradores_explicacoes = $result['id'];
                            $parlamentar = $result['parlamentar'];
                            $nome = $result['nome'];
                            $ordem = $result['ordem'];
                            $url_video = $result['url_video'];
                            $observacao = $result['observacao'];
                          
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td>".$result['nome']."</td>
                                    <td>".$ordem."</td>
                                    <td>".$url_video."</td>
                                    <td>".$observacao."</td>                                                                    
                                    <td align=center width='150'>
                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'cadastro_sessoes_plenarias_oradores_explicacoes/$id/view/excluir_oradores_explicacoes/$id_oradores_explicacoes\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                \">	<i class='far fa-trash-alt'></i>
                                            </div>
                                            <div class='g_editar' title='Editar' data-toggle='modal' data-target='#oradores_explicacoesEdit".$id_oradores_explicacoes."'><i class='fas fa-pencil-alt'></i></div> 
                                            
                                    </td>
                                </tr>";
                                include("../mod_includes/modal/oradores_explicacoesEdit.php");
                        }
                        echo "</table>";
                        
                }
                else
                {
                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                }
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
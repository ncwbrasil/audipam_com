<?php
$pagina_link = 'cadastro_bancadas';
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
        
        <!--CONTEUDO CENTRO -->
		<div class="content-wrapper">
            <div class='mensagem'></div>
            <?php                     
            $page = "Cadastro &raquo; <a href='cadastro_bancadas/view'>Bancadas</a>";
            if(isset($_GET['id'])){$id = $_GET['id'];}
            $legislatura   = $_POST['legislatura'];
            $nome   = $_POST['nome'];
            $partido   = $_POST['partido'];
            $data_criacao = reverteData($_POST['data_criacao']);    
            $data_extincao = reverteData($_POST['data_extincao']);   if($data_extincao == ""){$data_extincao = null;}
            $descricao   = $_POST['descricao'];
            
            $dados = array(
                
                'legislatura' 		    => $legislatura,
                'nome' 		    => $nome,
                'partido' 		    => $partido,
                'data_criacao' 		    => $data_criacao,
                'data_extincao' 		    => $data_extincao,
                'descricao' 		    => $descricao                
                );
        
            if($action == "adicionar")
            {                                   
                $sql = "INSERT INTO cadastro_bancadas SET ".bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                if($stmt->execute($dados))
                {		
                    $id = $PDO_PROCLEGIS->lastInsertId();

                   
                    if($erro != 1)
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
                            mensagem("X","<i class='fa fa-exclamation-circle'></i> Falha ao comunicar com o equipamento. Error: <?php echo $return['msg'];?>");
                        </script>
                        <?php 
                    

                    }
                
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
            
            if($action == 'editar')
            {
                $sql = "UPDATE cadastro_bancadas SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id;
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
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Falha ao comunicar com o equipamento. Error: <?php echo $return['msg'];?>");
                    </script>
                    <?php
                }            
            }
            
            if($action == 'excluir')
            {
                
                $sql = "UPDATE cadastro_bancadas SET ativo = :ativo WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id);
                $stmt->bindValue(':ativo',0);
                if($stmt->execute())
                {
                    // unlink($foto_antiga);
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
            $fil_legislatura = $_REQUEST['fil_legislatura'];
            if($fil_legislatura == '')
            {
                $nome_query = " 1 = 1 ";
            }
            else
            {
                $fil_legislatura1 = $fil_legislatura2 = $fil_legislatura3 = "%".$fil_legislatura."%";
                $nome_query = " (aux_parlamentares_legislaturas.numero LIKE :fil_legislatura1 ) ";
            }
            $sql = "SELECT *, aux_parlamentares_legislaturas.numero as numero,
                                YEAR(aux_parlamentares_legislaturas.data_inicio) as data_inicio, 
                                YEAR(aux_parlamentares_legislaturas.data_fim) as data_fim,
                                aux_parlamentares_partidos.nome as nome_partido,
                                cadastro_bancadas.nome as nome,
                                cadastro_bancadas.id as id,
                                cadastro_bancadas.data_criacao as data_criacao,
                                cadastro_bancadas.data_extincao as data_extincao                                                
                    FROM cadastro_bancadas 
                    LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_bancadas.legislatura
                    LEFT JOIN aux_parlamentares_partidos ON aux_parlamentares_partidos.id = cadastro_bancadas.partido                           
                    WHERE ".$nome_query." AND cadastro_bancadas.ativo = :ativo			
                    ORDER BY cadastro_bancadas.id DESC
                    LIMIT :primeiro_registro, :num_por_pagina ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);    
            $stmt->bindParam(':fil_legislatura1', 	$fil_legislatura1);
            $stmt->bindValue(':ativo', 1);
            $stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
            $stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if($pagina == "view")
            {
                echo "
                <div class='titulo'> $page  </div>
                <div id='botoes'>
                    <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"".$pagina_link."/add\");'><i class='fas fa-plus'></i></div>
                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_bancadas/view'>
                        <input name='fil_legislatura' id='fil_legislatura' value='$fil_legislatura' placeholder='Legislatura'>
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
                            <td class='titulo_tabela'>Legislatura</td>
                            <td class='titulo_tabela'>Nome da Bancada</td>
                            <td class='titulo_tabela'>Partido</td>                            
                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                        </tr>";
                        $c=0;
                        while($result = $stmt->fetch())
                        {
                            $id = $result['id'];                            
                            $legislatura = $result['numero']." (".$result['data_inicio']." - ".$result['data_fim'].")";
                            $partido = $result['nome_partido']." (".$result['sigla'].")";
                            $nome = $result['nome']; 

                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td>$legislatura</td>
                                    <td>$nome</td>
                                    <td>$partido</td>
                                    <td align=center>
                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/view/excluir/$id?pag=$pag\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                \">	<i class='far fa-trash-alt'></i>
                                            </div>
                                            <div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/edit/$id?pag=$pag\");'><i class='fas fa-pencil-alt'></i></div>
                                            
                                    </td>
                                </tr>";
                        }
                        echo "</table>";
                        $cnt = "SELECT COUNT(*) FROM cadastro_bancadas  
                                LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_bancadas.legislatura
                                LEFT JOIN aux_parlamentares_partidos ON aux_parlamentares_partidos.id = cadastro_bancadas.partido                        
                                WHERE ".$nome_query." ";
                        $stmt = $PDO_PROCLEGIS->prepare($cnt);     
                        $stmt->bindParam(':fil_legislatura1', 	$fil_legislatura1);
                              
                        $variavel = "&fil_legislatura=$fil_legislatura";            
                        include("../../core/mod_includes/php/paginacao.php");
                }
                else
                {
                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                }
            }
            if($pagina == 'add')
            {
                echo "	
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_bancadas/view/adicionar'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                           
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>                                
                                <p><label>Legislatura*:</label> <select name='legislatura' id='legislatura' class='obg'>
                                    <option value=''>Legislatura</option>";
                                        $sql = "SELECT *, YEAR(data_inicio) as data_inicio, YEAR(data_fim) as data_fim 
                                                FROM aux_parlamentares_legislaturas 
                                                WHERE ativo = :ativo
                                                ORDER BY numero";
                                        $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                        $stmt_int->bindValue(':ativo',1);                                  
                                        $stmt_int->execute();
                                        while($result_int = $stmt_int->fetch())
                                        {
                                            echo "<option value='".$result_int['id']."'>".$result_int['numero']." (".$result_int['data_inicio']." - ".$result_int['data_fim'].")</option>";
                                        }
                                    echo "
                                </select>     
                                <p><label>Nome da Bancada*:</label> <input name='nome' id='nome' placeholder='Nome da Bancada' class='obg'>
                                <p><label>Partido*:</label> <select name='partido' id='partido' class='obg'>
                                    <option value=''>Partido</option> ";
                                        $sql = "SELECT *
                                                FROM aux_parlamentares_partidos
                                                WHERE ativo = :ativo
                                                ORDER BY sigla";
                                        $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                        $stmt_int->bindValue(':ativo',1);                                  
                                        $stmt_int->execute();
                                        while($result_int = $stmt_int->fetch())
                                        {
                                            echo "<option value='".$result_int['id']."'>".$result_int['nome']." (".$result_int['sigla'].")</option>";
                                        }
                                    echo "                                                                
                                </select>    
                                <p><label>Data Criação *:</label> <input name='data_criacao' id='data_criacao' placeholder='Data Criação' onkeypress='return mascaraData(this,event);' class='obg'>
                                <p><label>Data Extinção:</label> <input name='data_extincao' id='data_extincao' placeholder='Data Extinção' onkeypress='return mascaraData(this,event);'>                                
                                <p><label>Descrição:</label> <input name='descricao' id='descricao' placeholder='Descrição'>                                                                                               
                            </div>	                                                                            
                        </div>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_bancadas/view'; value='Cancelar'/></center>
                        </center>
                    </div>
                </form>
                ";
            }
           
            if($pagina == 'edit')
            {            		
                
                $sql = "SELECT *, aux_parlamentares_legislaturas.numero as numero,
                                    YEAR(aux_parlamentares_legislaturas.data_inicio) as data_inicio, 
                                    YEAR(aux_parlamentares_legislaturas.data_fim) as data_fim,
                                    aux_parlamentares_partidos.nome as nome_partido,
                                    cadastro_bancadas.nome as nome,
                                    cadastro_bancadas.id as id,
                                    cadastro_bancadas.data_criacao as data_criacao,
                                    cadastro_bancadas.data_extincao as data_extincao                                                     
                        FROM cadastro_bancadas 
                        LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_bancadas.legislatura
                        LEFT JOIN aux_parlamentares_partidos ON aux_parlamentares_partidos.id = cadastro_bancadas.partido                        
                        WHERE cadastro_bancadas.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                   
                    $result = $stmt->fetch();                                                 
                    $legislatura    = $result['legislatura'];
                    $legislatura_n  = $result['numero']." (".$result['data_inicio']." - ".$result['data_fim'].")";
                    $partido         = $result['partido'];
                    $partido_n       = $result['nome_partido']." (".$result['sigla'].")";
                    $nome         = $result['nome'];

                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_bancadas/view/editar/$id'>
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                                                  
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                            <p><label>Legislatura*:</label> <select name='legislatura' id='legislatura' class='obg'>
                                <option value='$legislatura'>$legislatura_n</option>";
                                    $sql = "SELECT *, YEAR(data_inicio) as data_inicio, YEAR(data_fim) as data_fim 
                                            FROM aux_parlamentares_legislaturas 
                                            WHERE ativo = :ativo
                                            ORDER BY numero";
                                    $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                    $stmt_int->bindValue(':ativo',1);                                  
                                    $stmt_int->execute();
                                    while($result_int = $stmt_int->fetch())
                                    {
                                        echo "<option value='".$result_int['id']."'>".$result_int['numero']." (".$result_int['data_inicio']." - ".$result_int['data_fim'].")</option>";
                                    }
                                echo "
                            </select>  
                            <p><label>Nome da Bancada*:</label> <input name='nome' id='nome' value='".$result['nome']."' placeholder='Nome da Bancada' class='obg'>                                
                               
                            <p><label>Partido*:</label> <select name='partido' id='partido' class='obg'>
                                <option value='$partido'>$partido_n</option> ";
                                $sql = "SELECT *
                                                FROM aux_parlamentares_partidos
                                                WHERE ativo = :ativo
                                                ORDER BY sigla";
                                        $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                                        $stmt_int->bindValue(':ativo',1);       
                                        $stmt_int->execute();
                                        while($result_int = $stmt_int->fetch())
                                        {
                                            echo "<option value='".$result_int['id']."'>".$result_int['nome']." (".$result_int['sigla'].")</option>";
                                        }
                                    echo "                                                                         
                            </select>   
                            <p><label>Data Criação *:</label> <input name='data_criacao' id='data_criacao' value='".reverteData($result['data_criacao'])."' placeholder='Data Criação'  class='obg' onkeypress='return mascaraData(this,event);'>
                            <p><label>Data Extinção:</label> <input name='data_extincao' id='data_extincao' value='".reverteData($result['data_extincao'])."' placeholder='Data Extinção' onkeypress='return mascaraData(this,event);'>                                
                            <p><label>Descrição:</label> <input name='descricao' id='descricao' value='".$result['descricao']."' placeholder='Descrição'>                                
                                                                              
                            </div>                        
                                                 				
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_bancadas/view'; value='Cancelar'/></center>
                            </center>
                        </div>
                    </form>
                    ";
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
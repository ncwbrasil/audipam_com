<?php
$pagina_link = 'aux_mesa_diretora';
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
            $page = "Auxiliares &raquo; <a href='aux_mesa_diretora/view'>Mesa Diretora</a> &raquo; <a href='aux_mesa_diretora_sessoes/view'>Sessões Legislativas</a>";
            if(isset($_GET['id'])){$id = $_GET['id'];}            
            $numero = $_POST['numero'];
            $tipo = $_POST['tipo'];
            $legislatura = $_POST['legislatura'];
            $data_inicio = reverteData($_POST['data_inicio']);        
            $data_fim = reverteData($_POST['data_fim']);        
            $data_inicio_intervalo = reverteData($_POST['data_inicio_intervalo']);if($data_inicio_intervalo == ""){ $data_inicio_intervalo = null;}       
            $data_fim_intervalo = reverteData($_POST['data_fim_intervalo']);        if($data_fim_intervalo == ""){ $data_fim_intervalo = null;}     
           
            $dados = array(
                'numero' 		        => $numero,
                'tipo' 		            => $tipo,
                'legislatura' 		    => $legislatura,
                'data_inicio' 	        => $data_inicio,
                'data_fim' 		        => $data_fim,
                'data_inicio_intervalo' => $data_inicio_intervalo,
                'data_fim_intervalo' 	=> $data_fim_intervalo                   
                );
        
            if($action == "adicionar")
            {                       
            
                $sql = "INSERT INTO aux_mesa_diretora_sessoes SET ".bindFields($dados);
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
                $sql = "UPDATE aux_mesa_diretora_sessoes SET ".bindFields($dados)." WHERE id = :id ";
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
                
                $sql = "UPDATE aux_mesa_diretora_sessoes set ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id);
                $stmt->bindValue(':ativo',0);
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
            $fil_numero = $_REQUEST['fil_numero'];
            if($fil_numero == '')
            {
                $numero_query = " 1 = 1 ";
            }
            else
            {
                $fil_numero1 = "%".$fil_numero."%";
                $numero_query = " (aux_mesa_diretora_sessoes.numero LIKE :fil_numero1 ) ";
            }
              
            $sql = "SELECT *, aux_mesa_diretora_sessoes.id as id, 
                              aux_mesa_diretora_sessoes.numero as numero,
                              aux_mesa_diretora_sessoes.data_inicio as data_inicio,
                              aux_mesa_diretora_sessoes.data_fim as data_fim,
                              aux_parlamentares_legislaturas.numero as numero_legis,
                              YEAR(aux_parlamentares_legislaturas.data_inicio) as inicio_legis,
                              YEAR(aux_parlamentares_legislaturas.data_fim) as fim_legis
                              
                    FROM aux_mesa_diretora_sessoes 
                    LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = aux_mesa_diretora_sessoes.legislatura
                    WHERE aux_mesa_diretora_sessoes.ativo = :ativo and ".$numero_query." 			
                    ORDER BY aux_mesa_diretora_sessoes.data_inicio DESC
                    LIMIT :primeiro_registro, :num_por_pagina ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);    
            $stmt->bindParam(':fil_numero1', 	$fil_numero1);
                
            $stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
            $stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
            $stmt->bindValue(':ativo', 	1);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if($pagina == "view")
            {
              
                echo "
                <div class='titulo'> $page  </div>
                <div id='botoes'>
                    <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"aux_mesa_diretora_sessoes/add\");'><i class='fas fa-plus'></i></div>
                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='aux_mesa_diretora_sessoes/view'>
                        <input name='fil_numero' id='fil_numero' value='$fil_numero' placeholder='Número'>
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
                            <td class='titulo_tabela'>Número</td>
                            <td class='titulo_tabela'>Tipo</td>
                            <td class='titulo_tabela'>Legislatura</td>
                            <td class='titulo_tabela'>Data Início</td>
                            <td class='titulo_tabela'>Data Fim</td>
                            <td class='titulo_tabela'>Início Intervalo</td>
                            <td class='titulo_tabela'>Fim Intervalo</td>
                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                        </tr>";
                        $c=0;
                        while($result = $stmt->fetch())
                        {
                            $id = $result['id'];
                            
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td>".$result['numero']."</td>
                                    <td>".$result['tipo']."</td>
                                    <td>".$result['numero_legis']." (".$result['inicio_legis']." - ".$result['fim_legis'].")</td>
                                    <td>".reverteData($result['data_inicio'])."</td>
                                    <td>".reverteData($result['data_fim'])."</td>                                    
                                    <td>".reverteData($result['data_inicio_intervalo'])."</td>
                                    <td>".reverteData($result['data_fim_intervalo'])."</td>
                                    <td align=center>
                                        <div class='g_excluir' title='Excluir' onclick=\"
                                            abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'aux_mesa_diretora_sessoes/view/excluir/$id?pag=$pag\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                            \">	<i class='far fa-trash-alt'></i>
                                        </div>
                                        <div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"aux_mesa_diretora_sessoes/edit/$id?pag=$pag\");'><i class='fas fa-pencil-alt'></i></div>                                            
                                    </td>
                                </tr>";
                        }
                        echo "</table>";
                        $cnt = "SELECT COUNT(*) FROM aux_mesa_diretora_sessoes  
                                LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = aux_mesa_diretora_sessoes.legislatura                    
                                WHERE ".$numero_query." ";
                        $stmt = $PDO_PROCLEGIS->prepare($cnt);     
                        $stmt->bindParam(':fil_numero1', 	$fil_numero1);
                            
                        $variavel = "&fil_numero=$fil_numero";              
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
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='aux_mesa_diretora_sessoes/view/adicionar' autocomplete='off'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                            
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>                                                                          
                                <p><label>Número *:</label> <input name='numero' id='numero' placeholder='Número' class='obg'>
                                <p><label>Tipo *:</label> <select name='tipo' id='tipo' class='obg'>
                                    <option value=''>Tipo</option>
                                    <option value='Ordinária'>Ordinária</option>
                                    <option value='Extraordinária'>Extraordinária</option>
                                </select>
                                <p><label>Legislatura *:</label> <select name='legislatura' id='legislatura' class='obg'>
                                    <option value=''>Legislatura</option>";
                                        $sql = "SELECT *, YEAR(data_inicio) as data_inicio, YEAR(data_fim) as data_fim 
                                                FROM aux_parlamentares_legislaturas 
                                                WHERE ativo = :ativo
                                                ORDER BY numero";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);     
                                        $stmt->bindValue(':ativo',1);                                  
                                        $stmt->execute();
                                        while($result = $stmt->fetch())
                                        {
                                            echo "<option value='".$result['id']."'>".$result['numero']." (".$result['data_inicio']." - ".$result['data_fim'].")</option>";
                                        }
                                    echo "
                                </select>
                                <p><label>Data Início *:</label> <input name='data_inicio' id='data_inicio' placeholder='Data Ínicio '  onkeypress='return mascaraData(this,event);' class='obg'>
                                <p><label>Data Fim *:</label> <input name='data_fim' id='data_fim' placeholder='Data Fim' class='obg' onkeypress='return mascaraData(this,event);'>
                                <p><label>Início Intervalo:</label> <input name='data_inicio_intervalo' id='data_inicio_intervalo' placeholder='Início Intervalo' onkeypress='return mascaraData(this,event);'>
                                <p><label>Fim Intervalo:</label> <input name='data_fim_intervalo' id='data_fim_intervalo' placeholder='Fim Intervalo'  onkeypress='return mascaraData(this,event);'>
                                
                            </div>	                                                                        
                        </div>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_mesa_diretora_sessoes/view'; value='Cancelar'/></center>
                        </center>
                    </div>
                </form>
                ";
            }
            
            if($pagina == 'edit')
            {            		
                $sql = "SELECT *, aux_mesa_diretora_sessoes.id as id, 
                                    aux_mesa_diretora_sessoes.numero as numero,
                                    aux_mesa_diretora_sessoes.data_inicio as data_inicio,
                                    aux_mesa_diretora_sessoes.data_fim as data_fim,
                                    aux_parlamentares_legislaturas.numero as numero_legis,
                                    YEAR(aux_parlamentares_legislaturas.data_inicio) as inicio_legis,
                                    YEAR(aux_parlamentares_legislaturas.data_fim) as fim_legis
                                    
                        FROM aux_mesa_diretora_sessoes 
                        LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = aux_mesa_diretora_sessoes.legislatura                
                        WHERE aux_mesa_diretora_sessoes.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();
                    
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='aux_mesa_diretora_sessoes/view/editar/$id' autocomplete='off'>
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                                                 
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>                                    
                                <p><label>Número *:</label> <input name='numero' id='numero' value='".$result['numero']."' placeholder='Número'  class='obg'>
                                <p><label>Tipo *:</label> <select name='tipo' id='tipo' class='obg'>
                                    <option value='".$result['tipo']."'>".$result['tipo']."</option>
                                    <option value='Ordinária'>Ordinária</option>
                                    <option value='Extraordinária'>Extraordinária</option>
                                </select>
                                <p><label>Legislatura *:</label> <select name='legislatura' id='legislatura' class='obg'>
                                    <option value='".$result['legislatura']."'>".$result['numero']." (".$result['inicio_legis']." - ".$result['fim_legis'].")</option>";
                                        $sql = "SELECT *, YEAR(data_inicio) as data_inicio, YEAR(data_fim) as data_fim 
                                                FROM aux_parlamentares_legislaturas 
                                                ORDER BY numero";
                                        $stmt_legis = $PDO_PROCLEGIS->prepare($sql);
                                        $stmt_legis->execute();
                                        while($result_legis = $stmt_legis->fetch())
                                        {
                                            echo "<option value='".$result_legis['id']."'>".$result_legis['numero']." (".$result_legis['data_inicio']." - ".$result_legis['data_fim'].")</option>";
                                        }
                                    echo "
                                </select>
                                <p><label>Data Início *:</label> <input name='data_inicio' value='".reverteData($result['data_inicio'])."' id='data_inicio' placeholder='Data Ínicio '  class='obg'  onkeypress='return mascaraData(this,event);'>
                                <p><label>Data Fim *:</label> <input name='data_fim' value='".reverteData($result['data_fim'])."' id='data_fim' placeholder='Data Fim '  class='obg'  onkeypress='return mascaraData(this,event);'>                                                       
                                <p><label>Início Intervalo:</label> <input name='data_inicio_intervalo' value='".reverteData($result['data_inicio_intervalo'])."' id='data_inicio_intervalo' placeholder='Início Intervalo'  onkeypress='return mascaraData(this,event);'>
                                <p><label>Fim Intervalo:</label> <input name='data_inicio_intervalo' value='".reverteData($result['data_fim_intervalo'])."' id='data_fim_intervalo' placeholder='Fim Intervalo'  onkeypress='return mascaraData(this,event);'>
                            </div>                                                                        				
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_mesa_diretora_sessoes/view'; value='Cancelar'/></center>
                            </center>
                        </div>
                    </form>
                    ";
                }
            }	
            ?>
    	</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
</body>
</html>
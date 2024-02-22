<?php
$pagina_link = 'aux_bancadas';
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
            $page = "Auxiliares &raquo; <a href='aux_bancadas/view'>Bancadas</a> &raquo; <a href='aux_bancadas_frentes/view'>Frentes Parlamentares</a>";
            if(isset($_GET['id'])){$id = $_GET['id'];}
            $nome = $_POST['nome'];
            $descricao = $_POST['descricao'];    
            $data_criacao = reverteData($_POST['data_criacao']);    
            $data_dissolucao = reverteData($_POST['data_dissolucao']);   if($data_dissolucao == ""){$data_dissolucao = null;}
            
            $dados = array(
                
                'nome' 		        => $nome,
                'descricao' 		=> $descricao,
                'data_criacao' 		=> $data_criacao,
                'data_dissolucao' 	=> $data_dissolucao
                );
        
            if($action == "adicionar")
            {                       
            
                $sql = "INSERT INTO aux_bancadas_frentes SET ".bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                if($stmt->execute($dados))
                {		
                    $ultimo_id = $PDO_PROCLEGIS->lastInsertId();                               				
                    $erro=0;
                    $sql = "SELECT * FROM cadastro_parlamentares ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if($rows > 0 )
                    {
                        while($result = $stmt->fetch())
                        {
                            $id_parlamentar = $result['id'];
                            $parlamentar 	= $_POST['item_check_'.$id_parlamentar];   
                            if($parlamentar != '')                                                   
                            {
                                $sql = "INSERT INTO aux_bancadas_frentes_parlamentares SET 
                                frente 		= :ultimo_id,
                                parlamentar 	= :id_parlamentar
                                ";
                                $stmt_insert = $PDO_PROCLEGIS->prepare($sql);
                                $stmt_insert->bindParam(':ultimo_id', 	$ultimo_id);
                                $stmt_insert->bindParam(':id_parlamentar', 		$id_parlamentar);                                
                                if($stmt_insert->execute())
                                {
                                }
                                else
                                {
                                    $erro=1;
                                }
                            }
                        }
                    }

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
                $sql = "UPDATE aux_bancadas_frentes SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id;
                if($stmt->execute($dados))
                {        
                    $ultimo_id = $id;   
                    $sql = "SELECT * FROM cadastro_parlamentares ";
                    $stmt_itens = $PDO_PROCLEGIS->prepare($sql);
                    $stmt_itens->execute();
                    $rows_itens = $stmt_itens->rowCount();
                    if($rows_itens > 0 )
                    {
                        while($result = $stmt_itens->fetch())
                        {
                            
                            $id_parlamentar = $result['id'];
                            $parlamentar = $_POST['item_check_'.$id_parlamentar];                                                        
                            $sql = "SELECT * FROM aux_bancadas_frentes_parlamentares 
                                    WHERE frente = :ultimo_id AND parlamentar = :id_parlamentar ";
                            $stmt_compara = $PDO_PROCLEGIS->prepare($sql);
                            $stmt_compara->bindParam(':ultimo_id', 	$ultimo_id);                            
                            $stmt_compara->bindParam(':id_parlamentar', 	$id_parlamentar);
                            $stmt_compara->execute();
                            $rows_compara = $stmt_compara->rowCount();                            
                            if($rows_compara == 0 && $parlamentar != '')
                            {
                                $sql = "INSERT INTO aux_bancadas_frentes_parlamentares SET 
                                        frente 		= :ultimo_id,                                        
                                        parlamentar 	= :parlamentar
                                        ";
                                $stmt_insert = $PDO_PROCLEGIS->prepare($sql);
                                $stmt_insert->bindParam(':ultimo_id', 	$ultimo_id);                                
                                $stmt_insert->bindParam(':parlamentar', 	$parlamentar);
                                if($stmt_insert->execute())
                                {
                                    //echo "Inserido";
                                }
                                else
                                {
                                    $erro=1;
                                }
                            }
                            elseif($rows_compara > 0 && $parlamentar == '')
                            {                                
                                $id_frente = $stmt_compara->fetch(PDO::FETCH_OBJ)->id;
                                $sql = "DELETE FROM aux_bancadas_frentes_parlamentares WHERE id = :id ";
                                $stmt_delete = $PDO_PROCLEGIS->prepare($sql);
                                $stmt_delete->bindParam(':id', 	$id_frente);
                                if($stmt_delete->execute())
                                {
                                    //echo "Deletado";
                                }
                                else
                                {
                                    $erro=1;
                                }
                            }
                            elseif($rows_compara > 0 && $parlamentar != '')
                            {
                                $id_frente = $stmt_compara->fetch(PDO::FETCH_OBJ)->id;
                                $sql = "UPDATE aux_bancadas_frentes_parlamentares SET 
                                        frente 		= :ultimo_id,                                        
                                        parlamentar 	= :parlamentar
                                        WHERE id = :id
                                        ";
                                $stmt_insert = $PDO_PROCLEGIS->prepare($sql);
                                $stmt_insert->bindParam(':ultimo_id', 	$ultimo_id);                                
                                $stmt_insert->bindParam(':parlamentar', 	$parlamentar);                               
                                $stmt_insert->bindParam(':id', 	$id_frente);
                                if($stmt_insert->execute())
                                {
                                    //echo "Atualizado";
                                }
                                else
                                {
                                    $erro=1;
                                }
                            }
                        }
                    } 
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
                
                $sql = "UPDATE aux_bancadas_frentes set ativo = :ativo WHERE id = :id";
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
            $fil_nome = $_REQUEST['fil_nome'];
            if($fil_nome == '')
            {
                $nome_query = " 1 = 1 ";
            }
            else
            {
                $fil_nome1 = "%".$fil_nome."%";
                $nome_query = " (nome LIKE :fil_nome1 ) ";
            }
              
            $sql = "SELECT * FROM aux_bancadas_frentes 
                    WHERE aux_bancadas_frentes.ativo = :ativo and ".$nome_query." 			
                    ORDER BY id DESC
                    LIMIT :primeiro_registro, :num_por_pagina ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);    
            $stmt->bindParam(':fil_nome1', 	$fil_nome1);
                
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
                    <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"aux_bancadas_frentes/add\");'><i class='fas fa-plus'></i></div>
                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='aux_bancadas_frentes/view'>
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
                            <td class='titulo_tabela'>Nome</td>                            
                            <td class='titulo_tabela'>Descrição</td>                            
                            <td class='titulo_tabela'>Data Criação</td>   
                            <td class='titulo_tabela'>Parlamentares</td>                                                                                 
                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                        </tr>";
                        $c=0;
                        while($result = $stmt->fetch())
                        {
                            $id = $result['id'];
                            $sql = "SELECT *, cadastro_parlamentares.nome as nome FROM aux_bancadas_frentes_parlamentares
                                    LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_bancadas_frentes_parlamentares.parlamentar
                                    WHERE frente = :frente 		
                                    ";
                            $stmt_parlamentares = $PDO_PROCLEGIS->prepare($sql);    
                            $stmt_parlamentares->bindParam(':frente', 	$id);     
                            $stmt_parlamentares->execute();
                            $rows_parlamentares = $stmt_parlamentares->rowCount();
                            $parlamentares = "";
                            if($rows_parlamentares > 0)
                            {
                                while($result_parlamentares = $stmt_parlamentares->fetch())
                                {
                                    $parlamentares .= "<li>".$result_parlamentares['nome']."</li>";
                                }
                            }
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td>".$result['nome']."</td>                              
                                    <td>".$result['descricao']."</td>                              
                                    <td>".reverteData($result['data_criacao'])."</td>                              
                                    <td>".$parlamentares."</td>                              
                                    <td align=center>
                                        <div class='g_excluir' title='Excluir' onclick=\"
                                            abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'aux_bancadas_frentes/view/excluir/$id?pag=$pag\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                            \">	<i class='far fa-trash-alt'></i>
                                        </div>
                                        <div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"aux_bancadas_frentes/edit/$id?pag=$pag\");'><i class='fas fa-pencil-alt'></i></div>                                            
                                    </td>
                                </tr>";
                        }
                        echo "</table>";
                        $cnt = "SELECT COUNT(*) FROM aux_bancadas_frentes  WHERE ".$nome_query."";
                        $stmt = $PDO_PROCLEGIS->prepare($cnt);     
                        $stmt->bindParam(':fil_nome1', 	$fil_nome1);
                            
                        $variavel = "&fil_nome=$fil_nome";              
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
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='aux_bancadas_frentes/view/adicionar' autocomplete='off'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                            
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>                                                                          
                                <p><label>Nome *:</label> <input name='nome' id='nome' placeholder='Nome' class='obg'>                                
                                <p><label>Descrição:</label> <input name='descricao' id='descricao' placeholder='Descrição'> 
                                <p><label>Data Criação *:</label> <input name='data_criacao' id='data_criacao' placeholder='Data Criação'  class='obg' onkeypress='return mascaraData(this,event);'>
                                <p><label>Data Dissolução:</label> <input name='data_dissolucao' id='data_dissolucao' placeholder='Data Dissolução' onkeypress='return mascaraData(this,event);'>                                
                                <div style='width:100%; display:table;'>
                                    <p><label>Selecione os parlamentares:</label>
                                    <div style='width:83%; display:table; float:left;'>";
                                                        
                                                                                            
                                    $sql = "SELECT * FROM cadastro_parlamentares WHERE ativo = :ativo
                                            ";
                                    $stmt_submodulo = $PDO_PROCLEGIS->prepare($sql);     
                                    $stmt_submodulo->bindValue(':ativo', 1); 
                                    $stmt_submodulo->execute();
                                    $rows_submodulo = $stmt_submodulo->rowCount();
                                    if($rows_submodulo > 0)
                                    {
                                        
                                        while($result_submodulo = $stmt_submodulo->fetch())
                                        {
                                            echo " 
                                            <div class='left' style='width:47%; padding:4px 0;'>
                                                <input type='checkbox' class='marcar' name='item_check_".$result_submodulo['id']."' id='item_check_".$result_submodulo['id']."' value='".$result_submodulo['id']."' > ".$result_submodulo['nome']."                                                    
                                            </div>
                                            ";
                                        }
                                    }
                                    else	
                                    {
                                        echo "Não há parlamentares cadastrados.";
                                    }                                      
                                    echo "
                                    </div>
                                </div>
                            </div>	                                                                        
                        </div>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_bancadas_frentes/view'; value='Cancelar'/></center>
                        </center>
                    </div>
                </form>
                ";
            }
            
            if($pagina == 'edit')
            {            		
                $sql = "SELECT * FROM aux_bancadas_frentes 
                        WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();
                    
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='aux_bancadas_frentes/view/editar/$id' autocomplete='off'>
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                                                 
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Nome *:</label> <input name='nome' id='nome' value='".$result['nome']."' placeholder='Nome'  class='obg'>                                
                                <p><label>Descrição:</label> <input name='descricao' id='descricao' value='".$result['descricao']."' placeholder='Descrição'>                                
                                <p><label>Data Criação *:</label> <input name='data_criacao' id='data_criacao' value='".reverteData($result['data_criacao'])."' placeholder='Data Criação'  class='obg' onkeypress='return mascaraData(this,event);'>
                                <p><label>Data Dissolução:</label> <input name='data_dissolucao' id='data_dissolucao' value='".reverteData($result['data_dissolucao'])."' placeholder='Data Dissolução' onkeypress='return mascaraData(this,event);'>                                
                                <div style='width:100%; display:table;'>
                                    <p><label>Selecione os parlamentares:</label>
                                    <div style='width:83%; display:table; float:left;'>";
                                        $sql = "SELECT * FROM cadastro_parlamentares WHERE ativo = :ativo
                                                 ";
                                        $stmt_submodulo = $PDO_PROCLEGIS->prepare($sql);
                                        $stmt_submodulo->bindValue(':ativo', 1);  
                                        $stmt_submodulo->execute();
                                        $rows_submodulo = $stmt_submodulo->rowCount();
                                        if($rows_submodulo > 0)
                                        {
                                            while($result_submodulo = $stmt_submodulo->fetch())
                                            {
                                                $sql = "SELECT * FROM aux_bancadas_frentes_parlamentares 
                                                        WHERE frente = :id AND parlamentar = :parlamentar AND ativo = :ativo";
                                                $stmt_compara = $PDO_PROCLEGIS->prepare($sql);
                                                $stmt_compara->bindParam(':id', $id);
                                                $stmt_compara->bindValue(':ativo', 1); 
                                                $stmt_compara->bindParam(':parlamentar', $result_submodulo['id']);
                                                $stmt_compara->execute();
                                                $rows_compara = $stmt_compara->rowCount();                                                        
                                                if($rows_compara > 0)
                                                {
                                                    
                                                    $result = $stmt_compara->fetch();                                                            
                                                    echo "
                                                    <div class='left' style='width:47%; padding:4px 0;'>
                                                        <input class='marcar'  checked type='checkbox' name='item_check_".$result_submodulo['id']."' id='item_check_".$result_submodulo['id']."' value='".$result_submodulo['id']."' > ".$result_submodulo['nome']."                                                                
                                                    </div>
                                                    ";
                                                }
                                                else
                                                {
                                                    echo "
                                                    <div class='left' style='width:47%; padding:4px 0;'>
                                                        <input class='marcar' type='checkbox' name='item_check_".$result_submodulo['id']."' id='item_check_".$result_submodulo['id']."' value='".$result_submodulo['id']."' > ".$result_submodulo['nome']."                                                        
                                                    </div>
                                                    ";
                                                }
                                            }
                                        }
                                        else
                                        {
                                            echo "Não há parlamentares.";
                                        }                                            
                                        echo "                               
                                    </div>
                                </div>
                            </div>                                                                        				
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_bancadas_frentes/view'; value='Cancelar'/></center>
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
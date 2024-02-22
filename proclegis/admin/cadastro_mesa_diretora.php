<?php
$pagina_link = 'cadastro_mesa_diretora';
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
            $page = "Cadastro &raquo; <a href='cadastro_mesa_diretora/view'>Mesa Diretora</a>";
            if(isset($_GET['id'])){$id = $_GET['id'];}
            $legislatura   = $_POST['legislatura'];
            $sessao   = $_POST['sessao'];
            
            
            $dados = array(
                
                'legislatura' 		    => $legislatura,
                'sessao' 		    => $sessao
                );
        
            if($action == "adicionar")
            {                                   
                $sql = "INSERT INTO cadastro_mesa_diretora SET ".bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                if($stmt->execute($dados))
                {		
                    $id = $PDO_PROCLEGIS->lastInsertId();

                    //COMPOSICAO - CAMPOS DINÂMICOS		
                   
                    if(!empty($_POST['composicao']) && is_array($_POST['composicao']))
                    {
                        //LIMPA ARRAY
                        foreach($_POST['composicao'] as $item => $valor) 
                        {
                            $composicao_filtrado[$item] = array_filter($valor);
                        }
                        //
                        foreach($composicao_filtrado as $item => $valor) 
                        {		
                            if(!empty($valor))
                            {				
                                //INVERTE DATA
                                // if(isset($valor['for_data_vcto']))
                                // {
                                //     $data_nova = implode("-",array_reverse(explode("/",$valor['for_data_vcto'])));
                                //     unset($valor['for_data_vcto']);
                                //     $valor['for_data_vcto'] = $data_nova;
                                // }
                                //
                               
                                $valor['mesa_diretora'] = $id;                                
                                $sql = "INSERT INTO cadastro_mesa_diretora_composicao SET ".bindFields($valor);
                                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                                if($stmt->execute($valor))
                                {
                                    //INSERE
                                }
                                else{ $erro=1; $err = $stmt->errorInfo();}
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
                $sql = "UPDATE cadastro_mesa_diretora SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id;
                if($stmt->execute($dados))
                {
                    
                    // FORMACAO - EXCLUI OS REMOVIDOS
                    if(!empty($_POST['composicao']) && is_array($_POST['composicao']))
                    {
                        //LIMPA ARRAY
                        foreach($_POST['composicao'] as $item => $valor) 
                        {
                            $composicao_filtrado[$item] = array_filter($valor);
                        }
                        //
                        
                        $a_excluir = array();
                        foreach($composicao_filtrado as $item) 
                        {
                            if(isset($item['id']))
                            {
                                $a_excluir[] = $item['id'];
                            }
                        }
                        if(!empty($a_excluir))
                        {
                            $sql = "DELETE FROM cadastro_mesa_diretora_composicao WHERE mesa_diretora = :id AND id NOT IN (".implode(",",$a_excluir).") ";
                            
                            $stmt = $PDO_PROCLEGIS->prepare($sql); 
                            $stmt->bindParam(':id', $id);
                            if($stmt->execute())
                            {
                                //echo "Excluido <br>";
                            }
                            else{ $erro=1; $err = $stmt->errorInfo();}
                        }
                        else
                        {
                            $sql = "DELETE FROM cadastro_mesa_diretora_composicao WHERE mesa_diretora = :id ";
                            $stmt = $PDO_PROCLEGIS->prepare($sql); 
                            $stmt->bindParam(':id', $id);
                            if($stmt->execute())
                            {
                                //echo "Excluido todos <br>";
                            }
                            else{ $erro=1; $err = $stmt->errorInfo();}
                        }
                    }
                    else
                    {
                        $sql = "DELETE FROM cadastro_mesa_diretora_composicao WHERE mesa_diretora = :id ";
                        $stmt = $PDO_PROCLEGIS->prepare($sql); 
                        $stmt->bindParam(':id', $id);
                        if($stmt->execute())
                        {
                            //echo "Excluido todos <br>";
                        }
                        else{ $erro=1; $err = $stmt->errorInfo();}
                    }
                    
                    // FORMACAO - ATUALIZA OU INSERE NOVOS
                    if(!empty($_POST['composicao']) && is_array($_POST['composicao']))
                    {
                        //LIMPA ARRAY
                        foreach($_POST['composicao'] as $item => $valor) 
                        {
                            $composicao_filtrado[$item] = array_filter($valor);
                        }
                        //
                        foreach(array_filter($composicao_filtrado) as $item => $valor) 
                        {
                            if(isset($valor['id']))
                            {
                                //INVERTE DATA
                                // if(isset($valor['for_data_vcto']))
                                // {
                                //     $data_nova = implode("-",array_reverse(explode("/",$valor['for_data_vcto'])));
                                //     unset($valor['for_data_vcto']);
                                //     $valor['for_data_vcto'] = $data_nova;
                                // }
                                //
                                
                                $valor2 = $valor;
                                unset($valor2['id']);		
                                                                                                    
                                $sql = "UPDATE cadastro_mesa_diretora_composicao SET ".bindFields($valor2)." WHERE id = :id";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                                if($stmt->execute($valor))
                                {
                                    //echo "Atualizado <br>";
                                }
                                else{ $erro=1; $err = $stmt->errorInfo();}
                            }
                            else
                            {
                                //INVERTE DATA
                                // if(isset($valor['for_data_vcto']))
                                // {
                                //     $data_nova = implode("-",array_reverse(explode("/",$valor['for_data_vcto'])));
                                //     unset($valor['for_data_vcto']);
                                //     $valor['for_data_vcto'] = $data_nova;
                                // }
                                //

                                $valor['mesa_diretora'] = $id;
                                $sql = "INSERT INTO cadastro_mesa_diretora_composicao SET ".bindFields($valor);
                                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                                if($stmt->execute($valor))
                                {
                                    //echo "Inserido <br>";
                                }
                                else{ $erro=1; $err = $stmt->errorInfo();}
                            }
                        }
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
            
            if($action == 'excluir')
            {
                
                $sql = "UPDATE cadastro_mesa_diretora SET ativo = :ativo WHERE id = :id";
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
            $sql = "SELECT *, cadastro_mesa_diretora.id as id,
                            aux_parlamentares_legislaturas.numero as numero,
                            YEAR(aux_parlamentares_legislaturas.data_inicio) as data_inicio, 
                            YEAR(aux_parlamentares_legislaturas.data_fim) as data_fim,
                            aux_mesa_diretora_sessoes.numero as numero_sessao,
                            YEAR(aux_mesa_diretora_sessoes.data_inicio) as data_inicio_sessao, 
                            YEAR(aux_mesa_diretora_sessoes.data_fim) as data_fim_sessao                      
                    FROM cadastro_mesa_diretora 
                    
                    LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_mesa_diretora.legislatura
                    LEFT JOIN aux_mesa_diretora_sessoes ON aux_mesa_diretora_sessoes.id = cadastro_mesa_diretora.sessao                        
                    WHERE ".$nome_query." 	AND cadastro_mesa_diretora.ativo = :ativo		
                    ORDER BY cadastro_mesa_diretora.id DESC
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
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_mesa_diretora/view'>
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
                            <td class='titulo_tabela'>Composição da Mesa</td>
                            <td class='titulo_tabela'>Legislatura</td>
                            <td class='titulo_tabela'>Sessão</td>                            
                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                        </tr>";
                        $c=0;
                        while($result = $stmt->fetch())
                        {
                            $id = $result['id'];                            
                            $legislatura = $result['numero']." (".$result['data_inicio']." - ".$result['data_fim'].")";
                            $sessao = $result['numero_sessao']." (".$result['data_inicio_sessao']." - ".$result['data_fim_sessao'].")";
                            $comp="";
                            $sql = "SELECT * FROM cadastro_mesa_diretora_composicao
                                    LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = cadastro_mesa_diretora_composicao.parlamentar
                                    LEFT JOIN aux_mesa_diretora_cargos ON aux_mesa_diretora_cargos.id = cadastro_mesa_diretora_composicao.cargo                      
                                    WHERE mesa_diretora = :mesa_diretora 
                                    ORDER BY aux_mesa_diretora_cargos.id ASC ";
                            $stmt_comp = $PDO_PROCLEGIS->prepare($sql);                                
                            $stmt_comp->bindParam(':mesa_diretora', 	$id);        
                            $stmt_comp->execute();
                            $rows_comp = $stmt_comp->rowCount();
                            if($rows_comp > 0)
                            {
                                while($result_comp = $stmt_comp->fetch())
                                {
                                    $comp .= " <div class='perfil' style='background:url(".$result_comp["foto"].") center center; float:left; background-size: cover; border-radius:50px; width:20px; height:20px;  margin:0 auto; margin-right:5px;' border='0'></div>
                                    ".$result_comp['nome']." - ".$result_comp['descricao']."<p>";
                                }
                            } 
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td>$comp</td>
                                    <td>$legislatura</td>
                                    <td>$sessao</td>
                                    <td align=center>
                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/view/excluir/$id?pag=$pag\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                \">	<i class='far fa-trash-alt'></i>
                                            </div>
                                            <div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/edit/$id?pag=$pag\");'><i class='fas fa-pencil-alt'></i></div>
                                            <div class='g_exibir' title='Exibir' onclick='verificaPermissao(".$permissoes["view"].",\"".$pagina_link."/exib/$id?pag=$pag\");'><i class='fas fa-search'></i></div>
                                            
                                    </td>
                                </tr>";
                        }
                        echo "</table>";
                        $cnt = "SELECT COUNT(*) FROM cadastro_mesa_diretora  
                                LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_mesa_diretora.legislatura
                                LEFT JOIN aux_mesa_diretora_sessoes ON aux_mesa_diretora_sessoes.id = cadastro_mesa_diretora.sessao                        
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
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_mesa_diretora/view/adicionar'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            <li><a data-toggle='tab' 	href='#composicao'>Composição</a></li>
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
                                <p><label>Sessão Legislativa*:</label> <select name='sessao' id='sessao' class='obg'>
                                    <option value=''>Sessão Legislativa</option>                                                                       
                                </select>                                                                            
                            </div>	                            
                            <div id='composicao' class='tab-pane fade in' style='text-align:center'>
                                <div id='p_scents_composicao'> 
                                    <div class='bloco_composicao'>
                                        <p><label>Parlamentar*:</label> <select name='composicao[1][parlamentar]' class='obg'>
                                                <option value=''>Parlamentar</option>
                                                "; 
                                                $sql_ser = " SELECT * FROM cadastro_parlamentares ORDER BY nome ";
                                                $stmt_ser = $PDO_PROCLEGIS->prepare($sql_ser);
                                                $stmt_ser->execute();
                                                while($result_ser = $stmt_ser->fetch())
                                                {
                                                    echo "<option value='".$result_ser['id']."'>".$result_ser['nome']."</option>";
                                                }
                                                echo "                               
                                            </select>
                                        <p><label>Cargo*:</label> <select name='composicao[1][cargo]' class='obg'>
                                            <option value=''>Cargo</option>
                                            "; 
                                            $sql_ser = " SELECT * FROM aux_mesa_diretora_cargos ORDER BY descricao ";
                                            $stmt_ser = $PDO_PROCLEGIS->prepare($sql_ser);
                                            $stmt_ser->execute();
                                            while($result_ser = $stmt_ser->fetch())
                                            {
                                                echo "<option value='".$result_ser['id']."'>".$result_ser['descricao']."</option>";
                                            }
                                            echo "                               
                                        </select>
                                        <p><i class='fas fa-plus botao_dinamico_add' id='add_composicao' title='Adicionar'></i></p>
                                        <hr style='width:100%; border:none; height:1px; background:#DDD;'>
                                    </div>                                                                   
                                </div>                            
                            </div>                    
                        </div>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_mesa_diretora/view'; value='Cancelar'/></center>
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
                                  aux_mesa_diretora_sessoes.numero as numero_sessao,
                                  YEAR(aux_mesa_diretora_sessoes.data_inicio) as data_inicio_sessao, 
                                  YEAR(aux_mesa_diretora_sessoes.data_fim) as data_fim_sessao                      
                        FROM cadastro_mesa_diretora 
                        LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_mesa_diretora.legislatura
                        LEFT JOIN aux_mesa_diretora_sessoes ON aux_mesa_diretora_sessoes.id = cadastro_mesa_diretora.sessao                        
                        WHERE cadastro_mesa_diretora.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                   
                    $result = $stmt->fetch();                                                 
                    $legislatura    = $result['legislatura'];
                    $legislatura_n  = $result['numero']." (".$result['data_inicio']." - ".$result['data_fim'].")";
                    $sessao         = $result['sessao'];
                    $sessao_n       = $result['numero_sessao']." (".$result['data_inicio_sessao']." - ".$result['data_fim_sessao'].")";
                                
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_mesa_diretora/view/editar/$id'>
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            <li><a data-toggle='tab' 	href='#composicao'>Composição</a></li>                        
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
                            <p><label>Sessão Legislativa*:</label> <select name='sessao' id='sessao' class='obg'>
                                <option value='$sessao'>$sessao_n</option>                                                                       
                            </select>                                                    
                            </div>                        
                            <div id='composicao' class='tab-pane fade in'>
                                <div id='p_scents_composicao'>
                                ";
                                $sql = "SELECT *,cadastro_mesa_diretora_composicao.id as id FROM cadastro_mesa_diretora_composicao 
                                        LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = cadastro_mesa_diretora_composicao.parlamentar
                                        LEFT JOIN aux_mesa_diretora_cargos ON aux_mesa_diretora_cargos.id = cadastro_mesa_diretora_composicao.cargo
                                        WHERE mesa_diretora = :mesa_diretora AND cadastro_mesa_diretora_composicao.ativo = :ativo" ;
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':mesa_diretora', $id);
                                $stmt->bindValue(':ativo',1);   
                                $stmt->execute();
                                $rows = $stmt->rowCount();
                                if($rows > 0)
                                {
                                    $x=0;
                                    while($result = $stmt->fetch())
                                    {
                                        $x++;
                                        echo "
                                        <div class='bloco_composicao'>
                                            <input type='hidden' name='composicao[$x][id]' id='id' value='".$result['id']."'>
                                            "; if($x > 1){ echo "<br><br><hr><p>";}else{ echo "<br>";} 
                                            echo "
                                            <p><label>Parlamentar*:</label> <select name='composicao[$x][parlamentar]' class='obg'>
                                                <option value='".$result['parlamentar']."'>".$result['nome']."</option>
                                                "; 
                                                $sql_ser = " SELECT * FROM cadastro_parlamentares WHERE ativo = :ativo ORDER BY nome ";
                                                $stmt_ser = $PDO_PROCLEGIS->prepare($sql_ser);
                                                $stmt_ser->bindValue(':ativo',1);   
                                                $stmt_ser->execute();
                                                while($result_ser = $stmt_ser->fetch())
                                                {
                                                    echo "<option value='".$result_ser['id']."'>".$result_ser['nome']."</option>";
                                                }
                                                echo "                               
                                            </select>
                                            <p><label>Cargo*:</label> <select name='composicao[$x][cargo]' class='obg'>
                                                <option value='".$result['cargo']."'>".$result['descricao']."</option>
                                                "; 
                                                $sql_ser = " SELECT * FROM aux_mesa_diretora_cargos WHERE ativo = :ativo ORDER BY descricao ";
                                                $stmt_ser = $PDO_PROCLEGIS->prepare($sql_ser);
                                                $stmt_ser->bindValue(':ativo',1);   
                                                $stmt_ser->execute();
                                                while($result_ser = $stmt_ser->fetch())
                                                {
                                                    echo "<option value='".$result_ser['id']."'>".$result_ser['descricao']."</option>";
                                                }
                                                echo "                               
                                            </select>
                                            <p><i class='fas fa-plus botao_dinamico_add' id='add_composicao' title='Adicionar'></i> <i class='far fa-trash-alt botao_dinamico_rmv' id='rem_composicao' title='Remover' ></i>
											<hr style='width:100%; border:none; height:1px; background:#DDD;'>
                                        </div>
                                        ";
                                    }
                                }
                                else
                                {
                                    echo "
                                    <div class='bloco_composicao'>
                                        <input type='hidden' name='composicao[1][id]' id='id'>
                                        <p><label>Parlamentar*:</label> <select name='composicao[1][parlamentar]' class='obg'>
                                                <option value=''>Parlamentar</option>
                                                "; 
                                                $sql_ser = " SELECT * FROM cadastro_parlamentares WHERE ativo = :ativo ORDER BY nome ";
                                                $stmt_ser = $PDO_PROCLEGIS->prepare($sql_ser);
                                                $stmt_ser->bindValue(':ativo',1);   
                                                $stmt_ser->execute();
                                                while($result_ser = $stmt_ser->fetch())
                                                {
                                                    echo "<option value='".$result_ser['id']."'>".$result_ser['nome']."</option>";
                                                }
                                                echo "                               
                                            </select>
                                        <p><label>Cargo*:</label> <select name='composicao[1][cargo]' class='obg'>
                                            <option value=''>Cargo</option>
                                            "; 
                                            $sql_ser = " SELECT * FROM aux_mesa_diretora_cargos WHERE ativo = :ativo ORDER BY descricao ";
                                            $stmt_ser = $PDO_PROCLEGIS->prepare($sql_ser);
                                            $stmt_ser->bindValue(':ativo',1);   
                                            $stmt_ser->execute();
                                            while($result_ser = $stmt_ser->fetch())
                                            {
                                                echo "<option value='".$result_ser['id']."'>".$result_ser['descricao']."</option>";
                                            }
                                            echo "                               
                                        </select>
                                        <p><i class='fas fa-plus botao_dinamico_add' id='add_composicao' title='Adicionar'></i></p>
                                    </div>
                                    ";
                                }
                                echo "
                                </div>
                            </div>                        				
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_mesa_diretora/view'; value='Cancelar'/></center>
                            </center>
                        </div>
                    </form>
                    ";
                }
            }	
            if($pagina == 'exib')
            {            		                             
                $sql = "SELECT *, aux_parlamentares_legislaturas.numero as numero,
                                  YEAR(aux_parlamentares_legislaturas.data_inicio) as data_inicio, 
                                  YEAR(aux_parlamentares_legislaturas.data_fim) as data_fim,
                                  aux_mesa_diretora_sessoes.numero as numero_sessao,
                                  YEAR(aux_mesa_diretora_sessoes.data_inicio) as data_inicio_sessao, 
                                  YEAR(aux_mesa_diretora_sessoes.data_fim) as data_fim_sessao                      
                        FROM cadastro_mesa_diretora 
                        LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_mesa_diretora.legislatura
                        LEFT JOIN aux_mesa_diretora_sessoes ON aux_mesa_diretora_sessoes.id = cadastro_mesa_diretora.sessao                        
                        WHERE cadastro_mesa_diretora.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();    
                                                                 
                    $legislatura = $result['numero']." (".$result['data_inicio']." - ".$result['data_fim'].")";
                    $sessao = $result['numero_sessao']." (".$result['data_inicio_sessao']." - ".$result['data_fim_sessao'].")";
                    $comp="";
                   
                    $sql = "SELECT * FROM cadastro_mesa_diretora_composicao
                            LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = cadastro_mesa_diretora_composicao.parlamentar
                            LEFT JOIN aux_mesa_diretora_cargos ON aux_mesa_diretora_cargos.id = cadastro_mesa_diretora_composicao.cargo                      
                            WHERE mesa_diretora = :mesa_diretora 
                            ORDER BY aux_mesa_diretora_cargos.id ASC ";
                    $stmt_comp = $PDO_PROCLEGIS->prepare($sql);                                
                    $stmt_comp->bindParam(':mesa_diretora', 	$id);        
                    $stmt_comp->execute();
                    $rows_comp = $stmt_comp->rowCount();
                    if($rows_comp > 0)
                    {
                        while($result_comp = $stmt_comp->fetch())
                        {
                            $comp .= "<div style='float:left; text-align:center; width:15%;'>
                                        <div class='perfil' style='background:url(".$result_comp["foto"].") center center;  background-size: cover; border-radius:50px; width:50px; height:50px; margin:0 auto;' border='0'></div>
                                       ".$result_comp['nome']."<br>".$result_comp['descricao']."
                                        </div>";
                        }
                    }                    
                    echo "
                        <div class='titulo'> $page &raquo; Exibir </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                                           
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <table width='100%' cellpadding='8'>
                                    <tr>
                                        <td class='bold' align='right' width='12%'>Legislatura:</td>
                                        <td  width='88%'>$legislatura</td>
                                    </tr>
                                    <tr>
                                        <td class='bold' align='right'  width='12%'>Sessão Legislativa:</td>
                                        <td  width='88%'>$sessao</td>
                                    </tr>
                                    <tr>
                                        <td class='bold' align='right' width='12%' valign='top'>Composição:</td>
                                        <td  width='88%'>$comp</td>
                                    
                                    </tr>
                                </table>                                                                                                                                                    
                            </div>                                                            
                            <br>               				
                            <center>                                                        
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_mesa_diretora/view'; value='Voltar'/></center>
                            </center>
                        </div>
                    ";
                }
            }	
            ?>
    	</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
    <!-- MODAL -->
	<script type="text/javascript" src="../../core/mod_includes/js/mdbootstrap/js/jquery-3.4.1.min.js"></script>
	<!-- Bootstrap tooltips -->
	<script type="text/javascript" src="../../core/mod_includes/js/mdbootstrap/js/popper.min.js"></script>
	<!-- Bootstrap core JavaScript -->
	<script type="text/javascript" src="../../core/mod_includes/js/mdbootstrap/js/bootstrap.min.js"></script>
	<!-- MDB core JavaScript -->
    <script type="text/javascript" src="../../core/mod_includes/js/mdbootstrap/js/mdb.min.js"></script>
    <script>
	/// LEADS -> SERVICOS ///
		$(function() 
		{
			var scntDiv_composicao = $('#p_scents_composicao');
			//var i = $('#p_scents div.bloco').size() + 1;
			var x = jQuery('div.bloco_composicao').length + 1;
				
			jQuery(document).on('click','#add_composicao',function() 
			{				
				var total=0;
				jQuery('<div class="bloco_composicao">'+					
					'<br><br>'+
					'<input type="hidden" name="composicao['+x+'][id]" id="id">'+
					'<p><label>Parlamentar*:</label> <select name="composicao['+x+'][parlamentar]" class="obg">'+
							'<option value="">Parlamentar</option>'+
							'<?php $sql_ser = " SELECT * FROM cadastro_parlamentares  ORDER BY nome ";?>'+
							'<?php $stmt_ser = $PDO_PROCLEGIS->prepare($sql_ser);?>'+
							'<?php $stmt_ser->execute();?>'+
							'<?php while($result_ser = $stmt_ser->fetch()) { ?>'+
							'<?php echo "<option value=\"".$result_ser["id"]."\">".$result_ser["nome"]."</option>";?>'+
							'<?php }?>'+							                              
						'</select>'+
                        '<p><label>Cargo*:</label> <select name="composicao['+x+'][cargo]" class="obg">'+
							'<option value="">Cargo</option>'+
							'<?php $sql_ser = " SELECT * FROM aux_mesa_diretora_cargos ORDER BY descricao ";?>'+
							'<?php $stmt_ser = $PDO_PROCLEGIS->prepare($sql_ser);?>'+
							'<?php $stmt_ser->execute();?>'+
							'<?php while($result_ser = $stmt_ser->fetch()) { ?>'+
							'<?php echo "<option value=\"".$result_ser["id"]."\">".$result_ser["descricao"]."</option>";?>'+
							'<?php }?>'+							                              
						'</select>'+
					'<i class="fas fa-plus botao_dinamico_add" id="add_composicao" title="Adicionar"></i> &nbsp; <i class="far fa-trash-alt botao_dinamico_rmv" id="rem_composicao" title="Remover" ></i><hr style="width:100%; border:none; height:1px; background:#DDD;"></div>').appendTo(scntDiv_composicao);
				//i++;
				x++;
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
				return false;
			});
		
			jQuery(document).on('click','#rem_composicao', function() 
			{ 			
				var total=0;
				if( x >= 1 )
				{
					jQuery(this).parents('div.bloco_composicao').remove();
					//i--;
					//x--;
					
				}
				return false;
			});
		});
    </script>
</body>
</html>
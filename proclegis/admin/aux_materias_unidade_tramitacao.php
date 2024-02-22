<?php
$pagina_link = 'aux_materias';
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
            $page = "Auxiliares &raquo; <a href='aux_materias/view'>Matérias Legislativas</a> &raquo; <a href='aux_materias_unidade_tramitacao/view'>Unidades de Tramitação</a>";
            if(isset($_GET['id'])){$id = $_GET['id'];}            
            $orgao = $_POST['orgao'];if($orgao == "" ) { $orgao = null;}   
            $comissao = $_POST['comissao']; if($comissao == "" ) { $comissao = null;}           
            $parlamentar = $_POST['parlamentar'];if($parlamentar == "" ) { $parlamentar = null;}
            $usuario_responsavel = $_POST['usuario_responsavel'];if($usuario_responsavel == "" ) { $usuario_responsavel = null;}
                   
           
            $dados = array(
                
                'orgao' 		=> $orgao,
                'comissao' 		    => $comissao,                
                'parlamentar' 		=> $parlamentar,
                'usuario_responsavel' 	=> $usuario_responsavel
                );
        
            if($action == "adicionar")
            {                       
            
                $sql = "INSERT INTO aux_materias_unidade_tramitacao SET ".bindFields($dados);
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
                $sql = "UPDATE aux_materias_unidade_tramitacao SET ".bindFields($dados)." WHERE id = :id ";
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
                
                $sql = "UPDATE aux_materias_unidade_tramitacao set ativo = :ativo WHERE id = :id";
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
            $fil_orgao = $_REQUEST['fil_orgao'];
            if($fil_orgao == '')
            {
                $orgao_query = " 1 = 1 ";
            }
            else
            {
                $fil_orgao1 = "%".$fil_orgao."%";
                $orgao_query = " (aux_materias_unidade_tramitacao.orgao LIKE :fil_orgao1 ) ";
            }
              
            $sql = "SELECT *, aux_materias_unidade_tramitacao.id as id
                            , aux_materias_orgaos.sigla as sigla_orgao
                            , aux_materias_orgaos.nome as nome_orgao
                            , cadastro_comissoes.sigla as sigla_comissao
                            , cadastro_comissoes.nome as nome_comissao
                            , cadastro_parlamentares.nome as nome_parlamentar
                    FROM aux_materias_unidade_tramitacao 
                    LEFT JOIN aux_materias_orgaos ON aux_materias_orgaos.id = aux_materias_unidade_tramitacao.orgao
                    LEFT JOIN cadastro_comissoes ON cadastro_comissoes.id = aux_materias_unidade_tramitacao.comissao
                    LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_materias_unidade_tramitacao.parlamentar
                    LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = aux_materias_unidade_tramitacao.usuario_responsavel
                    WHERE aux_materias_unidade_tramitacao.ativo = :ativo and ".$orgao_query." 			
                    ORDER BY aux_materias_unidade_tramitacao.id DESC
                    LIMIT :primeiro_registro, :num_por_pagina ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);    
            $stmt->bindParam(':fil_orgao1', 	$fil_orgao1);
                
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
                    <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"aux_materias_unidade_tramitacao/add\");'><i class='fas fa-plus'></i></div>
                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='aux_materias_unidade_tramitacao/view'>
                        <input name='fil_orgao' id='fil_orgao' value='$fil_orgao' placeholder='Sigla'>
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
                            <td class='titulo_tabela'>Unidade de Tramitação</td>
                            <td class='titulo_tabela'>Usuário Responsável</td>
                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                        </tr>";
                        $c=0;
                        while($result = $stmt->fetch())
                        {
                            $id = $result['id'];
                            if($result['orgao'])
                            {
                                $unidade = $result['sigla_orgao']." - ".$result['nome_orgao'];
                            }
                            if($result['parlamentar'])
                            {
                                $unidade = $result['nome_parlamentar'];
                            }
                            if($result['comissao'])
                            {
                                $unidade = $result['sigla_comissao']." - ".$result['nome_comissao'];
                            }
                            $usu_nome = $result['usu_nome'];
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td>$unidade</td>
                                    <td>$usu_nome</td>
                                    <td align=center>
                                        <div class='g_excluir' title='Excluir' onclick=\"
                                            abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'aux_materias_unidade_tramitacao/view/excluir/$id?pag=$pag\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                            \">	<i class='far fa-trash-alt'></i>
                                        </div>
                                        <div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"aux_materias_unidade_tramitacao/edit/$id?pag=$pag\");'><i class='fas fa-pencil-alt'></i></div>                                            
                                    </td>
                                </tr>";
                        }
                        echo "</table>";
                        $cnt = "SELECT COUNT(*) FROM aux_materias_unidade_tramitacao  
                                WHERE ".$orgao_query."  ";
                        $stmt = $PDO_PROCLEGIS->prepare($cnt);     
                        $stmt->bindParam(':fil_orgao1', 	$fil_orgao1);
                            
                        $variavel = "&fil_orgao=$fil_orgao";              
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
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='aux_materias_unidade_tramitacao/view/adicionar' autocomplete='off'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                            
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>  
                            <p class='bold'>ATENÇÃO: Selecione apenas um dos campos abaixo:</p>                                                                        
                                <p><label>Órgão:</label> 
                                    <select name='orgao' id='orgao' class='orgao_unidade'>
                                        <option value=''>Órgão</option>
                                        ";
                                        $sql = "SELECT *
                                                FROM aux_materias_orgaos
                                                WHERE ativo = :ativo
                                                ORDER BY sigla";
                                        $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                        $stmt_int->bindValue(':ativo',1);                               
                                        $stmt_int->execute();
                                        while($result_int = $stmt_int->fetch())
                                        {
                                            echo "<option value='".$result_int['id']."'>".$result_int['sigla']." - ".$result_int['nome']."</option>";
                                        }
                                        echo "
                                    </select>
                                <p><label>Comissão:</label> 
                                    <select name='comissao' id='comissao' class='comissao_unidade'>
                                        <option value=''>Comissão</option>
                                        ";
                                        $sql = "SELECT *, YEAR(data_inicio) as inicio
                                                        , cadastro_comissoes.id as id
                                                FROM cadastro_comissoes
                                                LEFT JOIN ( cadastro_comissoes_composicao 
                                                    LEFT JOIN aux_comissoes_periodos ON aux_comissoes_periodos.id = cadastro_comissoes_composicao.periodo)
                                                ON cadastro_comissoes_composicao.comissao = cadastro_comissoes.id
                                                WHERE cadastro_comissoes.ativo = :ativo
                                                GROUP BY cadastro_comissoes.id 
                                                ORDER BY data_inicio DESC";
                                        $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                        $stmt_int->bindValue(':ativo',1);                                 
                                        $stmt_int->execute();
                                        while($result_int = $stmt_int->fetch())
                                        {
                                            echo "<option value='".$result_int['id']."'>(".$result_int['inicio'].") ".$result_int['sigla']." - ".$result_int['nome']."</option>";
                                        }
                                        echo "
                                    </select>
                                <p><label>Parlamentar:</label>
                                    <select name='parlamentar' id='parlamentar' class='parlamentar_unidade'>
                                        <option value=''>Parlamentar</option>
                                        ";
                                        $sql = "SELECT *
                                                FROM cadastro_parlamentares
                                                WHERE ativo =:ativo
                                                ORDER BY nome";
                                        $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                        $stmt_int->bindValue(':ativo',1);                                
                                        $stmt_int->execute();
                                        while($result_int = $stmt_int->fetch())
                                        {
                                            echo "<option value='".$result_int['id']."'>".$result_int['nome']."</option>";
                                        }
                                        echo "
                                    </select>    
                                <br><br><p class='bold'>O usuário selecionado abaixo, receberá uma notificação por email toda vez que uma matéria legislativa for tramitada para esta unidade de tramitação.</p>                                                               
                                <p><label>Usuário responsável:</label>
                                    <select name='usuario_responsavel' id='usuario_responsavel' >
                                        <option value=''>Usuário responsável</option>
                                        ";
                                        $sql = "SELECT *
                                                FROM cadastro_usuarios
                                                WHERE ativo =:ativo
                                                ORDER BY usu_nome";
                                        $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                        $stmt_int->bindValue(':ativo',1);                                    
                                        $stmt_int->execute();
                                        while($result_int = $stmt_int->fetch())
                                        {
                                            echo "<option value='".$result_int['usu_id']."'>".$result_int['usu_nome']."</option>";
                                        }
                                        echo "
                                    </select>
                            </div>	                                                                        
                        </div>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_materias_unidade_tramitacao/view'; value='Cancelar'/></center>
                        </center>
                    </div>
                </form>
                ";
            }
            
            if($pagina == 'edit')
            {            		
                $sql = "SELECT *, aux_materias_orgaos.sigla as sigla_orgao
                                , aux_materias_orgaos.nome as nome_orgao
                                , cadastro_comissoes.sigla as sigla_comissao
                                , cadastro_comissoes.nome as nome_comissao
                                , cadastro_parlamentares.nome as nome_parlamentar
                                , YEAR(aux_comissoes_periodos.data_inicio) as inicio
                        FROM aux_materias_unidade_tramitacao 
                        LEFT JOIN aux_materias_orgaos ON aux_materias_orgaos.id = aux_materias_unidade_tramitacao.orgao
                        LEFT JOIN (cadastro_comissoes 
                            LEFT JOIN ( cadastro_comissoes_composicao 
                                LEFT JOIN aux_comissoes_periodos ON aux_comissoes_periodos.id = cadastro_comissoes_composicao.periodo)
                            ON cadastro_comissoes_composicao.comissao = cadastro_comissoes.id )
                        ON cadastro_comissoes.id = aux_materias_unidade_tramitacao.comissao
                        LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_materias_unidade_tramitacao.parlamentar
                        LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = aux_materias_unidade_tramitacao.usuario_responsavel
                        WHERE aux_materias_unidade_tramitacao.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();
                    
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='aux_materias_unidade_tramitacao/view/editar/$id' autocomplete='off'>
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                                                 
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>  
                            <p class='bold'>ATENÇÃO: Selecione apenas um dos campos abaixo:</p>                                    
                                <p><label>Órgão:</label> 
                                    <select name='orgao' id='orgao' class='orgao_unidade'>
                                        <option value='".$result['orgao']."'>".$result['sigla_orgao']." - ".$result['nome_orgao']."</option>
                                        ";
                                        $sql = "SELECT *
                                                FROM aux_materias_orgaos
                                                WHERE ativo =:ativo
                                                ORDER BY sigla";
                                        $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                        $stmt_int->bindValue(':ativo',1);                                   
                                        $stmt_int->execute();
                                        while($result_int = $stmt_int->fetch())
                                        {
                                            echo "<option value='".$result_int['id']."'>".$result_int['sigla']." - ".$result_int['nome']."</option>";
                                        }
                                        echo "
                                    </select>
                                <p><label>Comissão:</label> 
                                    <select name='comissao' id='comissao' class='comissao_unidade'>
                                        <option value='".$result['comissao']."'>(".$result['inicio'].") ".$result['sigla_comissao']." - ".$result['nome_comissao']."</option>
                                        ";
                                        $sql = "SELECT *, YEAR(data_inicio) as inicio
                                                        , cadastro_comissoes.id as id
                                                FROM cadastro_comissoes
                                                LEFT JOIN ( cadastro_comissoes_composicao 
                                                    LEFT JOIN aux_comissoes_periodos ON aux_comissoes_periodos.id = cadastro_comissoes_composicao.periodo)
                                                ON cadastro_comissoes_composicao.comissao = cadastro_comissoes.id
                                                WHERE cadastro_comissoes.ativo = :ativo
                                                GROUP BY cadastro_comissoes.id 
                                                ORDER BY data_inicio DESC";
                                        $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                        $stmt_int->bindValue(':ativo',1);                                   
                                        $stmt_int->execute();
                                        while($result_int = $stmt_int->fetch())
                                        {
                                            echo "<option value='".$result_int['id']."'>(".$result_int['inicio'].") ".$result_int['sigla']." - ".$result_int['nome']."</option>";
                                        }
                                        echo "
                                    </select>
                                <p><label>Parlamentar:</label>
                                    <select name='parlamentar' id='parlamentar' class='parlamentar_unidade'>
                                        <option value='".$result['parlamentar']."'>".$result['nome_parlamentar']."</option>
                                        ";
                                        $sql = "SELECT *
                                                FROM cadastro_parlamentares
                                                WHERE ativo =:ativo
                                                ORDER BY nome";
                                        $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                        $stmt_int->bindValue(':ativo',1);                                
                                        $stmt_int->execute();
                                        while($result_int = $stmt_int->fetch())
                                        {
                                            echo "<option value='".$result_int['id']."'>".$result_int['nome']."</option>";
                                        }
                                        echo "
                                    </select>  
                                
                                    <br><br><p class='bold'>O usuário selecionado abaixo, receberá uma notificação por email toda vez que uma matéria legislativa for tramitada para esta unidade de tramitação.</p>                                                               
                                    <p><label>Usuário responsável:</label>
                                        <select name='usuario_responsavel' id='usuario_responsavel' >
                                            <option value='".$result['usuario_resposavel']."'>".$result['usu_nome']."</option>
                                            ";
                                            $sql = "SELECT *
                                                    FROM cadastro_usuarios
                                                    WHERE ativo =:ativo 
                                                    ORDER BY usu_nome";
                                            $stmt_int = $PDO_PROCLEGIS->prepare($sql);     
                                            $stmt_int->bindValue(':ativo',1);                                  
                                            $stmt_int->execute();
                                            while($result_int = $stmt_int->fetch())
                                            {
                                                echo "<option value='".$result_int['usu_id']."'>".$result_int['usu_nome']."</option>";
                                            }
                                            echo "
                                        </select>
                            </div>                                                                        				
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_materias_unidade_tramitacao/view'; value='Cancelar'/></center>
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
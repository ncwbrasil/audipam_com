<?php
$pagina_link = 'aux_parlamentares';
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
            $page = "Auxiliares &raquo; <a href='aux_parlamentares/view'>Parlamentares</a> &raquo; <a href='aux_parlamentares_coligacoes/view'>Coligações</a>";
            
            if(isset($_GET['id'])){$id = $_GET['id'];}
            $legislatura = $_POST['legislatura'];
            $nome = $_POST['nome'];
            $votos = $_POST['votos'];    if($votos == ""){ $votos = null;}                  
            $data_criacao = reverteData($_POST['data_criacao']);
            $data_extincao = reverteData($_POST['data_extincao']);if($data_extincao == ""){ $data_extincao = null;}
            
            $dados = array(
                
                'legislatura' 		=> $legislatura,        
                'nome' 		    => $nome,
                'votos' 		=> $votos
                );
        
            if($action == "adicionar")
            {                       
            
                $sql = "INSERT INTO aux_parlamentares_coligacoes SET ".bindFields($dados);
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
                $sql = "UPDATE aux_parlamentares_coligacoes SET ".bindFields($dados)." WHERE id = :id ";
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
                
                $sql = "UPDATE aux_parlamentares_coligacoes set ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id);
                $stmt->bindValue(':ativo',0);
                if($stmt->execute())
                {
                    // unlink($logo_antiga);
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
                $nome_query = " (nome LIKE :fil_nome1 ) ";
            }
            $sql = "SELECT *, aux_parlamentares_coligacoes.id as id,
                              YEAR(data_inicio) as data_inicio, YEAR(data_fim) as data_fim
                              FROM aux_parlamentares_coligacoes 
                    LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = aux_parlamentares_coligacoes.legislatura                        
                    WHERE aux_parlamentares_coligacoes.ativo = :ativo and ".$nome_query."			
                    ORDER BY aux_parlamentares_coligacoes.id DESC
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
                    <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"aux_parlamentares_coligacoes/add\");'><i class='fas fa-plus'></i></div>
                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='aux_parlamentares_coligacoes/view'>
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
                            <td class='titulo_tabela'>Legislatura</td>
                            <td class='titulo_tabela'>Votos</td>
                            <td class='titulo_tabela' align='right'>Gerenciar</td>                            
                        </tr>";
                        $c=0;
                        while($result = $stmt->fetch())
                        {
                            $id = $result['id'];
                            $legislatura = $result['numero']." (".$result['data_inicio']." - ".$result['data_fim'].")";
                            $nome =$result['nome'];
                            $votos = $result['votos'];                            
                            
                            
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td>$nome</td>
                                    <td>$legislatura</td>
                                    <td>$votos</td>                                                                       
                                    <td align=center>
                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'aux_parlamentares_coligacoes/view/excluir/$id?pag=$pag\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                \">	<i class='far fa-trash-alt'></i>
                                            </div>
                                            <div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"aux_parlamentares_coligacoes/edit/$id?pag=$pag\");'><i class='fas fa-pencil-alt'></i></div>                                            
                                    </td>
                                </tr>";
                        }
                        echo "</table>";
                        $cnt = "SELECT COUNT(*) FROM aux_parlamentares_coligacoes  WHERE ".$nome_query."";
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
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='aux_parlamentares_coligacoes/view/adicionar'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                            
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Legislatura:</label> <select name='legislatura' id='legislatura'>
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
                                <p><label>Nome:</label> <input name='nome' id='nome' placeholder='Nome' class='obg'>
                                <p><label>Votos:</label> <input name='votos' id='votos' placeholder='Votos' >                                
                            </div>	                                                                        
                        </div>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_parlamentares_coligacoes/view'; value='Cancelar'/></center>
                        </center>
                    </div>
                </form>
                ";
            }
            
            if($pagina == 'edit')
            {            		                
                $sql = "SELECT *,YEAR(data_inicio) as data_inicio, YEAR(data_fim) as data_fim FROM aux_parlamentares_coligacoes 
                        LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = aux_parlamentares_coligacoes.legislatura
                        WHERE aux_parlamentares_coligacoes.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();                                                                   
                               
                    
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='aux_parlamentares_coligacoes/view/editar/$id'>
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                                               
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Legislatura:</label> <select name='legislatura' id='legislatura'>
                                    <option value='".$result['legislatura']."'>".$result['numero']." (".$result['data_inicio']." - ".$result['data_fim'].")</option>";
                                    $sql = "SELECT *, YEAR(data_inicio) as data_inicio, YEAR(data_fim) as data_fim 
                                            FROM aux_parlamentares_legislaturas 
                                            WHERE ativo = :ativo
                                            ORDER BY numero";
                                    $stmt_leg = $PDO_PROCLEGIS->prepare($sql);
                                    $stmt_leg->bindValue(':ativo',1);  
                                    $stmt_leg->execute();
                                    while($result_leg = $stmt_leg->fetch())
                                    {
                                        echo "<option value='".$result_leg['id']."'>".$result_leg['numero']." (".$result_leg['data_inicio']." - ".$result_leg['data_fim'].")</option>";
                                    }
                                    echo "
                                </select>  
                                
                                <p><label>Nome:</label> <input name='nome' id='nome' value='".$result['nome']."' placeholder='Nome'  class='obg'>
                                <p><label>Votos:</label> <input name='votos' id='votos' value='".$result['votos']."' placeholder='Votos' >                                
                            </div>                                                                       				
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_parlamentares_coligacoes/view'; value='Cancelar'/></center>
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
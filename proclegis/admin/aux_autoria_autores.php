<?php
$pagina_link = 'aux_autoria';
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
            $page = "Auxiliares &raquo; <a href='aux_autoria/view'>Autoria</a> &raquo; <a href='aux_autoria_autores/view'>Autores</a>";
            if(isset($_GET['id'])){$id = $_GET['id'];}
            $tipo_autor = $_POST['tipo_autor'];
            $nome = $_POST['nome'];    
            $usuario = $_POST['usuario']; if($usuario == ""){ $usuario = null;}
            $parlamentar = $_POST['parlamentar']; 
            $bancada = $_POST['bancada']; 
            $bloco = $_POST['bloco']; 
            $frente = $_POST['frente']; 
            $comissao = $_POST['comissao'];    
            $orgao = $_POST['orgao'];    

           
            $dados = array(
                
                'tipo_autor' 	=> $tipo_autor,
                'usuario' 	    => $usuario,
                'parlamentar' 	=> $parlamentar,
                'bancada' 		=> $bancada,
                'bloco' 		=> $bloco,
                'frente' 		=> $frente,
                'comissao' 		=> $comissao,
                'orgao' 		=> $orgao,
                'nome' 		    => $nome
            );
            
            if($action == "adicionar")
            {                       
            
                $sql = "INSERT INTO aux_autoria_autores SET ".bindFields($dados);
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
                $sql = "UPDATE aux_autoria_autores SET ".bindFields($dados)." WHERE id = :id ";
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
                
                $sql = "UPDATE aux_autoria_autores set ativo = :ativo WHERE id = :id";
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
              
            $sql = "SELECT *, aux_autoria_autores.id as id
                    FROM aux_autoria_autores
                    LEFT JOIN aux_autoria_tipo_autor ON  aux_autoria_tipo_autor.id = aux_autoria_autores.tipo_autor
                    LEFT JOIN cadastro_usuarios ON  cadastro_usuarios.usu_id = aux_autoria_autores.usuario
                    WHERE aux_autoria_autores.ativo = :ativo and ".$nome_query."			
                    ORDER BY aux_autoria_autores.id DESC
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
                    <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"aux_autoria_autores/add\");'><i class='fas fa-plus'></i></div>
                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='aux_autoria_autores/view'>
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
                            <td class='titulo_tabela'>Tipo de Autor</td>                            
                            <td class='titulo_tabela'>Nome</td>                            
                            <td class='titulo_tabela'>Vinculado a usuário</td>                            
                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                        </tr>";
                        $c=0;
                        while($result = $stmt->fetch())
                        {
                            $id = $result['id'];
                            
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td>".$result['descricao']."</td>                              
                                    <td>".$result['nome']."</td>                              
                                    <td>".$result['usu_nome']."</td>                              
                                    <td align=center>
                                        <div class='g_excluir' title='Excluir' onclick=\"
                                            abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'aux_autoria_autores/view/excluir/$id?pag=$pag\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                            \">	<i class='far fa-trash-alt'></i>
                                        </div>
                                        <div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"aux_autoria_autores/edit/$id?pag=$pag\");'><i class='fas fa-pencil-alt'></i></div>                                            
                                    </td>
                                </tr>";
                        }
                        echo "</table>";
                        $cnt = "SELECT COUNT(*) FROM aux_autoria_autores  WHERE ".$nome_query."";
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
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='aux_autoria_autores/view/adicionar' autocomplete='off'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                            
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>                                                                          
                                <p><label>Tipo *:</label> <select name='tipo_autor' id='tipo_autor' class='obg'>   
                                    <option value=''>Tipo de autor</option>
                                    ";
                                    $sql = " SELECT * FROM aux_autoria_tipo_autor WHERE ativo = :ativo ORDER BY descricao";
                                    $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                                    $stmt_int->bindValue(':ativo', 	1);                                    
                                    $stmt_int->execute();
                                    while($result_int = $stmt_int->fetch())
                                    {
                                        echo "<option value='".$result_int['id']."'>".$result_int['descricao']."</option>";
                                    }
                                    echo "
                                </select> 
                                <p>
                                <div id='autores'>
                                    
                                </div>
                                <p><label>Nome *:</label> <input name='nome' id='nome' placeholder='Nome' class='obg'>
                                <p class='titulo'> Vincular a um usuário <p>
                                <p><label>Usuário:</label> <select name='usuario' id='usuario'>   
                                    <option value=''>Usuário</option>
                                    ";
                                    $sql = " SELECT * FROM cadastro_usuarios WHERE ativo = :ativo   ORDER BY usu_nome";
                                    $stmt_int = $PDO_PROCLEGIS->prepare($sql);  
                                    $stmt_int->bindValue(':ativo', 	1);                            
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
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_autoria_autores/view'; value='Cancelar'/></center>
                        </center>
                    </div>
                </form>
                ";
            }
            
            if($pagina == 'edit')
            {            
               	
                $sql = "SELECT * FROM aux_autoria_autores
                        LEFT JOIN aux_autoria_tipo_autor ON  aux_autoria_tipo_autor.id = aux_autoria_autores.tipo_autor
                        LEFT JOIN cadastro_usuarios ON  cadastro_usuarios.usu_id = aux_autoria_autores.usuario
                        WHERE aux_autoria_autores.id = :id AND aux_autoria_autores.ativo = :ativo ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->bindValue(':ativo', 	1); 
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();
                    
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='aux_autoria_autores/view/editar/$id' autocomplete='off'>
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                                                 
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Tipo *:</label> <select name='tipo_autor' id='tipo_autor' class='obg'>   
                                    <option value='".$result['tipo_autor']."'>".$result['descricao']."</option>
                                    ";
                                    $sql = " SELECT * FROM aux_autoria_tipo_autor WHERE ativo = :ativo ORDER BY descricao";
                                    $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                                    $stmt_int->bindValue(':ativo', 	1);                            
                                    $stmt_int->execute();
                                    while($result_int = $stmt_int->fetch())
                                    {
                                        echo "<option value='".$result_int['id']."'>".$result_int['descricao']."</option>";
                                    }
                                    echo "
                                </select>
                                <p><label>Nome:</label> <input name='nome' id='nome' value='".$result['nome']."' placeholder='Nome'  class='obg'>                                
                                <p class='titulo'> Vincular a um usuário <p>
                                <p><label>Usuário:</label> <select name='usuario' id='usuario'>   
                                    <option value='".$result['usuario']."'>".$result['usu_nome']."</option>
                                    ";
                                    $sql = " SELECT * FROM cadastro_usuarios WHERE ativo = :ativo  ORDER BY usu_nome";
                                    $stmt_int = $PDO_PROCLEGIS->prepare($sql);  
                                    $stmt_int->bindValue(':ativo', 	1);                              
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
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_autoria_autores/view'; value='Cancelar'/></center>
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
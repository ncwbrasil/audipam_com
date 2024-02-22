<?php
$pagina_link = 'aux_sessoes_plenarias';
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
            $page = "Auxiliares &raquo; <a href='aux_sessoes_plenarias/view'>Sessões Plenárias</a> &raquo; <a href='aux_sessoes_plenarias_tipos/view'>Tipos de Sessão</a>";
            
            if(isset($_GET['id'])){$id = $_GET['id'];}
            $descricao = $_POST['descricao'];
            $quorum = $_POST['quorum'];
             $dados = array(
                
                'descricao' 		=> $descricao,        
                'quorum' 		    => $quorum
                );
        
            if($action == "adicionar")
            {                       
            
                $sql = "INSERT INTO aux_sessoes_plenarias_tipos SET ".bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                if($stmt->execute($dados))
                {		
                    $id = $PDO_PROCLEGIS->lastInsertId();

                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';
                    
                    $caminho = "../uploads/partidos/";
                    foreach($_FILES as $key => $files)
                    {
                        $files_test = array_filter($files['name']);
                        if(!empty($files_test))
                        {
                            if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
                            if(!empty($files["name"]["logo"]))
                            {
                                $quorumArquivo 	= $files["name"]["logo"];
                                $quorumTemporario = $files["tmp_name"]["logo"];                            
                                $extensao = pathinfo($quorumArquivo, PATHINFO_EXTENSION);
                                $logo	= $caminho;
                                $logo .= "logo_".md5(mt_rand(1,10000).$quorumArquivo).'.'.$extensao;					
                                move_uploaded_file($quorumTemporario, ($logo));
                                $imnfo = getimagesize($logo);
                                $img_w = $imnfo[0];	  // largura
                                $img_h = $imnfo[1];	  // altura
                                if($img_w > 500 || $img_h > 500)
                                {
                                    $image = WideImage::load($logo);
                                    $image = $image->resize(500, 500);
                                    $image->saveToFile($logo);
                                }
                                $sql = "UPDATE aux_sessoes_plenarias_tipos SET 
                                        logo 	 = :logo
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':logo',$logo);
                                $stmt->bindParam(':id',$id);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
                                
                                //CONVERTE FOTO PARA BASE64
                                $imagedata = file_get_contents($logo);                             
                                $base64 = base64_encode($imagedata);
                            }					
                        }
                    }
                    //                				
                
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
                $sql = "UPDATE aux_sessoes_plenarias_tipos SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id;
                if($stmt->execute($dados))
                {
                    
                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';
                    $caminho = "../uploads/partidos/";
                    foreach($_FILES as $key => $files)
                    {
                        $files_test = array_filter($files['name']);
                        if(!empty($files_test))
                        {
                            if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
                            if(!empty($files["name"]["logo"]))
                            {
                                $quorumArquivo 	= $files["name"]["logo"];
                                $quorumTemporario = $files["tmp_name"]["logo"];
                                $extensao = pathinfo($quorumArquivo, PATHINFO_EXTENSION);
                                $logo	= $caminho;
                                $logo .= "logo_".md5(mt_rand(1,10000).$quorumArquivo).'.'.$extensao;					
                                move_uploaded_file($quorumTemporario, ($logo));
                                $imnfo = getimagesize($logo);
                                $img_w = $imnfo[0];	  // largura
                                $img_h = $imnfo[1];	  // altura
                                if($img_w > 500 || $img_h > 500)
                                {
                                    $image = WideImage::load($logo);
                                    $image = $image->resize(500, 500);
                                    $image->saveToFile($logo);
                                }
                                $sql = "UPDATE aux_sessoes_plenarias_tipos SET 
                                        logo 	 = :logo
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':logo',$logo);
                                $stmt->bindParam(':id',$id);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
                                
                                //CONVERTE FOTO PARA BASE64
                                $imagedata = file_get_contents($logo);                             
                                $base64 = base64_encode($imagedata);
                            }					
                        }
                    }
                    //

                    
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
                
                $sql = "UPDATE aux_sessoes_plenarias_tipos set ativo = :ativo WHERE id = :id";
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
            if($action == 'ativar')
            {
                log_operacao($id, $PDO_PROCLEGIS);
                $sql = "UPDATE aux_sessoes_plenarias_tipos SET status = :status WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':status',1);
                $stmt->bindParam(':id',$id);
                $stmt->execute();
            }
            if($action == 'desativar')
            {
                log_operacao($id, $PDO_PROCLEGIS);
                $sql = "UPDATE aux_sessoes_plenarias_tipos SET status = :status WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':status',0);
                $stmt->bindParam(':id',$id);
                $stmt->execute();
            }
            $num_por_pagina = 10;
            if(!$pag){$primeiro_registro = 0; $pag = 1;}
            else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
            $fil_descricao = $_REQUEST['fil_descricao'];
            if($fil_descricao == '')
            {
                $descricao_query = " 1 = 1 ";
            }
            else
            {
                $fil_descricao1 = $fil_descricao2 = $fil_descricao3 = "%".$fil_descricao."%";
                $descricao_query = " (descricao LIKE :fil_descricao1 ) ";
            }
            $sql = "SELECT * FROM aux_sessoes_plenarias_tipos 
                    WHERE aux_sessoes_plenarias_tipos.ativo = :ativo and ".$descricao_query." 			
                    ORDER BY id DESC
                    LIMIT :primeiro_registro, :num_por_pagina ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);    
            $stmt->bindParam(':fil_descricao1', 	$fil_descricao1);
                
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
                    <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"aux_sessoes_plenarias_tipos/add\");'><i class='fas fa-plus'></i></div>
                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='aux_sessoes_plenarias_tipos/view'>
                        <input name='fil_descricao' id='fil_descricao' value='$fil_descricao' placeholder='Descrição'>
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
                            <td class='titulo_tabela'>Descrição</td>
                            <td class='titulo_tabela'>Quórum Mínimo</td>
                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                        </tr>";
                        $c=0;
                        while($result = $stmt->fetch())
                        {
                            $id = $result['id'];
                            $descricao = $result['descricao'];
                            $quorum = $result['quorum'];
                            
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td>$descricao</td>
                                    <td>$quorum</td>
                                    <td align=center>
                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'aux_sessoes_plenarias_tipos/view/excluir/$id?pag=$pag\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                \">	<i class='far fa-trash-alt'></i>
                                            </div>
                                            <div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"aux_sessoes_plenarias_tipos/edit/$id?pag=$pag\");'><i class='fas fa-pencil-alt'></i></div>                                            
                                    </td>
                                </tr>";
                        }
                        echo "</table>";
                        $cnt = "SELECT COUNT(*) FROM aux_sessoes_plenarias_tipos  WHERE ".$descricao_query."";
                        $stmt = $PDO_PROCLEGIS->prepare($cnt);     
                        $stmt->bindParam(':fil_descricao1', 	$fil_descricao1);
                         
                        $variavel = "&fil_descricao=$fil_descricao";                 
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
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='aux_sessoes_plenarias_tipos/view/adicionar'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                            
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Descrição:</label> <input name='descricao' id='descricao' placeholder='Descrição' class='obg'>
                                <p><label>Quórum Mínimo:</label> <input name='quorum' id='quorum' placeholder='Quórum Mínimo' class='obg'>
                            </div>                    
                        </div>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_sessoes_plenarias_tipos/view'; value='Cancelar'/></center>
                        </center>
                    </div>
                </form>
                ";
            }
            
            if($pagina == 'edit')
            {            		
                $sql = "SELECT * FROM aux_sessoes_plenarias_tipos 
                        WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();                                                                   
                    $logo = $result['logo'];                
                    
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='aux_sessoes_plenarias_tipos/view/editar/$id'>
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                                               
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Descrição:</label> <input name='descricao' id='descricao' value='".$result['descricao']."' placeholder='Descrição'  class='obg'>
                                <p><label>Quórum Mínimo:</label> <input name='quorum' id='quorum' value='".$result['quorum']."' placeholder='Quórum Mínimo'  class='obg'>                            
                            </div>                        				
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_sessoes_plenarias_tipos/view'; value='Cancelar'/></center>
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
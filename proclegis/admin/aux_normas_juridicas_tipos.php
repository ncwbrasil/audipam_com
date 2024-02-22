<?php
$pagina_link = 'aux_normas_juridicas';
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
            $page = "Auxiliares &raquo; <a href='aux_normas_juridicas/view'>Normas Jurídicas</a> &raquo; <a href='aux_normas_juridicas_tipos/view'>Tipos de Normas Jurídicas</a>";
            
            if(isset($_GET['id'])){$id = $_GET['id'];}
            $sigla = $_POST['sigla'];
            $nome = $_POST['nome'];
             $dados = array(
                
                'sigla' 		=> $sigla,        
                'nome' 		    => $nome
                );
        
            if($action == "adicionar")
            {                       
            
                $sql = "INSERT INTO aux_normas_juridicas_tipos SET ".bindFields($dados);
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
                                $nomeArquivo 	= $files["name"]["logo"];
                                $nomeTemporario = $files["tmp_name"]["logo"];                            
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $logo	= $caminho;
                                $logo .= "logo_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($logo));
                                $imnfo = getimagesize($logo);
                                $img_w = $imnfo[0];	  // largura
                                $img_h = $imnfo[1];	  // altura
                                if($img_w > 500 || $img_h > 500)
                                {
                                    $image = WideImage::load($logo);
                                    $image = $image->resize(500, 500);
                                    $image->saveToFile($logo);
                                }
                                $sql = "UPDATE aux_normas_juridicas_tipos SET 
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
                $sql = "UPDATE aux_normas_juridicas_tipos SET ".bindFields($dados)." WHERE id = :id ";
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
                                $nomeArquivo 	= $files["name"]["logo"];
                                $nomeTemporario = $files["tmp_name"]["logo"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $logo	= $caminho;
                                $logo .= "logo_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($logo));
                                $imnfo = getimagesize($logo);
                                $img_w = $imnfo[0];	  // largura
                                $img_h = $imnfo[1];	  // altura
                                if($img_w > 500 || $img_h > 500)
                                {
                                    $image = WideImage::load($logo);
                                    $image = $image->resize(500, 500);
                                    $image->saveToFile($logo);
                                }
                                $sql = "UPDATE aux_normas_juridicas_tipos SET 
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
                
                $sql = "DELETE FROM aux_normas_juridicas_tipos WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id);
                if($stmt->execute())
                {
                    unlink($logo_antiga);

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
                $sql = "UPDATE aux_normas_juridicas_tipos SET status = :status WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':status',1);
                $stmt->bindParam(':id',$id);
                $stmt->execute();
            }
            if($action == 'desativar')
            {
                $sql = "UPDATE aux_normas_juridicas_tipos SET status = :status WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':status',0);
                $stmt->bindParam(':id',$id);
                $stmt->execute();
            }
            $num_por_pagina = 10;
            if(!$pag){$primeiro_registro = 0; $pag = 1;}
            else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
            $fil_sigla = $_REQUEST['fil_sigla'];
            if($fil_sigla == '')
            {
                $sigla_query = " 1 = 1 ";
            }
            else
            {
                $fil_sigla1 = $fil_sigla2 = $fil_sigla3 = "%".$fil_sigla."%";
                $sigla_query = " (sigla LIKE :fil_sigla1 ) ";
            }
            $sql = "SELECT * FROM aux_normas_juridicas_tipos 
                    WHERE ".$sigla_query." 			
                    ORDER BY id DESC
                    LIMIT :primeiro_registro, :num_por_pagina ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);    
            $stmt->bindParam(':fil_sigla1', 	$fil_sigla1);
                
            $stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
            $stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if($pagina == "view")
            {
                echo "
                <div class='titulo'> $page  </div>
                <div id='botoes'>
                    <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"aux_normas_juridicas_tipos/add\");'><i class='fas fa-plus'></i></div>
                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='aux_normas_juridicas_tipos/view'>
                        <input name='fil_sigla' id='fil_sigla' value='$fil_sigla' placeholder='Sigla'>
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
                            <td class='titulo_tabela'>Sigla</td>
                            <td class='titulo_tabela'>Nome</td>
                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                        </tr>";
                        $c=0;
                        while($result = $stmt->fetch())
                        {
                            $id = $result['id'];
                            $sigla = $result['sigla'];
                            $nome = $result['nome'];
                            $descricao = $result['descricao'];
                            $data_criacao = reverteData($result['data_criacao']);
                            $logo = $result['logo'];
                            if($logo == '')
                            {
                                $logo = '../../core/imagens/perfil.png';
                            }
                            
                            
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td>$sigla</td>
                                    <td>$nome</td>
                                    <td align=center>
                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'aux_normas_juridicas_tipos/view/excluir/$id?pag=$pag\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                \">	<i class='far fa-trash-alt'></i>
                                            </div>
                                            <div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"aux_normas_juridicas_tipos/edit/$id?pag=$pag\");'><i class='fas fa-pencil-alt'></i></div>                                            
                                    </td>
                                </tr>";
                        }
                        echo "</table>";
                        $cnt = "SELECT COUNT(*) FROM aux_normas_juridicas_tipos  WHERE ".$sigla_query."";
                        $stmt = $PDO_PROCLEGIS->prepare($cnt);     
                        $stmt->bindParam(':fil_sigla1', 	$fil_sigla1);
                         
                        $variavel = "&fil_sigla=$fil_sigla";                 
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
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='aux_normas_juridicas_tipos/view/adicionar'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                            
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Sigla:</label> <input name='sigla' id='sigla' placeholder='Sigla' class='obg'>
                                <p><label>Nome:</label> <input name='nome' id='nome' placeholder='Nome' class='obg'>
                            </div>                    
                        </div>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_normas_juridicas_tipos/view'; value='Cancelar'/></center>
                        </center>
                    </div>
                </form>
                ";
            }
            
            if($pagina == 'edit')
            {            		
                $sql = "SELECT * FROM aux_normas_juridicas_tipos 
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
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='aux_normas_juridicas_tipos/view/editar/$id'>
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                                               
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Sigla:</label> <input name='sigla' id='sigla' value='".$result['sigla']."' placeholder='Sigla'  class='obg'>
                                <p><label>Nome:</label> <input name='nome' id='nome' value='".$result['nome']."' placeholder='Nome'  class='obg'>                            
                            </div>                        				
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='aux_normas_juridicas_tipos/view'; value='Cancelar'/></center>
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
<?php
$pagina_link = 'cadastro_comissoes';
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
            $page = "Cadastro &raquo; <a href='cadastro_comissoes/view'>Comissões</a>";
            if(isset($_GET['id'])){$id = $_GET['id'];}
            $nome   = $_POST['nome'];
            $sigla   = $_POST['sigla'];
            $tipo  = $_POST['tipo'];
            $data_criacao  = reverteData($_POST['data_criacao']);if($data_criacao == ""){ $data_criacao = null;}
            $data_extincao  = reverteData($_POST['data_extincao']);if($data_extincao == ""){ $data_extincao = null;}
            $unidade_deliberativa    = $_POST['unidade_deliberativa'];
            $status = $_POST['status'];      
            
            $dados = array(
                
                'nome' 		    => $nome,
                'sigla' 		    => $sigla,
                'tipo' 		    => $tipo,
                'data_criacao' 		    => $data_criacao,
                'data_extincao' 		    => $data_extincao,
                'unidade_deliberativa' 		    => $unidade_deliberativa,
                'status' 		=> $status
                );
        
            if($action == "adicionar")
            {                       
            
                $sql = "INSERT INTO cadastro_comissoes SET ".bindFields($dados);
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
                $sql = "UPDATE cadastro_comissoes SET ".bindFields($dados)." WHERE id = :id ";
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

                // $sql = "DELETE FROM cadastro_comissoes WHERE id = :id";
                $sql = "UPDATE cadastro_comissoes SET ativo = :ativo WHERE id = :id";
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
            if($action == 'ativar')
            {
                $sql = "UPDATE cadastro_comissoes SET status = :status WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':status',1);
                $stmt->bindParam(':id',$id);
                $stmt->execute();

                log_operacao($id, $PDO_PROCLEGIS);  
            }
            if($action == 'desativar')
            {
                echo "aaa".$id;
                $sql = "UPDATE cadastro_comissoes SET status = :status WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':status',0);
                $stmt->bindParam(':id',$id);
                $stmt->execute();

                log_operacao($id, $PDO_PROCLEGIS);  
            }

            if($action == "adicionar_composicao")
            {                       
                $periodo   = $_POST['periodo'];
                $parlamentar   = $_POST['parlamentar'];
                $cargo  = $_POST['cargo'];
                $titular    = $_POST['titular'];
                $data_designacao  = reverteData($_POST['data_designacao']);
                $data_desligamento  = reverteData($_POST['data_desligamento']);
                $motivo_desligamento    = $_POST['motivo_desligamento'];

                if($data_desligamento == ''){
                    $data_desligamento = NULL; 
                }
                
                
                $dados = array(
                    'comissao' 		        => $id,
                    'periodo' 		        => $periodo,
                    'parlamentar' 		    => $parlamentar,
                    'cargo' 		        => $cargo,
                    'titular' 		        => $titular,
                    'data_designacao' 	    => $data_designacao,
                    'data_desligamento' 	=> $data_desligamento,
                    'motivo_desligamento' 	=> $motivo_desligamento
                    );
                $sql = "INSERT INTO cadastro_comissoes_composicao SET ".bindFields($dados);
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
            if($action == "editar_composicao")
            {                       
                $id_composicao   = $_POST['id_composicao'];
                $periodo   = $_POST['periodo'];
                $parlamentar   = $_POST['parlamentar'];
                $cargo  = $_POST['cargo'];
                $titular    = $_POST['titular'];
                $data_designacao  = reverteData($_POST['data_designacao']);
                $data_desligamento  = reverteData($_POST['data_desligamento']);
                $motivo_desligamento    = $_POST['motivo_desligamento'];  
                
                if($data_desligamento == ''){
                    $data_desligamento = NULL; 
                }
                
                $dados = array(
                    'comissao' 		=> $id,
                    'periodo' 		    => $periodo,
                    'parlamentar' 		=> $parlamentar,
                    'cargo' 		        => $cargo,
                    'titular' 		        => $titular,
                    'data_designacao' 	=> $data_designacao,
                    'data_desligamento' 		    => $data_desligamento,
                    'motivo_desligamento' 		    => $motivo_desligamento
                    );

                    
                $sql = "UPDATE cadastro_comissoes_composicao SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id_composicao;
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
            if($action == 'excluir_composicao')
            {
                $id_sub = $_GET['id_sub'];
                $sql = "UPDATE cadastro_comissoes_composicao set ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id_sub);
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

            if($action == "adicionar_reunioes")
            {                       
                $periodo   = $_POST['periodo'];
                $numero   = $_POST['numero'];
                $nome  = $_POST['nome'];
                $local    = $_POST['local'];
                $data_reuniao  = reverteData($_POST['data_reuniao']);
                $hora_inicio    = $_POST['hora_inicio'];
                $hora_termino    = $_POST['hora_termino'];
                $observacao    = $_POST['observacao'];
               
                
                $dados = array(
                    'comissao' 		=> $id,
                    'periodo' 		    => $periodo,
                    'numero' 		    => $numero,
                    'nome' 		        => $nome,
                    'local' 		        => $local,
                    'data_reuniao' 	=> $data_reuniao,
                    'hora_inicio' 		    => $hora_inicio,
                    'hora_termino' 		    => $hora_termino,
                    'observacao' 		    => $observacao
                    );
                $sql = "INSERT INTO cadastro_comissoes_reunioes SET ".bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                if($stmt->execute($dados))
                {		
                    $id_reuniao = $PDO_PROCLEGIS->lastInsertId();

                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';
                    
                    $caminho = "../uploads/reunioes/";
                   
                    foreach($_FILES as $key => $files)
                    {
                        $files_test = array_filter($files['name']);
                        if(!empty($files_test))
                        {
                            if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
                            if(!empty($files["name"]["pauta"]))
                            {
                               
                                $nomeArquivo 	= $files["name"]["pauta"];
                                $nomeTemporario = $files["tmp_name"]["pauta"];                            
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $pauta	= $caminho;
                                $pauta .= "pauta_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($pauta));
                                $imnfo = getimagesize($pauta);
                                $img_w = $imnfo[0];	  // largura
                                $img_h = $imnfo[1];	  // altura
                                if($img_w > 500 || $img_h > 500)
                                {
                                    $image = WideImage::load($pauta);
                                    $image = $image->resize(500, 500);
                                    $image->saveToFile($pauta);
                                }
                                $sql = "UPDATE cadastro_comissoes_reunioes SET 
                                        pauta 	 = :pauta
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':pauta',$pauta);
                                $stmt->bindParam(':id',$id_reuniao);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
                                
                                //CONVERTE FOTO PARA BASE64
                                // $imagedata = file_get_contents($pauta);                             
                                // $base64 = base64_encode($imagedata);
                            }					
                        }
                    }
                    //  

                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';
                    
                    $caminho = "../uploads/reunioes/";
                   
                    foreach($_FILES as $key => $files)
                    {
                        $files_test = array_filter($files['name']);
                        if(!empty($files_test))
                        {
                            if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
                            if(!empty($files["name"]["ata"]))
                            {
                               
                                $nomeArquivo 	= $files["name"]["ata"];
                                $nomeTemporario = $files["tmp_name"]["ata"];                            
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $ata	= $caminho;
                                $ata .= "ata_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($ata));
                                $imnfo = getimagesize($ata);
                                $img_w = $imnfo[0];	  // largura
                                $img_h = $imnfo[1];	  // altura
                                if($img_w > 500 || $img_h > 500)
                                {
                                    $image = WideImage::load($ata);
                                    $image = $image->resize(500, 500);
                                    $image->saveToFile($ata);
                                }
                                $sql = "UPDATE cadastro_comissoes_reunioes SET 
                                        ata 	 = :ata
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':ata',$ata);
                                $stmt->bindParam(':id',$id_reuniao);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
                                
                                //CONVERTE FOTO PARA BASE64
                                // $imagedata = file_get_contents($ata);                             
                                // $base64 = base64_encode($imagedata);
                            }					
                        }
                    }
                    //  
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
            if($action == "editar_reunioes")
            {                       
                $id_reuniao   = $_POST['id_reuniao'];
                $periodo   = $_POST['periodo'];
                $numero   = $_POST['numero'];
                $nome  = $_POST['nome'];
                $local    = $_POST['local'];
                $data_reuniao  = reverteData($_POST['data_reuniao']);
                $hora_inicio    = $_POST['hora_inicio'];
                $hora_termino    = $_POST['hora_termino'];
                $observacao    = $_POST['observacao'];
               
                
                $dados = array(
                    'comissao' 		=> $id,
                    'periodo' 		    => $periodo,
                    'numero' 		    => $numero,
                    'nome' 		        => $nome,
                    'local' 		        => $local,
                    'data_reuniao' 	=> $data_reuniao,
                    'hora_inicio' 		    => $hora_inicio,
                    'hora_termino' 		    => $hora_termino,
                    'observacao' 		    => $observacao
                    );

                    
                $sql = "UPDATE cadastro_comissoes_reunioes SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id_reuniao;
                if($stmt->execute($dados))
                {		
                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';
                    
                    $caminho = "../uploads/reunioes/";
                   
                    foreach($_FILES as $key => $files)
                    {
                        $files_test = array_filter($files['name']);
                        if(!empty($files_test))
                        {
                            if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
                            if(!empty($files["name"]["pauta"]))
                            {
                               
                                $nomeArquivo 	= $files["name"]["pauta"];
                                $nomeTemporario = $files["tmp_name"]["pauta"];                            
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $pauta	= $caminho;
                                $pauta .= "pauta_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($pauta));
                                $imnfo = getimagesize($pauta);
                                $img_w = $imnfo[0];	  // largura
                                $img_h = $imnfo[1];	  // altura
                                if($img_w > 500 || $img_h > 500)
                                {
                                    $image = WideImage::load($pauta);
                                    $image = $image->resize(500, 500);
                                    $image->saveToFile($pauta);
                                }
                                $sql = "UPDATE cadastro_comissoes_reunioes SET 
                                        pauta 	 = :pauta
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':pauta',$pauta);
                                $stmt->bindParam(':id',$id_reuniao);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
                                
                                //CONVERTE FOTO PARA BASE64
                                // $imagedata = file_get_contents($pauta);                             
                                // $base64 = base64_encode($imagedata);
                            }					
                        }
                    }
                    //  

                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';
                    
                    $caminho = "../uploads/reunioes/";
                   
                    foreach($_FILES as $key => $files)
                    {
                        $files_test = array_filter($files['name']);
                        if(!empty($files_test))
                        {
                            if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
                            if(!empty($files["name"]["ata"]))
                            {
                               
                                $nomeArquivo 	= $files["name"]["ata"];
                                $nomeTemporario = $files["tmp_name"]["ata"];                            
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $ata	= $caminho;
                                $ata .= "ata_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($ata));
                                $imnfo = getimagesize($ata);
                                $img_w = $imnfo[0];	  // largura
                                $img_h = $imnfo[1];	  // altura
                                if($img_w > 500 || $img_h > 500)
                                {
                                    $image = WideImage::load($ata);
                                    $image = $image->resize(500, 500);
                                    $image->saveToFile($ata);
                                }
                                $sql = "UPDATE cadastro_comissoes_reunioes SET 
                                        ata 	 = :ata
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':ata',$ata);
                                $stmt->bindParam(':id',$id_reuniao);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
                                
                                //CONVERTE FOTO PARA BASE64
                                // $imagedata = file_get_contents($ata);                             
                                // $base64 = base64_encode($imagedata);
                            }					
                        }
                    }
                    // 
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
            if($action == 'excluir_reunioes')
            {
                $id_sub = $_GET['id_sub'];
                $sql = "UPDATE FROM cadastro_comissoes_reunioes set ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id_sub);
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
            $fil_nome = $_REQUEST['fil_nome'];
            if($fil_nome == '')
            {
                $nome_query = " 1 = 1 ";
            }
            else
            {
                $fil_nome1 = $fil_nome2 = $fil_nome3 = "%".$fil_nome."%";
                $nome_query = " (cadastro_comissoes.nome LIKE :fil_nome1 ) ";
            }
            $sql = "SELECT *, aux_comissoes_tipos.nome as tipo_nome, 
                              cadastro_comissoes.nome as nome,
                              cadastro_comissoes.sigla as sigla,
                              cadastro_comissoes.id as id
                     FROM cadastro_comissoes 
                    LEFT JOIN aux_comissoes_tipos ON aux_comissoes_tipos.id = cadastro_comissoes.tipo 
                    WHERE ".$nome_query." AND cadastro_comissoes.ativo = :ativo		
                    ORDER BY cadastro_comissoes.id DESC
                    LIMIT :primeiro_registro, :num_por_pagina ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);    
            $stmt->bindParam(':fil_nome1', 	$fil_nome1);
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
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_comissoes/view'>
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
                            <td class='titulo_tabela'>Comissão</td>
                            <td class='titulo_tabela'>Tipo</td>
                            <td class='titulo_tabela'>Data criação</td>
                            <td class='titulo_tabela' align='center'>Status</td>
                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                        </tr>";
                        $c=0;
                        while($result = $stmt->fetch())
                        {
                            $id = $result['id'];
                            $nome = $result['nome'];
                            $sigla = $result['sigla'];
                            $tipo_descricao = $result['tipo_nome'];
                            $data_criacao = reverteData($result['data_criacao']);
                            $status = $result['status'];
                          
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td>$sigla - $nome</td>
                                    <td>$tipo_descricao</td>
                                    <td>$data_criacao</td>
                                    <td align=center>";
                                    if($status == 1)
                                    {
                                        echo "<i class='fas fa-check' style='color: green;'></i>";
                                    }
                                    else
                                    {
                                        echo "<i class='fas fa-times'  style='color: red;'></i>";
                                    }
                                    echo "
                                    </td>
                                    <td align=center>
                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/view/excluir/$id?pag=$pag\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                \">	<i class='far fa-trash-alt'></i>
                                            </div>
                                            <div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/edit/$id?pag=$pag\");'><i class='fas fa-pencil-alt'></i></div>
                                            ";
                                            if($status == 1)
                                            {
                                                echo "<div class='g_status' title='Desativar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/view/desativar/$id?pag=$pag\");'><i class='fas fa-sync-alt'></i></div>";
                                            }
                                            else
                                            {
                                                echo "<div class='g_status' title='Ativar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/view/ativar/$id?pag=$pag\");'><i class='fas fa-sync-alt'></i></div>";
                                            }
                                            echo "
                                            <div class='g_exibir' title='Exibir' onclick='verificaPermissao(".$permissoes["view"].",\"".$pagina_link."/exib/$id?pag=$pag\");'><i class='fas fa-search'></i></div>
                                            
                                    </td>
                                </tr>";
                        }
                        echo "</table>";
                        $cnt = "SELECT COUNT(*) FROM cadastro_comissoes  WHERE ".$nome_query."";
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
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_comissoes/view/adicionar'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                        
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Nome *:</label> <input name='nome' id='nome' placeholder='Nome' class='obg'>
                                <p><label>Sigla *:</label> <input name='sigla' id='sigla' placeholder='Sigla' class='obg'>
                                <p><label>Tipo:</label> <select name='tipo' id='tipo'>
                                    <option value=''>Tipo</option>";
                                    $sql = " SELECT * FROM aux_comissoes_tipos 
                                             WHERE ativo = :ativo
                                             ORDER BY sigla";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);
                                        $stmt->bindValue(':ativo',1);                                     
                                        $stmt->execute();
                                        while($result = $stmt->fetch())
                                        {
                                            echo "<option value='".$result['id']."'>".$result['sigla']." - ".$result['nome']."</option>";
                                        }
                                    echo "
                                </select>     
                                <p><label>Data criação:</label> <input name='data_criacao' placeholder='Data criação' onkeypress='return mascaraData(this,event);'>
                                <p><label>Data extinção:</label> <input name='data_extincao'  placeholder='Data extinção' onkeypress='return mascaraData(this,event);'>
                                <p><label>Unidade deliberativa:</label> <select name='unidade_deliberativa' id='unidade_deliberativa'>
                                    <option value=''>Unidade deliberativa</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>                                    
                                </select>                                            
                                <p><label>Status:</label> 
                                <input type='radio' name='status' value='1' checked> Ativo <br>
                                <input type='radio' name='status' value='0'> Inativo<br>
                            </div>	                                                                        
                        </div>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_comissoes/view'; value='Cancelar'/></center>
                        </center>
                    </div>
                </form>
                ";
            }
            
            if($pagina == 'edit')
            {            		
                $sql = "SELECT *, aux_comissoes_tipos.nome as tipo_nome,
                                  cadastro_comissoes.nome as nome,
                                  cadastro_comissoes.sigla as sigla 
                        FROM cadastro_comissoes 
                        LEFT JOIN aux_comissoes_tipos ON aux_comissoes_tipos.id = cadastro_comissoes.tipo
                        WHERE cadastro_comissoes.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();                                                 
                    $nome = $result['nome'];
                    $sigla   = $result['sigla'];
                    $tipo  = $result['tipo'];
                  
                    $tipo_nome  = $result['tipo_nome'];
                    $data_criacao  = reverteData($result['data_criacao']);
                    $data_extincao  = reverteData($result['data_extincao']);
                    $unidade_deliberativa    = $result['unidade_deliberativa'];
                    $status = $result['status'];                                               
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_comissoes/view/editar/$id'>
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                                                 
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Nome:</label> <input name='nome' id='nome' value='$nome' placeholder='Nome'  class='obg'>
                                <p><label>Sigla:</label> <input name='sigla' id='sigla' value='$sigla' placeholder='Sigla'>
                                <p><label>Tipo:</label> <select name='tipo' id='tipo'>
                                    <option value='$tipo'>$tipo_nome</option>";
                                    $sql = "SELECT * FROM aux_comissoes_tipos 
                                            WHERE ativo = :ativo
                                            ORDER BY sigla";
                                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                                    $stmt->bindValue(':ativo',1);                                                                       								
                                    $stmt->execute();
                                    while($result = $stmt->fetch())
                                    {
                                        echo "<option value='".$result['id']."'>".$result['sigla']." - ".$result['nome']."</option>";
                                    }
                                    echo "
                                </select>
                                <p><label>Data criação:</label> <input name='data_criacao' value='$data_criacao'  placeholder='Data criação' onkeypress='return mascaraData(this,event);'>
                                <p><label>Data extinção:</label> <input name='data_extincao' value='$data_extincao' placeholder='Data criação' onkeypress='return mascaraData(this,event);'>
                                <p><label>Unidade deliberativa:</label> <select name='unidade_deliberativa' id='unidade_deliberativa'>
                                    <option value='$unidade_deliberativa'>$unidade_deliberativa</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>                                    
                                </select>                                            
                                <p><label>Status:</label> ";
                                if($status == 1)
                                {
                                    echo "<input type='radio' name='status' value='1' checked> Ativo <br>
                                        <input type='radio' name='status' value='0'> Inativo
                                        ";
                                }
                                else
                                {
                                    echo "<input type='radio' name='status' value='1'> Ativo <br>
                                        <input type='radio' name='status' value='0' checked> Inativo
                                        ";
                                }
                                echo "                                                        
                            </div>                                                                     				
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_comissoes/view'; value='Cancelar'/></center>
                            </center>
                        </div>
                    </form>
                    ";
                }
            }	
            if($pagina == 'exib')
            {            		
                include("../mod_includes/modal/composicaoAdd.php");
                include("../mod_includes/modal/reunioesAdd.php");
                
                        
                
                $sql = "SELECT *, aux_comissoes_tipos.nome as tipo_nome,
                                  cadastro_comissoes.nome as nome,
                                  cadastro_comissoes.sigla as sigla 
                        FROM cadastro_comissoes 
                        LEFT JOIN aux_comissoes_tipos ON aux_comissoes_tipos.id = cadastro_comissoes.tipo
                        WHERE cadastro_comissoes.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();                                                 
                    $nome = $result['nome'];
                    $sigla   = $result['sigla'];
                    $tipo  = $result['tipo'];
                    $tipo_nome  = $result['tipo_nome'];
                    $data_criacao  = reverteData($result['data_criacao']);
                    $data_extincao  = reverteData($result['data_extincao']);
                    $unidade_deliberativa    = $result['unidade_deliberativa'];
                    $status = $result['status'];                                               
                    echo "
                        <div class='titulo'> $page &raquo; Exibir </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            <li><a data-toggle='tab' href='#composicao' id='composicao-tab'>Composição</a></li>        
                            <li><a data-toggle='tab' href='#reunioes' id='reunioes-tab'>Reuniões</a></li>
                            <li><a data-toggle='tab' href='#materias' id='materias-tab'>Matérias em Tramitação</a></li>                 
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <table width='100%' cellpadding='8'>
                                    <tr>
                                        <td class='bold' align='right' width='10%'>Nome:</td>
                                        <td  width='40%'>$nome</td>
                                        <td class='bold' align='right'  width='10%'>Sigla:</td>
                                        <td  width='40%'>$sigla</td>
                                    </tr>
                                    <tr>
                                        <td class='bold' align='right'>Tipo:</td>
                                        <td  >$tipo_nome</td>
                                        <td class='bold' align='right'>Unidade deliberativa:</td>
                                        <td >$unidade_deliberativa</td>
                                    </tr>
                                    <tr>
                                        <td class='bold' align='right' >Data criação:</td>
                                        <td >$data_criacao</td>
                                        <td class='bold' align='right' >Data extinção:</td>
                                        <td >$data_extincao</td>
                                        
                                    </tr>                                    
                                    <tr>
                                        <td class='bold' align='right' width='11%'>Status:</td>                                        
                                        <td  width='37%' colspan='7'>";
                                        if($status == 1)
                                        {
                                            echo "Ativo";
                                        }
                                        else
                                        {
                                            echo "Inativo";
                                        }
                                        echo "</td>                                      
                                    </tr>                                    
                                </table>                                                                                                                                                    
                            </div>                        
                            <div id='composicao' class='tab-pane fade in'>
                                ";
                                $sql = "SELECT *, cadastro_comissoes_composicao.id as id_composicao
                                        FROM cadastro_comissoes_composicao 
                                        LEFT JOIN aux_comissoes_periodos ON aux_comissoes_periodos.id = cadastro_comissoes_composicao.periodo
                                        LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = cadastro_comissoes_composicao.parlamentar
                                        LEFT JOIN aux_comissoes_cargos ON aux_comissoes_cargos.id = cadastro_comissoes_composicao.cargo
                                        WHERE comissao = :comissao AND cadastro_comissoes_composicao.ativo = :ativo
                                        ORDER BY cadastro_comissoes_composicao.id ASC
                                       ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                $stmt->bindParam(':fil_nome1', 	$fil_nome1);
                                $stmt->bindParam(':comissao', 	$id);    
                                $stmt->bindValue(':ativo', 	1);    
                                $stmt->execute();
                                $rows = $stmt->rowCount();
                                                
                                echo "
                                <div id='botoes'>
                                    <div class='g_adicionar' title='Adicionar' data-toggle='modal' data-target='#composicaoAdd'><i class='fas fa-plus'></i></div>
                                </div>";
                                if ($rows > 0)
                                {
                                    echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela'>Período</td>
                                            <td class='titulo_tabela'>Parlamentar</td>
                                            <td class='titulo_tabela'>Cargo</td>
                                            <td class='titulo_tabela'>Data Designação</td>
                                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                                        </tr>";
                                        $c=0;
                                        while($result = $stmt->fetch())
                                        {
                                            $id_composicao = $result['id_composicao'];
                                            $periodo_id = $result['periodo'];
                                            $data_inicio = reverteData($result['data_inicio']);
                                            $data_fim = reverteData($result['data_fim']);
                                            $cargo_id = $result['cargo'];
                                            $cargo = $result['descricao'];
                                            $parlamentar_id = $result['parlamentar'];
                                            $parlamentar = $result['nome'];
                                            $motivo_desligamento = $result['motivo_desligamento'];
                                            $titular = $result['titular'];
                                            $periodo = $result['numero']." (".$result['data_inicio']." - ".$result['data_fim'].")";
                                            $data_designacao = reverteData($result['data_designacao']);
                                            $data_desligamento = reverteData($result['data_desligamento']);
                                            $observacao = $result['observacao'];
                                            
                                            
                                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                            echo "<tr class='$c1'>
                                                    <td>$data_inicio - $data_fim</td>                                                    
                                                    <td>$parlamentar</td>
                                                    <td>$cargo</td>
                                                    <td>$data_designacao</td>
                                                    <td align=center>
                                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/exib/$id/excluir_composicao/$id_composicao#composicao\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                \">	<i class='far fa-trash-alt'></i>
                                                            </div>
                                                            <div class='g_editar' title='Editar' data-toggle='modal' data-target='#composicaoEdit".$id_composicao."'><i class='fas fa-pencil-alt'></i></div>                                                                                                                        
                                                    </td>
                                                </tr>";
                                                include("../mod_includes/modal/composicaoEdit.php");
                                        }
                                        

                                        echo "</table>";                                        
                                }
                                else
                                {
                                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                }                                
                                echo "
                            </div>
                            <div id='reunioes' class='tab-pane fade in'>
                            ";
                            $sql = "SELECT *, cadastro_comissoes_reunioes.id as id_reuniao
                                            , cadastro_comissoes_reunioes.nome as nome
                                    FROM cadastro_comissoes_reunioes 
                                    LEFT JOIN aux_comissoes_periodos ON aux_comissoes_periodos.id = cadastro_comissoes_reunioes.periodo
                                    WHERE comissao = :comissao
                                    ORDER BY cadastro_comissoes_reunioes.id DESC
                                   ";
                            $stmt = $PDO_PROCLEGIS->prepare($sql);    
                            $stmt->bindParam(':fil_nome1', 	$fil_nome1);
                            $stmt->bindParam(':comissao', 	$id);                                    
                            $stmt->execute();
                            $rows = $stmt->rowCount();
                                            
                            echo "
                            <div id='botoes'>
                                <div class='g_adicionar' title='Adicionar' data-toggle='modal' data-target='#reunioesAdd'><i class='fas fa-plus'></i></div>
                            </div>";
                            if ($rows > 0)
                            {
                                echo "
                                <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                    <tr>
                                        <td class='titulo_tabela'>Período</td>
                                        <td class='titulo_tabela'>Nome</td>
                                        <td class='titulo_tabela'>Data</td>
                                        <td class='titulo_tabela' align='center'>Ata</td>
                                        <td class='titulo_tabela' align='center'>Pauta</td>
                                        <td class='titulo_tabela' align='right'>Gerenciar</td>
                                    </tr>";
                                    $c=0;
                                    while($result = $stmt->fetch())
                                    {
                                        $id_reuniao = $result['id_reuniao'];
                                        $periodo_id = $result['periodo'];
                                        $periodo = $result['numero']." (".$result['data_inicio']." - ".$result['data_fim'].")";
                                        $data_inicio = reverteData($result['data_inicio']);
                                        $data_fim = reverteData($result['data_fim']);
                                        $nome = $result['nome'];
                                        $numero = $result['numero'];
                                        $local = $result['local'];
                                        $data_reuniao = reverteData($result['data_reuniao']);
                                        $hora_inicio = $result['hora_inicio'];
                                        $hora_termino = $result['hora_termino'];
                                        $pauta = $result['pauta'];
                                        $ata = $result['ata'];
                                        $observacao = $result['observacao'];
                                        
                                        
                                        if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                        echo "<tr class='$c1'>
                                                <td>$data_inicio - $data_fim</td>                                                    
                                                <td>$nome</td>
                                                <td>$data_reuniao</td>
                                                <td  align='center'>";
                                                if($ata != "")
                                                {
                                                    echo "<a href='$ata' target='_blank'><i class='fas fa-paperclip'></i></a>";
                                                }
                                                echo "
                                                </td>
                                                <td  align='center'>";
                                                if($pauta != "")
                                                {
                                                    echo "<a href='$pauta' target='_blank'><i class='fas fa-paperclip'></i></a>";
                                                }
                                                echo "
                                                </td>
                                                <td align=center>
                                                        <div class='g_excluir' title='Excluir' onclick=\"
                                                            abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/exib/$id/excluir_reunioes/$id_reuniao#reunioes\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                            \">	<i class='far fa-trash-alt'></i>
                                                        </div>
                                                        <div class='g_editar' title='Editar' data-toggle='modal' data-target='#reunioesEdit".$id_reuniao."'><i class='fas fa-pencil-alt'></i></div>                                                                                                                        
                                                </td>
                                            </tr>";
                                            include("../mod_includes/modal/reunioesEdit.php");
                                    }
                                    

                                    echo "</table>";                                        
                            }
                            else
                            {
                                echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                            }                                
                            echo "
                            </div>
                            <div id='materias' class='tab-pane fade in'>
                                ";
                                $sql = "SELECT *,                                         
                                        cadastro_comissoes_dependentes.id as id_dependentes
                                        FROM cadastro_comissoes_dependentes 
                                        LEFT JOIN aux_comissoes_tipos_dependentes ON aux_comissoes_tipos_dependentes.id = cadastro_comissoes_dependentes.tipo_dependente
                                        WHERE parlamentar = :parlamentar
                                        ORDER BY cadastro_comissoes_dependentes.id DESC
                                       ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                $stmt->bindParam(':fil_nome1', 	$fil_nome1);
                                $stmt->bindParam(':parlamentar', 	$id);                                    
                                $stmt->execute();
                                $rows = $stmt->rowCount();
                                                
                                echo "
                                <div id='botoes'>
                                    <div class='g_adicionar' title='Adicionar' data-toggle='modal' data-target='#dependentesAdd'><i class='fas fa-plus'></i></div>
                                </div>";
                                if ($rows > 0)
                                {
                                    echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela'>Tipo de dependente</td>
                                            <td class='titulo_tabela'>Nome</td>
                                            <td class='titulo_tabela'>Sexo</td>
                                            <td class='titulo_tabela'>Data nasc.</td>
                                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                                        </tr>";
                                        $c=0;
                                        while($result = $stmt->fetch())
                                        {
                                            $id_dependentes = $result['id_dependentes'];
                                            $tipo_dependente = $result['tipo_dependente'];
                                            $descricao = $result['descricao'];
                                            $nome = $result['nome'];
                                            $sexo = $result['sexo'];                                                                                                                               
                                            $data_criacao = reverteData($result['data_criacao']);                                                                                        
                                            
                                            
                                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                            echo "<tr class='$c1'>
                                                    <td>$descricao</td>
                                                    <td>$nome</td>
                                                    <td>$sexo</td>
                                                    <td>$data_criacao</td>                                                    
                                                    <td align=center>
                                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/exib/$id/excluir_dependentes/$id_dependentes#dependentes\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                \">	<i class='far fa-trash-alt'></i>
                                                            </div>
                                                            <div class='g_editar' title='Editar' data-toggle='modal' data-target='#dependentesEdit".$id_dependentes."'><i class='fas fa-pencil-alt'></i></div>                                                                                                                        
                                                    </td>
                                                </tr>";
                                                include("../mod_includes/modal/dependentesEdit.php");
                                        }
                                        

                                        echo "</table>";                                        
                                }
                                else
                                {
                                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                }                                
                                echo "
                            </div>         
                            <br>               				
                            <center>                                                        
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_comissoes/view'; value='Voltar'/></center>
                            </center>
                        </div>
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
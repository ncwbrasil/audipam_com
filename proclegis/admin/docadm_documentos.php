<?php
$pagina_link = 'docadm_documentos';
include_once("../../core/mod_includes/php/funcoes.php");
include_once("../../core/mod_includes/php/funcoes_certificado.php");
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
            $page = "Cadastro &raquo; <a href='docadm_documentos/view'>Documentos Administrativos</a>";
            if(isset($_GET['id'])){$id = $_GET['id'];}
            $tipo  = $_POST['tipo'];
            $classificacao  = $_POST['classificacao'];
            $numero   = $_POST['numero'];
            $ano   = $_POST['ano'];
            $data  = reverteData($_POST['data']);if($data == ""){ $data  = null;}
            $interessado   = $_POST['interessado'];
            $descricao   = $_POST['descricao'];
            $em_tramitacao   = $_POST['em_tramitacao'];
            $regime_tramitacao   = $_POST['regime_tramitacao'];
            $restrito    = $_POST['restrito'];
            

            $dados = array(                
                'tipo' 		    => $tipo,
                'classificacao' => $classificacao,
                'numero' 		=> $numero,
                'ano' 		    => $ano,
                'data' 		    => $data,
                'interessado' 	=> $interessado,
                'descricao' 	=> $descricao,
                'em_tramitacao' => $em_tramitacao,
                'regime_tramitacao' => $regime_tramitacao,
                'restrito' 		=> $restrito,
                'cadastrado_por'=> $_SESSION['usuario_id'],                
                );
        
            if($action == "adicionar")
            {                                   
                $sql = "INSERT INTO docadm_documentos SET ".bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                if($stmt->execute($dados))
                {		
                    $id = $PDO_PROCLEGIS->lastInsertId();

                    
                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';                    
                    $caminho = "../uploads/documentos_adm/";                                      
                    foreach($_FILES as $key => $files)
                    {
                        $files_test = array_filter($files['name']);
                        if(!empty($files_test))
                        {
                            if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
                            if(!empty($files["name"]["texto_original"]))
                            {
                               
                                $nomeArquivo 	= $files["name"]["texto_original"];
                                $nomeTemporario = $files["tmp_name"]["texto_original"];                            
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $texto_original	= $caminho;
                                $texto_original .= "texto_original_".md5(mt_rand(1,10000).date('YmdHis').$nomeArquivo).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($texto_original));
                                $imnfo = getimagesize($texto_original);
                                $img_w = $imnfo[0];	  // largura
                                $img_h = $imnfo[1];	  // altura
                                if($img_w > 500 || $img_h > 500)
                                {
                                    $image = WideImage::load($texto_original);
                                    $image = $image->resize(500, 500);
                                    $image->saveToFile($texto_original);
                                }
                                
                               
                                $sql = "UPDATE docadm_documentos SET 
                                        texto_original 	 = :texto_original
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':texto_original',$texto_original);
                                $stmt->bindParam(':id',$id);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
                                
                                //CONVERTE FOTO PARA BASE64
                                // $imagedata = file_get_contents($texto_original);                             
                                // $base64 = base64_encode($imagedata);
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
                $dados['alterado_por'] = $_SESSION['usuario_id'];
                $dados['data_alteracao'] = date("Y-m-d H:i:s");
                
                $sql = "UPDATE docadm_documentos SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id;
                if($stmt->execute($dados))
                {
                        
                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';                    
                    $caminho = "../uploads/documentos_adm/";                                      
                    foreach($_FILES as $key => $files)
                    {
                        $files_test = array_filter($files['name']);
                        if(!empty($files_test))
                        {
                            if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
                            if(!empty($files["name"]["texto_original"]))
                            {
                               
                                $nomeArquivo 	= $files["name"]["texto_original"];
                                $nomeTemporario = $files["tmp_name"]["texto_original"];                            
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $texto_original	= $caminho;
                                $texto_original .= "texto_original_".md5(mt_rand(1,10000).date('YmdHis').$nomeArquivo).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($texto_original));
                                $imnfo = getimagesize($texto_original);
                                $img_w = $imnfo[0];	  // largura
                                $img_h = $imnfo[1];	  // altura
                                if($img_w > 500 || $img_h > 500)
                                {
                                    $image = WideImage::load($texto_original);
                                    $image = $image->resize(500, 500);
                                    $image->saveToFile($texto_original);
                                }

                                $sql = "UPDATE docadm_documentos SET 
                                        texto_original 	 = :texto_original
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':texto_original',$texto_original);
                                $stmt->bindParam(':id',$id);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
                                
                                //CONVERTE FOTO PARA BASE64
                                // $imagedata = file_get_contents($texto_original);                             
                                // $base64 = base64_encode($imagedata);
                            }					
                        }
                    }
                    //  

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
                $sql = "SELECT texto_original FROM docadm_documentos WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id);
                if($stmt->execute())
                {
                    $result = $stmt->fetch();
                    $texto_original = $result['texto_original'];
                }

                $sql = "DELETE FROM docadm_documentos WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id);
                if($stmt->execute())
                {
                    unlink($texto_original);

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
                $sql = "UPDATE docadm_documentos SET status = :status WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':status',1);
                $stmt->bindParam(':id',$id);
                $stmt->execute();
            }
            if($action == 'desativar')
            {
                $sql = "UPDATE docadm_documentos SET status = :status WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':status',0);
                $stmt->bindParam(':id',$id);
                $stmt->execute();
            }
            if($action == "adicionar_anexada")
            {        
                               
                $tipo_documento   = $_POST['tipo_documento'];
                $documento_anexado   = $_POST['documento_anexado'];
                $data_anexacao  = reverteData($_POST['data_anexacao']);
                $data_desanexacao  = reverteData($_POST['data_desanexacao']);if($data_desanexacao == ""){$data_desanexacao = null;}
                
                $dados = array(
                    'documento' 		=> $id,
                    'tipo_documento' 		    => $tipo_documento,
                    'documento_anexado' 		    => $documento_anexado,
                    'data_anexacao' 		        => $data_anexacao,
                    'data_desanexacao' 		        => $data_desanexacao
                    );
                $sql = "INSERT INTO docadm_documentos_anexados SET ".bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                if($stmt->execute($dados))
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
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php 
                }	
            }
            if($action == "editar_anexada")
            {                       
                $id_anexado   = $_POST['id_anexado'];
                $tipo_documento   = $_POST['tipo_documento'];
                $documento_anexado   = $_POST['documento_anexado'];
                $data_anexacao  = reverteData($_POST['data_anexacao']);
                $data_desanexacao  = reverteData($_POST['data_desanexacao']);if($data_desanexacao == ""){$data_desanexacao = null;}
                
                $dados = array(
                    'documento' 		=> $id,
                    'tipo_documento' 		    => $tipo_documento,
                    'documento_anexado' 		    => $documento_anexado,
                    'data_anexacao' 		        => $data_anexacao,
                    'data_desanexacao' 		        => $data_desanexacao
                    );

                    
                $sql = "UPDATE docadm_documentos_anexados SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id_anexado;
                if($stmt->execute($dados))
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
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php 
                }	
            }
            if($action == 'excluir_anexada')
            {
                $id_sub = $_GET['id_sub'];
                $sql = "DELETE FROM docadm_documentos_anexados WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id_sub);
                if($stmt->execute())
                {
                    unlink($foto_antiga);

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
            if($action == "adicionar_doc_acessorio")
            {            
                           
                $tipo_documento   = $_POST['tipo_documento'];
                $nome   = $_POST['nome'];
                $autor   = $_POST['autor'];if($autor == ""){$autor = null;}
                $data  = reverteData($_POST['data']);
                $ementa   = $_POST['ementa'];if($ementa == ""){$ementa = null;}

                $dados = array_filter(array(
                    'documento' 		=> $id,
                    'tipo_documento' 		    => $tipo_documento,
                    'nome' 		    => $nome,
                    'autor' 		        => $autor,
                    'data' 		        => $data,
                    'ementa' 		        => $ementa
                    ));
                $sql = "INSERT INTO docadm_documentos_doc_acessorio SET ".bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                if($stmt->execute($dados))
                {		
                    $id_doc_acessorio = $PDO_PROCLEGIS->lastInsertId();

                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';                    
                    $caminho = "../uploads/doc_acessorio/";                                      
                    foreach($_FILES as $key => $files)
                    {
                        $files_test = array_filter($files['name']);
                        if(!empty($files_test))
                        {
                            if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
                            if(!empty($files["name"]["anexo"]))
                            {
                               
                                $nomeArquivo 	= $files["name"]["anexo"];
                                $nomeTemporario = $files["tmp_name"]["anexo"];                            
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $anexo	= $caminho;
                                $anexo .= "anexo_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($anexo));
                                // $imnfo = getimagesize($anexo);
                                // $img_w = $imnfo[0];	  // largura
                                // $img_h = $imnfo[1];	  // altura
                                // if($img_w > 500 || $img_h > 500)
                                // {
                                //     $image = WideImage::load($anexo);
                                //     $image = $image->resize(500, 500);
                                //     $image->saveToFile($anexo);
                                // }

                                $sql = "UPDATE docadm_documentos_doc_acessorio SET 
                                        anexo 	 = :anexo
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':anexo',$anexo);
                                $stmt->bindParam(':id',$id_doc_acessorio);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
                                
                                //CONVERTE FOTO PARA BASE64
                                // $imagedata = file_get_contents($texto_original);                             
                                // $base64 = base64_encode($imagedata);
                            }					
                        }
                    }
                    //  

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
            if($action == "editar_doc_acessorio")
            {                       
                $id_doc_acessorio   = $_POST['id_doc_acessorio'];
                $tipo_documento   = $_POST['tipo_documento'];
                $nome   = $_POST['nome'];
                $autor   = $_POST['autor'];if($autor == ""){$autor = null;}
                $data  = reverteData($_POST['data']);
                $ementa   = $_POST['ementa'];if($ementa == ""){$ementa = null;}
               
                $dados = array(
                    'documento' 		=> $id,
                    'tipo_documento' 		    => $tipo_documento,
                    'nome' 		    => $nome,
                    'autor' 		        => $autor,
                    'data' 		        => $data,
                    'ementa' 		        => $ementa
                    );

                    
                $sql = "UPDATE docadm_documentos_doc_acessorio SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id_doc_acessorio;
                if($stmt->execute($dados))
                {		
                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';                    
                    $caminho = "../uploads/doc_acessorio/";                                      
                    foreach($_FILES as $key => $files)
                    {
                        $files_test = array_filter($files['name']);
                        if(!empty($files_test))
                        {
                            if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
                            if(!empty($files["name"]["anexo"]))
                            {
                               
                                $nomeArquivo 	= $files["name"]["anexo"];
                                $nomeTemporario = $files["tmp_name"]["anexo"];                            
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $anexo	= $caminho;
                                $anexo .= "anexo_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($anexo));
                                // $imnfo = getimagesize($anexo);
                                // $img_w = $imnfo[0];	  // largura
                                // $img_h = $imnfo[1];	  // altura
                                // if($img_w > 500 || $img_h > 500)
                                // {
                                //     $image = WideImage::load($anexo);
                                //     $image = $image->resize(500, 500);
                                //     $image->saveToFile($anexo);
                                // }

                                $sql = "UPDATE docadm_documentos_doc_acessorio SET 
                                        anexo 	 = :anexo
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':anexo',$anexo);
                                $stmt->bindParam(':id',$id_doc_acessorio);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
                                
                                //CONVERTE FOTO PARA BASE64
                                // $imagedata = file_get_contents($texto_original);                             
                                // $base64 = base64_encode($imagedata);
                            }					
                        }
                    }
                    //  
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
            if($action == 'excluir_doc_acessorio')
            {
                $id_sub = $_GET['id_sub'];

                $sql = "SELECT anexo FROM docadm_documentos_doc_acessorio WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id_sub);
                if($stmt->execute())
                {
                    $result = $stmt->fetch();
                    $anexo = $result['anexo'];
                }


                $sql = "DELETE FROM docadm_documentos_doc_acessorio WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id_sub);
                if($stmt->execute())
                {
                    unlink($anexo);

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
            if($action == "adicionar_tramitacao")
            {                       
                $unidade_origem   = $_POST['unidade_origem'];
                $unidade_destino   = $_POST['unidade_destino'];
                $data_tramitacao  = reverteData($_POST['data_tramitacao']);
                $hora_tramitacao  = $_POST['hora_tramitacao'];
                $data_encaminhamento  = reverteData($_POST['data_encaminhamento']);if($data_encaminhamento == ""){$data_encaminhamento = null;}
                $data_fim_prazo  = reverteData($_POST['data_fim_prazo']);if($data_fim_prazo == ""){$data_fim_prazo = null;}
                $status_tramitacao  = $_POST['status_tramitacao'];
                $urgente            = $_POST['urgente'];
                $texto_acao         = $_POST['texto_acao'];
                $responsavel         = $_POST['responsavel'];
                
                $dados = array(
                    'documento' 		=> $id,
                    'unidade_origem' 		    => $unidade_origem,
                    'unidade_destino' 		    => $unidade_destino,
                    'data_tramitacao' 		        => $data_tramitacao,
                    'hora_tramitacao' 		        => $hora_tramitacao,
                    'data_encaminhamento' 		        => $data_encaminhamento,
                    'data_fim_prazo' 		        => $data_fim_prazo,
                    'status_tramitacao' 		        => $status_tramitacao,
                    'urgente' 		        => $urgente,
                    'texto_acao' 		        => $texto_acao,
                    'responsavel' 		        => $responsavel
                    );
                $sql = "INSERT INTO docadm_documentos_tramitacao SET ".bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                if($stmt->execute($dados))
                {		
                    $id_tramitacao = $PDO_PROCLEGIS->lastInsertId();
                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';                    
                    $caminho = "../uploads/docadm_tramitacao/$id_tramitacao/";                                      
                    foreach($_FILES as $key => $files)
                    {
                        $files_test = array_filter($files['name']);
                        if(!empty($files_test))
                        {
                            if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
                            if(!empty($files["name"]["anexo"]))
                            {
                               
                                $nomeArquivo 	= $files["name"]["anexo"];
                                $nomeTemporario = $files["tmp_name"]["anexo"];                            
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $anexo	= $caminho;
                                $anexo .= "anexo_".md5(mt_rand(1,10000).$nomeArquivo.date('YmdHis')).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($anexo));
                                // $imnfo = getimagesize($anexo);
                                // $img_w = $imnfo[0];	  // largura
                                // $img_h = $imnfo[1];	  // altura
                                // if($img_w > 500 || $img_h > 500)
                                // {
                                //     $image = WideImage::load($anexo);
                                //     $image = $image->resize(500, 500);
                                //     $image->saveToFile($anexo);
                                // }

                                $sql = "UPDATE docadm_documentos_tramitacao SET 
                                        anexo 	 = :anexo
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':anexo',$anexo);
                                $stmt->bindParam(':id',$id_tramitacao);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
                                
                                //CONVERTE FOTO PARA BASE64
                                // $imagedata = file_get_contents($texto_original);                             
                                // $base64 = base64_encode($imagedata);
                            }					
                        }
                    }
                    //  

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
            if($action == "editar_tramitacao")
            {                       
                $id_tramitacao   = $_POST['id_tramitacao'];
                $unidade_origem   = $_POST['unidade_origem'];
                $unidade_destino   = $_POST['unidade_destino'];
                $data_tramitacao  = reverteData($_POST['data_tramitacao']);
                $hora_tramitacao  = $_POST['hora_tramitacao'];
                $data_encaminhamento  = reverteData($_POST['data_encaminhamento']);if($data_encaminhamento == ""){$data_encaminhamento = null;}
                $data_fim_prazo  = reverteData($_POST['data_fim_prazo']);if($data_fim_prazo == ""){$data_fim_prazo = null;}
                $status_tramitacao  = $_POST['status_tramitacao'];
                $urgente            = $_POST['urgente'];
                $texto_acao         = $_POST['texto_acao'];
                $responsavel         = $_POST['responsavel'];
                
                
                $dados = array(
                    'documento' 		=> $id,
                    'unidade_origem' 		    => $unidade_origem,
                    'unidade_destino' 		    => $unidade_destino,
                    'data_tramitacao' 		        => $data_tramitacao,
                    'hora_tramitacao' 		        => $hora_tramitacao,
                    'data_encaminhamento' 		        => $data_encaminhamento,
                    'data_fim_prazo' 		        => $data_fim_prazo,
                    'status_tramitacao' 		        => $status_tramitacao,
                    'urgente' 		        => $urgente,
                    'texto_acao' 		        => $texto_acao,
                    'responsavel' 		        => $responsavel
                    );

                    
                $sql = "UPDATE docadm_documentos_tramitacao SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id_tramitacao;
                if($stmt->execute($dados))
                {		
                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';                    
                    $caminho = "../uploads/docadm_tramitacao/$id_tramitacao/";                                      
                    foreach($_FILES as $key => $files)
                    {
                        $files_test = array_filter($files['name']);
                        if(!empty($files_test))
                        {
                            if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
                            if(!empty($files["name"]["anexo"]))
                            {
                               
                                $nomeArquivo 	= $files["name"]["anexo"];
                                $nomeTemporario = $files["tmp_name"]["anexo"];                            
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $anexo	= $caminho;
                                $anexo .= "anexo_".md5(mt_rand(1,10000).$nomeArquivo.date('YmdHis')).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($anexo));
                                // $imnfo = getimagesize($anexo);
                                // $img_w = $imnfo[0];	  // largura
                                // $img_h = $imnfo[1];	  // altura
                                // if($img_w > 500 || $img_h > 500)
                                // {
                                //     $image = WideImage::load($anexo);
                                //     $image = $image->resize(500, 500);
                                //     $image->saveToFile($anexo);
                                // }

                                $sql = "UPDATE docadm_documentos_tramitacao SET 
                                        anexo 	 = :anexo
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':anexo',$anexo);
                                $stmt->bindParam(':id',$id_tramitacao);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
                                
                                //CONVERTE FOTO PARA BASE64
                                // $imagedata = file_get_contents($texto_original);                             
                                // $base64 = base64_encode($imagedata);
                            }					
                        }
                    }
                    //

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
            if($action == 'excluir_tramitacao')
            {
                $id_sub = $_GET['id_sub'];
                $sql = "DELETE FROM docadm_documentos_tramitacao WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id_sub);
                if($stmt->execute())
                {
                    unlink($foto_antiga);

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
            if($action == 'confirmar_assinatura')
            {              
               
                $sql = "SELECT *
                        FROM docadm_documentos
                        WHERE id = :id                      
                    ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);                    
                $stmt->bindParam(':id', 	$id);                                    
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {                   
                    while($result = $stmt->fetch())
                    {
                        $anexo = $result['texto_original'];           
                        
                        //PEGA DADOS DA ASSINATURA DO USUÁRIO, SE ELE TIVER
                        $sql = "SELECT * FROM cadastro_usuarios WHERE usu_id = :usu_id";
                        $stmt_usu = $PDO_PROCLEGIS->prepare($sql);                                                
                        $stmt_usu->bindParam(':usu_id', 	$_SESSION['usuario_id']);                                    
                        if($stmt_usu->execute())
                        {                                                                                                      
                            $result_usu = $stmt_usu->fetch();
                            $cert = $result_usu['usu_certificado'];
                            $pass = $result_usu['usu_cert_senha'];                            
                            $certificado = recuperaDadosCertificado($cert, $pass);                            
                            $validTo = substr($certificado['validTo'],0,-1);
                            $validade = date("Y-m-d H:i:s", strtotime("20".substr($validTo,0,2)."-".substr($validTo,2,2)."-".substr($validTo,4,2)." ".substr($validTo,6,2).":".substr($validTo,8,2).":".substr($validTo,10,2)));
                            if($validade < $data_ass)
                            {
                                ?>
                                <script>
                                    mensagem("X","<i class='fas fa-exclamation-circle'></i> Seu certificado digital está expirado, contate o administrador do sistema");
                                </script>
                                <?php                 
                            }
                            else
                            {                                    
                                $dados_ass = $certificado['subject']['CN'];
                                $data_ass  = date("d/m/Y H:i:s");
                                
                                // PEGA QTD DE ASSINATURAS
                                $sql = "SELECT * FROM docadm_documentos_assinaturas 
                                        WHERE documento = :documento ";
                                $stmt_qtd = $PDO_PROCLEGIS->prepare($sql);
                                $stmt_qtd->bindParam(':documento',$id);  
                                $stmt_qtd->execute();  
                                $rows_qtd = $stmt_qtd->rowCount();                                                                                                                                                                                                                   
                              
                                $retorno = array();                        
                                $retorno = assinaDocumento($cert, $pass, $anexo, $dados_ass, $data_ass, $rows_qtd); 
                                                            
                                if($retorno['result'] == "Documento assinado com sucesso!")
                                {        
                                    
                                    $nome_anexo = end(explode("/",$retorno['file']));

                                    $sql = "UPDATE docadm_documentos SET 
                                            texto_original           = :texto_original
                                            WHERE id = :id ";
                                    $stmt = $PDO_PROCLEGIS->prepare($sql);                                    
                                    $stmt->bindParam(':texto_original',$retorno['file']);
                                    $stmt->bindParam(':nome_anexo',$nome_anexo);
                                    //$stmt->bindParam(':nome_documento',$retorno['file']);
                                    $stmt->bindValue(':confirmacao_recebimento',1);
                                    $stmt->bindValue(':usu_recebimento',$_SESSION['usuario_id']);
                                    $stmt->bindParam(':id',$id);
                                    $stmt->execute();
                                    if($stmt->execute())
                                    {                   
                                                             
                                        $sql = "INSERT INTO docadm_documentos_assinaturas SET 
                                                        documento           = :documento, 
                                                        credenciais      = :credenciais,                                             
                                                        assinado = :assinado ";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);
                                        $stmt->bindParam(':documento',$id);
                                        $cred = $dados_ass. " - ". $data_ass;
                                        $stmt->bindParam(':credenciais',$cred);
                                        $stmt->bindValue(':assinado',1);                                                                                                                                                                
                                        if($stmt->execute())
                                        {
                                            
                                        }
                                        log_operacao($id, $PDO_PROCLEGIS);

                                        ?>
                                        <script>
                                            mensagem("Ok","<i class='fas fa-check-circle'></i> Documento recebido e assinado com sucesso!");
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
                                else
                                {
                                    ?>
                                    <script>
                                        mensagem("X","<i class='fa fa-exclamation-circle'></i> <?php echo $retorno['result'];?> ");
                                    </script>
                                    <?php
                                }

                            }                        
                        }
                    }
                }                                           
            }

            $num_por_pagina = 10;
            if(!$pag){$primeiro_registro = 0; $pag = 1;}
            else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
            $fil_tipo = $_REQUEST['fil_tipo'];
            if($fil_tipo == '')
            {
                $tipo_query = " 1 = 1 ";
            }
            else
            {
                $tipo_query = " (docadm_documentos.tipo = :fil_tipo) ";
            }
            $fil_classificacao = $_REQUEST['fil_classificacao'];
            if($fil_classificacao == '')
            {
                $classificacao_query = " 1 = 1 ";
            }
            else
            {
                $classificacao_query = " (docadm_documentos.classificacao = :fil_classificacao) ";
            }
            $fil_numero = $_REQUEST['fil_numero'];
            if($fil_numero == '')
            {
                $numero_query = " 1 = 1 ";
            }
            else
            {
                $numero_query = " (docadm_documentos.numero = :fil_numero) ";
            }
            $fil_ano = $_REQUEST['fil_ano'];
            if($fil_ano == '')
            {
                $ano_query = " 1 = 1 ";
            }
            else
            {
                $ano_query = " (docadm_documentos.ano = :fil_ano) ";
            }
            
            $sql = "SELECT *, aux_administrativo_tipo_documento.nome as tipo_nome,
                              aux_administrativo_tipo_documento.sigla as tipo_sigla,
                              docadm_documentos.id as id
                     FROM docadm_documentos 
                    LEFT JOIN aux_administrativo_tipo_documento ON aux_administrativo_tipo_documento.id = docadm_documentos.tipo  
                    LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = docadm_documentos.cadastrado_por  
                    WHERE ".$classificacao_query." AND  ".$tipo_query." AND  ".$numero_query." AND  ".$ano_query." AND ( restrito = 'Não' OR ( restrito = 'Sim' AND cadastrado_por = ".$_SESSION['usuario_id'].") )
                    GROUP BY docadm_documentos.id			
                    ORDER BY docadm_documentos.id DESC
                    LIMIT :primeiro_registro, :num_por_pagina ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);    
            $stmt->bindParam(':fil_tipo', 	$fil_tipo);                
            $stmt->bindParam(':fil_classificacao', 	$fil_classificacao);                
            $stmt->bindParam(':fil_autor', 	$fil_autor);                
            $stmt->bindParam(':fil_numero', 	$fil_numero);                
            $stmt->bindParam(':fil_ano', 	$fil_ano);                
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
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='docadm_documentos/view'>
                        <select name='fil_tipo' id='fil_tipo'>
                            <option  value=''>Tipo de Documento</option>
                            ";
                            
                            $sql = " SELECT * FROM aux_administrativo_tipo_documento 

                                    ORDER BY nome";
                            $stmt_filtro = $PDO_PROCLEGIS->prepare($sql);
                                                                
                            $stmt_filtro->execute();
                            while($result_filtro = $stmt_filtro->fetch())
                            {
                                echo "<option value='".$result_filtro['id']."' ";if($_REQUEST['fil_tipo'] == $result_filtro['id']) echo " selected "; echo ">".$result_filtro['nome']."</option>";
                            }                        
                            echo "
                        </select>
                        <select name='fil_classificacao' id='fil_classificacao'>
                            <option  value=''>Classificação</option>
                            ";
                            
                            $sql = " SELECT * FROM aux_administrativo_classificacao
                                    ORDER BY nome";
                            $stmt_filtro = $PDO_PROCLEGIS->prepare($sql);
                                                                
                            $stmt_filtro->execute();
                            while($result_filtro = $stmt_filtro->fetch())
                            {
                                echo "<option value='".$result_filtro['id']."' ";if($_REQUEST['fil_classifacacao'] == $result_filtro['id']) echo " selected "; echo ">".$result_filtro['nome']."</option>";
                            }                        
                            echo "
                        </select>
                        <input name='fil_numero' id='fil_numero' value='$fil_numero' placeholder='Número'>
                        <input name='fil_ano' id='fil_ano' value='$fil_ano' placeholder='Ano'>                        
                        <input type='submit' value='Filtrar'> 
                        </form>            
                    </div>    
                </div>
                ";
                if ($rows > 0)
                {
                    echo "
                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                        ";
                        $c=0;
                        while($result = $stmt->fetch())
                        {
                            $id = $result['id'];
                            $texto_original = $result['texto_original']; 
                            
                            //PEGA DADOS DA ASSINATURA DO USUÁRIO, SE ELE TIVER
                            $sql = "SELECT * FROM cadastro_usuarios WHERE usu_id = :usu_id";
                            $stmt_usu = $PDO_PROCLEGIS->prepare($sql);                                                
                            $stmt_usu->bindParam(':usu_id', 	$_SESSION['usuario_id']);                                    
                            if($stmt_usu->execute())
                            {                                                
                                $result_usu = $stmt_usu->fetch();
                                $cert = $result_usu['usu_certificado'];
                                $pass = $result_usu['usu_cert_senha'];
                            
                                $certificado = recuperaDadosCertificado($cert, $pass);
                                $validTo = substr($certificado['validTo'],0,-1);
                                $validade = date("Y-m-d H:i:s", strtotime("20".substr($validTo,0,2)."-".substr($validTo,2,2)."-".substr($validTo,4,2)." ".substr($validTo,6,2).":".substr($validTo,8,2).":".substr($validTo,10,2)));
                                
                                if($validade < date("Y-m-d"))
                                {
                                    $dados_ass = "Seu certificado digital está expirado, contate o administrador do sistema";
                                    $perm_ass = 0;
                                }
                                else
                                {    
                                    $dados_ass = $certificado['subject']['CN'];
                                    $data_ass  = date("d/m/Y H:i:s");
                                    $perm_ass = "1";
                                }                        
                            }

                            //PEGA ASSINATURAS
                            $sql = "SELECT * FROM docadm_documentos_assinaturas 
                                    WHERE documento = :documento AND assinado = :assinado";
                            $stmt_usu = $PDO_PROCLEGIS->prepare($sql);                                                
                            $stmt_usu->bindParam(':documento', $id); 
                            $stmt_usu->bindValue(':assinado', 1); 
                            $stmt_usu->execute();
                            $rows_usu = $stmt_usu->rowCount();
                            $credenciais="";
                            if($rows_usu > 0)
                            {                                                
                                while($result_usu = $stmt_usu->fetch())
                                {
                                    $credenciais .= $result_usu['credenciais']."\n";
                                }                                                                      
                            }

                            // TRAMITACAO
                            $sql = "SELECT *, docadm_documentos_tramitacao.id as id_tramitacao
                                            , aux_administrativo_status_tramitacao.nome as nome_status                                                   
                                            , cadastro_usuarios.usu_nome as nome_responsavel                                               
                                    FROM docadm_documentos_tramitacao 
                                    LEFT JOIN aux_materias_unidade_tramitacao t1 ON t1.id = docadm_documentos_tramitacao.unidade_origem
                                    LEFT JOIN aux_materias_unidade_tramitacao t2 ON t2.id = docadm_documentos_tramitacao.unidade_destino                                        
                                    LEFT JOIN aux_administrativo_status_tramitacao ON aux_administrativo_status_tramitacao.id = docadm_documentos_tramitacao.status_tramitacao  
                                    LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = docadm_documentos_tramitacao.responsavel                                                                                          
                                    WHERE documento = :documento
                                    ORDER BY docadm_documentos_tramitacao.data_tramitacao ASC
                                    ";
                            $stmt_status = $PDO_PROCLEGIS->prepare($sql);                                    
                            $stmt_status->bindParam(':documento', 	$id);                                    
                            $stmt_status->execute();
                            $rows_status = $stmt_status->rowCount();

                            $nome_status = $unidade_destino = $destino = $data_tramitacao ="";       

                            if ($rows_status > 0)
                            {
                               
                                while($result_status = $stmt_status->fetch())
                                {
                                    $hora_tramitacao = substr($result_status['hora_tramitacao'],0,5);
                                    $data_tramitacao = reverteData($result_status['data_tramitacao']);
                                    $unidade_destino = $result_status['unidade_destino'];
                                    // PEGA DADOS DA UNIDADE DESTINO
                                    $sql = "SELECT *, aux_materias_orgaos.sigla as sigla_orgao
                                                    , aux_materias_orgaos.nome as nome_orgao
                                                    , cadastro_comissoes.sigla as sigla_comissao
                                                    , cadastro_comissoes.nome as nome_comissao
                                                    , cadastro_parlamentares.nome as nome_parlamentar
                                                    
                                            FROM aux_materias_unidade_tramitacao
                                            LEFT JOIN aux_materias_orgaos ON aux_materias_orgaos.id = aux_materias_unidade_tramitacao.orgao
                                            LEFT JOIN cadastro_comissoes ON cadastro_comissoes.id = aux_materias_unidade_tramitacao.comissao
                                            LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_materias_unidade_tramitacao.parlamentar
                                            WHERE aux_materias_unidade_tramitacao.id = :id
                                        ";
                                    $stmt_destino = $PDO_PROCLEGIS->prepare($sql);                                                
                                    $stmt_destino->bindParam(':id', 	$unidade_destino);                                    
                                    if($stmt_destino->execute())
                                    {
                                        $result_destino = $stmt_destino->fetch();
                                        $destino = $result_destino['nome_parlamentar'].$result_destino['sigla_orgao']." ".$result_destino['nome_orgao'].$result_destino['sigla_comissao']." ".$result_destino['nome_comissao'];
                                    }

                                    
                                    $nome_status = $result_status['nome_status'];
                                }
                            }

                            // ASSINATURA DIGITAL
                            if($texto_original != '' && $credenciais != '')
                            {       
                                $n_assinado = 0;                                                   
                                $assinatura = "<i class='fas fa-file-signature hand' style='color:green; font-size:22px;' data-toggle='tooltip' data-placement='bottom'  title='".$credenciais."'></i><br>";                                                           
                            }  
                            elseif($texto_original != '' && $credenciais == '')
                            {     
                                $n_assinado = 1;
                                $assinatura =  "<span style='color:red; font-weight:bold;'>Não assinado</span>";                                                     
                                //echo "<i class='fas fa-file-signature hand' style='color:red; font-size:22px;' data-placement='bottom'></i>";                                                           
                            }  
                              

                            // PROTOCOLO
                            $sql = "SELECT *, protocolo_gerais.id as id_protocolo                                              
                                    FROM protocolo_gerais 
                                    LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = protocolo_gerais.cadastrado_por                                                                                          
                                    WHERE protocolo_gerais.documento = :documento
                                    
                                    ";
                            $stmt_status = $PDO_PROCLEGIS->prepare($sql);                                    
                            $stmt_status->bindParam(':documento', 	$id);                                    
                            $stmt_status->execute();
                            $rows_status = $stmt_status->rowCount();

                            $protocolo = $numero = $ano = "";       

                            if ($rows_status > 0 && $n_assinado == 0)
                            {                          
                                while($result_status = $stmt_status->fetch())
                                {
                                    $id_protocolo = $result_status['id_protocolo'];
                                    $numero = str_pad($result_status['numero'],6,"0",STR_PAD_LEFT);
                                    $ano = $result_status['ano'];                                   
                                }
                                if($numero != "" && $ano != "")
                                {
                                    $protocolo = "<a href='protocolo_gerais/exib/".$id_protocolo."'>".$numero. " / ". $ano."</a>";
                                }
                            }
                            elseif($rows_status == 0 && $n_assinado == 0)
                            {
                                $protocolo = "<a href='protocolo_gerais/add_documentos/".$id."' class='red'>Documento ainda não protocolado.</a>";
                            }
                            else
                            {
                                $protocolo = "Antes de protocolar, é necessário assinar o documento (botão azul no canto direito).";
                            }

                          

                           
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td>
                                        <p class='bold hand' style='font-size:16px; text-decoration:underline;' onclick='verificaPermissao(".$permissoes["view"].",\"".$pagina_link."/exib/$id?pag=$pag\");'>
                                            ".$result['tipo_sigla']." ".$result['numero']."/".$result['ano']." - ".$result['tipo_nome']."
                                        </p>
                                        <span class='bold'>Data apresentação:</span> ".reverteData($result['data'])."<p>
                                        <span class='bold'>Regime tramitação:</span> ".$result['regime_tramitacao']."<p>                                        
                                        <span class='bold'>Assinatura:</span> ".$assinatura."<p>
                                        <span class='bold'>Protocolo:</span> ".$protocolo."<p>
                                        <span class='bold'>Descrição:</span> ".$result['descricao']."<p>
                                        <span class='bold'>Cadastrado por:</span> ".$result['usu_nome']."<p>
                                        "; if($result['texto_original']){ echo "<span class='bold'>Texto original:</span> <a href='".$result['texto_original']."' target='_blank'><i class='fas fa-file-alt' style='font-size:20px;'></i></a><p>";} echo "
                                        ";
                                        if ( $nome_status != "" && $destino != "")
                                        {
                                            echo "
                                            <div style='border:1px solid #DDD; padding: 20px;'>
                                            <p class='titulo'>ÚLTIMA TRAMITAÇÃO</p>
                                            <span class='bold'>Status:</span> ".$nome_status."<p>
                                            <span class='bold'>Unidade atual:</span> ".$destino."<p>
                                            <span class='bold'>Data:</span> ".reverteData($result['data'])." às $hora_tramitacao<p>
                                            </div>
                                                                                
                                    ";                                            
                                        }
                                        echo "</td>
                                        <td align=center width='190'>";
                                       
                                        if ($protocolo == "<a href='protocolo_gerais/add_documentos/".$id."' class='red'>Documento ainda não protocolado.</a>")
                                        {
                                            echo "
                                            
                                            
                                                <div class='g_excluir' title='Excluir' onclick=\"
                                                    abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                        'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                        '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/view/excluir/$id?pag=$pag\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                        '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                    \">	<i class='far fa-trash-alt'></i>
                                                </div>
                                                ";
                                        }

                                            echo"
                                            <div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/edit/$id?pag=$pag\");'><i class='fas fa-pencil-alt'></i></div>                                            
                                            <div class='g_exibir' title='Exibir' onclick='verificaPermissao(".$permissoes["view"].",\"".$pagina_link."/exib/$id?pag=$pag\");'><i class='fas fa-search'></i></div>
                                            ";
                                            if($texto_original != ''  && $perm_ass == 1){
                                                echo "<div class='g_exibir' title='Assinar e receber documento' onclick=\"
                                                         abreMask('<p class=\'titulo\'>Alerta</p><p>'+
                                                             '<form name=\'form_filtro\' id=\'form_filtro\' enctype=\'multipart/form-data\' method=\'post\'  action=\'$pagina_link/view/confirmar_assinatura/$id\'>'+
                                                             'Essa operação não poderá ser desfeita.<br>Deseja realmente confirmar o recebimento e assinar o arquivo abaixo? <br><br>'+
                                                             '<span class=\'bold\'>Dados da assinatura:</span><br>".$dados_ass." <br><br>'+
                                                             '<span class=\'bold\'>Anexo a ser assinado:</span><br><a href=\'".$texto_original."\' target=\'_blank\'><i class=\'fas fa-file-signature\' style=\'vertical-align:bottom; font-size:20px; margin-right: 7px;\'></i></a> <br><br><br>'+
                                                             '<input value=\' Sim \' type=\'submit\' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                             '<input value=\' Não \' type=\'button\' class=\'close_janela\'></for');
                                                         \">	<i class='fas fa-signature'></i>
                                                     </div>"; 
                                             }
                                             echo "
                                             <div class='g_black' title='QRCODE' onclick=\"
                                                abreMask('<p class=\'titulo\'>QRCODE</p><p>'+
                                                    '<img src=\'qrcode_materias.php?id=$id&pagina=admin/docadm_documentos_qrcode/exib\' width=\'200\' ><br><br>'+
                                                    '<input value=\' Fechar \' type=\'button\' class=\'close_janela\'>');
                                                \">	<i class='fa fa-qrcode' aria-hidden='true'></i>
                                            </div>
                                    </td>
                                </tr>";
                            
                        }
                        echo "</table>";
                        $cnt = "SELECT COUNT(*) FROM docadm_documentos                                      
                                LEFT JOIN aux_administrativo_tipo_documento ON aux_administrativo_tipo_documento.id = docadm_documentos.tipo  
                                LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = docadm_documentos.cadastrado_por  
                                WHERE ".$classificacao_query."   AND  ".$tipo_query."AND  ".$numero_query."  AND  ".$ano_query." ";
                        $stmt = $PDO_PROCLEGIS->prepare($cnt);     
                        $stmt->bindParam(':fil_tipo', 	$fil_tipo);
                        $stmt->bindParam(':fil_classificacao', 	$fil_classificacao);                
                        $stmt->bindParam(':fil_numero', 	$fil_numero);                
                        $stmt->bindParam(':fil_ano', 	$fil_ano);
                        $variavel = "&fil_tipo=$fil_tipo&fil_classificacao=$fil_classificacao&fil_numero=$fil_numero&fil_ano=$fil_ano";            
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
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='docadm_documentos/view/adicionar'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                                    
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Tipo *:</label> <select name='tipo' id='tipo' class='tipo_documento obg'>
                                    <option value=''>Tipo</option>";
                                    $sql = " SELECT * FROM aux_administrativo_tipo_documento 
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
                                <p><label>Classificação *:</label> <select name='classificacao' id='classificacao' class='obg'>
                                    <option value=''>Classificação</option>";
                                    $sql = " SELECT * FROM aux_administrativo_classificacao
                                             WHERE ativo = :ativo
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
                                <p><label>Número *:</label> <input name='numero' id='numero' placeholder='Número' class='obg'>
                                <p><label>Ano *:</label> <input name='ano' id='ano' placeholder='Ano' class='obg'>
                                <p><label>Data apresentação *:</label> <input name='data' placeholder='Data apresentação' class='obg' onkeypress='return mascaraData(this,event);'>
                                <p><label>Interessado:</label> <input name='interessado' id='interessado' placeholder='Interessado'>
                                <p><label>Descrição *:</label> <textarea name='descricao' id='descricao' placeholder='Descrição' class='obg'></textarea>
                                <p><label>Em tramitação? *</label> <select name='em_tramitacao' id='em_tramitacao'  class='obg'>
                                    <option value=''>Em tramitação?</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>                                    
                                </select>
                                <p><label>Regime de tramitação *:</label> <select name='regime_tramitacao' id='regime_tramitacao'  class='obg'>
                                    <option value=''>Regime de tramitação</option>
                                    <option value='Ordinário'>Ordinário</option>
                                    <option value='Urgente'>Urgente</option>                                    
                                </select>
                                <p><label>Documento restrito? *</label> <select name='restrito' id='restrito'  class='obg'>
                                    <option value=''>Documento restrito?</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>                                    
                                </select>
                                <p><label>Texto Original:</label> <input type='file' name='texto_original[texto_original]' id='texto_original' placeholder='Texto Original'> 
                            </div>
                        </div>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='docadm_documentos/view'; value='Cancelar'/></center>
                        </center>
                    </div>
                </form>
                ";
            }
            
            if($pagina == 'edit')
            {            		
                $sql = "SELECT *, t1.nome as tipo_nome,
                                  t1.sigla as tipo_sigla,  
                                  t2.nome as classificacao_nome,                                
                                  docadm_documentos.id as id
                        FROM docadm_documentos 
                        LEFT JOIN aux_administrativo_tipo_documento t1 ON t1.id = docadm_documentos.tipo                                                             
                        LEFT JOIN aux_administrativo_classificacao t2 ON t2.id = docadm_documentos.classificacao                                                             
                        WHERE docadm_documentos.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();                                                                                                                 
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='docadm_documentos/view/editar/$id'>
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                                                                                   
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Tipo *:</label> <select name='tipo' id='tipo' class='obg'>
                                    <option value='".$result['tipo']."'>".$result['tipo_sigla']." - ".$result['tipo_nome']."</option>";
                                    $sql = "SELECT * FROM aux_administrativo_tipo_documento 
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
                                <p><label>Classificação *:</label> <select name='classificacao' id='classificacao' class='obg'>
                                    <option value='".$result['classificacao']."'>".$result['classificacao_nome']."</option>";
                                    $sql = "SELECT * FROM aux_administrativo_classificacao 
                                            WHERE ativo = :ativo 
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
                                <p><label>Número *:</label> <input name='numero' id='numero' value='".$result['numero']."' placeholder='Número'  class='obg'>
                                <p><label>Ano *:</label> <input name='ano' id='ano' value='".$result['ano']."' placeholder='Ano'>                                
                                <p><label>Data apresentação *:</label> <input name='data' value='".reverteData($result['data'])."'  class='obg'  placeholder='Data apresentação' onkeypress='return mascaraData(this,event);'>
                                <p><label>Interessado:</label> <input name='interessado' id='interessado' value='".$result['interessado']."' placeholder='Interessado'>
                                <p><label>Descrição *:</label> <textarea name='descricao' id='descricao' placeholder='Descrição'>".$result['descricao']."</textarea>
                                <p><label>Em tramitação?</label> <select name='em_tramitacao' id='em_tramitacao'>
                                    <option value='".$result['em_tramitacao']."'>".$result['em_tramitacao']."</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>                                    
                                </select>             
                                <p><label>Regime de tramitação:</label> <select name='regime_tramitacao' id='regime_tramitacao'  class='obg'>
                                    <option value='".$result['regime_tramitacao']."'>".$result['regime_tramitacao']."</option>
                                    <option value='Ordinário'>Ordinário</option>
                                    <option value='Urgente'>Urgente</option>                                    
                                </select>                                                   
                                <p><label>Documento restrito?</label> <select name='restrito' id='restrito'>
                                    <option value='".$result['restrito']."'>".$result['restrito']."</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>                                    
                                </select> 
                                <p><label>Texto Original:</label> ";if($result['texto_original'] != ''){ echo "<a href='".$result['texto_original']."' target='_blank'><i class='fas fa-paperclip' style='float:left;'></i></a>";} echo " &nbsp; 
                                <p><label>Alterar Texto Original:</label> <input type='file' name='texto_original[texto_original]'>                                
                            </div>                                                                 				
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='docadm_documentos/view'; value='Cancelar'/></center>
                            </center>
                        </div>
                    </form>
                    ";
                }
            }	
            if($pagina == 'exib')
            {        
                 		
                include("../mod_includes/modal//Docadm_AnexadoAdd.php");  
                include("../mod_includes/modal/Docadm_Doc_acessorioAdd.php");
                //include("../mod_includes/modal/tramitacaoAdd.php"); MOVIDO PARA FINAL PARA TRAVAR A UNIDADE ORIGEM QUANDO A DE DESTINO JÁ ESTIVER CADASTRADA                
                
                        
                
                $sql = "SELECT *, t1.nome as tipo_nome,
                                  t1.sigla as tipo_sigla,  
                                  t2.nome as classificacao_nome,                                
                                  docadm_documentos.id as id
                        FROM docadm_documentos 
                        LEFT JOIN aux_administrativo_tipo_documento t1 ON t1.id = docadm_documentos.tipo                                                             
                        LEFT JOIN aux_administrativo_classificacao t2 ON t2.id = docadm_documentos.classificacao                        
                        LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = docadm_documentos.cadastrado_por  
                        WHERE docadm_documentos.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();                                                 
                    echo "
                        <div class='titulo'> $page &raquo; Exibir </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            <li><a data-toggle='tab' href='#anexada' id='anexada-tab'>Anexadas</a></li>        
                            <li><a data-toggle='tab' href='#doc_acessorio' id='doc_acessorio-tab'>Doc. Acessório</a></li>    
                            <li><a data-toggle='tab' href='#tramitacao' id='tramitacao-tab'>Tramitação</a></li>    
                            
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active' >
                                <div style='display:table; width:100%'>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Tipo:</div>
                                        <div class='exib_value'>".$result['tipo_nome']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Classificação:</div>
                                        <div class='exib_value'>".$result['classificacao_nome']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Número:</div>
                                        <div class='exib_value'>".$result['numero']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Ano:</div>
                                        <div class='exib_value'>".$result['ano']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Data Apresetação:</div>
                                        <div class='exib_value'>".reverteData($result['data'])." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Interessado:</div>
                                        <div class='exib_value'>".$result['interessado']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Em tramitação?</div>
                                        <div class='exib_value'>".$result['em_tramitacao']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Regime de tramitação:</div>
                                        <div class='exib_value'>".$result['regime_tramitacao']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Doc. restrito?</div>
                                        <div class='exib_value'>".$result['restrito']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Texto Original:</div>
                                        <div class='exib_value'>"; ;if($result['texto_original'] != ''){ echo "<a href='".$result['texto_original']."' target='_blank'><i class='fas fa-paperclip' style='float:left;'></i></a>";} echo " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Cadastrado por:</div>
                                        <div class='exib_value'>".$result['usu_nome']." &nbsp;</div>
                                    </div>
                                </div>                                                                                                                                              
                            </div>                        
                            <div id='anexada' class='tab-pane fade in'>
                                ";
                                $sql = "SELECT *, docadm_documentos_anexados.id as id_anexado                                                  
                                        FROM docadm_documentos_anexados 
                                        LEFT JOIN aux_administrativo_tipo_documento ON aux_administrativo_tipo_documento.id = docadm_documentos_anexados.tipo_documento
                                        LEFT JOIN docadm_documentos ON docadm_documentos.id = docadm_documentos_anexados.documento_anexado                                        
                                        WHERE documento = :documento
                                        ORDER BY docadm_documentos_anexados.id DESC
                                       ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);                                    
                                $stmt->bindParam(':documento', 	$id);                                    
                                $stmt->execute();
                                $rows = $stmt->rowCount();
                                                
                                echo "
                                <div id='botoes'>
                                    <div class='g_adicionar' title='Adicionar' data-toggle='modal' data-target='#anexadoAdd'><i class='fas fa-plus'></i></div>
                                </div>";
                                if ($rows > 0)
                                {
                                    echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela'>Tipo de documento</td>
                                            <td class='titulo_tabela'>Documento Anexado</td>                                            
                                            <td class='titulo_tabela'>Data Anexação</td>
                                            <td class='titulo_tabela'>Data desanexação</td>
                                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                                        </tr>";
                                        $c=0;
                                        while($result = $stmt->fetch())
                                        {
                                            $id_anexado = $result['id_anexado'];
                                            $tipo_documento = $result['tipo_documento'];
                                            $sigla = $result['sigla'];
                                            $documento_anexado = $result['documento_anexado'];
                                            $nome = $result['nome'];
                                            $numero = $result['numero'];
                                            $ano = $result['ano'];                                 
                                            $data_anexacao = reverteData($result['data_anexacao']);
                                            $data_desanexacao = reverteData($result['data_desanexacao']);                                            
                                            
                                            
                                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                            echo "<tr class='$c1'>
                                                    <td>$sigla - $nome</td>                                                    
                                                    <td><a href='docadm_documentos/exib/".$documento_anexado."' target='_blank'>Nº $numero de $ano</a></td>
                                                    <td>$data_anexacao</td>
                                                    <td>$data_desanexacao</td>
                                                    <td align=center>
                                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/exib/$id/excluir_anexada/$id_anexado#anexada\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                \">	<i class='far fa-trash-alt'></i>
                                                            </div>
                                                            <div class='g_editar' title='Editar' data-toggle='modal' data-target='#anexadoEdit".$id_anexado."'><i class='fas fa-pencil-alt'></i></div>                                                                                                                        
                                                    </td>
                                                </tr>";
                                                include("../mod_includes/modal/Docadm_AnexadoEdit.php");
                                        }
                                        

                                        echo "</table>";                                        
                                }
                                else
                                {
                                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                }                                
                                echo "
                            </div>                            
                            <div id='doc_acessorio' class='tab-pane fade in'>
                                ";
                                $sql = "SELECT *, docadm_documentos_doc_acessorio.id as id_doc_acessorio
                                                , docadm_documentos_doc_acessorio.nome as nome
                                                , aux_administrativo_tipo_documento.nome as nome_tipo                                                  
                                        FROM docadm_documentos_doc_acessorio 
                                        LEFT JOIN aux_administrativo_tipo_documento ON aux_administrativo_tipo_documento.id = docadm_documentos_doc_acessorio.tipo_documento
                                        WHERE documento = :documento
                                        ORDER BY docadm_documentos_doc_acessorio.id DESC
                                       ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);                                    
                                $stmt->bindParam(':documento', 	$id);                                    
                                $stmt->execute();
                                $rows = $stmt->rowCount();
                                                
                                echo "
                                <div id='botoes'>
                                    <div class='g_adicionar' title='Adicionar' data-toggle='modal' data-target='#doc_acessorioAdd'><i class='fas fa-plus'></i></div>
                                </div>";
                                if ($rows > 0)
                                {
                                    echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela'>Tipo de documento</td>
                                            <td class='titulo_tabela'>Nome</td>                                            
                                            <td class='titulo_tabela'>Autor</td>
                                            <td class='titulo_tabela'>Data</td>
                                            <td class='titulo_tabela' align='center'>Anexo</td>
                                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                                        </tr>";
                                        $c=0;
                                        while($result = $stmt->fetch())
                                        {
                                            $id_doc_acessorio = $result['id_doc_acessorio'];
                                            $tipo_documento = $result['tipo_documento'];
                                            $nome_tipo = $result['nome_tipo'];
                                            $nome = $result['nome'];
                                            $ementa = $result['ementa'];
                                            $autor = $result['autor'];
                                            $data = reverteData($result['data']);
                                            $anexo = $result['anexo'];                                 
                                            
                                            
                                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                            echo "<tr class='$c1'>
                                                    <td>$nome_tipo</td>                                                    
                                                    <td>$nome</td>
                                                    <td>$autor</td>
                                                    <td>$data</td>
                                                    <td  align='center'>";if($anexo != ""){ echo "<a href='".$anexo."' target='_blank'><i class='fas fa-file-alt' style='font-size:20px;'></i></a>";} echo "</td>
                                                    <td align=center>
                                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/exib/$id/excluir_doc_acessorio/$id_doc_acessorio#doc_acessorio\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                \">	<i class='far fa-trash-alt'></i>
                                                            </div>
                                                            <div class='g_editar' title='Editar' data-toggle='modal' data-target='#doc_acessorioEdit".$id_doc_acessorio."'><i class='fas fa-pencil-alt'></i></div>                                                                                                                        
                                                    </td>
                                                </tr>";
                                                include("../mod_includes/modal/Docadm_Doc_acessorioEdit.php");
                                        }
                                        

                                        echo "</table>";                                        
                                }
                                else
                                {
                                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                }                                
                                echo "
                            </div>  
                            <div id='tramitacao' class='tab-pane fade in'>
                                ";
                                $sql = "SELECT *, docadm_documentos_tramitacao.id as id_tramitacao
                                                , aux_administrativo_status_tramitacao.nome as nome_status                                                   
                                                , cadastro_usuarios.usu_nome as nome_responsavel                                               
                                        FROM docadm_documentos_tramitacao 
                                        LEFT JOIN aux_materias_unidade_tramitacao t1 ON t1.id = docadm_documentos_tramitacao.unidade_origem
                                        LEFT JOIN aux_materias_unidade_tramitacao t2 ON t2.id = docadm_documentos_tramitacao.unidade_destino                                        
                                        LEFT JOIN aux_administrativo_status_tramitacao ON aux_administrativo_status_tramitacao.id = docadm_documentos_tramitacao.status_tramitacao  
                                        LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = docadm_documentos_tramitacao.responsavel                                                                                          
                                        WHERE documento = :documento
                                        ORDER BY docadm_documentos_tramitacao.data_tramitacao ASC
                                       ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);                                    
                                $stmt->bindParam(':documento', 	$id);                                    
                                $stmt->execute();
                                $rows = $stmt->rowCount();
                                                
                                echo "
                                <div id='botoes'>
                                    <a href='pasta_virtual_adm/$id/'><div class='g_botao'>Pasta Virtual</div></a>

                                    <div class='g_adicionar' title='Adicionar' data-toggle='modal' data-target='#tramitacaoAdd'><i class='fas fa-plus'></i></div>
                                </div>";
                                if ($rows > 0)
                                {
                                    echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela'>Origem / Destino</td>                                            
                                            <td class='titulo_tabela'>Responsável</td>                                            
                                            <td class='titulo_tabela'>Data tramitação</td>
                                            <td class='titulo_tabela'>Status</td>
                                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                                        </tr>";
                                        $c=0;
                                        while($result = $stmt->fetch())
                                        {
                                            $id_tramitacao = $result['id_tramitacao'];
                                            $unidade_origem = $result['unidade_origem'];
                                             
                                            // PEGA DADOS DA UNIDADE ORIGEM
                                            $sql = "SELECT *, aux_materias_orgaos.sigla as sigla_orgao
                                                            , aux_materias_orgaos.nome as nome_orgao
                                                            , cadastro_comissoes.sigla as sigla_comissao
                                                            , cadastro_comissoes.nome as nome_comissao
                                                            , cadastro_parlamentares.nome as nome_parlamentar
                                                    FROM aux_materias_unidade_tramitacao
                                                    LEFT JOIN aux_materias_orgaos ON aux_materias_orgaos.id = aux_materias_unidade_tramitacao.orgao
                                                    LEFT JOIN cadastro_comissoes ON cadastro_comissoes.id = aux_materias_unidade_tramitacao.comissao
                                                    LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_materias_unidade_tramitacao.parlamentar
                                                    WHERE aux_materias_unidade_tramitacao.id = :id
                                                ";
                                            $stmt_origem = $PDO_PROCLEGIS->prepare($sql);                                                
                                            $stmt_origem->bindParam(':id', 	$unidade_origem);                                    
                                            if($stmt_origem->execute())
                                            {
                                                $result_origem = $stmt_origem->fetch();
                                                $origem = $result_origem['nome_parlamentar'].$result_origem['sigla_orgao']." ".$result_origem['nome_orgao'].$result_origem['sigla_comissao']." ".$result_origem['nome_comissao'];
                                            }
                                            
                                            
                                            $unidade_destino = $result['unidade_destino'];
                                            // PEGA DADOS DA UNIDADE DESTINO
                                            $sql = "SELECT *, aux_materias_orgaos.sigla as sigla_orgao
                                                            , aux_materias_orgaos.nome as nome_orgao
                                                            , cadastro_comissoes.sigla as sigla_comissao
                                                            , cadastro_comissoes.nome as nome_comissao
                                                            , cadastro_parlamentares.nome as nome_parlamentar
                                                            
                                                    FROM aux_materias_unidade_tramitacao
                                                    LEFT JOIN aux_materias_orgaos ON aux_materias_orgaos.id = aux_materias_unidade_tramitacao.orgao
                                                    LEFT JOIN cadastro_comissoes ON cadastro_comissoes.id = aux_materias_unidade_tramitacao.comissao
                                                    LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_materias_unidade_tramitacao.parlamentar
                                                    WHERE aux_materias_unidade_tramitacao.id = :id
                                                ";
                                            $stmt_destino = $PDO_PROCLEGIS->prepare($sql);                                                
                                            $stmt_destino->bindParam(':id', 	$unidade_destino);                                    
                                            if($stmt_destino->execute())
                                            {
                                                $result_destino = $stmt_destino->fetch();
                                                $destino = $result_destino['nome_parlamentar'].$result_destino['sigla_orgao']." ".$result_destino['nome_orgao'].$result_destino['sigla_comissao']." ".$result_destino['nome_comissao'];

                                                $ultima_tramitacao = $result_destino['comissao'];
                                            }

                                            $data_tramitacao = reverteData($result['data_tramitacao']);
                                            $hora_tramitacao = substr($result['hora_tramitacao'],0,5);
                                            $data_encaminhamento = reverteData($result['data_encaminhamento']);                                            
                                            $data_fim_prazo = reverteData($result['data_fim_prazo']);                                            
                                            $status_tramitacao = $result['status_tramitacao'];                                 
                                            $nome_status = $result['nome_status'];                                 
                                            
                                            $urgente = $result['urgente'];
                                            $texto_acao = $result['texto_acao'];
                                            $anexo = $result['anexo'];
                                            $responsavel = $result['responsavel'];
                                            $nome_responsavel = $result['nome_responsavel'];
                                            
                                            
                                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                            echo "<tr class='$c1'>
                                                    <td>$origem <i class='fas fa-long-arrow-alt-right' style='font-size:18px; margin:0 5px'></i> $destino</td>
                                                    <td>$nome_responsavel</td>
                                                    <td>$data_tramitacao<br>$hora_tramitacao</td>
                                                    <td>$nome_status</td>
                                                    <td align=center>
                                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/exib/$id/excluir_tramitacao/$id_tramitacao#tramitacao\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                \">	<i class='far fa-trash-alt'></i>
                                                            </div>
                                                            <div class='g_editar' title='Editar' data-toggle='modal' data-target='#tramitacaoEdit".$id_tramitacao."'><i class='fas fa-pencil-alt'></i></div>                                                                                                                        
                                                    </td>
                                                </tr>";
                                                include("../mod_includes/modal/Docadm_TramitacaoEdit.php");
                                        }
                                        
                                        

                                        echo "</table>";                                        
                                }
                                else
                                {
                                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                }                                   
                                include("../mod_includes/modal/Docadm_TramitacaoAdd.php");                        
                                echo "
                            </div>
                            <br>               				
                            <center>                                                        
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='docadm_documentos/view'; value='Voltar'/></center>
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
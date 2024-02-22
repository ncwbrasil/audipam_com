<?php
$pagina_link = 'cadastro_materias';
include_once("../../core/mod_includes/php/funcoes.php");
include_once("../../core/mod_includes/php/funcoes_certificado.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php 
        include("header.php");      
    ?> 
</head>
<body>
	<main class="cd-main-content">    
    	<!--MENU-->
		<?php include("../mod_menu/menu.php"); ?>
        
        <!--CONTEUDO CENTRO-->
		<div class="content-wrapper">
            <div class='mensagem'></div>
            <?php            
            $page = "Cadastro &raquo; <a href='cadastro_materias/group'>Matérias Legislativas</a>";
            if(isset($_GET['id'])){$id = $_GET['id'];}
            $tipo  = $_POST['tipo'];
            $numero   = $_POST['numero'];
            $ano   = $_POST['ano'];
            $data_apresentacao  = reverteData($_POST['data_apresentacao']);if($data_apresentacao == ""){ $data_apresentacao  = null;}
            $protocolo   = $_POST['protocolo'];
            $apresentacao   = $_POST['apresentacao'];
            $apelido   = $_POST['apelido'];
            $dias_prazo   = $_POST['dias_prazo'];
            $materia_polemica   = $_POST['materia_polemica'];
            $objeto   = $_POST['objeto'];
            $regime_tramitacao   = $_POST['regime_tramitacao'];if($regime_tramitacao == ""){ $regime_tramitacao = null;}
            $em_tramitacao   = $_POST['em_tramitacao'];
            $data_fim_prazo  = reverteData($_POST['data_fim_prazo']);if($data_fim_prazo == ""){ $data_fim_prazo = null;}
            $data_publicacao  = reverteData($_POST['data_publicacao']);if($data_publicacao == ""){ $data_publicacao = null;}
            $complementar    = $_POST['complementar'];
            $tipo_origem_externa = $_POST['tipo_origem_externa'];   if($tipo_origem_externa == ""){ $tipo_origem_externa = null;}   
            $numero_externa   = $_POST['numero_externa'];if($numero_externa == ""){ $numero_externa = null;}
            $ano_externa   = $_POST['ano_externa'];
            $local_origem   = $_POST['local_origem'];if($local_origem == ""){ $local_origem = null;}    
            $data_externa  = reverteData($_POST['data_externa']);if($data_externa == ""){ $data_externa = null;}
            $ementa   = $_POST['ementa'];
            
            $tipo_autor   = $_POST['tipo_autor'];
            $autor   = $_POST['autor'];

            $endereco       = $_POST['endereco'];
            $end_numero     = $_POST['end_numero'];
            $cidade         = $_POST['cidade'];

            if ($endereco != "") {
                $address = $endereco . ", " . $end_numero . " - " . $cidade_cidade;
                $curl = curl_init();

                curl_setopt($curl, CURLOPT_URL, 'https://maps.googleapis.com/maps/api/geocode/json?address=' . rawurlencode($address) . '&key=AIzaSyDxr7kMaP7K8wVBB9fDqzjYfWVdklRvajM');

                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

                $json = json_decode(curl_exec($curl), true);
                // echo "<pre>";
                // print_r($json);
                $latitude = $json['results'][0]['geometry']['location']['lat'];
                $longitude = $json['results'][0]['geometry']['location']['lng'];
                curl_close($curl);
            } else {
                $endereco = null;
                $end_numero = null;
                $cidade = null;
                $latitude = null; 
                $longitude = null; 
            }

            $dados = array(
                
                'tipo' 		    => $tipo,
                'numero' 		    => $numero,
                'ano' 		    => $ano,
                'data_apresentacao' 		    => $data_apresentacao,
                'protocolo' 		    => $protocolo,
                'apresentacao' 		    => $apresentacao,
                'apelido' 		=> $apelido,
                'dias_prazo' 		=> $dias_prazo,
                'materia_polemica' 		=> $materia_polemica,
                'objeto' 		=> $objeto,
                'regime_tramitacao' 		=> $regime_tramitacao,
                'em_tramitacao' 		=> $em_tramitacao,
                'data_fim_prazo' 		=> $data_fim_prazo,
                'data_publicacao' 		=> $data_publicacao,
                'complementar' 		=> $complementar,
                'tipo_origem_externa' 		=> $tipo_origem_externa,
                'numero_externa' 		=> $numero_externa,
                'ano_externa' 		=> $ano_externa,
                'local_origem' 		=> $local_origem,
                'data_externa' 		=> $data_externa,
                'ementa' 		=> $ementa, 
                'endereco'          => $endereco, 
                'end_numero'        => $end_numero,
                'cidade'            => $cidade, 
                'latitude'          => $latitude, 
                'longitude'         => $longitude,
                'cadastrado_por'         => $_SESSION['usuario_id'],
            );
        
            if($action == "adicionar")
            {                                   
                $sql = "INSERT INTO cadastro_materias SET ".bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                if($stmt->execute($dados))
                {		
                    $id = $PDO_PROCLEGIS->lastInsertId();
                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';                    
                    $caminho = "../uploads/materias/";                                      
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
                                $texto_original .= "texto_original_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
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

                                $sql = "UPDATE cadastro_materias SET 
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

                    //AUTORES
                    $sql = "INSERT INTO cadastro_materias_autoria SET 
                                        materia 	    = :materia,
                                        tipo_autor 	    = :tipo_autor,
                                        autor 	        = :autor,
                                        primeiro_autor 	= :primeiro_autor";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                    $stmt->bindParam(':materia',$id);
                    $stmt->bindParam(':tipo_autor',$tipo_autor);
                    $stmt->bindParam(':autor',$autor);
                    $primeiro_autor = "Sim";
                    $stmt->bindParam(':primeiro_autor',$primeiro_autor);
                    if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}

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
                            mensagem("X","<i class='fa fa-exclamation-circle'></i> Ocorreu um erro, verifique os campos obrigatórios. Error: <?php echo $return['msg'];?>");
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

                $sql = "UPDATE cadastro_materias SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id;
                if($stmt->execute($dados))
                {
                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';                    
                    $caminho = "../uploads/materias/";                                      
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
                                $texto_original .= "texto_original_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
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

                                $sql = "UPDATE cadastro_materias SET 
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
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Ocorreu um erro, verifique os campos obrigatórios. Error: <?php echo $return['msg'];?>");
                    </script>
                    <?php
                }            
            }
            
            if($action == 'excluir')
            {
                $sql = "SELECT texto_original FROM cadastro_materias WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id);
                if($stmt->execute())
                {
                    $result = $stmt->fetch();
                    $texto_original = $result['texto_original'];
                }

                $sql = "UPDATE cadastro_materias SET ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id);
                $stmt->bindValue (':ativo',0);
                if($stmt->execute())
                {
                    //unlink($texto_original);
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
                $sql = "UPDATE cadastro_materias SET status = :status WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':status',1);
                $stmt->bindParam(':id',$id);
                $stmt->execute();
            }

            if($action == 'desativar')
            {
                $sql = "UPDATE cadastro_materias SET status = :status WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':status',0);
                $stmt->bindParam(':id',$id);
                $stmt->execute();
            }

            if($action == "adicionar_anexada")
            {                       
                $tipo_materia   = $_POST['tipo_materia'];
                $materia_anexada   = $_POST['materia_anexada'];
                $data_anexacao  = reverteData($_POST['data_anexacao']);
                $data_desanexacao  = reverteData($_POST['data_desanexacao']);if($data_desanexacao == ""){$data_desanexacao = null;}
                
                $dados = array(
                    'materia' 		=> $id,
                    'tipo_materia' 		    => $tipo_materia,
                    'materia_anexada' 		    => $materia_anexada,
                    'data_anexacao' 		        => $data_anexacao,
                    'data_desanexacao' 		        => $data_desanexacao
                    );
                $sql = "INSERT INTO cadastro_materias_anexadas SET ".bindFields($dados);
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

            if($action == "editar_anexada")
            {                       
                $id_anexada   = $_POST['id_anexada'];
                $tipo_materia   = $_POST['tipo_materia'];
                $materia_anexada   = $_POST['materia_anexada'];
                $data_anexacao  = reverteData($_POST['data_anexacao']);
                $data_desanexacao  = reverteData($_POST['data_desanexacao']);if($data_desanexacao == ""){$data_desanexacao = null;}
                
                $dados = array(
                    'materia' 		=> $id,
                    'tipo_materia' 		    => $tipo_materia,
                    'materia_anexada' 		    => $materia_anexada,
                    'data_anexacao' 		        => $data_anexacao,
                    'data_desanexacao' 		        => $data_desanexacao
                    );

                    
                $sql = "UPDATE cadastro_materias_anexadas SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id_anexada;
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

            if($action == 'excluir_anexada')
            {
                $id_sub = $_GET['id_sub'];
                $sql = "UPDATE cadastro_materias_anexadas SET ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id_sub);
                $stmt->bindValue(':ativo',0);
                if($stmt->execute())
                {
                    //unlink($foto_antiga);
                    log_operacao($id_sub, $PDO_PROCLEGIS); 
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

            if($action == "adicionar_assuntos")
            {                       
                $assunto   = $_POST['assunto'];
               
                $dados = array_filter(array(
                    'materia' 		=> $id,
                    'assunto' 		    => $assunto
                    ));
                $sql = "INSERT INTO cadastro_materias_assuntos SET ".bindFields($dados);
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
            
            if($action == "editar_assuntos")
            {                       
                $id_assuntos   = $_POST['id_assuntos'];
                $assunto   = $_POST['assunto'];
               
                $dados = array_filter(array(
                    'materia' 		=> $id,
                    'assunto' 		    => $assunto
                    ));

                    
                $sql = "UPDATE cadastro_materias_assuntos SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id_assuntos;
                if($stmt->execute($dados))
                {		
                    log_operacao($id_assuntos, $PDO_PROCLEGIS);                     

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

            if($action == 'excluir_assuntos')
            {
                $id_sub = $_GET['id_sub'];
                $sql = "UPDATE cadastro_materias_assuntos SET ativo =:ativo  WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id_sub);
                $stmt->bindValue(':ativo',0);
                if($stmt->execute())
                {
                    unlink($foto_antiga);
                    log_operacao($id_sub, $PDO_PROCLEGIS);     
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
            
            if($action == "adicionar_autoria")
            {                       
                $tipo_autor   = $_POST['tipo_autor'];
                $autor   = $_POST['autor'];
                $primeiro_autor   = $_POST['primeiro_autor'];

                $dados = array_filter(array(
                    'materia' 		    => $id,
                    'tipo_autor' 		=> $tipo_autor,
                    'autor' 		    => $autor,
                    'primeiro_autor' 	=> $primeiro_autor
                    ));
                $sql = "INSERT INTO cadastro_materias_autoria SET ".bindFields($dados);
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

            if($action == "editar_autoria")
            {                       
                $id_autoria   = $_POST['id_autoria'];
                $tipo_autor   = $_POST['tipo_autor'];
                $autor   = $_POST['autor'];
                $primeiro_autor   = $_POST['primeiro_autor'];
                
                $dados = array_filter(array(
                    'materia' 		=> $id,
                    'tipo_autor' 		    => $tipo_autor,
                    'autor' 		    => $autor,
                    'primeiro_autor' 	=> $primeiro_autor
                    ));

                    
                $sql = "UPDATE cadastro_materias_autoria SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id_autoria;
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

            if($action == 'excluir_autoria')
            {
                $id_sub = $_GET['id_sub'];
                $sql = "UPDATE cadastro_materias_autoria SET ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id_sub);
                $stmt->bindValue(':ativo', 0); 
                if($stmt->execute())
                {
                    log_operacao($id_sub, $PDO_PROCLEGIS);                     

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

            if($action == "adicionar_despacho")
            {                       
                $comissao   = $_POST['comissao'];
               
                $dados = array_filter(array(
                    'materia' 		    => $id,
                    'comissao' 		=> $comissao
                    ));
                $sql = "INSERT INTO cadastro_materias_despacho SET ".bindFields($dados);
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

            if($action == "editar_despacho")
            {                       
                $id_despacho   = $_POST['id_despacho'];
                $comissao   = $_POST['comissao'];
             
                $dados = array_filter(array(
                    'materia' 		=> $id,
                    'comissao' 		    => $comissao
                    ));

                    
                $sql = "UPDATE cadastro_materias_despacho SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id_despacho;
                if($stmt->execute($dados))
                {	
                    log_operacao($id_despacho, $PDO_PROCLEGIS);                     
	
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

            if($action == 'excluir_despacho')
            {
                $id_sub = $_GET['id_sub'];
                $sql = "UPDATE cadastro_materias_despacho SET ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id_sub);
                $stmt->bindValue(':ativo',0);
                if($stmt->execute())
                {
                    log_operacao($id_sub, $PDO_PROCLEGIS);                     

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
                    'materia' 		=> $id,
                    'tipo_documento' 		    => $tipo_documento,
                    'nome' 		    => $nome,
                    'autor' 		        => $autor,
                    'data' 		        => $data,
                    'ementa' 		        => $ementa
                    ));
                $sql = "INSERT INTO cadastro_materias_doc_acessorio SET ".bindFields($dados);
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

                                $sql = "UPDATE cadastro_materias_doc_acessorio SET 
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
                    log_operacao($id_doc_acessorio, $PDO_PROCLEGIS); 

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
                    'materia' 		=> $id,
                    'tipo_documento' 		    => $tipo_documento,
                    'nome' 		    => $nome,
                    'autor' 		        => $autor,
                    'data' 		        => $data,
                    'ementa' 		        => $ementa
                    );

                    
                $sql = "UPDATE cadastro_materias_doc_acessorio SET ".bindFields($dados)." WHERE id = :id ";
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

                                $sql = "UPDATE cadastro_materias_doc_acessorio SET 
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
                    log_operacao($id_doc_acessorio, $PDO_PROCLEGIS);             

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

                $sql = "SELECT anexo FROM cadastro_materias_doc_acessorio WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id_sub);
                if($stmt->execute())
                {
                    $result = $stmt->fetch();
                    $anexo = $result['anexo'];
                }

                $sql = "UPDATE cadastro_materias_doc_acessorio SET ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id_sub);
                $stmt->bindValue(':ativo', 0);
                if($stmt->execute())
                {
                    //unlink($anexo);
                    log_operacao($id_sub, $PDO_PROCLEGIS);           
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

            if($action == "adicionar_leg_citada")
            {                       
                $tipo_norma   = $_POST['tipo_norma'];
                $norma_juridica   = $_POST['norma_juridica'];
                $disposicao   = $_POST['disposicao'];
                $parte   = $_POST['parte'];
                $livro   = $_POST['livro'];
                $titulo   = $_POST['titulo'];
                $capitulo   = $_POST['capitulo'];
                $secao   = $_POST['secao'];
                $subsecao   = $_POST['subsecao'];
                $artigo   = $_POST['artigo'];
                $paragrafo   = $_POST['paragrafo'];
                $inciso   = $_POST['inciso'];
                $alinea   = $_POST['alinea'];
                $item   = $_POST['item'];
                
                $dados = array(
                    'materia' 		=> $id,
                    'tipo_norma' 		    => $tipo_norma,
                    'norma_juridica' 		    => $norma_juridica,
                    'disposicao' 		    => $disposicao,
                    'parte' 		        => $parte,
                    'livro' 		        => $livro,
                    'titulo'        => $titulo,
                    'capitulo'        => $capitulo,
                    'secao'        => $secao,
                    'subsecao'        => $subsecao,
                    'artigo'        => $artigo,
                    'paragrafo'        => $paragrafo,
                    'inciso'        => $inciso,
                    'alinea'        => $alinea,
                    'item'        => $item
                    );
                $sql = "INSERT INTO cadastro_materias_leg_citada SET ".bindFields($dados);
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

            if($action == "editar_leg_citada")
            {                       
                $id_leg_citada   = $_POST['id_leg_citada'];
                $tipo_norma   = $_POST['tipo_norma'];
                $norma_juridica   = $_POST['norma_juridica'];
                $disposicao   = $_POST['disposicao'];
                $parte   = $_POST['parte'];
                $livro   = $_POST['livro'];
                $titulo   = $_POST['titulo'];
                $capitulo   = $_POST['capitulo'];
                $secao   = $_POST['secao'];
                $subsecao   = $_POST['subsecao'];
                $artigo   = $_POST['artigo'];
                $paragrafo   = $_POST['paragrafo'];
                $inciso   = $_POST['inciso'];
                $alinea   = $_POST['alinea'];
                $item   = $_POST['item'];
                
                $dados = array(
                    'materia' 		=> $id,
                    'tipo_norma' 		    => $tipo_norma,
                    'norma_juridica' 		    => $norma_juridica,
                    'disposicao' 		    => $disposicao,
                    'parte' 		        => $parte,
                    'livro' 		        => $livro,
                    'titulo'        => $titulo,
                    'capitulo'        => $capitulo,
                    'secao'        => $secao,
                    'subsecao'        => $subsecao,
                    'artigo'        => $artigo,
                    'paragrafo'        => $paragrafo,
                    'inciso'        => $inciso,
                    'alinea'        => $alinea,
                    'item'        => $item
                    );

                    
                $sql = "UPDATE cadastro_materias_leg_citada SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id_leg_citada;
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

            if($action == 'excluir_leg_citada')
            {
                $id_sub = $_GET['id_sub'];
                $sql = "UPDATE cadastro_materias_leg_citada SET ativo=:ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id_sub);
                $stmt->bindValue(':ativo',0);
                if($stmt->execute())
                {    
                    log_operacao($id_sub, $PDO_PROCLEGIS);      
                
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
                $turno              = $_POST['turno'];
                $urgente            = $_POST['urgente'];
                $texto_acao         = $_POST['texto_acao'];
                $responsavel         = $_POST['responsavel'];
                $paginas         = $_POST['paginas'];
                $nome_documento         = $_POST['nome_documento'];
                
                $dados = array(
                    'materia' 		=> $id,
                    'unidade_origem' 		    => $unidade_origem,
                    'unidade_destino' 		    => $unidade_destino,
                    'data_tramitacao' 		        => $data_tramitacao,
                    'hora_tramitacao' 		        => $hora_tramitacao,
                    'data_encaminhamento' 		        => $data_encaminhamento,
                    'data_fim_prazo' 		        => $data_fim_prazo,
                    'status_tramitacao' 		        => $status_tramitacao,
                    'turno' 		        => $turno,
                    'urgente' 		        => $urgente,
                    'texto_acao' 		        => $texto_acao,
                    'responsavel' 		        => $responsavel,
                    'paginas' 		        => $paginas,
                    'nome_documento' 		        => $nome_documento
                    );
                $sql = "INSERT INTO cadastro_materias_tramitacao SET ".bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                if($stmt->execute($dados))
                {		
                    
                    $id_tramitacao = $PDO_PROCLEGIS->lastInsertId();                  
                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';                    
                    $caminho = "../uploads/materia_tramitacao/$id_tramitacao/";                                      
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

                                $sql = "UPDATE cadastro_materias_tramitacao SET 
                                        anexo 	 = :anexo,
                                        nome_anexo 	 = :nome_anexo
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':anexo',$anexo);
                                $stmt->bindParam(':nome_anexo',$nomeArquivo);
                                $stmt->bindParam(':id',$id_tramitacao);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
                                
                                //CONVERTE FOTO PARA BASE64
                                // $imagedata = file_get_contents($texto_original);                             
                                // $base64 = base64_encode($imagedata);
                            }					
                        }
                    }
                    //  
                   
                    // ENVIA EMAIL PARA RESPONSAVEL
                    $sql = "SELECT *
                            FROM aux_materias_unidade_tramitacao
                            LEFT JOIN ( cadastro_materias_tramitacao 
                                LEFT JOIN cadastro_materias ON cadastro_materias.id = cadastro_materias_tramitacao.materia )
                            ON cadastro_materias_tramitacao.unidade_destino = aux_materias_unidade_tramitacao.id  
                            LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = aux_materias_unidade_tramitacao.usuario_responsavel
                            WHERE cadastro_materias_tramitacao.id = :id_tramitacao
                            ";
                    $stmt_email = $PDO_PROCLEGIS->prepare($sql);    
                    $stmt_email->bindParam(':id_tramitacao', 	$id_tramitacao);                                    
                    $stmt_email->execute();
                    $rows_email = $stmt_email->rowCount();
                    if($rows_email > 0 )
                    {
                        
                        while($result_email = $stmt_email->fetch())
                        {
                            $numero = $result_email['numero'];
                            $ano = $result_email['ano'];
                            $am_email = $result_email['usu_email'];
                            
                            if($am_email)
                            {
                                include("../mail/envia_tramitacao_materia_interna.php");         
                            }               
                        }
                    }
                    
                    // ENVIA EMAILS PARA INTERESSADOS
                    $sql = "SELECT *
                            FROM aux_acompanhar_materia 
                            LEFT JOIN cadastro_materias ON cadastro_materias.id = aux_acompanhar_materia.am_materia  
                            WHERE am_materia = :am_materia
                            ";
                    $stmt_email = $PDO_PROCLEGIS->prepare($sql);    
                    $stmt_email->bindParam(':am_materia', 	$id);                                    
                    $stmt_email->execute();
                    $rows_email = $stmt_email->rowCount();
                    if($rows_email > 0 )
                    {
                        while($result_email = $stmt_email->fetch())
                        {
                            $numero = $result_email['numero'];
                            $ano = $result_email['ano'];
                            $am_email = $result_email['am_email'];
                            
                            if($am_email)
                            {
                                include("../mail/envia_tramitacao_materia.php");
                            }
                            
                        }
                    }
                    
                    log_operacao($id_tramitacao, $PDO_PROCLEGIS);  


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
                $turno              = $_POST['turno'];
                $urgente            = $_POST['urgente'];
                $texto_acao         = $_POST['texto_acao'];
                $responsavel         = $_POST['responsavel'];
                $paginas         = $_POST['paginas'];
                $nome_documento         = $_POST['nome_documento'];
                
                $dados = array(
                    'materia' 		=> $id,
                    'unidade_origem' 		    => $unidade_origem,
                    'unidade_destino' 		    => $unidade_destino,
                    'data_tramitacao' 		        => $data_tramitacao,
                    'hora_tramitacao' 		        => $hora_tramitacao,
                    'data_encaminhamento' 		        => $data_encaminhamento,
                    'data_fim_prazo' 		        => $data_fim_prazo,
                    'status_tramitacao' 		        => $status_tramitacao,
                    'turno' 		        => $turno,
                    'urgente' 		        => $urgente,
                    'texto_acao' 		        => $texto_acao,
                    'responsavel' 		        => $responsavel,
                    'paginas' 		        => $paginas,
                    'nome_documento' 		        => $nome_documento
                    );

                    
                $sql = "UPDATE cadastro_materias_tramitacao SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id_tramitacao;
                if($stmt->execute($dados))
                {		

                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';                    
                    $caminho = "../uploads/materia_tramitacao/$id_tramitacao/";                                      
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

                                $sql = "UPDATE cadastro_materias_tramitacao SET 
                                        anexo 	 = :anexo,
                                        nome_anexo 	 = :nome_anexo
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':anexo',$anexo);
                                $stmt->bindParam(':nome_anexo',$nomeArquivo);
                                $stmt->bindParam(':id',$id_tramitacao);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
                                
                                //CONVERTE FOTO PARA BASE64
                                // $imagedata = file_get_contents($texto_original);                             
                                // $base64 = base64_encode($imagedata);
                            }					
                        }
                    }
                    //
                    log_operacao($id_tramitacao, $PDO_PROCLEGIS);      


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
                $sql = "UPDATE cadastro_materias_tramitacao SET ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id_sub);
                $stmt->bindValue(':ativo', 0);
                if($stmt->execute())
                {
                    
                    //unlink($foto_antiga);
                    log_operacao($id_sub, $PDO_PROCLEGIS);      

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

            if($action == "adicionar_relatoria")
            {                       
                $comissao   = $_POST['comissao'];
                $periodo   = $_POST['periodo'];
                $parlamentar   = $_POST['parlamentar'];
                $data_designacao  = reverteData($_POST['data_designacao']);
                $data_destituicao  = reverteData($_POST['data_destituicao']);if($data_destituicao == ""){$data_destituicao = null;}
                $motivo_fim_relatoria   = $_POST['motivo_fim_relatoria'];
                
                $dados = array(
                    'materia' 		=> $id,
                    'comissao' 		    => $comissao,
                    'periodo' 		    => $periodo,
                    'parlamentar' 		    => $parlamentar,
                    'data_designacao' 		        => $data_designacao,
                    'data_destituicao' 		        => $data_destituicao,
                    'motivo_fim_relatoria'  => $motivo_fim_relatoria
                    );
                $sql = "INSERT INTO cadastro_materias_relatoria SET ".bindFields($dados);
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

            if($action == "editar_relatoria")
            {                       
                $id_relatoria   = $_POST['id_relatoria'];
                $comissao   = $_POST['comissao'];                
                $periodo   = $_POST['periodo'];
                $parlamentar   = $_POST['parlamentar'];
                $data_designacao  = reverteData($_POST['data_designacao']);
                $data_destituicao  = reverteData($_POST['data_destituicao']);if($data_destituicao == ""){$data_destituicao = null;}
                $motivo_fim_relatoria   = $_POST['motivo_fim_relatoria'];
                
                $dados = array(
                    'materia' 		=> $id,
                    'comissao' 		    => $comissao,
                    'periodo' 		    => $periodo,
                    'parlamentar' 		    => $parlamentar,
                    'data_designacao' 		        => $data_designacao,
                    'data_destituicao' 		        => $data_destituicao,
                    'motivo_fim_relatoria'  => $motivo_fim_relatoria
                    );

                    
                $sql = "UPDATE cadastro_materias_relatoria SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id_relatoria;
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

            if($action == 'excluir_relatoria')
            {
                $id_sub = $_GET['id_sub'];
                $sql = "UPDATE cadastro_materias_relatoria SET ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id_sub);
                $stmt->bindValue(':ativo',0);
                if($stmt->execute())
                {
                    //unlink($foto_antiga);
                    log_operacao($id_sub, $PDO_PROCLEGIS);

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

            if($action == 'confirmar_recebimento')
            {
                $id_sub = $_GET['id_sub'];
                $usu_recebimento = $_SESSION['usuario_id']; 
                $sql = "UPDATE cadastro_materias_tramitacao SET confirmacao_recebimento = :confirmacao_recebimento, usu_recebimento = :usu_recebimento WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':confirmacao_recebimento',1);
                $stmt->bindValue(':usu_recebimento',$usu_recebimento);
                $stmt->bindParam(':id',$id_sub);
                $stmt->execute();
                if($stmt->execute())
                {
                    log_operacao($id_sub, $PDO_PROCLEGIS);

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
                $id_sub = $_GET['id_sub'];
               
                $sql = "SELECT *
                        FROM cadastro_materias_tramitacao 
                        WHERE id = :id                      
                    ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);                    
                $stmt->bindParam(':id', 	$id_sub);                                    
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {                   
                    while($result = $stmt->fetch())
                    {
                        $anexo = $result['anexo'];           
                        
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
                                $sql = "SELECT * FROM cadastro_materias_tramitacao_assinaturas 
                                        WHERE tramitacao = :tramitacao ";
                                $stmt_qtd = $PDO_PROCLEGIS->prepare($sql);
                                $stmt_qtd->bindParam(':tramitacao',$id_sub);  
                                $stmt_qtd->execute();  
                                $rows_qtd = $stmt_qtd->rowCount();                                                                                                                                                                                                                   
                              
                                $retorno = array();                        
                                $retorno = assinaDocumento($cert, $pass, $anexo, $dados_ass, $data_ass, $rows_qtd); 
                                                             
                                if($retorno['result'] == "Documento assinado com sucesso!")
                                {        
                                    
                                    $nome_anexo = end(explode("/",$retorno['file']));

                                    $sql = "UPDATE cadastro_materias_tramitacao SET 
                                            anexo           = :anexo, 
                                            nome_anexo      = :nome_anexo,                                             
                                            confirmacao_recebimento = :confirmacao_recebimento, 
                                            usu_recebimento = :usu_recebimento 
                                            WHERE id = :id ";
                                    $stmt = $PDO_PROCLEGIS->prepare($sql);                                    
                                    $stmt->bindParam(':anexo',$retorno['file']);
                                    $stmt->bindParam(':nome_anexo',$nome_anexo);
                                    //$stmt->bindParam(':nome_documento',$retorno['file']);
                                    $stmt->bindValue(':confirmacao_recebimento',1);
                                    $stmt->bindValue(':usu_recebimento',$_SESSION['usuario_id']);
                                    $stmt->bindParam(':id',$id_sub);
                                    $stmt->execute();
                                    if($stmt->execute())
                                    {                                        
                                        $sql = "INSERT INTO cadastro_materias_tramitacao_assinaturas SET 
                                                        tramitacao           = :tramitacao, 
                                                        credenciais      = :credenciais,                                             
                                                        assinado = :assinado ";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);
                                        $stmt->bindParam(':tramitacao',$id_sub);
                                        $cred = $dados_ass. " - ". $data_ass;
                                        $stmt->bindParam(':credenciais',$cred);
                                        $stmt->bindValue(':assinado',1);                                                                                                                                                                
                                        if($stmt->execute())
                                        {
                                        }
                                        log_operacao($id_sub, $PDO_PROCLEGIS);

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
            
            $num_por_pagina = 20;
            if(!$pag){$primeiro_registro = 0; $pag = 1;}
            else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
            $fil_tipo = $_REQUEST['fil_tipo'];
            if($fil_tipo == '')
            {
                $tipo_query = " 1 = 1 ";
            }
            else
            {
                $tipo_query = " (cadastro_materias.tipo = :fil_tipo) ";
            }
            $fil_numero = $_REQUEST['fil_numero'];
            if($fil_numero == '')
            {
                $numero_query = " 1 = 1 ";
            }
            else
            {
                $numero_query = " (cadastro_materias.numero = :fil_numero) ";
            }
            $fil_ano = $_REQUEST['fil_ano'];
            if($fil_ano == '')
            {
                $ano_query = " 1 = 1 ";
            }
            else
            {
                $ano_query = " (cadastro_materias.ano = :fil_ano) ";
            }
            $fil_ementa = $_REQUEST['fil_ementa'];
            if($fil_ementa == '')
            {
                $ementa_query = " 1 = 1 ";
            }
            else
            {
                $fil_ementa1 = "%".$fil_ementa."%";
                $ementa_query = " (ementa LIKE :fil_ementa1 ) ";
            }
            $fil_autor = $_REQUEST['fil_autor'];
            if($fil_autor == '')
            {
                $autor_query = " 1 = 1 ";
            }
            else
            {
                $autor_query = " (cadastro_materias_autoria.autor = :fil_autor) ";
            }

            // PARA MONTAR BARRA DE NAVEGAÇÃO
            if($fil_tipo)
            {
                $sql = "SELECT aux_materias_tipos.nome as tipo_nome
                        FROM cadastro_materias 
                        LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_materias.tipo  
                        WHERE ".$ementa_query." AND  ".$autor_query." AND  ".$tipo_query." AND  ".$numero_query." AND  ".$ano_query." 
                        ";
                $stmt_nome = $PDO_PROCLEGIS->prepare($sql);    
                $stmt_nome->bindParam(':fil_tipo', 	$fil_tipo);                
                $stmt_nome->bindParam(':fil_ementa1', 	$fil_ementa1);                
                $stmt_nome->bindParam(':fil_autor', 	$fil_autor);                
                $stmt_nome->bindParam(':fil_numero', 	$fil_numero);                
                $stmt_nome->bindParam(':fil_ano', 	$fil_ano);                
                $stmt_nome->bindParam(':primeiro_registro', 	$primeiro_registro);
                $stmt_nome->bindParam(':num_por_pagina', 	$num_por_pagina);
                $stmt_nome->execute();
                $rows_nome = $stmt_nome->rowCount();
                $result_nome = $stmt_nome->fetch();
                $tipo_nome = $result_nome['tipo_nome'];
            }   
            else
            {
                $tipo_nome = "Todos";
            }

            if($pagina == "group")
            {
                 $sql = "SELECT *, aux_materias_tipos.nome as tipo_nome,
                                aux_materias_tipos.sigla as tipo_sigla,
                                cadastro_materias.id as id
                        FROM cadastro_materias 
                        LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_materias.tipo  
                        LEFT JOIN ( cadastro_materias_autoria 
                            LEFT JOIN (aux_autoria_autores 
                                LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_autoria_autores.parlamentar)
                            ON aux_autoria_autores.id = cadastro_materias_autoria.autor)
                        ON cadastro_materias_autoria.materia = cadastro_materias.id                   
                        GROUP BY cadastro_materias.tipo			
                        ORDER BY aux_materias_tipos.nome ASC 
                        LIMIT :primeiro_registro, :num_por_pagina ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);    
                $stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
                $stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
                $stmt->execute();
                $rows = $stmt->rowCount();

                echo "
                <div class='titulo'> $page  </div>
                <div id='botoes'>
                    <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"".$pagina_link."/add\");'><i class='fas fa-plus'></i></div>                       
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
                            $tipo = $result['tipo'];
                           
                            $sql = "SELECT COUNT(*) as cnt FROM cadastro_materias 
                                    WHERE tipo = :tipo 
                                   ";
                            $stmt_cnt = $PDO_PROCLEGIS->prepare($sql); 
                            $stmt_cnt->bindParam(':tipo', 	$tipo);                                
                            $stmt_cnt->execute();
                            $result_cnt = $stmt_cnt->fetch();
                            $cnt = $result_cnt['cnt'];
                          
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td>
                                        <p class='bold hand' style='font-size:16px; text-decoration:underline;' onclick='verificaPermissao(".$permissoes["view"].",\"".$pagina_link."/view/?fil_tipo=".$result['tipo']."\");'>
                                            ".$result['tipo_sigla']." - ".$result['tipo_nome']."
                                        </p>                                        
                                        $cnt matérias cadastradas. 
                                    </td>                                                                        
                                </tr>";
                        }
                        echo "</table>";
                        $cnt = "SELECT COUNT(*) FROM (
                                    SELECT COUNT(*) FROM cadastro_materias  
                                    LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_materias.tipo  
                                    LEFT JOIN ( cadastro_materias_autoria 
                                        LEFT JOIN (aux_autoria_autores 
                                            LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_autoria_autores.parlamentar)
                                        ON aux_autoria_autores.id = cadastro_materias_autoria.autor)
                                    ON cadastro_materias_autoria.materia = cadastro_materias.id  
                                    GROUP BY cadastro_materias.tipo) as count ";
                        $stmt = $PDO_PROCLEGIS->prepare($cnt);     
                        $variavel = "&fil_ementa=$fil_ementa&fil_tipo=$fil_tipo&fil_autor=$fil_autor&fil_numero=$fil_numero&fil_ano=$fil_ano";            
                        include("../../core/mod_includes/php/paginacao.php");
                }
                else
                {
                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                }
            }
            if($pagina == "view")
            {
                $num_por_pagina = 20;
                if(!$pag){$primeiro_registro = 0; $pag = 1;}
                else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
                $sql = "SELECT *, aux_materias_tipos.nome as tipo_nome,
                                aux_materias_tipos.sigla as tipo_sigla,
                                cadastro_materias.id as id 
                        FROM cadastro_materias 
                        LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_materias.tipo  
                        LEFT JOIN ( cadastro_materias_autoria 
                            LEFT JOIN (aux_autoria_autores 
                                LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = aux_autoria_autores.parlamentar)
                            ON aux_autoria_autores.id = cadastro_materias_autoria.autor)
                        ON cadastro_materias_autoria.materia = cadastro_materias.id   
                        WHERE cadastro_materias.ativo = :ativo AND ".$ementa_query." AND  ".$autor_query." AND  ".$tipo_query." AND  ".$numero_query." AND  ".$ano_query." 
                        GROUP BY cadastro_materias.id			
                        ORDER BY cadastro_materias.id DESC
                        LIMIT :primeiro_registro, :num_por_pagina ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);    
                $stmt->bindParam(':fil_tipo', 	$fil_tipo);                
                $stmt->bindParam(':fil_ementa1', 	$fil_ementa1);                
                $stmt->bindParam(':fil_autor', 	$fil_autor);                
                $stmt->bindParam(':fil_numero', 	$fil_numero);                
                $stmt->bindParam(':fil_ano', 	$fil_ano);                
                $stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
                $stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
                $stmt->bindValue(':ativo', 	1);
                $stmt->execute();
                $rows = $stmt->rowCount();
                                     
                echo "
                <div class='titulo'> $page  &raquo; $tipo_nome </div>
                <div id='botoes'>
                    <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"".$pagina_link."/add\");'><i class='fas fa-plus'></i></div>
                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_materias/view'>
                        <select name='fil_tipo' id='fil_tipo'>
                            <option  value=''>Tipo de Matéria</option>
                            ";
                            
                            $sql = " SELECT * FROM aux_materias_tipos ORDER BY nome";
                            $stmt_filtro = $PDO_PROCLEGIS->prepare($sql);
                                                                
                            $stmt_filtro->execute();
                            while($result_filtro = $stmt_filtro->fetch())
                            {
                                echo "<option value='".$result_filtro['id']."' ";if($_REQUEST['fil_tipo'] == $result_filtro['id']) echo " selected "; echo ">".$result_filtro['nome']."</option>";
                            }                        
                            echo "
                        </select>
                        <input name='fil_numero' id='fil_numero' value='$fil_numero' placeholder='Número'>
                        <input name='fil_ano' id='fil_ano' value='$fil_ano' placeholder='Ano'>
                        <input name='fil_ementa' id='fil_ementa' value='$fil_ementa' placeholder='Ementa'>
                        <select name='fil_autor' id='fil_autor'>
                            <option  value=''>Autor</option>
                            ";
                            
                            $sql = " SELECT * FROM aux_autoria_autores ORDER BY nome";
                            $stmt_filtro = $PDO_PROCLEGIS->prepare($sql);                                                                
                            $stmt_filtro->execute();
                            while($result_filtro = $stmt_filtro->fetch())
                            {
                                echo "<option value='".$result_filtro['id']."' ";if($_REQUEST['fil_autor'] == $result_filtro['id']) echo " selected "; echo ">".$result_filtro['nome']."</option>";
                            }                        
                            echo "
                        </select>
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
                          
                            // AUTORES
                            $autor=array();
                            $sql = "SELECT *
                                    FROM cadastro_materias_autoria
                                    LEFT JOIN aux_autoria_autores ON aux_autoria_autores.id = cadastro_materias_autoria.autor                                    
                                    WHERE cadastro_materias_autoria.materia = :materia	";
                            $stmt_aut = $PDO_PROCLEGIS->prepare($sql);                                
                            $stmt_aut->bindParam(':materia', 	$id);                                
                            $stmt_aut->execute();
                            $rows_aut = $stmt_aut->rowCount();
                            if($rows_aut > 0)
                            {
                                while($result_aut = $stmt_aut->fetch())
                                {
                                    $autor[] = $result_aut['nome'];
                                }
                            }
                          
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td>
                                        <p class='bold hand' style='font-size:16px; text-decoration:underline;' onclick='verificaPermissao(".$permissoes["view"].",\"".$pagina_link."/exib/$id?pag=$pag&fil_tipo=$fil_tipo\");'>
                                            ".$result['tipo_sigla']." ".$result['numero']."/".$result['ano']." - ".$result['tipo_nome']."
                                        </p>
                                        <span class='bold'>Ementa:</span> ".$result['ementa']."<p>
                                        <span class='bold'>Data apresentação:</span> ".reverteData($result['data_apresentacao'])."<p>
                                        <span class='bold'>Autor(es):</span> ".implode(", ",$autor)."<p>";
                                        if($result['texto_original']){ echo "<span class='bold'>Texto original:</span> <a href='".$result['texto_original']."' target='_blank'><i class='fas fa-file-alt' style='font-size:20px;'></i></a><p>";} 
                                        $sql_tramitacao='SELECT * FROM cadastro_materias
                                                        LEFT JOIN cadastro_materias_tramitacao  as h1 ON h1.materia = cadastro_materias.id
                                                        LEFT JOIN aux_materias_status_tramitacao ON aux_materias_status_tramitacao.id = h1.status_tramitacao               
                                                        WHERE cadastro_materias.id = :id AND h1.id = (SELECT MAX(h2.id) FROM cadastro_materias_tramitacao h2 where h2.materia = h1.materia)'; 
                                        $stmt_tramitacao = $PDO_PROCLEGIS->prepare($sql_tramitacao);                                
                                        $stmt_tramitacao->bindParam(':id', 	$id);                                
                                        $stmt_tramitacao->execute();
                                        $rows_tramitacao = $stmt_tramitacao->rowCount();
                                        if($rows_tramitacao > 0)
                                        {  
                                            $result_tramitacao = $stmt_tramitacao->fetch(); 
                                            
                                            switch ($result_tramitacao['id'])
                                            {
                                                case 1:
                                                    $cor = "#000";
                                                break;
                                                case 2:
                                                    $cor = "#333";
                                                break;
                                                case 3:
                                                    $cor = "#ff0000";
                                                break;
                                                case 4:
                                                    $cor = "#ff8c00";
                                                break;
                                                case 5:
                                                    $cor = "#f24f00";
                                                break;
                                                case 6:
                                                    $cor = "#eead2d";
                                                break;
                                                case 7:
                                                    $cor = "#228b22";
                                                break;
                                            }
                                            echo "<span class='bold'>Status da Tramitação : </span> <b class='bold' style='color: $cor'>".$result_tramitacao['nome']."</b></p>";
                                        }
                                        $pagina = 'materia_legislativa'; 
                                echo "                                                                       
                                    </td>                                    
                                    <td align=center width='200'>
                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/view/excluir/$id?pag=$pag\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                \">	<i class='far fa-trash-alt'></i>
                                            </div>
                                            <div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/edit/$id?pag=$pag&fil_tipo=$fil_tipo\");'><i class='fas fa-pencil-alt'></i></div>                                            
                                            <div class='g_exibir' title='Exibir' onclick='verificaPermissao(".$permissoes["view"].",\"".$pagina_link."/exib/$id?pag=$pag&fil_tipo=$fil_tipo\");'><i class='fas fa-search'></i></div>
                                            
                                            <div class='g_exibir' title='QRCODE' onclick=\"
                                                abreMask('<p class=\'titulo\'>QRCODE</p><p>'+
                                                    '<img src=\'qrcode_materias.php?id=$id&pagina=$pagina\' width=\'200\' ><br><br>'+
                                                    '<input value=\' Fechar \' type=\'button\' class=\'close_janela\'>');
                                                \">	<i class='fa fa-qrcode' aria-hidden='true'></i>
                                            </div>

                                    </td>
                                </tr>";
                              
                        }
                        echo "</table>";
                        $cnt = "SELECT COUNT(*) FROM cadastro_materias  
                                WHERE ".$ementa_query."  AND  ".$autor_query."  AND  ".$tipo_query."AND  ".$numero_query."  AND  ".$ano_query."
                                ";
                        $stmt = $PDO_PROCLEGIS->prepare($cnt);     
                        $stmt->bindParam(':fil_tipo', 	$fil_tipo);
                        $stmt->bindParam(':fil_ementa1', 	$fil_ementa1);
                        $stmt->bindParam(':fil_autor', 	$fil_autor);
                        $stmt->bindParam(':fil_numero', 	$fil_numero);                
                        $stmt->bindParam(':fil_ano', 	$fil_ano);
                        $variavel = "&fil_ementa=$fil_ementa&fil_tipo=$fil_tipo&fil_autor=$fil_autor&fil_numero=$fil_numero&fil_ano=$fil_ano";            
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
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_materias/view/adicionar'>
                    <div class='titulo'> $page &raquo; <a href='cadastro_materias/view/?fil_tipo=$fil_tipo'> $tipo_nome </a> &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            <li><a data-toggle='tab' href='#origem_externa'>Origem Externa</a></li>                        
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Tipo *:</label> <select name='tipo' id='tipo' class='obg'>
                                    <option value=''>Tipo</option>";
                                    $sql = " SELECT * FROM aux_materias_tipos 
                                             WHERE aux_materias_tipos.ativo = :ativo
                                             ORDER BY sigla";
                                        $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                                        $stmt_int->bindValue(':ativo', 1);                                                                            
                                        $stmt_int->execute();
                                        while($result_int = $stmt_int->fetch())
                                        {
                                            echo "<option value='".$result_int['id']."'>".$result_int['sigla']." - ".$result_int['nome']."</option>";
                                        }
                                    echo "
                                </select>

                                <p><label>Ano *:</label> <input name='ano' id='ano' placeholder='Ano' class='obg' value ='".date('Y')."'>
                                <p><label>Número *:</label> <input name='numero' id='numero' placeholder='Número' class='obg'>
                                <p><label>Data apresentação *:</label> <input name='data_apresentacao' placeholder='Data apresentação' class='obg' onkeypress='return mascaraData(this,event);'>
                                <p><label>Protocolo:</label> <input name='protocolo' id='protocolo' placeholder='Protocolo'>
                                <p><label>Tipo de apresentação:</label> <select name='apresentacao' id='apresentacao'>
                                    <option value=''>Tipo de apresentação</option>
                                    <option value='Oral'>Oral</option>
                                    <option value='Escrita'>Escrita</option>                                    
                                </select>
                                <p><label>Tipo de autor:</label> <select name='tipo_autor' id='tipo_autor' class='tp_autor' class='obg'>
                                    <option value=''>Tipo de autor</option>";
                                    $sql = " SELECT * FROM aux_autoria_tipo_autor 
                                             WHERE aux_autoria_tipo_autor.ativo = :ativo
                                             ORDER BY descricao";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);
                                        $stmt->bindValue(':ativo', 1);                 
                                        $stmt->execute();
                                        while($result = $stmt->fetch())
                                        {
                                            echo "<option value='".$result['id']."'>".$result['descricao']."</option>";
                                        }
                                    echo "
                                </select>
                                <p><label>Autor:</label> <select name='autor' id='autor' class='obg'>
                                    <option value=''>Autor</option>                                   
                                </select>
                                <p><label>Texto Original:</label> <input type='file' name='texto_original[texto_original]' id='texto_original' placeholder='Texto Original'> 
                                <p><label>Apelido:</label> <input name='apelido' id='apelido' placeholder='Apelido'>
                                <p><label>Dias prazo:</label> <input name='dias_prazo' id='dias_prazo' placeholder='Dias prazo'>
                                <!--<p><label>Matéria polêmica?</label> <select name='materia_polemica' id='materia_polemica'>
                                    <option value=''>Matéria polêmica?</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>                                    
                                </select> -->
                                <p><label>Objeto:</label> <input name='objeto' id='objeto' placeholder='Objeto'>
                                <p><label>Regime de tramitação:</label> <select name='regime_tramitacao' id='regime_tramitacao'>
                                    <option value=''>Regime de tramitação</option>
                                    ";
                                    $sql = " SELECT * FROM aux_materias_regime_tramitacao 
                                            WHERE aux_materias_regime_tramitacao.ativo = :ativo
                                             ORDER BY descricao";
                                        $stmt_reg = $PDO_PROCLEGIS->prepare($sql);
                                        $stmt_reg->bindValue(':ativo', 1);   
                                        $stmt_reg->execute();
                                        while($result_reg = $stmt_reg->fetch())
                                        {
                                            echo "<option value='".$result_reg['id']."'>".$result_reg['descricao']."</option>";
                                        }
                                    echo "                                  
                                </select> 
                                <p><label>Em tramitação?</label> <select name='em_tramitacao' id='em_tramitacao'>
                                    <option value=''>Em tramitação?</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>                                    
                                </select> 
                                <p><label>Data fim prazo *:</label> <input name='data_fim_prazo' id='data_fim_prazo' placeholder='Data fim prazo' class='obg' onkeypress='return mascaraData(this,event);'>
                                <p><label>Data publicação:</label> <input name='data_publicacao' id='data_publicacao' placeholder='Data publicação' onkeypress='return mascaraData(this,event);'>
                                <p><label>É complementar?</label> <select name='complementar' id='complementar'>
                                    <option value=''>É complementar?</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>                                    
                                </select>  
                                <p><label>Ementa *:</label> <textarea name='ementa' id='ementa' placeholder='Ementa' class='obg'></textarea>
                                <p><label>Endereço:</label> <input name='endereco' id='endereco' placeholder='Endereço'>
                                <p><label>Número:</label> <input name='end_numero' id='end_numero' placeholder='Número'>
                                <p><label>Cidade:</label> <input name='cidade' id='cidade' placeholder='cidade'>
                                  
                            </div>	                                                                        
                            <div id='origem_externa' class='tab-pane fade in'>
                                <p><label>Tipo de origem externa:</label> <select name='tipo_origem_externa' id='tipo_origem_externa'>
                                    <option value=''>Tipo de origem externa</option>";
                                    $sql = " SELECT * FROM aux_materias_tipos 
                                             WHERE aux_materias_tipos.ativo = :ativo
                                             ORDER BY sigla";
                                        $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                                        $stmt_int->bindValue(':ativo', 1);                             
                                        $stmt_int->execute();
                                        while($result_int = $stmt_int->fetch())
                                        {
                                            echo "<option value='".$result_int['id']."'>".$result_int['sigla']." - ".$result_int['nome']."</option>";
                                        }
                                    echo "
                                </select>
                                <p><label>Número:</label> <input name='numero_externa' id='numero_externa' placeholder='Número'>
                                <p><label>Ano:</label> <input name='ano_externa' id='ano_externa' placeholder='Ano'>
                                <p><label>Origem:</label> <select name='local_origem' id='local_origem'>
                                    <option value=''>Origem</option>";
                                    $sql = " SELECT * FROM aux_materias_origem 
                                              WHERE aux_materias_origem.ativo = :ativo
                                             ORDER BY sigla";
                                        $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                                        $stmt_int->bindValue(':ativo', 1);        
                                        $stmt_int->execute();
                                        while($result_int = $stmt_int->fetch())
                                        {
                                            echo "<option value='".$result_int['id']."'>".$result_int['sigla']." - ".$result_int['nome']."</option>";
                                        }
                                    echo "
                                </select>
                                <p><label>Data:</label> <input name='data_externa' placeholder='Data' onkeypress='return mascaraData(this,event);'>
                                
                            </div>
                        </div>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_materias/view'; value='Cancelar'/></center>
                        </center>
                    </div>
                </form>
                ";
            }
            
            if($pagina == 'edit')
            {            		
                $sql = "SELECT *, t1.nome as tipo_nome,
                                  t1.sigla as tipo_sigla,
                                  t2.sigla as sigla_externa,
                                  t2.nome as nome_externa,
                                  t3.sigla as sigla_origem,
                                  t3.nome as nome_origem,
                                  t4.descricao as descricao_regime,
                                  cadastro_materias.id as id
                        FROM cadastro_materias 
                        LEFT JOIN aux_materias_tipos t1 ON t1.id = cadastro_materias.tipo                                         
                        LEFT JOIN aux_materias_tipos t2 ON t2.id = cadastro_materias.tipo_origem_externa                                         
                        LEFT JOIN aux_materias_origem t3 ON t3.id = cadastro_materias.local_origem                                         
                        LEFT JOIN aux_materias_regime_tramitacao t4 ON t4.id = cadastro_materias.regime_tramitacao                                         
                        WHERE cadastro_materias.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();                                                                                                                 
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_materias/view/editar/$id'>
                        <div class='titulo'> $page &raquo; <a href='cadastro_materias/view/?fil_tipo=$fil_tipo'> $tipo_nome </a> &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>     
                            <li><a data-toggle='tab' href='#origem_externa'>Origem Externa</a></li>                                                     
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Tipo *:</label> <select name='tipo' id='tipo' class='obg'>
                                    <option value='".$result['tipo']."'>".$result['tipo_sigla']." - ".$result['tipo_nome']."</option>";
                                    $sql = "SELECT * FROM aux_materias_tipos 
                                             
                                            ORDER BY sigla";
                                    $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                                                                                                         								
                                    $stmt_int->execute();
                                    while($result_int = $stmt_int->fetch())
                                    {
                                        echo "<option value='".$result_int['id']."'>".$result_int['sigla']." - ".$result_int['nome']."</option>";
                                    }
                                    echo "
                                </select>
                                <p><label>Número *:</label> <input name='numero' id='numero' value='".$result['numero']."' placeholder='Número'  class='obg'>
                                <p><label>Ano *:</label> <input name='ano' id='ano' value='".$result['ano']."' placeholder='Ano'>                                
                                <p><label>Data apresentação:</label> <input name='data_apresentacao' value='".reverteData($result['data_apresentacao'])."'  placeholder='Data apresentação' onkeypress='return mascaraData(this,event);'>
                                <p><label>Protocolo:</label> <input name='protocolo' id='protocolo' value='".$result['protocolo']."' placeholder='Protocolo'>
                                <p><label>Tipo de apresentação:</label> <select name='apresentacao' id='apresentacao'>
                                    <option value='".$result['apresentacao']."'>".$result['apresentacao']."</option>
                                    <option value='Oral'>Oral</option>
                                    <option value='Escrita'>Escrita</option>                                    
                                </select>                                
                                <p><label>Texto Original:</label> ";if($result['texto_original'] != ''){ echo "<a href='".$result['texto_original']."' target='_blank'><i class='fas fa-paperclip' style='float:left;'></i></a>";} echo " &nbsp; 
                                <p><label>Alterar Texto Original:</label> <input type='file' name='texto_original[texto_original]'>
                                <p><label>Apelido:</label> <input name='apelido' id='apelido' value='".$result['apelido']."'  placeholder='Apelido'>
                                <p><label>Dias prazo:</label> <input name='dias_prazo' id='dias_prazo' value='".$result['dias_prazo']."'  placeholder='Dias prazo'>
                                <!--<p><label>Matéria polêmica?</label> <select name='materia_polemica' id='materia_polemica'>
                                    <option value='".$result['materia_polemica']."'>".$result['materia_polemica']."</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>                                    
                                </select> -->
                                <p><label>Objeto:</label> <input name='objeto' id='objeto' value='".$result['objeto']."'  placeholder='Objeto'>
                                <p><label>Regime de tramitação:</label> <select name='regime_tramitacao' id='regime_tramitacao'>
                                    <option value='".$result['regime_tramitacao']."'>".$result['descricao']."</option>
                                    ";
                                    $sql = " SELECT * FROM aux_materias_regime_tramitacao 
                                              
                                             ORDER BY descricao";
                                        $stmt_reg = $PDO_PROCLEGIS->prepare($sql);
                                        $stmt_reg->execute();
                                        while($result_reg = $stmt_reg->fetch())
                                        {
                                            echo "<option value='".$result_reg['id']."'>".$result_reg['descricao']."</option>";
                                        }
                                    echo "                                   
                                </select> 
                                <p><label>Em tramitação?</label> <select name='em_tramitacao' id='em_tramitacao'>
                                    <option value='".$result['em_tramitacao']."'>".$result['em_tramitacao']."</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>                                    
                                </select> 
                                <p><label>Data fim prazo *:</label> <input name='data_fim_prazo' id='data_fim_prazo' value='".reverteData($result['data_fim_prazo'])."' placeholder='Data fim prazo' class='obg' onkeypress='return mascaraData(this,event);'>
                                <p><label>Data publicação:</label> <input name='data_publicacao' id='data_publicacao' value='".reverteData($result['data_publicacao'])."' placeholder='Data publicação' onkeypress='return mascaraData(this,event);'>
                                <p><label>É complementar?</label> <select name='complementar' id='complementar'>
                                    <option value='".$result['complementar']."'>".$result['complementar']."</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>                                    
                                </select>  
                                <p><label>Ementa *:</label> <textarea name='ementa' id='ementa' placeholder='Ementa' class='obg'>".$result['ementa']."</textarea>  
                                <p><label>Endereço:</label> <input name='endereco' id='endereco' placeholder='Endereço' value='".$result['endereco']."'>
                                <p><label>Número:</label> <input name='end_numero' id='end_numero' placeholder='Número' value='".$result['end_numero']."'>
                                <p><label>Cidade:</label> <input name='cidade' id='cidade' placeholder='cidade' value='".$result['cidade']."'>
                                                      
                            </div>    
                            <div id='origem_externa' class='tab-pane fade in'>
                                <p><label>Tipo de origem externa:</label> <select name='tipo_origem_externa' id='tipo_origem_externa'>
                                    <option value='".$result['tipo_origem_externa']."'>".$result['sigla_externa']." - ".$result['nome_externa']."</option>";
                                    $sql = " SELECT * FROM aux_materias_tipos 
                                              
                                             ORDER BY sigla";
                                        $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                                                                            
                                        $stmt_int->execute();
                                        while($result_int = $stmt_int->fetch())
                                        {
                                            echo "<option value='".$result_int['id']."'>".$result_int['sigla']." - ".$result_int['nome']."</option>";
                                        }
                                    echo "
                                </select>
                                <p><label>Número:</label> <input name='numero_externa' id='numero_externa' value='".$result['numero_externa']."' placeholder='Número'>
                                <p><label>Ano:</label> <input name='ano_externa' id='ano_externa' value='".$result['ano_externa']."' placeholder='Ano'>
                                <p><label>Origem:</label> <select name='local_origem' id='local_origem'>
                                    <option value='".$result['local_origem']."'>".$result['sigla_origem']." - ".$result['nome_origem']."</option>";
                                    $sql = " SELECT * FROM aux_materias_origem 
                                              
                                             ORDER BY sigla";
                                        $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                                                                            
                                        $stmt_int->execute();
                                        while($result_int = $stmt_int->fetch())
                                        {
                                            echo "<option value='".$result_int['id']."'>".$result_int['sigla']." - ".$result_int['nome']."</option>";
                                        }
                                    echo "
                                </select>
                                <p><label>Data:</label> <input name='data_externa'  value='".reverteData($result['data_externa'])."' placeholder='Data' onkeypress='return mascaraData(this,event);'>
                                
                            </div>                                                                 				
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_materias/view'; value='Cancelar'/></center>
                            </center>
                        </div>
                    </form>
                    ";
                }
            }	

            if($pagina == 'exib')
            {            		
                include("../mod_includes/modal/anexadaAdd.php");
                include("../mod_includes/modal/assuntosAdd.php");
                include("../mod_includes/modal/autoriaAdd.php");
                include("../mod_includes/modal/despachoAdd.php");
                include("../mod_includes/modal/doc_acessorioAdd.php");
                include("../mod_includes/modal/leg_citadaAdd.php");
                //include("../mod_includes/modal/tramitacaoAdd.php"); MOVIDO PARA FINAL PARA TRAVAR A UNIDADE ORIGEM QUANDO A DE DESTINO JÁ ESTIVER CADASTRADA                                                                       
                
                $sql = "SELECT *, t1.nome as tipo_nome,
                                  t1.sigla as tipo_sigla,
                                  t2.sigla as sigla_externa,
                                  t2.nome as nome_externa,
                                  t3.sigla as sigla_origem,
                                  t3.nome as nome_origem,
                                  cadastro_materias.id as id
                        FROM cadastro_materias 
                        LEFT JOIN aux_materias_tipos t1 ON t1.id = cadastro_materias.tipo                                         
                        LEFT JOIN aux_materias_tipos t2 ON t2.id = cadastro_materias.tipo_origem_externa                                         
                        LEFT JOIN aux_materias_origem t3 ON t3.id = cadastro_materias.local_origem                                         
                        WHERE cadastro_materias.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();                                                 
                    echo "
                        <div class='titulo'> $page &raquo; <a href='cadastro_materias/view/?fil_tipo=$fil_tipo'> $tipo_nome </a> &raquo; ".$result['numero']."/".$result['ano']." </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            <li><a data-toggle='tab' href='#anexada' id='anexada-tab'>Anexadas</a></li>        
                            <li><a data-toggle='tab' href='#assuntos' id='assuntos-tab'>Assuntos</a></li>
                            <li><a data-toggle='tab' href='#autoria' id='autoria-tab'>Autoria</a></li>    
                            <li><a data-toggle='tab' href='#despacho' id='despacho-tab'>Despacho</a></li>    
                            <li><a data-toggle='tab' href='#doc_acessorio' id='doc_acessorio-tab'>Doc. Acessório</a></li>    
                            <li><a data-toggle='tab' href='#leg_citada' id='leg_citada-tab'>Leg. Citada</a></li>                 
                            <li><a data-toggle='tab' href='#tramitacao' id='tramitacao-tab'>Tramitação</a></li>    
                            <li><a data-toggle='tab' href='#relatoria' id='relatoria-tab'>Relatoria</a></li>                                 
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active' >
                                <div style='display:table; width:100%'>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Tipo:</div>
                                        <div class='exib_value'>".$result['tipo_nome']." &nbsp;</div>
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
                                        <div class='exib_value'>".reverteData($result['data_apresentacao'])." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Tipo de apresentação:</div>
                                        <div class='exib_value'>".$result['apresentacao']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Protocolo:</div>
                                        <div class='exib_value'>".$result['protocolo']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Texto Original:</div>
                                        <div class='exib_value'>"; ;if($result['texto_original'] != ''){ echo "<a href='".$result['texto_original']."' target='_blank'><i class='fas fa-paperclip' style='float:left;'></i></a>";} echo " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>&nbsp;</div>
                                        <div class='exib_value'>&nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Apelido:</div>
                                        <div class='exib_value'>".$result['apelido']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Dias prazo:</div>
                                        <div class='exib_value'>".$result['dias_prazo']." &nbsp;</div>
                                    </div>
                                    <!--<div class='exib_bloco'>
                                        <div class='exib_label'>Matéria polêmica?</div>
                                        <div class='exib_value'>".$result['materia_polemica']." &nbsp;</div>
                                    </div>-->
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>&nbsp;</div>
                                        <div class='exib_value'>&nbsp;</div>
                                    </div>
                                    <div class='exib_bloco_long'>
                                        <div class='exib_label'>Objeto:</div>
                                        <div class='exib_value'>".$result['objeto']." &nbsp;</div>
                                    </div>       
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Regime de tramitação:</div>
                                        <div class='exib_value'>".$result['regime_tramitacao']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Em tramitação?</div>
                                        <div class='exib_value'>".$result['em_tramitacao']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Fim prazo:</div>
                                        <div class='exib_value'>".reverteData($result['data_fim_prazo'])." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Data publicação:</div>
                                        <div class='exib_value'>".reverteData($result['data_publicacao'])." &nbsp;</div>
                                    </div>  
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>É complementar?</div>
                                        <div class='exib_value'>".$result['complementar']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco_long'>
                                        <div class='exib_label'>Ementa:</div>
                                        <div class='exib_value'>".$result['ementa']." &nbsp;</div>
                                    </div>";
                                    if($result['endereco']!=''){
                                        echo "
                                        <div class='exib_bloco_long'>
                                            <div class='exib_label'>Endereço :</div>
                                            <div class='exib_value'>".$result['endereco'].",".$result['end_numero']." - ".$result['cidade']."</div>
                                        </div>                                       
                                        ";
                                    }
                                echo "</div>                                                                                                                                              
                            </div>                        
                            <div id='anexada' class='tab-pane fade in'>
                                ";
                                $sql = "SELECT *, cadastro_materias_anexadas.id as id_anexada                                                  
                                        FROM cadastro_materias_anexadas 
                                        LEFT JOIN aux_materias_tipos ON aux_materias_tipos.id = cadastro_materias_anexadas.tipo_materia
                                        LEFT JOIN cadastro_materias ON cadastro_materias.id = cadastro_materias_anexadas.materia_anexada                                        
                                        WHERE materia = :materia
                                        ORDER BY cadastro_materias_anexadas.id DESC
                                       ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                $stmt->bindParam(':fil_ementa1', 	$fil_ementa1);
                                $stmt->bindParam(':materia', 	$id);                                    
                                $stmt->execute();
                                $rows = $stmt->rowCount();
                                                
                                echo "
                                <div id='botoes'>
                                    <div class='g_adicionar' title='Adicionar' data-toggle='modal' data-target='#anexadaAdd'><i class='fas fa-plus'></i></div>
                                </div>";
                                if ($rows > 0)
                                {
                                    echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela'>Tipo de matéria</td>
                                            <td class='titulo_tabela'>Matéria Anexada</td>                                            
                                            <td class='titulo_tabela'>Data Anexação</td>
                                            <td class='titulo_tabela'>Data desanexação</td>
                                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                                        </tr>";
                                        $c=0;
                                        while($result = $stmt->fetch())
                                        {
                                            $id_anexada = $result['id_anexada'];
                                            $tipo_materia = $result['tipo_materia'];
                                            $sigla = $result['sigla'];
                                            $materia_anexada = $result['materia_anexada'];
                                            $nome = $result['nome'];
                                            $numero = $result['numero'];
                                            $ano = $result['ano'];                                 
                                            $data_anexacao = reverteData($result['data_anexacao']);
                                            $data_desanexacao = reverteData($result['data_desanexacao']);                                            
                                            
                                            
                                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                            echo "<tr class='$c1'>
                                                    <td>$sigla - $nome</td>                                                    
                                                    <td>Nº $numero de $ano</td>
                                                    <td>$data_anexacao</td>
                                                    <td>$data_desanexacao</td>
                                                    <td align=center>
                                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/exib/$id/excluir_anexada/$id_anexada#anexada\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                \">	<i class='far fa-trash-alt'></i>
                                                            </div>
                                                            <div class='g_editar' title='Editar' data-toggle='modal' data-target='#anexadaEdit".$id_anexada."'><i class='fas fa-pencil-alt'></i></div>                                                                                                                        
                                                    </td>
                                                </tr>";
                                                include("../mod_includes/modal/anexadaEdit.php");
                                        }
                                        

                                        echo "</table>";                                        
                                }
                                else
                                {
                                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                }                                
                                echo "
                            </div>
                            <div id='assuntos' class='tab-pane fade in'>
                                ";
                                $sql = "SELECT *, cadastro_materias_assuntos.id as id_assuntos                                                  
                                        FROM cadastro_materias_assuntos 
                                        LEFT JOIN aux_materias_assuntos ON aux_materias_assuntos.id = cadastro_materias_assuntos.assunto
                                        WHERE materia = :materia
                                        ORDER BY cadastro_materias_assuntos.id DESC
                                       ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                $stmt->bindParam(':fil_ementa1', 	$fil_ementa1);
                                $stmt->bindParam(':materia', 	$id);                                    
                                $stmt->execute();
                                $rows = $stmt->rowCount();
                                                
                                echo "
                                <div id='botoes'>
                                    <div class='g_adicionar' title='Adicionar' data-toggle='modal' data-target='#assuntosAdd'><i class='fas fa-plus'></i></div>
                                </div>";
                                if ($rows > 0)
                                {
                                    echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela'>Assunto</td>
                                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                                        </tr>";
                                        $c=0;
                                        while($result = $stmt->fetch())
                                        {
                                            $id_assuntos = $result['id_assuntos'];
                                            $descricao = $result['descricao'];
                                            $assunto = $result['assunto'];                                            
                                            
                                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                            echo "<tr class='$c1'>
                                                    <td>$descricao</td>                                                    
                                                    <td align=center>
                                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/exib/$id/excluir_assuntos/$id_assuntos#assuntos\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                \">	<i class='far fa-trash-alt'></i>
                                                            </div>
                                                            <div class='g_editar' title='Editar' data-toggle='modal' data-target='#assuntosEdit".$id_assuntos."'><i class='fas fa-pencil-alt'></i></div>                                                                                                                        
                                                    </td>
                                                </tr>";
                                                include("../mod_includes/modal/assuntosEdit.php");
                                        }
                                        

                                        echo "</table>";                                        
                                }
                                else
                                {
                                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                }                                
                                echo "
                            </div>  
                            <div id='autoria' class='tab-pane fade in'>
                                ";
                                $sql = "SELECT *, cadastro_materias_autoria.id as id_autoria                                                  
                                        FROM cadastro_materias_autoria 
                                        LEFT JOIN aux_autoria_tipo_autor ON aux_autoria_tipo_autor.id = cadastro_materias_autoria.tipo_autor
                                        LEFT JOIN aux_autoria_autores ON aux_autoria_autores.id = cadastro_materias_autoria.autor                                        
                                        WHERE materia = :materia
                                        ORDER BY cadastro_materias_autoria.id DESC
                                       ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                $stmt->bindParam(':fil_ementa1', 	$fil_ementa1);
                                $stmt->bindParam(':materia', 	$id);                                    
                                $stmt->execute();
                                $rows = $stmt->rowCount();
                                                
                                echo "
                                <div id='botoes'>
                                    <div class='g_adicionar' title='Adicionar' data-toggle='modal' data-target='#autoriaAdd'><i class='fas fa-plus'></i></div>
                                </div>";
                                if ($rows > 0)
                                {
                                    echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela'>Autor</td>
                                            <td class='titulo_tabela'>Primeiro autor?</td>                                            
                                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                                        </tr>";
                                        $c=0;
                                        while($result = $stmt->fetch())
                                        {
                                            $id_autoria = $result['id_autoria'];
                                            $tipo_autor = $result['tipo_autor'];
                                            $descricao = $result['descricao'];
                                            $autor = $result['autor'];
                                            $nome = $result['nome'];
                                            $primeiro_autor = $result['primeiro_autor'];                                            
                                            
                                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                            echo "<tr class='$c1'>
                                                    <td>$nome</td>                                                    
                                                    <td>$primeiro_autor</td>
                                                    <td align=center>
                                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/exib/$id/excluir_autoria/$id_autoria#autoria\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                \">	<i class='far fa-trash-alt'></i>
                                                            </div>
                                                            <div class='g_editar' title='Editar' data-toggle='modal' data-target='#autoriaEdit".$id_autoria."'><i class='fas fa-pencil-alt'></i></div>                                                                                                                        
                                                    </td>
                                                </tr>";
                                                include("../mod_includes/modal/autoriaEdit.php");
                                        }
                                        

                                        echo "</table>";                                        
                                }
                                else
                                {
                                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                }                                
                                echo "
                            </div>
                            <div id='despacho' class='tab-pane fade in'>
                                ";
                                $sql = "SELECT *, cadastro_materias_despacho.id as id_despacho                                                  
                                        FROM cadastro_materias_despacho 
                                        LEFT JOIN cadastro_comissoes ON cadastro_comissoes.id = cadastro_materias_despacho.comissao
                                        WHERE materia = :materia
                                        ORDER BY cadastro_materias_despacho.id DESC
                                       ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                $stmt->bindParam(':fil_ementa1', 	$fil_ementa1);
                                $stmt->bindParam(':materia', 	$id);                                    
                                $stmt->execute();
                                $rows = $stmt->rowCount();
                                                
                                echo "
                                <div id='botoes'>
                                    <div class='g_adicionar' title='Adicionar' data-toggle='modal' data-target='#despachoAdd'><i class='fas fa-plus'></i></div>
                                </div>";
                                if ($rows > 0)
                                {
                                    echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela'>Comissão</td>
                                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                                        </tr>";
                                        $c=0;
                                        while($result = $stmt->fetch())
                                        {
                                            $id_despacho = $result['id_despacho'];
                                            $comissao = $result['comissao'];
                                            $nome = $result['nome'];
                                            $sigla = $result['sigla'];
                                             
                                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                            echo "<tr class='$c1'>
                                                    <td>$sigla - $nome</td>                                                    
                                                    <td align=center>
                                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/exib/$id/excluir_despacho/$id_despacho#despacho\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                \">	<i class='far fa-trash-alt'></i>
                                                            </div>
                                                            <div class='g_editar' title='Editar' data-toggle='modal' data-target='#despachoEdit".$id_despacho."'><i class='fas fa-pencil-alt'></i></div>                                                                                                                        
                                                    </td>
                                                </tr>";
                                                include("../mod_includes/modal/despachoEdit.php");
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
                                $sql = "SELECT *, cadastro_materias_doc_acessorio.id as id_doc_acessorio                                                  
                                        FROM cadastro_materias_doc_acessorio 
                                        LEFT JOIN aux_materias_documentos ON aux_materias_documentos.id = cadastro_materias_doc_acessorio.tipo_documento
                                        WHERE materia = :materia
                                        ORDER BY cadastro_materias_doc_acessorio.id DESC
                                       ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                $stmt->bindParam(':fil_ementa1', 	$fil_ementa1);
                                $stmt->bindParam(':materia', 	$id);                                    
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
                                            $descricao = $result['descricao'];
                                            $nome = $result['nome'];
                                            $ementa = $result['ementa'];
                                            $autor = $result['autor'];
                                            $data = reverteData($result['data']);
                                            $anexo = $result['anexo'];                                 
                                            
                                            
                                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                            echo "<tr class='$c1'>
                                                    <td>$descricao</td>                                                    
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
                                                include("../mod_includes/modal/doc_acessorioEdit.php");
                                        }
                                        

                                        echo "</table>";                                        
                                }
                                else
                                {
                                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                }                                
                                echo "
                            </div>  
                            <div id='leg_citada' class='tab-pane fade in'>
                                ";
                                $sql = "SELECT *, cadastro_materias_leg_citada.id as id_leg_citada                                                  
                                        FROM cadastro_materias_leg_citada 
                                        LEFT JOIN aux_normas_juridicas_tipos ON aux_normas_juridicas_tipos.id = cadastro_materias_leg_citada.tipo_norma
                                        LEFT JOIN cadastro_normas_juridicas ON cadastro_normas_juridicas.id = cadastro_materias_leg_citada.norma_juridica                                        
                                        WHERE cadastro_materias_leg_citada.materia = :materia
                                        ORDER BY cadastro_materias_leg_citada.id DESC
                                       ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                $stmt->bindParam(':fil_ementa1', 	$fil_ementa1);
                                $stmt->bindParam(':materia', 	$id);                                    
                                $stmt->execute();
                                $rows = $stmt->rowCount();
                                                
                                echo "
                                <div id='botoes'>
                                    <div class='g_adicionar' title='Adicionar' data-toggle='modal' data-target='#leg_citadaAdd'><i class='fas fa-plus'></i></div>
                                </div>";
                                if ($rows > 0)
                                {
                                    echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela'>Tipo de norma</td>
                                            <td class='titulo_tabela'>Norma jurídica</td>                                            
                                            <td class='titulo_tabela'>Citações</td>                                            
                                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                                        </tr>";
                                        $c=0;
                                        while($result = $stmt->fetch())
                                        {
                                            $id_leg_citada = $result['id_leg_citada'];
                                            $tipo_norma = $result['tipo_norma'];
                                            $sigla = $result['sigla'];
                                            $nome = $result['nome'];
                                            $norma_juridica = $result['norma_juridica'];
                                            $numero = $result['numero'];
                                            $ano = $result['ano'];                                 
                                            $ementa = $result['ementa'];                                 
                                            $disposicao   = $result['disposicao'];
                                            $parte   = $result['parte'];
                                            $livro   = $result['livro'];
                                            $titulo   = $result['titulo'];
                                            $capitulo   = $result['capitulo'];
                                            $secao   = $result['secao'];
                                            $subsecao   = $result['subsecao'];
                                            $artigo   = $result['artigo'];
                                            $paragrafo   = $result['paragrafo'];
                                            $inciso   = $result['inciso'];
                                            $alinea   = $result['alinea'];
                                            $item   = $result['item'];

                                            $citacoes = array_filter(array(
                                                'Disposição: ' 		    => $disposicao,
                                                'Parte: ' 		        => $parte,
                                                'Livro: ' 		        => $livro,
                                                'Título: '        => $titulo,
                                                'Capítulo: '        => $capitulo,
                                                'Seção: '        => $secao,
                                                'Subseção: '        => $subsecao,
                                                'Artigo: '        => $artigo,
                                                'Parágrafo: '        => $paragrafo,
                                                'Inciso: '        => $inciso,
                                                'Alínea: '        => $alinea,
                                                'Item: '        => $item
                                                ));
                                            

                                                
                                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                            echo "<tr class='$c1'>
                                                    <td>$sigla - $nome</td>                                                    
                                                    <td>Nº $numero de $ano</td>
                                                    <td>";
                                                    foreach($citacoes as $key => $value)
                                                    {
                                                        echo $key."<span class='bold'>".$value."</span><br>";
                                                    }
                                                    echo "</td>
                                                    <td align=center>
                                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/exib/$id/excluir_leg_citada/$id_leg_citada#leg_citada\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                \">	<i class='far fa-trash-alt'></i>
                                                            </div>
                                                            <div class='g_editar' title='Editar' data-toggle='modal' data-target='#leg_citadaEdit".$id_leg_citada."'><i class='fas fa-pencil-alt'></i></div>                                                                                                                        
                                                    </td>
                                                </tr>";
                                                include("../mod_includes/modal/leg_citadaEdit.php");
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
                                   

                                $sql = "SELECT *, cadastro_materias_tramitacao.id as id_tramitacao
                                                , aux_materias_status_tramitacao.nome as nome_status                                                   
                                                , cadastro_usuarios.usu_nome as nome_responsavel 
                                        FROM cadastro_materias_tramitacao 
                                        LEFT JOIN aux_materias_unidade_tramitacao t1 ON t1.id = cadastro_materias_tramitacao.unidade_origem
                                        LEFT JOIN aux_materias_unidade_tramitacao t2 ON t2.id = cadastro_materias_tramitacao.unidade_destino                                        
                                        LEFT JOIN aux_materias_status_tramitacao ON aux_materias_status_tramitacao.id = cadastro_materias_tramitacao.status_tramitacao  
                                        LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = cadastro_materias_tramitacao.responsavel
                                        WHERE materia = :materia AND cadastro_materias_tramitacao.ativo = :ativo
                                        ORDER BY cadastro_materias_tramitacao.data_tramitacao ASC
                                       ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                $stmt->bindParam(':fil_ementa1', 	$fil_ementa1);
                                $stmt->bindParam(':materia', 	$id);                                    
                                $stmt->bindValue(':ativo', 	1);                                    
                                $stmt->execute();
                                $rows = $stmt->rowCount();                              
                                echo "
                                <div id='botoes'>
                                    <a href='pasta_virtual_materias/$id/'><div class='g_botao'>Pasta Virtual</div></a>
                                    <div class='g_adicionar' title='Adicionar' data-toggle='modal' data-target='#tramitacaoAdd'><i class='fas fa-plus'></i></div>
                                </div>";
                                if ($rows > 0)
                                {
                                    echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela' align='center'>Data</td>
                                            <td class='titulo_tabela'>Tramitação</td>                                            
                                            <td class='titulo_tabela' align='center'>Assinado digitalmente?</td>
                                            <td class='titulo_tabela' align='center'>Recebido por</td>
                                            <td class='titulo_tabela' align='right' width='170'>Gerenciar</td>
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
                                            $turno = $result['turno'];
                                            $urgente = $result['urgente'];
                                            $texto_acao = $result['texto_acao'];
                                            $responsavel = $result['responsavel'];
                                            $nome_responsavel = $result['nome_responsavel'];
                                            $confirmacao_recebimento = $result['confirmacao_recebimento'];
                                            $usu_recebimento = $result['usu_recebimento'];
                                            $anexo = $result['anexo'];
                                            $paginas = $result['paginas'];
                                            $nome_documento = $result['nome_documento'];                                            

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
                                            $sql = "SELECT * FROM cadastro_materias_tramitacao_assinaturas 
                                                    WHERE tramitacao = :tramitacao AND assinado = :assinado";
                                            $stmt_usu = $PDO_PROCLEGIS->prepare($sql);                                                
                                            $stmt_usu->bindParam(':tramitacao', $id_tramitacao); 
                                            $stmt_usu->bindValue(':assinado', 1); 
                                            $stmt_usu->execute();
                                            $rows = $stmt_usu->rowCount();
                                            $credenciais="";
                                            if($rows > 0)
                                            {                                                
                                                while($result_usu = $stmt_usu->fetch())
                                                {
                                                    $credenciais .= $result_usu['credenciais']."\n";
                                                }                                                                      
                                            }
                                            
                                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                            echo "<tr class='$c1'>
                                                    <td align='center' valign='top'>$data_tramitacao<br>$hora_tramitacao</td>
                                                    <td>
                                                        <span class='bold'>$origem <i class='fas fa-long-arrow-alt-right'></i> $destino </span> <br>
                                                        ";if($anexo != '')
                                                        { 
                                                            echo "<a href='".$anexo."' target='_blank'><i class='far fa-file' style='vertical-align:bottom; font-size:20px; margin-right: 7px;'></i>Documento juntado</a>";
                                                            if($paginas)
                                                            {
                                                                echo " - página(s) ".$paginas;
                                                            }
                                                            echo "<br>";
                                                        } echo "
                                                        $nome_status <br>
                                                        $texto_acao <br>
                                                    </td>   
                                                    <td align='center'>";
                                                        if($anexo != '' && $credenciais != '')
                                                        {                                                          
                                                            echo "<i class='fas fa-file-signature hand' style='color:green; font-size:22px;' data-toggle='tooltip' data-placement='bottom'  title='".$credenciais."'></i><br>";                                                           
                                                        }  
                                                        elseif($anexo != '' && $credenciais == '')
                                                        {     
                                                            echo "<span style='color:red; font-weight:bold;'>Não assinado</span>";                                                     
                                                            //echo "<i class='fas fa-file-signature hand' style='color:red; font-size:22px;' data-placement='bottom'></i>";                                                           
                                                        }           
                                                    echo "</td>                                                 
                                                    <td align='center'>";
                                                        if($confirmacao_recebimento == 1){
                                                            $sql = "SELECT * FROM cadastro_usuarios WHERE usu_id = :usu_id";
                                                            $stmt_usu = $PDO_PROCLEGIS->prepare($sql);                                                
                                                            $stmt_usu->bindParam(':usu_id', 	$usu_recebimento);                                    
                                                            if($stmt_usu->execute())
                                                            {
                                                                $result_usu = $stmt_usu->fetch();
                                                                echo "<i class='fas fa-check-circle' style='color:green'></i><br>".$result_usu['usu_nome']; 
                                                            }
                                                        }            
                                                    echo "</td>
                                                    <td align=center>
                                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/exib/$id/excluir_tramitacao/$id_tramitacao#tramitacao\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                \">	<i class='far fa-trash-alt'></i>
                                                            </div>
                                                            <div class='g_editar' title='Editar' data-toggle='modal' data-target='#tramitacaoEdit".$id_tramitacao."'><i class='fas fa-pencil-alt'></i></div>"; 
                                                            if($confirmacao_recebimento <> 1 ){
                                                               echo "<div class='g_adicionar' title='Confirmar Recebimento' onclick=\"
                                                                        abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                            'Essa operação não poderá ser desfeita. Deseja realmente confirmar o recebimento deste item? <br><br>'+
                                                                            '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["edit"].",\'".$pagina_link."/exib/$id/confirmar_recebimento/$id_tramitacao#tramitacao\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                            '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                        \">	<i class='fas fa-check'></i>
                                                                    </div>"; 
                                                            }
                                                            if($anexo != ''  && $perm_ass == 1){
                                                                echo "<div class='g_exibir' title='Assinar e receber documento' onclick=\"
                                                                         abreMask('<p class=\'titulo\'>Alerta</p><p>'+
                                                                             '<form name=\'form_filtro\' id=\'form_filtro\' enctype=\'multipart/form-data\' method=\'post\'  action=\'$pagina_link/exib/$id/confirmar_assinatura/$id_tramitacao#tramitacao\'>'+
                                                                             'Essa operação não poderá ser desfeita.<br>Deseja realmente confirmar o recebimento e assinar o arquivo abaixo? <br><br>'+
                                                                             '<span class=\'bold\'>Dados da assinatura:</span><br>".$dados_ass." <br><br>'+
                                                                             '<span class=\'bold\'>Anexo a ser assinado:</span><br><a href=\'".$anexo."\' target=\'_blank\'><i class=\'fas fa-file-signature\' style=\'vertical-align:bottom; font-size:20px; margin-right: 7px;\'></i></a> <br><br><br>'+
                                                                             '<input value=\' Sim \' type=\'submit\' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                             '<input value=\' Não \' type=\'button\' class=\'close_janela\'></for');
                                                                         \">	<i class='fas fa-signature'></i>
                                                                     </div>"; 
                                                            }
                                                    echo"</td>
                                                </tr>";
                                                include("../mod_includes/modal/tramitacaoEdit.php");
                                                //include("../mod_includes/modal/tramitacaoAssinarDocumento.php");
                                        }                                                                                
                                        echo "</table>";                                        
                                }
                                else
                                {
                                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                }                                   
                                include("../mod_includes/modal/tramitacaoAdd.php");                        
                                echo "
                            </div>
                            <div id='relatoria' class='tab-pane fade in'>
                                ";
                                $sql = "SELECT *, cadastro_materias_relatoria.id as id_relatoria
                                                , cadastro_materias_relatoria.data_designacao as data_designacao 
                                                , cadastro_materias_relatoria.parlamentar as parlamentar
                                                , cadastro_comissoes.sigla as sigla
                                                , cadastro_comissoes.nome as nome_comissao
                                                , aux_comissoes_periodos.data_inicio as data_inicio
                                                , aux_comissoes_periodos.data_fim as data_fim
                                                , cadastro_materias_relatoria.comissao as comissao 
                                                , aux_materias_tipo_fim_relatoria.descricao as descricao_motivo                  
                                        FROM cadastro_materias_relatoria
                                        LEFT JOIN ( aux_comissoes_periodos 
                                            LEFT JOIN cadastro_comissoes_composicao ON cadastro_comissoes_composicao.periodo = aux_comissoes_periodos.id )
                                        ON aux_comissoes_periodos.id = cadastro_materias_relatoria.periodo
                                        LEFT JOIN cadastro_comissoes ON cadastro_comissoes.id = cadastro_materias_relatoria.comissao                                        
                                        LEFT JOIN aux_materias_tipo_fim_relatoria ON aux_materias_tipo_fim_relatoria.id = cadastro_materias_relatoria.motivo_fim_relatoria
                                        LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = cadastro_materias_relatoria.parlamentar                                        
                                        WHERE materia = :materia
                                        GROUP BY id_relatoria
                                        ORDER BY cadastro_materias_relatoria.id DESC
                                       ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                $stmt->bindParam(':fil_ementa1', 	$fil_ementa1);
                                $stmt->bindParam(':materia', 	$id);                                    
                                $stmt->execute();
                                $rows = $stmt->rowCount();
                                                
                                echo " 
                                <div id='botoes'>
                                    <div class='g_adicionar' title='Adicionar' data-toggle='modal' "; 
                                    if($ultima_tramitacao != "")
                                    { 
                                        echo "data-target='#relatoriaAdd'";
                                    }
                                    else
                                    { 
                                        echo "onclick='javascript:alert(\"A tramitação atual deve ser uma comissão.\");'";
                                    }
                                    echo "><i class='fas fa-plus'></i></div>
                                </div>";
                                if ($rows > 0)
                                {
                                    echo " 
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela'>Comissão</td>
                                            <td class='titulo_tabela'>Período da Composição</td>
                                            <td class='titulo_tabela'>Parlamentar</td>                                            
                                            <td class='titulo_tabela'>Data Designação</td>                                            
                                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                                        </tr>";
                                        $c=0;
                                        while($result = $stmt->fetch())
                                        {
                                            $id_relatoria = $result['id_relatoria'];
                                            $sigla = $result['sigla'];
                                            $data_inicio = reverteData($result['data_inicio']);
                                            $data_fim = reverteData($result['data_fim']);                                            
                                            $data_designacao = reverteData($result['data_designacao']);                                            
                                            $data_destituicao = reverteData($result['data_destituicao']);                                            
                                            $parlamentar = $result['parlamentar'];                                                                                                                    
                                            $nome = $result['nome'];                                                                                                                    
                                            $comissao = $result['comissao'];                                                                                                                    
                                            $nome_comissao = $result['nome_comissao'];
                                            $periodo = $result['periodo'];
                                            $motivo_fim_relatoria = $result['motivo_fim_relatoria'];
                                            $descricao_motivo = $result['descricao_motivo'];
                                            
                                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                            echo "<tr class='$c1'>
                                                    <td>$sigla - $nome_comissao</td>                                                    
                                                    <td>$data_inicio - $data_fim</td>                                                    
                                                    <td>$nome</td>
                                                    <td>$data_designacao</td>                                                    
                                                    <td align=center>
                                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/exib/$id/excluir_relatoria/$id_relatoria#relatoria\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                                \">	<i class='far fa-trash-alt'></i>
                                                            </div>
                                                            <div class='g_editar' title='Editar' data-toggle='modal' data-target='#relatoriaEdit".$id_relatoria."'><i class='fas fa-pencil-alt'></i></div>                                                                                                                        
                                                    </td>
                                                </tr>";
                                                include("../mod_includes/modal/relatoriaEdit.php");
                                        }
                                        
                                        

                                        echo "</table>";                                        
                                }
                                else
                                {
                                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                }    

                                include("../mod_includes/modal/relatoriaAdd.php");                        
                                echo "
                            </div>
                            <br>               				
                            <center>                                                        
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_materias/view/?pag=$pag&fil_tipo=$fil_tipo'; value='Voltar'/></center>
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
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
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

<script>
    $("#tipo").change(function() {
        var tipo = $(this).val();
        var ano = $('#ano').val(); 
        var cli = '<?php echo $_SESSION['cliente_url']?>'; 
        var sis = '<?php echo $_SESSION['sistema_url']?>';
        $.post("carrega_numeracao.php", {
                acao: 'carrega_numero_materia',
                tp: tipo, 
                an: ano, 
                cliente_url: cli,
                sistema_url: sis,
            },
            function(dados) {
                if (dados != '') {
                    $('#numero').val(dados);
                }
            }
        )
    });

    $( "#ano" ).change(function() {
        var tipo = $('#tipo').val(); 
		var ano = $(this).val();
        var cli = '<?php echo $_SESSION['cliente_url']?>'; 
        var sis = '<?php echo $_SESSION['sistema_url']?>';
        $.post("carrega_numeracao.php", {
                acao: 'carrega_numero_materia',
                tp: tipo, 
                an: ano, 
                cliente_url: cli,
                sistema_url: sis,
            },
            function(dados) {
                if (dados != '') {
                    $('#numero').val(dados);
                }
            }
        )
    });
</script>
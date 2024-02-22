<?php
$pagina_link = 'cadastro_sessoes_plenarias';
include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include("header.php");?> 
    <!-- script relogio -->
    <script>
    var clockid=new Array()
    var clockidoutside=new Array()
    var i_clock=-1
    var thistime= new Date()
    var hours=thistime.getHours()
    var minutes=thistime.getMinutes()
    var seconds=thistime.getSeconds()
    if (eval(hours) <10) {hours="0"+hours}
    if (eval(minutes) < 10) {minutes="0"+minutes}
    if (seconds < 10) {seconds="0"+seconds}
    var thistime = hours+":"+minutes+":"+seconds

    function writeclock() {
        i_clock++
        if (document.all || document.getElementById || document.layers) {
    clockid[i_clock]="clock"+i_clock
    document.write("<span id='"+clockid[i_clock]+"' style='position:relative'>"+thistime+"</span>")
        }
    }

    function clockon() {
        thistime= new Date()
        hours=thistime.getHours()
        minutes=thistime.getMinutes()
        seconds=thistime.getSeconds()
        if (eval(hours) <10) {hours="0"+hours}
        if (eval(minutes) < 10) {minutes="0"+minutes}
        if (seconds < 10) {seconds="0"+seconds}
        thistime = hours+":"+minutes+":"+seconds
        
        if (document.all) {
    for (i=0;i<=clockid.length-1;i++) {
        var thisclock=eval(clockid[i])
        thisclock.innerHTML=thistime
    }
        }

        if (document.getElementById) {
    for (i=0;i<=clockid.length-1;i++) {
        document.getElementById(clockid[i]).innerHTML=thistime
    }
        }
        var timer=setTimeout("clockon()",1000)
    }
    window.onload=clockon
    </SCRIPT>

    <!-- script relogio -->


    <script type="text/javascript">

    </script>
</head>
<body>
	<main class="cd-main-content">    
    	<!--MENU-->
		<?php include("../mod_menu/menu.php"); ?>
        
        <!--CONTEUDO CENTRO-->
		<div class="content-wrapper">
            <div class='mensagem'></div>
            <?php                     
            $page = "Cadastro &raquo; <a href='cadastro_sessoes_plenarias/view'>Sessões Plenárias</a>";
            if(isset($_GET['id'])){$id = $_GET['id'];}
            $legislatura  = $_POST['legislatura'];
            $sessao  = $_POST['sessao'];
            $nome  = $_POST['nome'];
            $tipo_sessao  = $_POST['tipo_sessao'];
            $numero   = $_POST['numero'];
            $data_abertura  = reverteData($_POST['data_abertura']);
            $hora_abertura   = $_POST['hora_abertura'];
            $iniciada   = $_POST['iniciada'];
            $data_encerramento  = reverteData($_POST['data_encerramento']);if($data_encerramento == ""){$data_encerramento = null;}
            $hora_encerramento   = $_POST['hora_encerramento'];if($hora_encerramento == ""){$hora_encerramento = null;}
            $finalizada   = $_POST['finalizada'];
            $url_audio   = $_POST['url_audio'];
            $url_video   = $_POST['url_video'];
            $tema_solene   = $_POST['tema_solene'];
            
            $dados = array(
                
                'nome' 		    => $nome,
                'legislatura' 		    => $legislatura,
                'sessao' 		    => $sessao,
                'tipo_sessao' 		    => $tipo_sessao,
                'numero' 		    => $numero,
                'data_abertura' 		    => $data_abertura,
                'hora_abertura' 		    => $hora_abertura,
                'iniciada' 		    => $iniciada,
                'data_encerramento' 		    => $data_encerramento,
                'hora_encerramento' 		=> $hora_encerramento,
                'finalizada' 		=> $finalizada,
                'url_audio' 		=> $url_audio,
                'url_video' 		=> $url_video,
                'tema_solene' 		=> $tema_solene
                );
        
            if($action == "adicionar")
            {                                   
                $sql = "INSERT INTO cadastro_sessoes_plenarias SET ".bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                if($stmt->execute($dados))
                {		
                    $id = $PDO_PROCLEGIS->lastInsertId();

                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';                    
                    $caminho = "../uploads/sessoes_plenarias/";                                      
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
                            }
                            if(!empty($files["name"]["ata"]))
                            {
                                $nomeArquivo = $files["name"]["ata"];
                                $nomeTemporario = $files["tmp_name"]["ata"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $ata = $caminho;
                                $ata .= "ata_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($ata));
                            }
                            if(!empty($files["name"]["anexo"]))
                            {
                                $nomeArquivo	= $files["name"]["anexo"];
                                $nomeTemporario = $files["tmp_name"]["anexo"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $anexo = $caminho;
                                $anexo .= "anexo".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($anexo));
                            }                                                       				
                        }
                    }
                    $sql = "UPDATE cadastro_sessoes_plenarias SET 
                            pauta 	 = :pauta,
                            ata = :ata,
                            anexo = :anexo
                            WHERE id = :id ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                    $stmt->bindParam(':pauta',$pauta);
                    $stmt->bindParam(':ata',$ata);
                    $stmt->bindParam(':anexo',$anexo);                    
                    $stmt->bindParam(':id',$id);
                    if($stmt->execute())
                    {							 
                    }
                    else
                    {
                        $erro=1;
                    }
                    //  

                    // CRONOMETROS                    
                    $sql = "INSERT INTO cadastro_sessoes_plenarias_cronometro SET 
                                        sessao 	    = :sessao,
                                        discurso 	    = :discurso,
                                        aparte 	        = :aparte,
                                        ordem 	        = :ordem,
                                        consideracoes   = :consideracoes";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                    $stmt->bindParam(':sessao',$id);
                    $discurso = "00:05:00";
                    $aparte = "00:05:00";
                    $ordem = "00:10:00";
                    $consideracoes = "00:10:00";
                    $stmt->bindParam(':discurso',$discurso);
                    $stmt->bindParam(':aparte',$aparte);                    
                    $stmt->bindParam(':ordem',$ordem);
                    $stmt->bindParam(':consideracoes',$consideracoes);
                    if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}

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
            
            if($action == 'editar')
            {
                $sql = "UPDATE cadastro_sessoes_plenarias SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id;
                if($stmt->execute($dados))
                {
                        
                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';                    
                    $caminho = "../uploads/sessoes_plenarias/"; 
                    
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
                                $sql = "UPDATE cadastro_sessoes_plenarias SET 
                                        pauta 	 = :pauta
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':pauta',$pauta);
                                $stmt->bindParam(':id',$id);
                                if($stmt->execute()){}else{$erro=1;}
                            }
                            if(!empty($files["name"]["ata"]))
                            {
                                $nomeArquivo = $files["name"]["ata"];
                                $nomeTemporario = $files["tmp_name"]["ata"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $ata = $caminho;
                                $ata .= "ata_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($ata));
                                 $sql = "UPDATE cadastro_sessoes_plenarias SET 
                                        ata 	 = :ata
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':ata',$ata);
                                $stmt->bindParam(':id',$id);
                                if($stmt->execute()){}else{$erro=1;}
                            }
                            if(!empty($files["name"]["anexo"]))
                            {
                                $nomeArquivo	= $files["name"]["anexo"];
                                $nomeTemporario = $files["tmp_name"]["anexo"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $anexo = $caminho;
                                $anexo .= "anexo".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($anexo));
                                $sql = "UPDATE cadastro_sessoes_plenarias SET 
                                        anexo 	 = :anexo
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':anexo',$anexo);
                                $stmt->bindParam(':id',$id);
                                if($stmt->execute()){}else{$erro=1;}
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
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Falha ao comunicar com o equipamento. Error: <?php echo $return['msg'];?>");
                    </script>
                    <?php
                }            
            }
            
            if($action == 'excluir')
            {
            
                
                // $sql = "SELECT texto_original FROM cadastro_sessoes_plenarias WHERE id = :id";
                // $stmt = $PDO_PROCLEGIS->prepare($sql);
                // $stmt->bindParam(':id',$id);
                // if($stmt->execute())
                // {
                //     $result = $stmt->fetch();
                //     $texto_original = $result['texto_original'];
                // }

                $sql = "UPDATE cadastro_sessoes_plenarias set ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id);
                $stmt->bindValue (':ativo',0);
                if($stmt->execute())
                {
                    // unlink($texto_original);
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
            
          
            if($action == 'editar_mesa')
            {
                
                // MESA - CAMPOS DINAMICOS
                if(!empty($_POST['mesa']) && is_array($_POST['mesa']))
                {
                    //LIMPA ARRAY
                    foreach($_POST['mesa'] as $item => $valor) 
                    {
                        $mesa_filtrado[$item] = array_filter($valor);
                    }
                    
                    //EXCLUI OS REMOVIDOS
                    $a_excluir = array();
                    foreach($mesa_filtrado as $item) 
                    {
                        if(isset($item['id']))
                        {
                            $a_excluir[] = $item['id'];
                        }
                    }
                    if(!empty($a_excluir))
                    {
                        $sql = "DELETE FROM cadastro_sessoes_plenarias_mesa WHERE sessao_plenaria = :id AND id NOT IN (".implode(",",$a_excluir).") ";
                        
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
                        $sql = "DELETE FROM cadastro_sessoes_plenarias_mesa WHERE sessao_plenaria = :id ";
                        $stmt = $PDO_PROCLEGIS->prepare($sql); 
                        $stmt->bindParam(':id', $id);
                        if($stmt->execute())
                        {
                            //echo "Excluido todos <br>";
                        }
                        else{ $erro=1; $err = $stmt->errorInfo();}
                    }
                    
                    foreach(array_filter($mesa_filtrado) as $item => $valor) 
                    {
                        //ATUALIZA EXISTENTES
                        if(isset($valor['id']))
                        {
                            $valor2 = $valor;
                            unset($valor2['id']);
                            
                            $sql = "UPDATE cadastro_sessoes_plenarias_mesa SET ".bindFields($valor2)." WHERE id = :id";
                            $stmt = $PDO_PROCLEGIS->prepare($sql);	
                            if($stmt->execute($valor))
                            {
                                //$id = $valor['id'];
                                //echo "Atualizado <br>";                                
                            }
                            else{ $erro=1; $err = $stmt->errorInfo();}                            
                        }
                        //INSERE NOVOS
                        else
                        {
                            $valor['sessao_plenaria'] = $id;
                            
                            $sql = "INSERT INTO cadastro_sessoes_plenarias_mesa SET ".bindFields($valor);
                            $stmt = $PDO_PROCLEGIS->prepare($sql);	
                            if($stmt->execute($valor))
                            {
                                //$id = $PDO_PROCLEGIS->lastInsertId();
                                //echo "Inserido <br>";
                               
                            }
                            else{ $erro=1; $err = $stmt->errorInfo();}                            
                        }
                    }
                }
                else
                {
                    $sql = "DELETE FROM cadastro_sessoes_plenarias_mesa WHERE sessao_plenaria = :id ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql); 
                    $stmt->bindParam(':id', $id);
                    if($stmt->execute())
                    {
                        //echo "Excluido todos <br>";
                    }
                    else{ $erro=1; $err = $stmt->errorInfo();}
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
            
            if($action == 'editar_ocorrencias')
            {
                
                $id_ocorrencia   = $_POST['id_ocorrencia'];
                $ocorrencias   = $_POST['ocorrencias'];
                
                $dados = array(
                    'sessao_plenaria' 		=> $id,
                    'ocorrencias' 		    => $ocorrencias
                    );

                if($id_ocorrencia != "")
                {
                    $sql = "UPDATE cadastro_sessoes_plenarias_ocorrencias SET ".bindFields($dados)." WHERE id = :id ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql); 
                    $dados['id'] =  $id_ocorrencia;
                }
                else
                {
                    $sql = "INSERT INTO cadastro_sessoes_plenarias_ocorrencias SET ".bindFields($dados)." ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                }
                                
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

            if($action == 'editar_presenca')
            {
                
                $presenca   = $_POST['presenca'];
                foreach($presenca as $key => $val)
                {
                    $sql = "SELECT * FROM cadastro_sessoes_plenarias_presenca
                            WHERE sessao_plenaria = :sessao_plenaria AND parlamentar = :parlamentar";
                    $stmt = $PDO_PROCLEGIS->prepare($sql); 
                    $stmt->bindParam(':sessao_plenaria', 	$id);
                    $stmt->bindParam(':parlamentar', 	    $val);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if($rows > 0)
                    {

                    }
                    else
                    {
                        
                        $sql = "INSERT INTO cadastro_sessoes_plenarias_presenca 
                                SET sessao_plenaria = :sessao_plenaria,
                                    parlamentar = :parlamentar ";
                        $stmt = $PDO_PROCLEGIS->prepare($sql);
                        $stmt->bindParam(':sessao_plenaria', 	$id);
                        $stmt->bindParam(':parlamentar', 	    $val);
                        if($stmt->execute()){}else{$erro=1;}
                    }

                   
                }

                if($presenca)
                {
                    $sql = "DELETE FROM cadastro_sessoes_plenarias_presenca 
                            WHERE sessao_plenaria = :sessao_plenaria AND parlamentar NOT IN (".implode(",",$presenca).") ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                    $stmt->bindParam(':sessao_plenaria', 	$id);
                    $stmt->bindParam(':parlamentar', 	    $val);
                    if($stmt->execute()){}else{$erro=1;}
                }
                else
                {
                    $sql = "DELETE FROM cadastro_sessoes_plenarias_presenca 
                            WHERE sessao_plenaria = :sessao_plenaria  ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                    $stmt->bindParam(':sessao_plenaria', 	$id);                    
                    if($stmt->execute()){}else{$erro=1;}
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
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php 
                }	               
            }

            if($action == 'editar_od_presenca')
            {
                
                $presenca   = $_POST['presenca'];
                foreach($presenca as $key => $val)
                {
                    $sql = "SELECT * FROM cadastro_sessoes_plenarias_od_presenca
                            WHERE sessao_plenaria = :sessao_plenaria AND parlamentar = :parlamentar";
                    $stmt = $PDO_PROCLEGIS->prepare($sql); 
                    $stmt->bindParam(':sessao_plenaria', 	$id);
                    $stmt->bindParam(':parlamentar', 	    $val);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if($rows > 0)
                    {

                    }
                    else
                    {
                        
                        $sql = "INSERT INTO cadastro_sessoes_plenarias_od_presenca 
                                SET sessao_plenaria = :sessao_plenaria,
                                    parlamentar = :parlamentar ";
                        $stmt = $PDO_PROCLEGIS->prepare($sql);
                        echo $id. "- ".$val;
                        $stmt->bindParam(':sessao_plenaria', 	$id);
                        $stmt->bindParam(':parlamentar', 	    $val);
                        if($stmt->execute()){}else{$erro=1; echo "ccc";}
                    }

                   
                }

                if($presenca)
                {
                    $sql = "DELETE FROM cadastro_sessoes_plenarias_od_presenca 
                            WHERE sessao_plenaria = :sessao_plenaria AND parlamentar NOT IN (".implode(",",$presenca).") ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                    $stmt->bindParam(':sessao_plenaria', 	$id);
                    $stmt->bindParam(':parlamentar', 	    $val);
                    if($stmt->execute()){}else{$erro=1; echo "bbb";}
                }
                else
                {
                    $sql = "DELETE FROM cadastro_sessoes_plenarias_od_presenca 
                            WHERE sessao_plenaria = :sessao_plenaria  ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                    $stmt->bindParam(':sessao_plenaria', 	$id);                    
                    if($stmt->execute()){}else{$erro=1; echo "aaa";}
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
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php 
                }	               
            }

            if($action == 'editar_exp_diversos')
            {
                $valores = array_combine($_POST['tipo_expediente'], $_POST['conteudo']);
               
                foreach($valores as $key => $val)
                {
                    $sql = "SELECT * FROM cadastro_sessoes_plenarias_exp_diversos 
                            WHERE sessao_plenaria = :sessao_plenaria AND
                                  tipo_expediente = :tipo_expediente";
                    $stmt = $PDO_PROCLEGIS->prepare($sql); 
                    $stmt->bindParam(':sessao_plenaria', 	$id);
                    $stmt->bindParam(':tipo_expediente', 	$key);
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if($rows > 0)
                    {
                        $result = $stmt->fetch();
                        $sql = "UPDATE cadastro_sessoes_plenarias_exp_diversos 
                                SET conteudo = :conteudo 
                                WHERE id = :id ";
                        $stmt = $PDO_PROCLEGIS->prepare($sql); 
                        $stmt->bindParam(':conteudo', 	$val);
                        $stmt->bindParam(':id', 	$result['id']);
                        if($stmt->execute()){}else{$erro=1;}
                    }
                    else
                    {
                        $sql = "INSERT INTO cadastro_sessoes_plenarias_exp_diversos 
                                SET sessao_plenaria = :sessao_plenaria,
                                    conteudo = :conteudo,
                                    tipo_expediente = :tipo_expediente ";
                        $stmt = $PDO_PROCLEGIS->prepare($sql); 
                        
                        $stmt->bindParam(':sessao_plenaria', 	$id);
                        $stmt->bindParam(':tipo_expediente', 	$key);
                        $stmt->bindParam(':conteudo', 	$val);
                        if($stmt->execute()){}else{$erro=1;}
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
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php 
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
                $tipo_query = " (cadastro_sessoes_plenarias.tipo_sessao = :fil_tipo) ";
            }
            $fil_numero = $_REQUEST['fil_numero'];
            if($fil_numero == '')
            {
                $numero_query = " 1 = 1 ";
            }
            else
            {
                $numero_query = " (cadastro_sessoes_plenarias.numero = :fil_numero) ";
            }
            $fil_ano = $_REQUEST['fil_ano'];
            if($fil_ano == '')
            {
                $ano_query = " 1 = 1 ";
            }
            else
            {
                $ano_query = " ( YEAR(cadastro_sessoes_plenarias.data_abertura) = :fil_ano) ";
            }
            $sql = "SELECT *, cadastro_sessoes_plenarias.id as id
                            , cadastro_sessoes_plenarias.nome as nome
                            , cadastro_sessoes_plenarias.numero as numero
                            , aux_parlamentares_legislaturas.numero as numero_legislatura
                            , YEAR(aux_parlamentares_legislaturas.data_inicio) as data_inicio_legislatura
                            , YEAR(aux_parlamentares_legislaturas.data_fim) as data_fim_legislatura
                            , aux_mesa_diretora_sessoes.numero as numero_sessao
                            , YEAR(aux_mesa_diretora_sessoes.data_inicio) as data_inicio_sessao
                            , YEAR(aux_mesa_diretora_sessoes.data_fim) as data_fim_sessao
                    FROM cadastro_sessoes_plenarias 
                    LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_sessoes_plenarias.legislatura                     
                    LEFT JOIN aux_mesa_diretora_sessoes ON aux_mesa_diretora_sessoes.id = cadastro_sessoes_plenarias.sessao                     
                    LEFT JOIN aux_sessoes_plenarias_tipos ON aux_sessoes_plenarias_tipos.id = cadastro_sessoes_plenarias.tipo_sessao                     
                    WHERE cadastro_sessoes_plenarias.ativo = :ativo AND ".$tipo_query." AND ".$numero_query." AND  ".$ano_query."
                    ORDER BY cadastro_sessoes_plenarias.id DESC
                    LIMIT :primeiro_registro, :num_por_pagina ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);    
            $stmt->bindParam(':fil_tipo', 	$fil_tipo);
            $stmt->bindParam(':fil_numero', 	$fil_numero);                
            $stmt->bindParam(':fil_ano', 	$fil_ano);          
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
                    <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"".$pagina_link."/add\");'><i class='fas fa-plus'></i></div>
                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_sessoes_plenarias/view'>
                        <select name='fil_tipo' id='fil_tipo'>
                            <option  value=''>Tipo de Sessão</option>
                            ";
                            
                            $sql = " SELECT * FROM aux_sessoes_plenarias_tipos 

                                    ORDER BY descricao";
                            $stmt_filtro = $PDO_PROCLEGIS->prepare($sql);
                                                                
                            $stmt_filtro->execute();
                            while($result_filtro = $stmt_filtro->fetch())
                            {
                                echo "<option value='".$result_filtro['id']."' ";if($_REQUEST['fil_tipo'] == $result_filtro['id']) echo " selected "; echo ">".$result_filtro['descricao']."</option>";
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
                          
                           
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td>
                                        <p class='bold hand' style='font-size:16px; text-decoration:underline;' onclick='verificaPermissao(".$permissoes["view"].",\"".$pagina_link."/exib/$id?pag=$pag\");'>
                                            ";
                                            if($result['nome'] != "")
                                            {
                                                echo $result['nome'];
                                            }
                                            else
                                            {
                                                echo $result['numero']."ª ".$result['descricao']." da ".$result['numero_sessao']." Sessão Legislativa da ".$result['numero_legislatura']." Legislatura ";
                                            }
                                            echo "
                                        </p>
                                        <span class='bold'>Tipo:</span>  ".$result['descricao']."<p>
                                        <span class='bold'>Data abertura:</span> ".reverteData($result['data_abertura'])." às ".substr($result['hora_abertura'],0,5)."<p>
                                        <span class='bold'>Legislatura:</span> ".$result['numero_legislatura']." (".$result['data_inicio_legislatura']." - ".$result['data_fim_legislatura'].")<p>
                                        <span class='bold'>Sessão legislativa:</span>  ".$result['numero_sessao']." (".$result['data_inicio_sessao']." - ".$result['data_fim_sessao'].")<p>
                                        "; if($result['pauta']){ echo "<span class='bold'>Pauta:</span> <a href='".$result['pauta']."' target='_blank'><i class='fas fa-file-alt' style='font-size:20px;'></i></a><p>";} echo "
                                        "; if($result['ata']){ echo "<span class='bold'>Ata:</span> <a href='".$result['ata']."' target='_blank'><i class='fas fa-file-alt' style='font-size:20px;'></i></a><p>";} echo "
                                        "; if($result['anexo']){ echo "<span class='bold'>Anexo:</span> <a href='".$result['anexo']."' target='_blank'><i class='fas fa-file-alt' style='font-size:20px;'></i></a><p>";} echo "
                                        
                                    </td>                                    
                                    <td align=center width='150'>
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
                        $cnt = "SELECT COUNT(*) FROM cadastro_sessoes_plenarias  
                                WHERE ".$tipo_query." AND ".$numero_query." AND  ".$ano_query."  ";
                        $stmt = $PDO_PROCLEGIS->prepare($cnt);     
                        $stmt->bindParam(':fil_tipo', 	$fil_tipo);
                        $stmt->bindParam(':fil_numero', 	$fil_numero);                
                        $stmt->bindParam(':fil_ano', 	$fil_ano);    
                        $variavel = "&fil_tipo=$fil_tipo&fil_numero=$fil_numero&fil_ano=$fil_ano";            
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
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_sessoes_plenarias/view/adicionar'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                                                  
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Nome da Sessão:</label> <input name='nome' id='nome' placeholder='Nome da Sessão'>
                                
                                <p><label>Legislatura*:</label> <select name='legislatura' id='legislatura' class='obg'>
                                    <option value=''>Legislatura</option>";
                                        $sql = "SELECT *, YEAR(data_inicio) as data_inicio, YEAR(data_fim) as data_fim 
                                                FROM aux_parlamentares_legislaturas 
                                                 
                                                ORDER BY numero";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);      
                                                                                                                                                   
                                        $stmt->execute();
                                        while($result = $stmt->fetch())
                                        {
                                            echo "<option value='".$result['id']."'>".$result['numero']." (".$result['data_inicio']." - ".$result['data_fim'].")</option>";
                                        }
                                    echo "
                                </select>
                                <p><label>Sessão Legislativa*:</label> <select name='sessao' id='sessao' class='obg'>
                                    <option value=''>Sessão Legislativa</option>                                                                       
                                </select>
                                <p><label>Tipo de Sessão*:</label> <select name='tipo_sessao' id='tipo_sessao' class='obg'>
                                    <option value=''>Tipo de Sessão</option>";
                                        $sql = "SELECT *
                                                FROM aux_sessoes_plenarias_tipos 
                                                 
                                                ORDER BY descricao";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);      
                                                                                                                                                   
                                        $stmt->execute();
                                        while($result = $stmt->fetch())
                                        {
                                            echo "<option value='".$result['id']."'>".$result['descricao']."</option>";
                                        }
                                    echo "
                                </select>
                                <p><label>Número *:</label> <input name='numero' id='numero' placeholder='Número' class='obg'>
                                <p><label>Data abertura *:</label> <input name='data_abertura' placeholder='Data abertura' class='obg' onkeypress='return mascaraData(this,event);'>
                                <p><label>Hora abertura *:</label> <input name='hora_abertura' placeholder='Hora abertura' class='obg' maxlength='5' onkeypress='return mascaraHorario(this,event);'>
                                <p><label>Sessão iniciada?</label> <select name='iniciada' id='iniciada'>
                                    <option value=''>Sessão iniciada?</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>                                    
                                </select>
                                <p><label>Data encerramento:</label> <input name='data_encerramento' placeholder='Data encerramento' onkeypress='return mascaraData(this,event);'>
                                <p><label>Hora encerramento:</label> <input name='hora_encerramento' placeholder='Hora encerramento' maxlength='5' onkeypress='return mascaraHorario(this,event);'>
                                <p><label>Sessão finalizada?</label> <select name='finalizada' id='finalizada'>
                                    <option value=''>Sessão finalizada?</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>                                    
                                </select>
                                <p><label>Pauta:</label> <input type='file' name='pauta[pauta]' id='pauta' placeholder='Pauta'> 
                                <p><label>Ata:</label> <input   type='file' name='ata[ata]' id='ata' placeholder='Ata'> 
                                <p><label>Anexo:</label> <input type='file' name='anexo[anexo]' id='anexo' placeholder='Anexo'> 
                                <p><label>URL Áudio:</label> <input name='url_audio' id='url_audio' placeholder='URL Aúdio (http://)'>
                                <p><label>URL Vídeo:</label> <input name='url_video' id='url_video' placeholder='URL Vídeo (http://)'>                                                         
                            </div>	                                                                                                    
                        </div>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_sessoes_plenarias/view'; value='Cancelar'/></center>
                        </center>
                    </div>
                </form>
                ";
            }
            
            if($pagina == 'edit')
            {            		
                $sql = "SELECT *, cadastro_sessoes_plenarias.id as id
                                , cadastro_sessoes_plenarias.nome as nome
                                , cadastro_sessoes_plenarias.numero as numero
                                , aux_parlamentares_legislaturas.numero as numero_legislatura
                                , YEAR(aux_parlamentares_legislaturas.data_inicio) as data_inicio_legislatura
                                , YEAR(aux_parlamentares_legislaturas.data_fim) as data_fim_legislatura
                                , aux_mesa_diretora_sessoes.numero as numero_sessao
                                , YEAR(aux_mesa_diretora_sessoes.data_inicio) as data_inicio_sessao
                                , YEAR(aux_mesa_diretora_sessoes.data_fim) as data_fim_sessao
                        FROM cadastro_sessoes_plenarias 
                        LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_sessoes_plenarias.legislatura                     
                        LEFT JOIN aux_mesa_diretora_sessoes ON aux_mesa_diretora_sessoes.id = cadastro_sessoes_plenarias.sessao                     
                        LEFT JOIN aux_sessoes_plenarias_tipos ON aux_sessoes_plenarias_tipos.id = cadastro_sessoes_plenarias.tipo_sessao                                         
                        WHERE cadastro_sessoes_plenarias.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();                                                                                                                 
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_sessoes_plenarias/view/editar/$id'>
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                                                                              
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Nome da Sessão:</label> <input name='nome' id='nome' value='".$result['nome']."' placeholder='Nome da Sessão'>
                                <p><label>Legislatura *:</label> <select name='legislatura' id='legislatura' class='obg'>
                                    <option value='".$result['legislatura']."'>".$result['numero_legislatura']." (".$result['data_inicio_legislatura']. " - ".$result['data_fim_legislatura'].")</option>";
                                    $sql = "SELECT *, YEAR(data_inicio) as data_inicio, YEAR(data_fim) as data_fim 
                                            FROM aux_parlamentares_legislaturas 
                                             
                                            ORDER BY numero";
                                    $stmt_int = $PDO_PROCLEGIS->prepare($sql);
                                                                                                         								
                                    $stmt_int->execute();
                                    while($result_int = $stmt_int->fetch())
                                    {
                                        echo "<option value='".$result_int['id']."'>".$result_int['numero']." (".$result_int['data_inicio']." - ".$result_int['data_fim'].")</option>";
                                    }
                                    echo "
                                </select>
                                <p><label>Sessão Legislativa*:</label> <select name='sessao' id='sessao' class='obg'>
                                    <option value='".$result['sessao']."'>".$result['numero_sessao']." (".$result['data_inicio_sessao']. " - ".$result['data_fim_sessao'].")</option>";
                                    $sql = "SELECT *, YEAR(data_inicio) as data_inicio, YEAR(data_fim) as data_fim 
		                                    FROM aux_mesa_diretora_sessoes 
                                            WHERE legislatura = :legislatura ";
                                    $stmt_int = $PDO_PROCLEGIS->prepare($sql);      
                                    $stmt_int->bindParam(':legislatura', 	$result['legislatura']);                                                                                                           
                                    $stmt_int->execute();
                                    while($result_int = $stmt_int->fetch())
                                    {
                                        echo "<option value='".$result_int['id']."'>".$result_int['numero']." (".$result_int['data_inicio']." - ".$result_int['data_fim'].")</option>";
                                    }
                                    echo "
                                </select>
                                <p><label>Tipo Sessão*:</label> <select name='tipo_sessao' id='tipo_sessao' class='obg'>
                                    <option value='".$result['tipo_sessao']."'>".$result['descricao']."</option>";
                                    $sql = "SELECT *
                                            FROM aux_sessoes_plenarias_tipos 
                                             
                                            ORDER BY descricao";
                                    $stmt_int = $PDO_PROCLEGIS->prepare($sql);      
                                                                                                                                               
                                    $stmt_int->execute();
                                    while($result_int = $stmt_int->fetch())
                                    {
                                        echo "<option value='".$result_int['id']."'>".$result_int['descricao']."</option>";
                                    }
                                    echo "
                                </select>
                                <p><label>Número *:</label> <input name='numero' id='numero' value='".$result['numero']."' placeholder='Número'  class='obg'>
                                <p><label>Data abertura *:</label> <input name='data_abertura' value='".reverteData($result['data_abertura'])."'   class='obg' placeholder='Data abertura' onkeypress='return mascaraData(this,event);'>
                                <p><label>Hora abertura *:</label> <input name='hora_abertura' value='".substr($result['hora_abertura'],0,5)."'    class='obg' placeholder='Hora abertura' maxlength='5' onkeypress='return mascaraHorario(this,event);'>
                                <p><label>Iniciada?</label> <select name='iniciada' id='iniciada'>
                                    <option value='".$result['iniciada']."'>".$result['iniciada']."</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>                                    
                                </select>  
                                <p><label>Data encerramento:</label> <input name='data_encerramento' value='".reverteData($result['data_encerramento'])."'  placeholder='Data encerramento' onkeypress='return mascaraData(this,event);'>
                                <p><label>Hora encerramento:</label> <input name='hora_encerramento' value='".substr($result['hora_encerramento'],0,5)."'  placeholder='Hora encerramento' maxlength='5' onkeypress='return mascaraHorario(this,event);'>
                                <p><label>Finalizada?</label> <select name='finalizada' id='finalizada'>
                                    <option value='".$result['finalizada']."'>".$result['finalizada']."</option>
                                    <option value='Sim'>Sim</option>
                                    <option value='Não'>Não</option>                                    
                                </select>                              
                                <p><label>Pauta:</label> ";if($result['pauta'] != ''){ echo "<a href='".$result['pauta']."' target='_blank'><i class='fas fa-paperclip' style='float:left;'></i></a>";} echo " &nbsp; 
                                <p><label>Alterar Pauta:</label> <input type='file' name='pauta[pauta]'>
                                <p><label>Ata:</label> ";if($result['ata'] != ''){ echo "<a href='".$result['ata']."' target='_blank'><i class='fas fa-paperclip' style='float:left;'></i></a>";} echo " &nbsp; 
                                <p><label>Alterar Ata:</label> <input type='file' name='ata[ata]'>
                                <p><label>Anexo:</label> ";if($result['pauta'] != ''){ echo "<a href='".$result['anexo']."' target='_blank'><i class='fas fa-paperclip' style='float:left;'></i></a>";} echo " &nbsp; 
                                <p><label>Alterar Anexo:</label> <input type='file' name='anexo[anexo]'>
                                <p><label>URL Áudio:</label> <input name='url_audio' id='url_audio' value='".$result['url_audio']."'  placeholder='URL Áudio'>
                                <p><label>URL Vídeo:</label> <input name='url_video' id='url_video' value='".$result['url_video']."'  placeholder='URL Vídeo'>
                                ";
                                if($result['tema_solene'])
                                {
                                    echo "<p><label>Tema Solene:</label> <textarea name='tema_solene' id='tema_solene' placeholder='Tema Solene'>".$result['tema_solene']."</textarea>";
                                }
                                echo "
                            </div>                                                                                                 				
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_sessoes_plenarias/view'; value='Cancelar'/></center>
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
                
                        
                
                $sql = "SELECT *, cadastro_sessoes_plenarias.id as id
                                , cadastro_sessoes_plenarias.numero as numero
                                , aux_parlamentares_legislaturas.numero as numero_legislatura
                                , YEAR(aux_parlamentares_legislaturas.data_inicio) as data_inicio_legislatura
                                , YEAR(aux_parlamentares_legislaturas.data_fim) as data_fim_legislatura
                                , aux_mesa_diretora_sessoes.numero as numero_sessao
                                , YEAR(aux_mesa_diretora_sessoes.data_inicio) as data_inicio_sessao
                                , YEAR(aux_mesa_diretora_sessoes.data_fim) as data_fim_sessao
                        FROM cadastro_sessoes_plenarias 
                        LEFT JOIN cadastro_sessoes_plenarias_cronometro ON cadastro_sessoes_plenarias_cronometro.sessao = cadastro_sessoes_plenarias.id                     
                        LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_sessoes_plenarias.legislatura                     
                        LEFT JOIN aux_mesa_diretora_sessoes ON aux_mesa_diretora_sessoes.id = cadastro_sessoes_plenarias.sessao                     
                        LEFT JOIN aux_sessoes_plenarias_tipos ON aux_sessoes_plenarias_tipos.id = cadastro_sessoes_plenarias.tipo_sessao                                      
                        WHERE cadastro_sessoes_plenarias.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();    
                    $sessao = $result['id'];
                    $t_discurso = $result['discurso'];
                    
                    $total_discurso = strtotime('1970-01-01 '.$t_discurso .'UTC');                    
                    $t_aparte = $result['aparte'];
                    $total_aparte = strtotime('1970-01-01 '.$t_aparte .'UTC');                    
                    $t_ordem = $result['ordem'];
                    $total_ordem = strtotime('1970-01-01 '.$t_ordem .'UTC');                    
                    $t_consideracoes = $result['consideracoes'];
                    $total_consideracoes = strtotime('1970-01-01 '.$t_consideracoes .'UTC');                    
                                                                 
                    echo "
                        <div class='titulo'> $page &raquo; Exibir </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            <li><a data-toggle='tab' href='#abertura' id='abertura-tab'>Abertura</a></li>        
                            <li><a data-toggle='tab' href='#expediente' id='expediente-tab'>Expediente</a></li>
                            <li><a data-toggle='tab' href='#ordem_dia' id='ordem_dia-tab'>Ordem do Dia</a></li>    
                            <!--<li><a data-toggle='tab' href='#resumo' id='resumo-tab'>Resumo</a></li>    -->
                            <li><a data-toggle='tab' href='#painel' id='painel-tab'>Painel Eletrônico</a></li>                                                                
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active' >
                                <div style='display:table; width:100%'>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Nome Sessão:</div>
                                        <div class='exib_value'>".$result['nome']."</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Legislatura:</div>
                                        <div class='exib_value'>".$result['numero_legislatura']." (".$result['data_inicio_legislatura']." - ".$result['data_fim_legislatura'].") &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Sessão:</div>
                                        <div class='exib_value'>".$result['numero_sessao']." (".$result['data_inicio_sessao']." - ".$result['data_fim_sessao'].") &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Tipo de Sessão:</div>
                                        <div class='exib_value'>".$result['descricao']."&nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Número:</div>
                                        <div class='exib_value'>".$result['numero']." &nbsp;</div>
                                    </div>                                    
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Data abertura:</div>
                                        <div class='exib_value'>".reverteData($result['data_abertura'])." às ".substr($result['hora_abertura'],0,5)." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Iniciada?</div>
                                        <div class='exib_value'>".$result['iniciada']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Data encerramento:</div>
                                        <div class='exib_value'>".reverteData($result['data_encerramento'])." às ".substr($result['hora_encerramento'],0,5)." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Finalizada?</div>
                                        <div class='exib_value'>".$result['finalizada']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Pauta:</div>
                                        <div class='exib_value'>"; ;if($result['pauta'] != ''){ echo "<a href='".$result['pauta']."' target='_blank'><i class='fas fa-paperclip' style='float:left;'></i></a>";} echo " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Ata:</div>
                                        <div class='exib_value'>"; ;if($result['ata'] != ''){ echo "<a href='".$result['ata']."' target='_blank'><i class='fas fa-paperclip' style='float:left;'></i></a>";} echo " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Anexo:</div>
                                        <div class='exib_value'>"; ;if($result['anexo'] != ''){ echo "<a href='".$result['anexo']."' target='_blank'><i class='fas fa-paperclip' style='float:left;'></i></a>";} echo " &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>&nbsp;</div>
                                        <div class='exib_value'>&nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>URL Áudio:</div>
                                        <div class='exib_value'>".$result['url_audio']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>URL Vídeo:</div>
                                        <div class='exib_value'>".$result['url_video']." &nbsp;</div>
                                    </div>
                                    ";
                                    if($result['tema_solene'] != "")
                                    {
                                        echo "
                                        <div class='exib_bloco_long'>
                                            <div class='exib_label'>Tema Solene:</div>
                                            <div class='exib_value'>".$result['tema_solene']." &nbsp;</div>
                                        </div> ";
                                    }
                                    echo "
                                </div>                                                                                                                                              
                            </div>                        
                            <div id='abertura' class='tab-pane fade in'>
                                <div style='width:100%; display:table;'>
                                    <a href='cadastro_sessoes_plenarias_mesa/$id/view'>
                                        <div class='modulos'>
                                        Mesa
                                        </div>
                                    </a>
                                    <a href='cadastro_sessoes_plenarias_presenca/$id/view'>
                                        <div class='modulos'>
                                        Presença
                                        </div>
                                    </a>
                                    <a href='cadastro_sessoes_plenarias_ab_ausencias/$id/view'>
                                        <div class='modulos'>
                                        Justificativas de Ausências
                                        </div>
                                    </a>
                                    <a href='cadastro_sessoes_plenarias_oradores_explicacoes/$id/view'>
                                        <div class='modulos'>
                                        Oradores das Explicações Pessoais
                                        </div>
                                    </a>
                                    <a href='cadastro_sessoes_plenarias_ocorrencias/$id/view'>
                                        <div class='modulos'>
                                        Ocorrências da Sessão
                                        </div>
                                    </a>
                                    <a href='cadastro_sessoes_plenarias_ab_retirada_pauta/$id/view'>
                                        <div class='modulos'>
                                        Retirada de Pauta
                                        </div>
                                    </a>

                                </div>
                            </div>
                            <div id='expediente' class='tab-pane fade in'>
                                <div style='width:100%; display:table;'>
                                    <a href='cadastro_sessoes_plenarias_exp_diversos/$id/view'>
                                        <div class='modulos'>
                                        Diversos
                                        </div>
                                    </a>
                                    <a href='cadastro_sessoes_plenarias_exp_materias/$id/view'>
                                        <div class='modulos'>
                                        Matérias Expediente
                                        </div>
                                    </a>
                                    <a href='cadastro_sessoes_plenarias_exp_oradores/$id/view'>
                                        <div class='modulos'>
                                        Oradores do Expediente
                                        </div>
                                    </a>
                                    <!--<a href='cadastro_sessoes_plenarias_exp_votacao_bloco/$id/view'>
                                        <div class='modulos'>
                                        Votação em Bloco
                                        </div>
                                    </a>                     -->              
                                </div>
                            </div>  
                            <div id='ordem_dia' class='tab-pane fade in'>
                                <div style='width:100%; display:table;'>
                                    <a href='cadastro_sessoes_plenarias_od_materias/$id/view'>
                                        <div class='modulos'>
                                        Matérias Ordem do Dia
                                        </div>
                                    </a>
                                    <a href='cadastro_sessoes_plenarias_od_presenca/$id/view'>
                                        <div class='modulos'>
                                        Presença Ordem do Dia
                                        </div>
                                    </a>
                                    
                                    <a href='cadastro_sessoes_plenarias_od_oradores/$id/view'>
                                        <div class='modulos'>
                                        Oradores Ordem Do Dia
                                        </div>
                                    </a>
                                    <!--<a href='cadastro_sessoes_plenarias_od_votacao_bloco/$id/view'>
                                        <div class='modulos'>
                                        Votação em Bloco
                                        </div>-->
                                    </a>                                   
                                </div>
                            </div>
                            <div id='resumo' class='tab-pane fade in'>
                                <div style='width:100%; display:table;'>
                                    <a href='cadastro_sessoes_plenarias_resumo/$id/view'>
                                        <div class='modulos'>
                                        Resumo
                                        </div>
                                    </a>
                                    <a href='cadastro_sessoes_plenarias_resumo_extrato/$id/view'>
                                        <div class='modulos'>
                                        Extrato
                                        </div>
                                    </a>                                                                                                   
                                </div>
                            </div>    
                            <div id='painel' class='tab-pane fade in'>
                                ".$result['numero']."ª Sessão ".$result['descricao']." da ".$result['numero_legislatura']." Legislatura
                                <p style='font-size: 25px; font-weight:bold;' ><script>writeclock();</SCRIPT></p>

                                <!--<input type='button' value='Iniciar Painel'>
                                <input type='button' value='Parar Painel'>-->
                                <input type='button' value='Abrir Painel' onclick='window.open(\"painel_eletronico/$id\", \"_blank\", \"toolbar=no,scrollbars=yes,resizable=yes,top=0,left=0,width=4000,height=4000\");'>
                                <p> Operação do cronômetro <p>
                                <label>Discurso: </label> 
                                <input type='text' value='".$t_discurso."' id='cron_discurso' style='width:150px'> 
                                <input type='button' value='Iniciar' onclick='startCountdown1(\"".$total_discurso."\",\"".$sessao."\",\"start\")'>
                                <input type='button' value='Zerar' onclick='startCountdown1(\"".$total_discurso."\",\"".$sessao."\",\"stop\")'>
                                <p>
                                <label>Aparte: </label>
                                <input type='text' value='".$t_aparte."' id='cron_aparte' style='width:150px'> 
                                <input type='button' value='Iniciar'  onclick='startCountdown2(\"".$total_aparte."\",\"".$sessao."\",\"start\")'>
                                <input type='button' value='Zerar' onclick='startCountdown2(\"".$total_aparte."\",\"".$sessao."\",\"stop\")'>
                                <p>
                                <label>Questão de Ordem: </label>
                                <input type='text' value='".$t_ordem."' id='cron_ordem' style='width:150px'> 
                                <input type='button' value='Iniciar'  onclick='startCountdown3(\"".$total_ordem."\",\"".$sessao."\",\"start\")'>
                                <input type='button' value='Zerar' onclick='startCountdown3(\"".$total_ordem."\",\"".$sessao."\",\"stop\")'>
                                <p>
                                <label>Considerações Finais: </label>
                                <input type='text' value='".$t_consideracoes."' id='cron_consideracoes' style='width:150px'> 
                                <input type='button' value='Iniciar'  onclick='startCountdown4(\"".$total_consideracoes."\",\"".$sessao."\",\"start\")'>
                                <input type='button' value='Zerar' onclick='startCountdown4(\"".$total_consideracoes."\",\"".$sessao."\",\"stop\")'>
                            </div>                          
                            <br>               				
                            <center>                                                        
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_sessoes_plenarias/view'; value='Voltar'/></center>
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
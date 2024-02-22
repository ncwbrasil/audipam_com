<?php
$pagina_link = 'protocolo_gerais';
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
            $page = "Protocolo &raquo; <a href='protocolo_gerais/view'>Listar Todos</a>";
            if(isset($_GET['id'])){$id = $_GET['id'];}
            $natureza  = $_POST['natureza'];
            $tipo_documento  = $_POST['tipo_documento'];
            $tipo_materia  = $_POST['tipo_materia'];
            $documento  = $_POST['documento'];
            $materia  = $_POST['materia'];
            $interessado   = $_POST['interessado'];
            $descricao   = $_POST['descricao'];
            
           
            //NUMERAÇÃO AUTOMÁTICA
            if($action == "adicionar_documento")
            {
                $ano   = date('Y');
                $sql = "SELECT numero 
                        FROM protocolo_gerais 
                        WHERE ano = :ano
                        
                        ORDER BY numero DESC 
                        LIMIT 0, 1";
                $stmt = $PDO_PROCLEGIS->prepare($sql);                        
                $stmt->bindParam(':ano', $ano);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();
                    $numero = $result['numero']+1;
                }
                else
                {
                    $numero = 1;
                }
            }
            

            

            $dados = array(                
                'natureza' 		    => $natureza,
                'tipo_documento' => $tipo_documento,
                'tipo_materia' => $tipo_materia,
                'documento' => $documento,
                'materia' => $materia,
                'numero' 		=> $numero,
                'ano' 		    => $ano,                
                'interessado' 	=> $interessado,
                'descricao' 	=> $descricao,
                'cadastrado_por'=> $_SESSION['usuario_id'],                
                );
        
            if($action == "adicionar")
            {                                   
                $sql = "INSERT INTO protocolo_gerais SET ".bindFields($dados);
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
                                
                               
                                $sql = "UPDATE protocolo_gerais SET 
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
                
                $sql = "UPDATE protocolo_gerais SET ".bindFields($dados)." WHERE id = :id ";
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

                                $sql = "UPDATE protocolo_gerais SET 
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
                $sql = "DELETE FROM protocolo_gerais WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id);
                if($stmt->execute())
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
            
            if($action == "adicionar_documento")
            {    
                // VERIFICA SE O DOCUMENTO JÁ ESTA PROTOCOLADO     
                $sql = "SELECT *FROM protocolo_gerais 
                        WHERE documento = :documento 
                        ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);    
                $stmt->bindParam(':documento', 	$documento);                                   
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();
                    $id_doc = $result['id'];
                    $numero = $result['numero'];
                    $ano = $result['ano'];
                    ?>
                    <script>
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Não foi possível concluir a operação pois este documento já esta protocolado: <a href='protocolo_gerais/exib/<?php echo $id_doc;?>'><?php echo str_pad($numero,6,"0",STR_PAD_LEFT);?>/<?php echo $ano;?> clique aqui para acessar.</a>");
                    </script>
                    <?php 
                }
                else
                {
                    $sql = "INSERT INTO protocolo_gerais SET ".bindFields($dados);
                    $stmt = $PDO_PROCLEGIS->prepare($sql);	
                    if($stmt->execute($dados))
                    {		
                        $id = $PDO_PROCLEGIS->lastInsertId();

                        
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
                                mensagem("X","<i class='fa fa-exclamation-circle'></i> Falha ao realizar o cadastro Error: <?php echo $return['msg'];?>");
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
                
            }

            $num_por_pagina = 10;
            if(!$pag){$primeiro_registro = 0; $pag = 1;}
            else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
            $fil_tipo_documento = $_REQUEST['fil_tipo_documento'];
            if($fil_tipo_documento == '')
            {
                $tipo_documento_query = " 1 = 1 ";
            }
            else
            {
                $tipo_documento_query = " (protocolo_gerais.tipo_documento = :fil_tipo_documento) ";
            }
            $fil_natureza = $_REQUEST['fil_natureza'];
            if($fil_natureza == '')
            {
                $natureza_query = " 1 = 1 ";
            }
            else
            {
                $natureza_query = " (protocolo_gerais.natureza = :fil_natureza) ";
            }
            
            $fil_tipo_materia = $_REQUEST['fil_tipo_materia'];
            if($fil_tipo_materia == '')
            {
                $tipo_materia_query = " 1 = 1 ";
            }
            else
            {
                $tipo_materia_query = " (protocolo_gerais.tipo_materia = :fil_tipo_materia) ";
            }
            $fil_numero = $_REQUEST['fil_numero'];
            if($fil_numero == '')
            {
                $numero_query = " 1 = 1 ";
            }
            else
            {
                $numero_query = " (protocolo_gerais.numero = :fil_numero) ";
            }
            $fil_ano = $_REQUEST['fil_ano'];
            if($fil_ano == '')
            {
                $ano_query = " 1 = 1 ";
            }
            else
            {
                $ano_query = " (protocolo_gerais.ano = :fil_ano) ";
            }
                        
            if($pagina == "view")
            {
                $sql = "SELECT *,   t1.nome as tipo_doc_nome,
                                    t1.sigla as tipo_doc_sigla,
                                    t2.nome as tipo_mat_nome,
                                    t2.sigla as tipo_mat_sigla, 
                                    t3.numero as numero_doc,
                                    t3.ano as ano_doc,
                                    t4.numero as numero_mat,
                                    t4.ano as ano_mat,   
                                    protocolo_gerais.id as id,
                                    protocolo_gerais.numero as numero,
                                    protocolo_gerais.ano as ano,
                                    protocolo_gerais.data_cadastro as data_cadastro

                        FROM protocolo_gerais 
                        LEFT JOIN aux_administrativo_tipo_documento t1 ON t1.id = protocolo_gerais.tipo_documento                                                                                                         
                        LEFT JOIN aux_materias_tipos t2 ON t2.id = protocolo_gerais.tipo_materia                                                                                                         
                        LEFT JOIN docadm_documentos t3 ON t3.id = protocolo_gerais.documento
                        LEFT JOIN cadastro_materias t4 ON t4.id = protocolo_gerais.materia                                                                                                         
                        LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = protocolo_gerais.cadastrado_por   
                        WHERE ".$natureza_query ." AND ".$tipo_documento_query." AND  ".$tipo_materia_query." AND  ".$numero_query." AND  ".$ano_query."
                        GROUP BY protocolo_gerais.id			
                        ORDER BY protocolo_gerais.id DESC
                        LIMIT :primeiro_registro, :num_por_pagina ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);    
                $stmt->bindParam(':fil_natureza', 	$fil_natureza);                
                $stmt->bindParam(':fil_tipo_documento', 	$fil_tipo_documento);                
                $stmt->bindParam(':fil_tipo_materia', 	$fil_tipo_materia);                
                $stmt->bindParam(':fil_numero', 	$fil_numero);                
                $stmt->bindParam(':fil_ano', 	$fil_ano);                
                $stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
                $stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
                $stmt->execute();
                $rows = $stmt->rowCount();
                
                echo "
                <div class='titulo'> $page  </div>
                <div id='botoes'>
                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                    <div class='filtro'>
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='protocolo_gerais/view'>
                        <input name='fil_numero' id='fil_numero' value='$fil_numero' placeholder='Número'>
                        <input name='fil_ano' id='fil_ano' value='$fil_ano' placeholder='Ano'>                        
                        <select name='fil_natureza' id='fil_natureza'>
                            <option  value=''>Natureza</option>
                            <option  value='Administrativo'>Administrativo</option>
                            <option  value='Legislativa'>Legislativa</option>
                        </select>
                        <select name='fil_tipo_documento' id='fil_tipo_documento'>
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
                        <select name='fil_tipo_materia' id='fil_tipo_materia'>
                            <option  value=''>Tipo de Matéria</option>
                            ";
                            
                            $sql = " SELECT * FROM aux_materias_tipos 

                                    ORDER BY nome";
                            $stmt_filtro = $PDO_PROCLEGIS->prepare($sql);
                                                                
                            $stmt_filtro->execute();
                            while($result_filtro = $stmt_filtro->fetch())
                            {
                                echo "<option value='".$result_filtro['id']."' ";if($_REQUEST['fil_tipo'] == $result_filtro['id']) echo " selected "; echo ">".$result_filtro['nome']."</option>";
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
//<span class='bold'>Documento vinculado:</span> <a href='"; if($result['documento']) {echo "docadm_documentos/exib/".$result['documento']."";} echo "' target='_blank'>".$result['tipo_doc_sigla']."".$result['tipo_mat_sigla']." ".$result['numero_doc']."".$result['numero_mat']."/".$result['ano_doc']."".$result['ano_mat']."</a><p>                                        

                           
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td>
                                        <p class='bold hand' style='font-size:16px; text-decoration:underline;' onclick='verificaPermissao(".$permissoes["view"].",\"".$pagina_link."/exib/$id?pag=$pag\");'>
                                            ".str_pad($result['numero'],6,"0",STR_PAD_LEFT)."/".$result['ano']."
                                        </p>
                                        <span class='bold'>Natureza:</span> ".$result['natureza']."<p>
                                        <span class='bold'>Tipo:</span> ".$result['tipo_doc_nome']."".$result['tipo_mat_nome']."<p>
                                        <span class='bold'>Documento vinculado:</span> <a href='".($result['natureza'] == 'Administrativo' ? 'docadm_documentos/exib/'.$result['documento'] : 'cadastro_materias/exib/'.$result['materia'])."' target='_blank'>".$result['tipo_doc_sigla']."".$result['tipo_mat_sigla']." ".$result['numero_doc']."".$result['numero_mat']."/".$result['ano_doc']."".$result['ano_mat']."</a><p>                                        
                                        <span class='bold'>Data:</span> ".reverteData(substr($result['data_cadastro'],0,10))." às ".substr($result['data_cadastro'],11,5)." <p>
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
                                            </div>";                                            
                                        }
                                        echo "
                                    </td>                                    
                                    <td align=center width='190'>
                                            <div class='g_excluir' title='Excluir' onclick=\"
                                                abreMask('<p class=\'titulo\'>Alerta</p><p><br><br>'+
                                                    'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/view/excluir/$id?pag=$pag\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                \">	<i class='far fa-trash-alt'></i>
                                            </div>
                                            <!--<div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/edit/$id?pag=$pag\");'><i class='fas fa-pencil-alt'></i></div>-->                                            
                                            <div class='g_exibir' title='Exibir' onclick='verificaPermissao(".$permissoes["view"].",\"".$pagina_link."/exib/$id?pag=$pag\");'><i class='fas fa-search'></i></div>
                                            <div class='g_black' style='padding:4px;' title='Gerar Etiqueta' onclick='window.open(\"protocolo-etiqueta/".$id."\", \"_blank\", \"toolbar=yes,scrollbars=yes,resizable=yes,top=100,left=100,width=800,height=350\");'><i class='fas fa-barcode hand' style='font-size:20px;'></i></div>
                                    </td>
                                </tr>";
                            
                        }
                        echo "</table>";
                        $cnt = "SELECT COUNT(*) FROM protocolo_gerais                                      
                               LEFT JOIN aux_administrativo_tipo_documento t1 ON t1.id = protocolo_gerais.tipo_documento                                                                                                         
                                LEFT JOIN aux_materias_tipos t2 ON t2.id = protocolo_gerais.tipo_materia                                                                                                         
                                LEFT JOIN docadm_documentos t3 ON t3.id = protocolo_gerais.documento
                                LEFT JOIN cadastro_materias t4 ON t4.id = protocolo_gerais.materia                                                                                                         
                                LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = protocolo_gerais.cadastrado_por   
                                WHERE ".$natureza_query ." AND ".$tipo_documento_query." AND  ".$tipo_materia_query." AND  ".$numero_query." AND  ".$ano_query." ";
                        $stmt = $PDO_PROCLEGIS->prepare($cnt);     
                        $stmt->bindParam(':fil_natureza', 	$fil_natureza);                
                        $stmt->bindParam(':fil_tipo_documento', 	$fil_tipo_documento);                
                        $stmt->bindParam(':fil_tipo_materia', 	$fil_tipo_materia);                
                        $stmt->bindParam(':fil_numero', 	$fil_numero);                
                        $stmt->bindParam(':fil_ano', 	$fil_ano); 
                        $variavel = "&fil_natureza=$fil_natureza&fil_tipo_documento=$fil_tipo_documento&fil_tipo_materia=$fil_tipo_materia&fil_numero=$fil_numero&fil_ano=$fil_ano";            
                        include("../../core/mod_includes/php/paginacao.php");
                }
                else
                {
                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                }
            }
            if($pagina == 'add_documentos')
            {
              
                // PEGA DADOS DO DOCUMENTO
                $sql = "SELECT *, aux_administrativo_tipo_documento.nome as nome_tipo
                                , docadm_documentos.id as id_documento                                
                        FROM docadm_documentos
                        LEFT JOIN aux_administrativo_tipo_documento ON aux_administrativo_tipo_documento.id = docadm_documentos.tipo
                        WHERE docadm_documentos.id = :id 
                    ";
                $stmt_doc = $PDO_PROCLEGIS->prepare($sql);                                                
                $stmt_doc->bindParam(':id', 	$id);   
                //$stmt_doc->bindValue(':ativo',1);         
                $stmt_doc->execute();
                $rows_doc = $stmt_doc->rowCount();                          
                if($rows_doc > 0)
                {
                    
                    $result_doc = $stmt_doc->fetch();

                    $id_tipo = $result_doc['tipo'];
                    $nome_tipo = $result_doc['nome_tipo'];
                    $natureza = "Administrativo";
                    $id_documento = $result_doc['id_documento'];
                    $sigla = $result_doc['sigla'];
                    $numero = $result_doc['numero'];
                    $ano = $result_doc['ano'];
                    
                }

                echo "	
               
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='protocolo_gerais/view/adicionar_documento'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                                    
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                            OBS: o número/ano do protocolo será gerado automaticamente.
                                <p><label>Natureza *:</label> <select name='natureza' id='natureza' readonly sclass='obg'>
                                    <option value='$natureza'>$natureza</option>
                                    <option value='Administrativo' selected>Administrativo</option>
                                    <option value='Legislativa'>Legislativa</option>
                                </select>
                                <p><label>Tipo *:</label> <select name='tipo_documento' id='tipo_documento' ";if($id_tipo){ echo " readonly ";} echo "class='obg'>
                                    <option value='$id_tipo'>$nome_tipo</option>";
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
                                <p><label>Documento *:</label> <select name='documento' id='documento' ";if($id_documento){ echo " readonly ";} echo "class='obg'>
                                    <option value='$id_documento'>$sigla $numero/$ano</option>                                    
                                </select>
                                <p><label>Interessado *:</label> <input name='interessado' id='interessado' placeholder='Interessado' class='obg'>
                                <p><label>Descrição:</label> <textarea name='descricao' id='descricao' placeholder='Descrição' ></textarea>   
                                <p><label>Cadastrado por *:</label> <input name='por' id='por' value='".$_SESSION["usuario_name"]."' readonly class='obg'>                             
                            </div>
                        </div>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Protocolar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='protocolo_gerais/view'; value='Cancelar'/></center>
                        </center>
                    </div>
                </form>
                ";
            }
            
            if($pagina == 'edit_documentos')
            {            		
                $sql = "SELECT *, t1.nome as tipo_nome,
                                  t1.sigla as tipo_sigla,  
                                  t2.nome as classificacao_nome,                                
                                  protocolo_gerais.id as id
                        FROM protocolo_gerais 
                        LEFT JOIN aux_administrativo_tipo_documento t1 ON t1.id = protocolo_gerais.tipo                                                             
                        LEFT JOIN aux_administrativo_classificacao t2 ON t2.id = protocolo_gerais.classificacao                                                             
                        WHERE protocolo_gerais.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();                                                                                                                 
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='protocolo_gerais/view/editar/$id'>
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
                                    <option value='".$result['regine_tramitacao']."'>".$result['regine_tramitacao']."</option>
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
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='protocolo_gerais/view'; value='Cancelar'/></center>
                            </center>
                        </div>
                    </form>
                    ";
                }
            }	
            if($pagina == 'exib')
            {                         		
                $sql = "SELECT *, t1.nome as tipo_doc_nome,
                                  t1.sigla as tipo_doc_sigla,
                                  t2.nome as tipo_mat_nome,
                                  t2.sigla as tipo_mat_sigla, 
                                  t3.numero as numero_doc,
                                  t3.ano as ano_doc,
                                  t4.numero as numero_mat,
                                  t4.ano as ano_mat,                                                                
                                  protocolo_gerais.id as id,
                                  protocolo_gerais.numero as numero,
                                  protocolo_gerais.ano as ano,
                                  protocolo_gerais.data_cadastro as data_cadastro,
                                  protocolo_gerais.interessado as interessado
                        FROM protocolo_gerais 
                        LEFT JOIN aux_administrativo_tipo_documento t1 ON t1.id = protocolo_gerais.tipo_documento                                                                                                         
                        LEFT JOIN aux_materias_tipos t2 ON t2.id = protocolo_gerais.tipo_materia                                                                                                         
                        LEFT JOIN docadm_documentos t3 ON t3.id = protocolo_gerais.documento
                        LEFT JOIN cadastro_materias t4 ON t4.id = protocolo_gerais.materia                                                                                                         
                        LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = protocolo_gerais.cadastrado_por  
                        WHERE protocolo_gerais.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();                                                 
                    echo "
                        <div class='titulo'> $page &raquo; Exibir protocolo </div>
                        <div class='g_black' style='padding:4px;' title='Gerar Etiqueta' onclick='window.open(\"protocolo-etiqueta/".$result['id']."\", \"_blank\", \"toolbar=yes,scrollbars=yes,resizable=yes,top=100,left=100,width=800,height=350\");'><i class='fas fa-barcode hand' style='font-size:20px;'></i></div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                            
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active' >
                                <div style='display:table; width:100%'>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Natureza:</div>
                                        <div class='exib_value'>".$result['natureza']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Tipo:</div>
                                        <div class='exib_value'>".$result['tipo_doc_nome']."".$result['tipo_mat_nome']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Documento vinculado:</div>
                                        <div class='exib_value'><a href='"; if($result['documento']) {echo "docadm_documentos/exib/".$result['documento']."";} echo "' target='_blank'>".$result['tipo_doc_sigla']."".$result['tipo_mat_sigla']." ".$result['numero_doc']."".$result['numero_mat']."/".$result['ano_doc']."".$result['ano_mat']."</a> &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'> &nbsp;</div>
                                        <div class='exib_value'> &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Número protocolo:</div>
                                        <div class='exib_value'>".str_pad($result['numero'],6,"0",STR_PAD_LEFT)."/".$result['ano']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Interessado:</div>
                                        <div class='exib_value'>".$result['interessado']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Data protocolo:</div>
                                        <div class='exib_value'>".reverteData(substr($result['data_cadastro'],0,10))." às ".substr($result['data_cadastro'],11,5)." &nbsp;</div>
                                    </div>                                    
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Cadastrado por:</div>
                                        <div class='exib_value'>".$result['usu_nome']." &nbsp;</div>
                                    </div>
                                    <div class='exib_bloco'>
                                        <div class='exib_label'>Descrição:</div>
                                        <div class='exib_value'>".$result['descricao']." &nbsp;</div>
                                    </div>
                                </div>                                                                                                                                              
                            </div>                                                   
                            <br>               				
                            <center>                                                        
                                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='protocolo_gerais/view'; value='Voltar'/></center>
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
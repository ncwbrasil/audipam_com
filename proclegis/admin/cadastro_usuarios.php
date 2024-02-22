<?php
$pagina_link = 'cadastro_usuarios';
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
            $page = "Cadastro &raquo; <a href='cadastro_usuarios/view'>Usuários</a>";
            if(isset($_GET['usu_id'])){$usu_id = $_GET['usu_id'];}
            $usu_setor = $_POST['usu_setor'];
            $usu_nome = $_POST['usu_nome'];
            $usu_cpf = $_POST['usu_cpf'];
            $usu_email = $_POST['usu_email'];
            $usu_senha = hash('sha512',$_POST['usu_senha']);
            $usu_telefone = $_POST['usu_telefone'];
            $usu_status = $_POST['usu_status'];      
            $sql = "SELECT * FROM cadastro_usuarios WHERE usu_id = :usu_id ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);
            $stmt->bindParam(':usu_id',$usu_id);
            $stmt->execute();
            $row = $stmt->rowCount();
            if($row > 0)
            {
                $senhacompara = $stmt->fetch(PDO::FETCH_OBJ)->usu_senha;		
            }
            if($_POST['usu_senha'] == $senhacompara)
            {
                $usu_senha = $senhacompara;
            }
            $usu_data_exp = reverteData($_POST['usu_data_exp']);
            $usu_hora_exp = $_POST['usu_hora_exp'];
            $usu_periodo_1 = $_POST['usu_periodo_1'];
            $usu_periodo_2 = $_POST['usu_periodo_2'];
            $usu_periodo_3 = $_POST['usu_periodo_3'];if($usu_periodo_3 == "" ) { $usu_periodo_3 = null;}
            $usu_periodo_4 = $_POST['usu_periodo_4'];if($usu_periodo_4 == "" ) { $usu_periodo_4 = null;}
            $usu_cert_senha = $_POST['usu_cert_senha'];   
            if($_POST['usu_tempo'] == ''){
                $usu_tempo = NULL;   
            } 
            else {
                $usu_tempo = $_POST['usu_tempo'];   
            }
           
            $dados = array(
                'usu_setor' 		=> $usu_setor,        
                'usu_nome' 		    => $usu_nome,
                'usu_telefone' 		=> $usu_telefone,
                'usu_email' 		=> $usu_email,
                'usu_senha' 		=> $usu_senha,
                'usu_status' 		=> $usu_status,
                'usu_cert_senha'    => $usu_cert_senha,
                'usu_tempo'         => $usu_tempo
            );
        
            if($action == "adicionar")
            {                       
                $sql = "INSERT INTO cadastro_usuarios SET ".bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                if($stmt->execute($dados))
                {		
                    $usu_id = $PDO_PROCLEGIS->lastInsertId();

                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';                    
                    $caminho = "../uploads/usuarios/";
                    foreach($_FILES as $key => $files)
                    {
                        $files_test = array_filter($files['name']);
                        if(!empty($files_test))
                        {
                            if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
                            if(!empty($files["name"]["foto"]))
                            {
                                $nomeArquivo 	= $files["name"]["foto"];
                                $nomeTemporario = $files["tmp_name"]["foto"];                            
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $usu_foto	= $caminho;
                                $usu_foto .= "foto_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($usu_foto));
                                $imnfo = getimagesize($usu_foto);
                                $img_w = $imnfo[0];	  // largura
                                $img_h = $imnfo[1];	  // altura
                                if($img_w > 500 || $img_h > 500)
                                {
                                    $image = WideImage::load($usu_foto);
                                    $image = $image->resize(500, 500);
                                    $image->saveToFile($usu_foto);
                                }
                                $sql = "UPDATE cadastro_usuarios SET 
                                        usu_foto 	 = :usu_foto
                                        WHERE usu_id = :usu_id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':usu_foto',$usu_foto);
                                $stmt->bindParam(':usu_id',$usu_id);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
                                
                                //CONVERTE FOTO PARA BASE64
                                $imagedata = file_get_contents($usu_foto);                             
                                $base64 = base64_encode($imagedata);
                            }					
                        }
                    }
                    //                				
                
                    //UPLOAD ARQUIVOS                                   
                    $caminho = "../uploads/usuarios/certificado/";
                    foreach($_FILES as $key => $files)
                    {
                        $files_test = array_filter($files['name']);
                        if(!empty($files_test))
                        {
                            if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
                            if(!empty($files["name"]["certificado"]))
                            {
                                $nomeArquivo 	= $files["name"]["certificado"];
                                $nomeTemporario = $files["tmp_name"]["certificado"];                            
                                //$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $usu_certificado	= $caminho;
                                $usu_certificado .= $nomeArquivo;					
                                move_uploaded_file($nomeTemporario, ($usu_certificado));
                                
                                $sql = "UPDATE cadastro_usuarios SET 
                                        usu_certificado   = :usu_certificado
                                        WHERE usu_id = :usu_id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':usu_certificado',$usu_certificado);
                                $stmt->bindParam(':usu_id',$usu_id);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}                                                                
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
                $sql = "UPDATE cadastro_usuarios SET ".bindFields($dados)." WHERE usu_id = :usu_id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['usu_id'] =  $usu_id;
                if($stmt->execute($dados))
                {
                    
                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';
                    $caminho = "../uploads/usuarios/";
                    foreach($_FILES as $key => $files)
                    {
                        $files_test = array_filter($files['name']);
                        if(!empty($files_test))
                        {
                            if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
                            if(!empty($files["name"]["foto"]))
                            {
                                $nomeArquivo 	= $files["name"]["foto"];
                                $nomeTemporario = $files["tmp_name"]["foto"];
                                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $usu_foto	= $caminho;
                                $usu_foto .= "foto_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($usu_foto));
                                $imnfo = getimagesize($usu_foto);
                                $img_w = $imnfo[0];	  // largura
                                $img_h = $imnfo[1];	  // altura
                                if($img_w > 500 || $img_h > 500)
                                {
                                    $image = WideImage::load($usu_foto);
                                    $image = $image->resize(500, 500);
                                    $image->saveToFile($usu_foto);
                                }
                                $sql = "UPDATE cadastro_usuarios SET 
                                        usu_foto 	 = :usu_foto
                                        WHERE usu_id = :usu_id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':usu_foto',$usu_foto);
                                $stmt->bindParam(':usu_id',$usu_id);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
                                
                                //CONVERTE FOTO PARA BASE64
                                $imagedata = file_get_contents($usu_foto);                             
                                $base64 = base64_encode($imagedata);
                            }					
                        }
                    }
                    //

                    //UPLOAD ARQUIVOS                                   
                    $caminho = "../uploads/usuarios/certificado/";
                    foreach($_FILES as $key => $files)
                    {
                        $files_test = array_filter($files['name']);
                        if(!empty($files_test))
                        {
                            if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
                            if(!empty($files["name"]["certificado"]))
                            {
                                $nomeArquivo 	= $files["name"]["certificado"];
                                $nomeTemporario = $files["tmp_name"]["certificado"];                            
                                //$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                                $usu_certificado	= $caminho;
                                $usu_certificado .= $nomeArquivo;					
                                move_uploaded_file($nomeTemporario, ($usu_certificado));
                                
                                $sql = "UPDATE cadastro_usuarios SET 
                                        usu_certificado   = :usu_certificado
                                        WHERE usu_id = :usu_id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':usu_certificado',$usu_certificado);
                                $stmt->bindParam(':usu_id',$usu_id);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}                                                                
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
                
                $sql = "DELETE FROM cadastro_usuarios WHERE usu_id = :usu_id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':usu_id',$usu_id);
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
            if($action == 'ativar')
            {
                $sql = "UPDATE cadastro_usuarios SET usu_status = :usu_status WHERE usu_id = :usu_id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':usu_status',1);
                $stmt->bindParam(':usu_id',$usu_id);
                $stmt->execute();
            }
            if($action == 'desativar')
            {
                $sql = "UPDATE cadastro_usuarios SET usu_status = :usu_status WHERE usu_id = :usu_id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':usu_status',0);
                $stmt->bindParam(':usu_id',$usu_id);
                $stmt->execute();
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
                $nome_query = " (usu_nome LIKE :fil_nome1 ) ";
            }
            $sql = "SELECT * FROM cadastro_usuarios 
                    LEFT JOIN admin_setores ON admin_setores.set_id = cadastro_usuarios.usu_setor	
                    WHERE ".$nome_query." 			
                    ORDER BY usu_id DESC
                    LIMIT :primeiro_registro, :num_por_pagina ";
            $stmt = $PDO_PROCLEGIS->prepare($sql);    
            $stmt->bindParam(':fil_nome1', 	$fil_nome1);
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
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_usuarios/view'>
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
                            <td class='titulo_tabela'>Foto</td>
                            <td class='titulo_tabela'>Nome</td>
                            <td class='titulo_tabela'>Setor</td>
                            <td class='titulo_tabela'>Email</td>
                            <td class='titulo_tabela' align='center'>Certificado Dig.</td>
                            <td class='titulo_tabela' align='center'>Status</td>
                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                        </tr>";
                        $c=0;
                        while($result = $stmt->fetch())
                        {
                            $usu_id = $result['usu_id'];
                            $usu_email = $result['usu_email'];
                            $set_nome = $result['set_nome'];
                            if($result['usu_visitante'] != "")
                            {
                                $set_nome = "Visitante<br><span class='detalhe'>Sem acesso ao sistema</span>";
                            }
                            $usu_nome = $result['usu_nome'];
                            $foto = $result['usu_foto'].$result['mof_foto'].$result['fun_foto'].$result['vis_foto'];
                            $usu_status = $result['usu_status'];
                            if($foto == '')
                            {
                                $foto = '../../core/imagens/perfil.png';
                            }
                            $usu_certificado = $result['usu_certificado'];
                            if($usu_certificado != '')
                            {
                                $usu_certificado = '<i class="fas fa-check-circle green"></i>';
                            }
                            else
                            {
                                $usu_certificado = '<i class="fas fa-check-circle red"></i>';
                            }
                            
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td><div class='perfil' style='background:url($foto) center center; background-size: cover; border-radius:50px; width:50px; height:50px;' border='0'></div></td>
                                    <td>$usu_nome</td>
                                    <td>$set_nome</td>
                                    <td>$usu_email</td>
                                    <td align='center'>$usu_certificado</td>
                                    <td align=center>";
                                    if($usu_status == 1)
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
                                                    '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/view/excluir/$usu_id?pag=$pag\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                                \">	<i class='far fa-trash-alt'></i>
                                            </div>
                                            <div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/edit/$usu_id?pag=$pag\");'><i class='fas fa-pencil-alt'></i></div>
                                            ";
                                            if($usu_status == 1)
                                            {
                                                echo "<div class='g_status' title='Desativar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/view/desativar/$usu_id?pag=$pag\");'><i class='fas fa-sync-alt'></i></div>";
                                            }
                                            else
                                            {
                                                echo "<div class='g_status' title='Ativar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/view/ativar/$usu_id?pag=$pag\");'><i class='fas fa-sync-alt'></i></div>";
                                            }
                                            echo "
                                    </td>
                                </tr>";
                        }
                        echo "</table>";
                        $cnt = "SELECT COUNT(*) FROM cadastro_usuarios  WHERE ".$nome_query."  ";
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
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_usuarios/view/adicionar'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            <li><a data-toggle='tab' 	href='#foto'>Foto</a></li>
                            <li><a data-toggle='tab' 	href='#certificado'>Certificado Digital</a></li>
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Setor:</label> <select name='usu_setor' id='usu_setor'>
                                    <option value=''>Setor</option>";
                                    $sql = "SELECT * FROM admin_setores 
                                            ORDER BY set_nome";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);                                                                          
                                        $stmt->execute();
                                        while($result = $stmt->fetch())
                                        {
                                            echo "<option value='".$result['set_id']."'>".$result['set_nome']."</option>";
                                        }
                                    echo "
                                </select>                                                
                                <p><label>Nome:</label> <input name='usu_nome' id='usu_nome' placeholder='Nome' class='obg'>
                                <p><label>Email:</label> <input name='usu_email' id='usu_email' placeholder='Email'  class='obg'>
                                <p><label>Senha:</label> <input type='password' name='usu_senha' id='usu_senha' placeholder='Senha' class='obg'>                            
                                <p><label>Telefone:</label> <input name='usu_telefone' id='usu_telefone' placeholder='Telefone' onkeypress='return mascaraTELEFONE(this);'>
                                <p><label>Tempo de Inatividade (min):</label> <input name='usu_tempo' id='usu_tempo' placeholder='Tempo de Inatividade'>
                                <p><label>Status:</label> 
                                <input type='radio' name='usu_status' value='1' checked> Ativo <br>
                                <input type='radio' name='usu_status' value='0'> Inativo<br>
                            </div>	                            
                            <div id='foto' class='tab-pane fade in' style='text-align:center'>
                                <p><label>Foto:</label> <input type='file' name='usu_foto[foto]' id='usu_foto' placeholder='Foto'>                            
                            </div>  
                            <div id='certificado' class='tab-pane fade in' style='text-align:center'>
                                <p><label>Certificado:</label> <input type='file' name='usu_certificado[certificado]' id='usu_certificado' placeholder='Certificado'>    
                                <p><label>Senha:</label> <input type='password' name='usu_cert_senha' id='usu_cert_senha' placeholder='Senha do certificado'>                        
                            </div>                  
                        </div>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_usuarios/view'; value='Cancelar'/></center>
                        </center>
                    </div>
                </form>
                ";
            }
            
            if($pagina == 'edit')
            {            		
                $sql = "SELECT * FROM cadastro_usuarios 
                        LEFT JOIN admin_setores ON admin_setores.set_id = cadastro_usuarios.usu_setor
                        WHERE usu_id = :usu_id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':usu_id', $usu_id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();
                    $usu_setor = $result['usu_setor'];
                    $set_nome = $result['set_nome'];if($set_nome == ''){ $set_nome = "Setor";}                                
                    $usu_nome = $result['usu_nome'];
                    $cli_nome = $result['cli_nome'];
                    $usu_cpf = $result['usu_cpf'];
                    $usu_email = $result['usu_email'];
                    $usu_senha = $result['usu_senha'];
                    $usu_telefone = $result['usu_telefone'];
                    $usu_status = $result['usu_status'];                                               
                    $usu_foto = $result['usu_foto'];
                    $usu_certificado = $result['usu_certificado'];
                    $usu_cert_senha = $result['usu_cert_senha'];
                    $usu_tempo      = $result['usu_tempo']; 
                    if($usu_acesso == 1)
                    {
                        $usu_acesso_n = "Limitado";
                    }
                    else
                    {
                        $usu_acesso_n = "Ilimitado";
                    }
                    
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_usuarios/view/editar/$usu_id'>
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            <li><a data-toggle='tab' 	href='#foto'>Foto</a></li>        
                            <li><a data-toggle='tab' 	href='#certificado'>Certificado Digital</a></li>                
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <label>Setor:</label> <select name='usu_setor' id='usu_setor'>
                                    <option value='$usu_setor'>$set_nome</option>";
                                    $sql = "SELECT * FROM admin_setores
                                            ORDER BY set_nome";
                                    $stmt = $PDO_PROCLEGIS->prepare($sql);                                    				
                                    $stmt->execute();
                                    while($result = $stmt->fetch())
                                    {
                                        echo "<option value='".$result['set_id']."'>".$result['set_nome']."</option>";
                                    }
                                    echo "
                                </select>  
                                                
                                <p><label>Nome:</label> <input name='usu_nome' id='usu_nome' value='$usu_nome' placeholder='Nome'  class='obg'>
                                <p><label>Email:</label> <input name='usu_email' id='usu_email' value='$usu_email' placeholder='Email'  class='obg'>
                                <p><label>Senha:</label> <input type='password' name='usu_senha' id='usu_senha' value='$usu_senha' placeholder='Senha' class='obg'>							
                                <p><label>Telefone:</label> <input name='usu_telefone' id='usu_telefone' value='$usu_telefone' placeholder='Telefone' onkeypress='return mascaraTELEFONE(this);'>
                                <p><label>Tempo de Inatividade (min):</label> <input name='usu_tempo' id='usu_tempo' value='$usu_tempo' placeholder='Tempo de Inatividade'>
                                <p><label>Status:</label> ";
                                if($usu_status == 1)
                                {
                                    echo "<input type='radio' name='usu_status' value='1' checked> Ativo <br>
                                        <input type='radio' name='usu_status' value='0'> Inativo
                                        ";
                                }
                                else
                                {
                                    echo "<input type='radio' name='usu_status' value='1'> Ativo <br>
                                        <input type='radio' name='usu_status' value='0' checked> Inativo
                                        ";
                                }
                                echo "                                                        
                            </div>                        
                            <div id='foto' class='tab-pane fade in'>
                                <p><label>Foto:</label> ";if($usu_foto != ''){ echo "<img src='".$usu_foto."' valign='middle' style='max-width:250px'>";} echo " &nbsp;                                 	
                                <p><label>Alterar Foto:</label> <input type='file' name='usu_foto[foto]' id='usu_foto'>							
                            </div>
                            <div id='certificado' class='tab-pane fade in'>
                                <p><label>Certificado:</label> ";if($usu_certificado != ''){ echo '<a href="'.$usu_certificado.'" target="_blank"><i class="fas fa-file-signature"></i></a>';} echo " &nbsp;                                 
                                <p><label>Atualizar certificado:</label> <input type='file' name='usu_certificado[certificado]' id='usu_certificado'>	
                                <p><label>Senha do certificado:</label> <input type='password' name='usu_cert_senha' id='usu_cert_senha' value='$usu_cert_senha' placeholder='Senha do certificado'>							
                                						
                            </div>                        				
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_usuarios/view'; value='Cancelar'/></center>
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
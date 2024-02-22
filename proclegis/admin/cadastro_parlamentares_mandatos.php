<?php
$pagina_link = 'cadastro_parlamentares';
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
            if(isset($_GET['id'])){$id = $_GET['id'];}
            $page = "Cadastro &raquo; <a href='cadastro_parlamentares/view'>Parlamentares</a> &raquo; <a href='cadastro_parlamentares/exib/".$id."#mandatos'>Mandatos</a>";
            $nome   = $_POST['nome'];
            $apelido   = $_POST['apelido'];
            $nivel_instrucao  = $_POST['nivel_instrucao'];
            $sexo  = $_POST['sexo'];
            $data_nasc  = reverteData($_POST['data_nasc']);
            $cpf    = $_POST['cpf'];
            $rg    = $_POST['rg'];
            $titulo_eleitor    = $_POST['titulo_eleitor'];
            $situacao_militar    = $_POST['situacao_militar'];
            $profissao    = $_POST['profissao'];
            $site    = $_POST['site'];
            $email = $_POST['email'];
            $n_gabinete = $_POST['n_gabinete'];
            $telefone = $_POST['telefone'];
            $cep = $_POST['cep'];
            $uf = $_POST['uf'];
            $municipio = $_POST['municipio'];
            $bairro = $_POST['bairro'];
            $endereco = $_POST['endereco'];
            $biografia = $_POST['biografia'];
            $status = $_POST['status'];      
            
            $dados = array(                
                'nome' 		    => $nome,
                'apelido' 		    => $apelido,
                'nivel_instrucao' 		    => $nivel_instrucao,
                'sexo' 		    => $sexo,
                'data_nasc' 		    => $data_nasc,
                'cpf' 		    => $cpf,
                'rg' 		    => $rg,
                'titulo_eleitor' 		    => $titulo_eleitor,
                'situacao_militar' 		    => $situacao_militar,
                'profissao' 		    => $profissao,
                'site' 		    => $site,
                'email' 		    => $email,
                'n_gabinete' 		    => $n_gabinete,
                'nome' 		    => $nome,
                'telefone' 		=> $telefone,
                'cep' 		=> $cep,
                'uf' 		=> $uf,
                'municipio' 		=> $municipio,
                'bairro' 		=> $bairro,
                'endereco' 		=> $endereco,
                'biografia' 		=> $biografia,
                'status' 		=> $status
            );
        
            if($action == "adicionar")
            {                       
            
                $sql = "INSERT INTO cadastro_parlamentares SET ".bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                if($stmt->execute($dados))
                {		
                    $id = $PDO_PROCLEGIS->lastInsertId();

                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';
                    
                    $caminho = "../uploads/parlamentares/";
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
                                $foto	= $caminho;
                                $foto .= "foto_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($foto));
                                $imnfo = getimagesize($foto);
                                $img_w = $imnfo[0];	  // largura
                                $img_h = $imnfo[1];	  // altura
                                if($img_w > 500 || $img_h > 500)
                                {
                                    $image = WideImage::load($foto);
                                    $image = $image->resize(500, 500);
                                    $image->saveToFile($foto);
                                }
                                $sql = "UPDATE cadastro_parlamentares SET 
                                        foto 	 = :foto
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':foto',$foto);
                                $stmt->bindParam(':id',$id);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
                                
                                //CONVERTE FOTO PARA BASE64
                                $imagedata = file_get_contents($foto);                             
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
                $sql = "UPDATE cadastro_parlamentares SET ".bindFields($dados)." WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql); 
                $dados['id'] =  $id;
                if($stmt->execute($dados))
                {
                    //UPLOAD ARQUIVOS
                    require_once '../../core/mod_includes/php/lib/WideImage.php';
                    $caminho = "../uploads/parlamentares/";
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
                                $foto	= $caminho;
                                $foto .= "foto_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
                                move_uploaded_file($nomeTemporario, ($foto));
                                $imnfo = getimagesize($foto);
                                $img_w = $imnfo[0];	  // largura
                                $img_h = $imnfo[1];	  // altura
                                if($img_w > 500 || $img_h > 500)
                                {
                                    $image = WideImage::load($foto);
                                    $image = $image->resize(500, 500);
                                    $image->saveToFile($foto);
                                }
                                $sql = "UPDATE cadastro_parlamentares SET 
                                        foto 	 = :foto
                                        WHERE id = :id ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);
                                $stmt->bindParam(':foto',$foto);
                                $stmt->bindParam(':id',$id);
                                if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
                                
                                //CONVERTE FOTO PARA BASE64
                                $imagedata = file_get_contents($foto);                             
                                $base64 = base64_encode($imagedata);
                            }					
                        }
                    }
                    //  
                    log_operacao($id, $PDO_PROCLEGIS);                    
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
                
                $sql = "UPDATE cadastro_parlamentares SET ativo = :ativo WHERE id = :id";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindParam(':id',$id);
                $stmt->bindValue(':ativo', 0);
                if($stmt->execute())
                {
                    //unlink($foto_antiga);
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
                $sql = "UPDATE cadastro_parlamentares SET status = :status WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':status',1);
                $stmt->bindParam(':id',$id);
                if($stmt->execute()){
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
            
            if($action == 'desativar')
            {
                $sql = "UPDATE cadastro_parlamentares SET status = :status WHERE id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);
                $stmt->bindValue(':status',0);
                $stmt->bindParam(':id',$id);
                if($stmt->execute()){
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
            $sql = "SELECT * FROM cadastro_parlamentares 
                    WHERE ".$nome_query." AND ativo = :ativo			
                    ORDER BY id DESC
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
                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_parlamentares/view'>
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
                            <td class='titulo_tabela' align='center'>Status</td>
                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                        </tr>";
                        $c=0;
                        while($result = $stmt->fetch())
                        {
                            $id = $result['id'];
                            $nome = $result['nome'];
                            $foto = $result['foto'].$result['mof_foto'].$result['fun_foto'].$result['vis_foto'];
                            $status = $result['status'];
                            if($foto == '')
                            {
                                $foto = '../../core/imagens/perfil.png';
                            }
                            
                            
                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                            echo "<tr class='$c1'>
                                    <td><div class='perfil' style='background:url($foto) center center; background-size: cover; border-radius:50px; width:50px; height:50px;' border='0'></div></td>
                                    <td>$nome</td>
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
                        $cnt = "SELECT COUNT(*) FROM cadastro_parlamentares  WHERE ".$nome_query."";
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
                <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_parlamentares/view/adicionar'>
                    <div class='titulo'> $page &raquo; Adicionar  </div>
                    <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            <li><a data-toggle='tab' 	href='#foto'>Foto</a></li>
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Nome *:</label> <input name='nome' id='nome' placeholder='Nome' class='obg'>
                                <p><label>Apelido:</label> <input name='apelido' id='apelido' placeholder='Apelido'>
                                <p><label>Nível Instrução:</label> <select name='nivel_instrucao' id='nivel_instrucao'>
                                    <option value=''>Nível Instrução</option>";
                                    $sql = " SELECT * FROM aux_parlamentares_nivel_instrucao 
                                             WHERE ativo = :ativo 
                                             ORDER BY descricao";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);
                                        $stmt->bindValue(':ativo',1);                                       
                                        $stmt->execute();
                                        while($result = $stmt->fetch())
                                        {
                                            echo "<option value='".$result['id']."'>".$result['descricao']."</option>";
                                        }
                                    echo "
                                </select>     
                                <p><label>Sexo:</label> <select name='sexo' id='sexo'>
                                    <option value=''>Sexo</option>
                                    <option value='Masculino'>Masculino</option>
                                    <option value='Feminino'>Feminino</option>                                    
                                </select>                                            
                                <p><label>Data Nascimento:</label> <input name='data_nasc' id='data_nasc' placeholder='Data Nascimento' onkeypress='return mascaraData(this,event);'>
                                <p><label>CPF:</label> <input name='cpf' id='cpf' placeholder='CPF'>
                                <p><label>RG:</label> <input name='rg' id='rg' placeholder='RG'>
                                <p><label>Título eleitor:</label> <input name='titulo_eleitor' id='titulo_eleitor' placeholder='Título eleitor'>
                                <p><label>Situação militar:</label> <select name='situacao_militar' id='situacao_militar'>
                                    <option value=''>Situação militar</option>";
                                    $sql = " SELECT * FROM aux_parlamentares_tipo_situacao_militar
                                             WHERE ativo = :ativo
                                             ORDER BY descricao";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);       
                                        $stmt->bindValue(':ativo',1);                                
                                        $stmt->execute();
                                        while($result = $stmt->fetch())
                                        {
                                            echo "<option value='".$result['id']."'>".$result['descricao']."</option>";
                                        }
                                    echo "
                                </select> 
                                <p><label>Profissão:</label> <input name='profissao' id='profissao' placeholder='Profissão'>
                                <p><label>Site:</label> <input name='site' id='site' placeholder='Site'>
                                <p><label>Email:</label> <input name='email' id='email' placeholder='Email'>
                                <p><label>Nº Gabinete:</label> <input name='n_gabinete' id='n_gabinete' placeholder='Nº Gabinete'>
                                <p><label>Telefone:</label> <input name='telefone' id='telefone' placeholder='Telefone' onkeypress='return mascaraTELEFONE(this);'>
                                <p><label>CEP:</label> <input name='cep' id='cep' placeholder='CEP' maxlength='9' onkeypress='mascaraCEP(this); return SomenteNumero(event);'>
                                <p><label>UF:</label> <select name='uf' id='uf'>
                                                            <option value=''>UF</option>
                                                            "; 
                                                            $sql = " SELECT * FROM end_uf ORDER BY uf_sigla";
                                                            $stmt = $PDO->prepare($sql);
                                                            $stmt->execute();
                                                            while($result = $stmt->fetch())
                                                            {
                                                                echo "<option value='".$result['uf_id']."'>".$result['uf_sigla']."</option>";
                                                            }
                                                            echo "
                                                        </select>
                                <p><label>Município:</label> <select name='municipio' id='municipio'>
                                    <option value=''>Município</option>
                                </select>
                                <p><label>Bairro:</label> <input name='bairro' id='bairro' placeholder='Bairro' />
                                <p><label>Endereço:</label> <input name='endereco' id='endereco' placeholder='Endereço' />
                                <p><label>Biografia:</label> <textarea name='biografia' id='biografia' placeholder='Biografia' /></textarea>
                                <p><label>Status:</label> 
                                <input type='radio' name='status' value='1' checked> Ativo <br>
                                <input type='radio' name='status' value='0'> Inativo<br>
                            </div>	                            
                            <div id='foto' class='tab-pane fade in' style='text-align:center'>
                                <p><label>Foto:</label> <input type='file' name='foto[foto]' id='foto' placeholder='Foto'>                            
                            </div>                    
                        </div>
                        <center>
                        <div id='erro' align='center'>&nbsp;</div>
                        <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_parlamentares/view'; value='Cancelar'/></center>
                        </center>
                    </div>
                </form>
                ";
            }
            
            if($pagina == 'edit')
            {            		
                $sql = "SELECT *, aux_parlamentares_nivel_instrucao.descricao as nivel_instrucao_descricao,
                                  aux_parlamentares_tipo_situacao_militar.descricao as situacao_militar_descricao                        
                        FROM cadastro_parlamentares 
                        LEFT JOIN aux_parlamentares_nivel_instrucao ON aux_parlamentares_nivel_instrucao.id = cadastro_parlamentares.nivel_instrucao
                        LEFT JOIN aux_parlamentares_tipo_situacao_militar ON aux_parlamentares_tipo_situacao_militar.id = cadastro_parlamentares.situacao_militar
                        LEFT JOIN end_uf ON end_uf.uf_id = cadastro_parlamentares.uf
                        LEFT JOIN end_municipios ON end_municipios.mun_id = cadastro_parlamentares.municipio
                        WHERE cadastro_parlamentares.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();                                                 
                    $nome = $result['nome'];
                    $apelido   = $result['apelido'];
                    $nivel_instrucao  = $result['nivel_instrucao'];
                    $nivel_instrucao_descricao  = $result['nivel_instrucao_descricao'];
                    $sexo  = $result['sexo'];
                    $data_nasc  = reverteData($result['data_nasc']);
                    $cpf    = $result['cpf'];
                    $rg    = $result['rg'];
                    $titulo_eleitor    = $result['titulo_eleitor'];
                    $situacao_militar    = $result['situacao_militar'];
                    $situacao_militar_descricao    = $result['situacao_militar_descricao'];
                    $profissao    = $result['profissao'];
                    $site    = $result['site'];
                    $email = $result['email'];
                    $n_gabinete = $result['n_gabinete'];
                    $telefone = $result['telefone'];
                    $cep = $result['cep'];
                    $uf = $result['uf'];
                    $uf_sigla = $result['uf_sigla'];
                    $municipio = $result['municipio'];
                    $mun_nome = $result['mun_nome'];
                    $bairro = $result['bairro'];
                    $endereco = $result['endereco'];
                    $biografia = $result['biografia'];
                    $status = $result['status'];                                               
                    $foto = $result['foto'];                    
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_parlamentares/view/editar/$id'>
                        <div class='titulo'> $page &raquo; Editar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            <li><a data-toggle='tab' 	href='#foto'>Foto</a></li>                        
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Nome:</label> <input name='nome' id='nome' value='$nome' placeholder='Nome'  class='obg'>
                                <p><label>Apelido:</label> <input name='apelido' id='apelido' value='$apelido' placeholder='Apelido'>
                                <p><label>Nível de instrução:</label> <select name='nivel_instrucao' id='nivel_instrucao'>
                                    <option value='$nivel_instrucao'>$nivel_instrucao_descricao</option>";
                                    $sql = "SELECT * FROM aux_parlamentares_nivel_instrucao 
                                            WHERE ativo = :ativo
                                            ORDER BY descricao";
                                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                                    $stmt->bindValue(':ativo',1);                                                                        								
                                    $stmt->execute();
                                    while($result = $stmt->fetch())
                                    {
                                        echo "<option value='".$result['id']."'>".$result['descricao']."</option>";
                                    }
                                    echo "
                                </select>
                                <p><label>Sexo:</label> <select name='sexo' id='sexo'>
                                    <option value='$sexo'>$sexo</option>
                                    <option value='Masculino'>Masculino</option>
                                    <option value='Feminino'>Feminino</option>                                    
                                </select>                                            
                                <p><label>Data Nascimento:</label> <input name='data_nasc' value='$data_nasc' id='data_nasc' placeholder='Data Nascimento' onkeypress='return mascaraData(this,event);'>
                                <p><label>CPF:</label> <input name='cpf' id='cpf' value='$cpf' placeholder='CPF'>
                                <p><label>RG:</label> <input name='rg' id='rg' value='$rg' placeholder='RG'>
                                <p><label>Título eleitor:</label> <input name='titulo_eleitor' value='$titulo_eleitor' id='titulo_eleitor' placeholder='Título eleitor'>
                                <p><label>Situação militar:</label> <select name='situacao_militar' id='situacao_militar'>
                                    <option value='$situacao_militar'>$situacao_militar_descricao</option>";
                                    $sql = "SELECT * FROM aux_parlamentares_tipo_situacao_militar 
                                            WHERE ativo = :ativo
                                            ORDER BY descricao";
                                        $stmt = $PDO_PROCLEGIS->prepare($sql);  
                                        $stmt->bindValue(':ativo',1);                                                                                                          
                                        $stmt->execute();
                                        while($result = $stmt->fetch())
                                        {
                                            echo "<option value='".$result['id']."'>".$result['descricao']."</option>";
                                        }
                                    echo "
                                </select> 
                                <p><label>Profissão:</label> <input name='profissao' value='$profissao' id='profissao' placeholder='Profissão'>
                                <p><label>Site:</label> <input name='site' id='site' value='$site' placeholder='Site'>
                                <p><label>Email:</label> <input name='email' id='email' value='$email' placeholder='Email'  class='obg'>
                                <p><label>Nº Gabinete:</label> <input name='n_gabinete' id='n_gabinete' value='$n_gabinete' placeholder='Nº Gabinete'>
                                <p><label>Telefone:</label> <input name='telefone' id='telefone' value='$telefone' placeholder='Telefone' onkeypress='return mascaraTELEFONE(this);'>
                                <p><label>CEP:</label> <input name='cep' id='cep' placeholder='CEP'  value='$cep' maxlength='9' onkeypress='mascaraCEP(this); return SomenteNumero(event);'>
                                <p><label>UF:</label> <select name='uf' id='uf'>
                                                            <option value='$uf'>$uf_sigla</option>
                                                            "; 
                                                            $sql = " SELECT * FROM end_uf ORDER BY uf_sigla";
                                                            $stmt = $PDO->prepare($sql);
                                                            $stmt->execute();
                                                            while($result = $stmt->fetch())
                                                            {
                                                                echo "<option value='".$result['uf_id']."'>".$result['uf_sigla']."</option>";
                                                            }
                                                            echo "
                                                        </select>
                                <p><label>Município:</label> <select name='municipio' id='municipio'>
                                    <option value='$municipio'>$mun_nome</option>
                                </select>
                                <p><label>Bairro:</label> <input name='bairro' value='$bairro' id='bairro' placeholder='Bairro' />
                                <p><label>Endereço:</label> <input name='endereco' value='$endereco' id='endereco' placeholder='Endereço' />
                                <p><label>Biografia:</label> <textarea name='biografia' id='biografia' placeholder='Biografia' />$biografia</textarea>
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
                            <div id='foto' class='tab-pane fade in'>
                                <p><label>Foto:</label> ";if($foto != ''){ echo "<img src='".$foto."' valign='middle' style='max-width:250px'>";} echo " &nbsp; 
                                <input type='hidden' name='foto_file' value='".$foto."'>	
                                <p><label>Alterar Foto:</label> <input type='file' name='foto[foto]' id='foto'>							
                            </div>                        				
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_parlamentares/view'; value='Cancelar'/></center>
                            </center>
                        </div>
                    </form>
                    ";
                }
            }	

            if($pagina == 'exib')
            {            		
                $sql = "SELECT *, aux_parlamentares_nivel_instrucao.descricao as nivel_instrucao_descricao,
                                  aux_parlamentares_tipo_situacao_militar.descricao as situacao_militar_descricao                        
                        FROM cadastro_parlamentares 
                        LEFT JOIN aux_parlamentares_nivel_instrucao ON aux_parlamentares_nivel_instrucao.id = cadastro_parlamentares.nivel_instrucao
                        LEFT JOIN aux_parlamentares_tipo_situacao_militar ON aux_parlamentares_tipo_situacao_militar.id = cadastro_parlamentares.situacao_militar
                        LEFT JOIN end_uf ON end_uf.uf_id = cadastro_parlamentares.uf
                        LEFT JOIN end_municipios ON end_municipios.mun_id = cadastro_parlamentares.municipio
                        WHERE cadastro_parlamentares.id = :id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                if($rows > 0)
                {
                    $result = $stmt->fetch();                                                 
                    $nome = $result['nome'];
                    $apelido   = $result['apelido'];
                    $nivel_instrucao  = $result['nivel_instrucao'];
                    $nivel_instrucao_descricao  = $result['nivel_instrucao_descricao'];
                    $sexo  = $result['sexo'];
                    $data_nasc  = reverteData($result['data_nasc']);
                    $cpf    = $result['cpf'];
                    $rg    = $result['rg'];
                    $titulo_eleitor    = $result['titulo_eleitor'];
                    $situacao_militar    = $result['situacao_militar'];
                    $situacao_militar_descricao    = $result['situacao_militar_descricao'];
                    $profissao    = $result['profissao'];
                    $site    = $result['site'];
                    $email = $result['email'];
                    $n_gabinete = $result['n_gabinete'];
                    $telefone = $result['telefone'];
                    $cep = $result['cep'];
                    $uf = $result['uf'];
                    $uf_sigla = $result['uf_sigla'];
                    $municipio = $result['municipio'];
                    $mun_nome = $result['mun_nome'];
                    $bairro = $result['bairro'];
                    $endereco = $result['endereco'];
                    $biografia = $result['biografia'];
                    $status = $result['status'];                                               
                    $foto = $result['foto'];                    
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='cadastro_parlamentares/view/editar/$id'>
                        <div class='titulo'> $page &raquo; Exibir </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
                            <li><a data-toggle='tab' href='#mandatos' id='mandatos-tab'>Mandatos</a></li>                        
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <table width='100%' cellpadding='8'>
                                    <tr>
                                        <td class='bold' align='right' width='11%' valign='top'>Foto:</td>                                        
                                        <td  width='37%' colspan='7'>";if($foto != ''){ echo "<img src='".$foto."' valign='middle' style='max-width:250px'>";} echo "</td>                                      
                                    </tr>
                                    <tr>
                                        <td class='bold' align='right' width='11%'>Nome:</td>
                                        <td  width='11%'>$nome</td>
                                        <td class='bold' align='right'  width='7%'>Apelido:</td>
                                        <td  width='11%'>$apelido</td>
                                        <td class='bold' align='right'  width='11%'>Nível de instrução:</td>
                                        <td  width='11%'>$nivel_instrucao_descricao</td>
                                        <td class='bold' align='right'  width='11%'>Sexo:</td>
                                        <td  width='15%'>$sexo</td>
                                    </tr>
                                    <tr>
                                        <td class='bold' align='right' width='11%'>Data Nascimento:</td>
                                        <td  width='11%'>$data_nasc</td>
                                        <td class='bold' align='right'  width='7%'>CPF:</td>
                                        <td  width='11%'>$cpf</td>
                                        <td class='bold' align='right'  width='11%'>RG:</td>
                                        <td  width='11%'>$rg</td>
                                        <td class='bold' align='right'  width='11%'>Título eleitor:</td>
                                        <td  width='15%'>$titulo_eleitor</td>
                                    </tr>
                                    <tr>
                                        <td class='bold' align='right' width='11%'>Situação militar:</td>
                                        <td  width='11%'>$situacao_militar_descricao</td>
                                        <td class='bold' align='right'  width='7%'>Profissão:</td>
                                        <td  width='11%'>$profissao</td>
                                        <td class='bold' align='right'  width='11%'>Site:</td>
                                        <td  width='11%'>$site</td>
                                        <td class='bold' align='right'  width='11%'>Email:</td>
                                        <td  width='15%'>$email</td>
                                    </tr>
                                    <tr>
                                        <td class='bold' align='right' width='11%'>Nº Gabinete:</td>
                                        <td  width='11%'>$n_gabinete</td>
                                        <td class='bold' align='right'  width='7%'>Telefone:</td>
                                        <td  width='11%'>$profissao</td>
                                        <td class='bold' align='right'  width='11%'>Endereço:</td>
                                        <td  width='37%' colspan='3'>$endereco - $bairro - $mun_nome/$uf_sigla - $cep</td>                                      
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
                                    <tr>
                                        <td class='bold' align='right' width='11%' valign='top'>Biografia:</td>                                        
                                        <td  width='37%' colspan='7'>".nl2br($biografia)."</td>                                      
                                    </tr>
                                </table>                                                                                                                                                    
                            </div>                        
                            <div id='mandatos' class='tab-pane fade in'>
                                ";
                                $sql = "SELECT * FROM cadastro_parlamentares_mandatos 
                                        WHERE parlamentar = :parlamentar
                                        ORDER BY id DESC
                                       ";
                                $stmt = $PDO_PROCLEGIS->prepare($sql);    
                                $stmt->bindParam(':fil_nome1', 	$fil_nome1);
                                                                    
                                $stmt->bindParam(':parlamentar', 	$id);                                    
                                $stmt->execute();
                                $rows = $stmt->rowCount();
                                                
                                echo "
                                <div id='botoes'>
                                    <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"cadastro_parlamentares_mandatos/add\");'><i class='fas fa-plus'></i></div>
                                    <div class='filtrar'><span class='f'><i class='fas fa-filter'></i></span></div>
                                    <div class='filtro'>
                                        <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_parlamentares/view'>
                                        <input name='fil_nome' id='fil_nome' value='$fil_nome' placeholder='Nome'>
                                        <input type='submit' value='Filtrar'> 
                                        </form>            
                                    </div>    
                                </div>";
                                if ($rows > 0)
                                {
                                    echo "
                                    <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                                        <tr>
                                            <td class='titulo_tabela'>Foto</td>
                                            <td class='titulo_tabela'>Nome</td>
                                            <td class='titulo_tabela' align='center'>Status</td>
                                            <td class='titulo_tabela' align='right'>Gerenciar</td>
                                        </tr>";
                                        $c=0;
                                        while($result = $stmt->fetch())
                                        {
                                            $id = $result['id'];
                                            $nome = $result['nome'];
                                            $foto = $result['foto'].$result['mof_foto'].$result['fun_foto'].$result['vis_foto'];
                                            $status = $result['status'];
                                            if($foto == '')
                                            {
                                                $foto = '../../core/imagens/perfil.png';
                                            }
                                            
                                            
                                            if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                                            echo "<tr class='$c1'>
                                                    <td><div class='perfil' style='background:url($foto) center center; background-size: cover; border-radius:50px; width:50px; height:50px;' border='0'></div></td>
                                                    <td>$nome</td>
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
                                        $cnt = "SELECT COUNT(*) FROM cadastro_parlamentares  WHERE ".$nome_query."";
                                        $stmt = $PDO_PROCLEGIS->prepare($cnt);     
                                        $stmt->bindParam(':fil_nome1', 	$fil_nome1);
                                              
                                        $variavel = "&fil_nome=$fil_nome";            
                                        include("../../core/mod_includes/php/paginacao.php");
                                }
                                else
                                {
                                    echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
                                }                                
                                echo "
                            </div>                        				
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_parlamentares/view'; value='Cancelar'/></center>
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
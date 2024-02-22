<?php
$pagina_link = 'cadastro_clientes';
//include_once("url.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../core/mod_includes/php/connect_sistema.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo include_once("url.php");?>
    <title>Audipam | Gerenciador de Sistemas</title>
    <meta	name="viewport" content="width=device-width, initial-scale=1">
    <meta 	name="author" content="MogiComp">
    <meta 	http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link 	rel="shortcut icon" href="../core/imagens/favicon.png">
    <link 	href="../core/mod_menu/css/reset.css" rel="stylesheet" > <!-- CSS reset -->
    <link 	href="../core/css/style.css" rel="stylesheet" type="text/css" />
    <script src="../core/mod_includes/js/jquery-2.1.4.js" type="text/javascript"></script>
    <script src="../core/mod_includes/js/funcoes.js" type="text/javascript"></script>
    <!-- TOOLBAR -->
    <link 	href="../core/mod_includes/js/toolbar/jquery.toolbars.css" rel="stylesheet" />
    <link 	href="../core/mod_includes/js/toolbar/bootstrap.icons.css" rel="stylesheet">
    <script src="../core/mod_includes/js/toolbar/jquery.toolbar.js"></script>
    <!-- TOOLBAR -->
    <link 	href="../core/mod_includes/js/janela/jquery-ui.css" rel="stylesheet" >
    <script src="../core/mod_includes/js/janela/jquery-ui.js"></script>
    <!-- MENU -->
    <link 	href="../core/mod_menu/css/style.css" rel="stylesheet" > <!-- Resource style -->
    <script src="../core/mod_menu/js/modernizr.js"></script> <!-- Modernizr -->
    <script src="../core/mod_menu/js/jquery.menu-aim.js"></script>
    <script src="../core/mod_menu/js/main.js"></script> <!-- Resource jQuery -->    
    <!-- FIM MENU -->
    <!-- ABAS -->
    <link 	href="../core/mod_includes/js/abas/bootstrap.css" rel="stylesheet">
    <script src="../core/mod_includes/js/abas/bootstrap.js"></script>
    <!-- ABAS -->
</head>
<body>
	<?php
    
	require_once('../core/mod_includes/php/funcoes-jquery.php');
	require_once('../core/mod_includes/php/verificalogin.php');
	require_once('../core/mod_includes/php/verificapermissao.php');	
    include("../core/mod_menu/barra.php");
      
	?>
	<main class="cd-main-content">
    
    	<!--MENU-->
		<?php include("../core/mod_menu/menu.php"); ?>
        
        <!--CONTEUDO CENTRO-->
		<div class="content-wrapper">
        <div class='mensagem'></div>
        
        <?php 

        
        ob_flush();
        flush();

        
        $page = "Cadastro &raquo; <a href='cadastro_clientes/view'>Clientes</a>";
        if(isset($_GET['cli_id'])){$cli_id = $_GET['cli_id'];}
        $cli_sistema 			= $_POST['cli_sistema'];
        $cli_nome 				= $_POST['cli_nome'];
        $cli_url  				= $_POST['cli_url'];
		$cli_cep 				= $_POST['cli_cep'];
		$cli_uf 				= $_POST['cli_uf'];
		$cli_municipio 			= $_POST['cli_municipio'];
		$cli_bairro 			= $_POST['cli_bairro'];
		$cli_endereco 			= $_POST['cli_endereco'];
		$cli_numero 			= $_POST['cli_numero'];
		$cli_comp 				= $_POST['cli_comp'];
		$cli_telefone 			= $_POST['cli_telefone'];
        $cli_whats 			    = $_POST['cli_whats'];
		$cli_email 				= $_POST['cli_email'];
		$cli_site 				= $_POST['cli_site'];
		$cli_status				= $_POST['cli_status'];
		$cli_analytics			= $_POST['cli_analytics'];
		
		
		
        $dados = array_filter(array(
            'cli_sistema' 			=> $cli_sistema,
			'cli_nome' 				=> $cli_nome,
            'cli_url' 				=> $cli_url,
			'cli_cep' 				=> $cli_cep,
            'cli_uf' 			    => $cli_uf,
            'cli_municipio' 		=> $cli_municipio,
            'cli_bairro' 			=> $cli_bairro,
            'cli_endereco' 			=> $cli_endereco,
            'cli_numero' 			=> $cli_numero,
            'cli_comp' 			    => $cli_comp,
            'cli_telefone' 		    => $cli_telefone,
            'cli_whats' 		    => $cli_whats,
            'cli_email' 			=> $cli_email,
            'cli_site' 			    => $cli_site,
            'cli_status' 			=> $cli_status,
            'cli_analytics' 			=> $cli_analytics
        ));
        
        if($action == "adicionar")
        {                       
           
            $sql = "INSERT INTO cadastro_clientes SET ".bindFields($dados);
            $stmt = $PDO->prepare($sql);	
            if($stmt->execute($dados))
            {		
                $cli_id = $PDO->lastInsertId();

                // CRIA BASE DE DADOS
                error_reporting(E_ALL);
                
                
                // $sql = "CREATE DATABASE IF NOT EXISTS audipamcom_proclegis_".$cli_url."; GRANT ALL ON `audipamcom_proclegis_".$cli_url."`.* TO 'root'@'localhost'; FLUSH PRIVILEGES;";
                // $stmt = $PDO_SOURCE->prepare($sql);	
                // if($stmt->execute())
                // {
                //    //include("copydb.php");

                // }
                // else
                // {
                //     print_r($stmt->errorInfo());
                // }

                //LINKS - CAMPOS DINÂMICOS			
                if(!empty($_POST['links']) && is_array($_POST['links']))
                {
                    //LIMPA ARRAY
                    foreach($_POST['links'] as $item => $valor) 
                    {
                        $links_filtrado[$item] = array_filter($valor);
                    }
                    //
                    foreach($links_filtrado as $item => $valor) 
                    {		
                        if(!empty($valor))
                        {				
                            $valor['lin_cliente'] = $cli_id;
                            $sql = "INSERT INTO cadastro_clientes_links SET ".bindFields($valor);
                            $stmt = $PDO->prepare($sql);	
                            if($stmt->execute($valor))
                            {
                                //INSERE
                            }
                            else{ $erro=1; $err = $stmt->errorInfo();}
                        }
                    }
                }             
                
                //UPLOAD ARQUIVOS
				require_once '../core/mod_includes/php/lib/WideImage.php';
				$caminho = "../proclegis/uploads/clientes/";
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
							$cli_foto	= $caminho;
							$cli_foto .= "foto_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
							move_uploaded_file($nomeTemporario, ($cli_foto));
							$imnfo = getimagesize($cli_foto);
							$img_w = $imnfo[0];	  // largura
							$img_h = $imnfo[1];	  // altura
							if($img_w > 500 || $img_h > 500)
							{
								$image = WideImage::load($cli_foto);
								$image = $image->resize(500, 500);
								$image->saveToFile($cli_foto);
							}
							$sql = "UPDATE cadastro_clientes SET 
									cli_foto 	 = :cli_foto
									WHERE cli_id = :cli_id ";
							$stmt = $PDO->prepare($sql);
							$stmt->bindParam(':cli_foto',$cli_foto);
							$stmt->bindParam(':cli_id',$cli_id);
							if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
						}					
					}
				}
				//
            
				?>
				<script>
                    jQuery('#mask , .janela, .janelaAcao').fadeOut(100 , function() {
                        jQuery('.janela, .janelaAcao').fadeOut(100 , function() {
                        jQuery('#mask').remove();  
                        jQuery('body').css({'overflow':'visible'});
                        });
                    }); 
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
            $sql = "UPDATE cadastro_clientes SET ".bindFields($dados)." WHERE cli_id = :cli_id ";
            $stmt = $PDO->prepare($sql); 
            $dados['cli_id'] =  $cli_id;
            if($stmt->execute($dados))
            {
                ## CAMPOS DINÂMICOS ##
                // LINKS - EXCLUI OS REMOVIDOS
                if(!empty($_POST['links']) && is_array($_POST['links']))
                {
                    //LIMPA ARRAY
                    foreach($_POST['links'] as $item => $valor) 
                    {
                        $links_filtrado[$item] = array_filter($valor);
                    }
                    //
                    
                    $a_excluir = array();
                    foreach($links_filtrado as $item) 
                    {
                        if(isset($item['lin_id']))
                        {
                            $a_excluir[] = $item['lin_id'];
                        }
                    }
                    if(!empty($a_excluir))
                    {
                        $sql = "DELETE FROM cadastro_clientes_links WHERE lin_cliente = :cli_id AND lin_id NOT IN (".implode(",",$a_excluir).") ";
                        
                        $stmt = $PDO->prepare($sql); 
                        $stmt->bindParam(':cli_id', $cli_id);
                        if($stmt->execute())
                        {
                            //echo "Excluido <br>";
                        }
                        else{ $erro=1; $err = $stmt->errorInfo();}
                    }
                    else
                    {
                        $sql = "DELETE FROM cadastro_clientes_links WHERE lin_cliente = :cli_id ";
                        $stmt = $PDO->prepare($sql); 
                        $stmt->bindParam(':cli_id', $cli_id);
                        if($stmt->execute())
                        {
                            //echo "Excluido todos <br>";
                        }
                        else{ $erro=1; $err = $stmt->errorInfo();}
                    }
                }
                else
                {
                    $sql = "DELETE FROM cadastro_clientes_links WHERE lin_cliente = :cli_id ";
                    $stmt = $PDO->prepare($sql); 
                    $stmt->bindParam(':cli_id', $cli_id);
                    if($stmt->execute())
                    {
                        //echo "Excluido todos <br>";
                    }
                    else{ $erro=1; $err = $stmt->errorInfo();}
                }
                
                // LINKS - ATUALIZA OU INSERE NOVOS
                if(!empty($_POST['links']) && is_array($_POST['links']))
                {
                    //LIMPA ARRAY
                    foreach($_POST['links'] as $item => $valor) 
                    {
                        $links_filtrado[$item] = array_filter($valor);
                    }
                    //
                    foreach(array_filter($links_filtrado) as $item => $valor) 
                    {
                        if(isset($valor['lin_id']))
                        {
                            $valor2 = $valor;
                            unset($valor2['lin_id']);
                            
                            $sql = "UPDATE cadastro_clientes_links SET ".bindFields($valor2)." WHERE lin_id = :lin_id";
                            $stmt = $PDO->prepare($sql);	
                            if($stmt->execute($valor))
                            {
                                //echo "Atualizado <br>";
                            }
                            else{ $erro=1; $err = $stmt->errorInfo();}
                        }
                        else
                        {
                            $valor['lin_cliente'] = $cli_id;
                            $sql = "INSERT INTO cadastro_clientes_links SET ".bindFields($valor);
                            $stmt = $PDO->prepare($sql);	
                            if($stmt->execute($valor))
                            {
                                //echo "Inserido <br>";
                            }
                            else{ $erro=1; $err = $stmt->errorInfo();}
                        }
                    }
                }

				// PARAMETROS - ATUALIZA OU INSERE NOVOS
                if(!empty($_POST['parametros']) && is_array($_POST['parametros']))
                {
                    //LIMPA ARRAY
                    foreach($_POST['parametros'] as $item => $valor) 
                    {
                        $parametros_filtrado[$item] = array_filter($valor);
                    }
                    //
                    foreach(array_filter($parametros_filtrado) as $item => $valor) 
                    {
                        if(isset($valor['pac_id']))
                        {
                            $valor2 = $valor;
                            unset($valor2['pac_id']);
                            $sql = "UPDATE parametros_clientes SET ".bindFields($valor2)." WHERE pac_id = :pac_id";
                            $stmt = $PDO_TRANSPARENCIA->prepare($sql);	
                            if($stmt->execute($valor))
                            {
                                //echo "Atualizado <br>";
                            }
                            else{ $erro=1; $err = $stmt->errorInfo();}
                        }
                        else
                        {
                            $valor['pac_cliente'] = $cli_id;
							$sql = "INSERT INTO parametros_clientes SET ".bindFields($valor);
                            $stmt = $PDO_TRANSPARENCIA->prepare($sql);	
                            if($stmt->execute($valor))
                            {
                                //echo "Inserido <br>";
                            }
                            else{ $erro=1; $err = $stmt->errorInfo();}
                        }
                    }
                }
				
				
				//UPLOAD ARQUIVOS
				require_once '../core/mod_includes/php/lib/WideImage.php';
				$caminho = "../proclegis/uploads/clientes/";
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
							$cli_foto	= $caminho;
							$cli_foto .= "foto_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
							move_uploaded_file($nomeTemporario, ($cli_foto));
							$imnfo = getimagesize($cli_foto);
							$img_w = $imnfo[0];	  // largura
							$img_h = $imnfo[1];	  // altura
							if($img_w > 500 || $img_h > 500)
							{
								$image = WideImage::load($cli_foto);
								$image = $image->resize(500, 500);
								$image->saveToFile($cli_foto);
							}
							$sql = "UPDATE cadastro_clientes SET 
									cli_foto 	 = :cli_foto
									WHERE cli_id = :cli_id ";
							$stmt = $PDO->prepare($sql);
							$stmt->bindParam(':cli_foto',$cli_foto);
							$stmt->bindParam(':cli_id',$cli_id);
							if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
						}					
					}
				}
				//
			}
            else{ $erro=1; $err = $stmt->errorInfo();}
            
			if(!$erro)
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
        
        if($action == 'excluir')
        {
			$sql = "DELETE FROM cadastro_clientes WHERE cli_id = :cli_id";
            $stmt = $PDO->prepare($sql);
            $stmt->bindParam(':cli_id',$cli_id);
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
            $fil_nome1 = $fil_nome2 = "%".$fil_nome."%";
            $nome_query = " (cli_nome LIKE :fil_nome1 OR cli_nome_fotografado LIKE :fil_nome2 ) ";
        }

        $sql = "SELECT * FROM cadastro_clientes 
                LEFT JOIN cadastro_sistemas ON cadastro_sistemas.sis_id = cadastro_clientes.cli_sistema	
				 WHERE ".$nome_query."
                ORDER BY cli_id DESC
                LIMIT :primeiro_registro, :num_por_pagina ";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':fil_nome', 	$fil_nome1);
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
                    <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_clientes/view'>
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
                        <td class='titulo_first' align='left'>Logo</td>
                        <td class='titulo_tabela' align='left'>Cliente</td>                    
                        <td class='titulo_tabela' align='left'>Sistema</td>
                        <td class='titulo_tabela' align='center'>Portal</td>
                        <td class='titulo_tabela' align='center'>Status</td>
                        <td class='titulo_last' align='center' >Gerenciar</td>
                    </tr>";
                    $c=0;
                    while($result = $stmt->fetch())
                    {
                        $cli_id 			= $result['cli_id'];
						$cli_sistema 		= $result['cli_sistema'];
						$cli_nome 			= $result['cli_nome'];
                        $cli_url 			= $result['cli_url'];
                        $cli_status 		= $result['cli_status'];
						$cli_foto = $result['cli_foto'];
                        if($cli_foto == '')
                        {
                            $cli_foto = 'imagens/perfil.png';
                        }
                        $sis_nome 			= $result['sis_nome'];
                        $sis_url 			= $result['sis_url'];
                        $sis_dominio 			= $result['sis_dominio'];
                        
                        if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
                        echo "<tr class='$c1'>
                                <td><img src='".$sis_url."/".$cli_foto."' border='0' width='120' /></td>
                                <td>$cli_nome</td>
                                <td>$sis_nome</td>
                                <td align=center><a href='$sis_dominio/$cli_url' target='_blank'><i class='fas fa-link'></i></td>
                                <td align=center>";
                                    if($cli_status == 1)
                                    {
                                    echo "<img border='0' src='imagens/icon-ativo.png' width='15' height='15'>";
                                    }
                                    else
                                    {
                                    echo "<img border='0' src='imagens/icon-inativo.png' width='15' height='15'>";
                                    }
                                    echo "
                                </td>
                                <td align=center  width='180'>
                                    <div class='g_excluir' title='Excluir' onclick=\"
                                        abreMask(
                                            'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
                                            '<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/view/excluir/$cli_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                                            '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                                        \">	<i class='far fa-trash-alt'></i>
                                    </div>
                                    <div class='g_editar' title='Editar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/edit/$cli_id\");'><i class='fas fa-pencil-alt'></i></div>
                                    ";
                                    if($cli_status == 1)
                                    {
                                        echo "<div class='g_status' title='Desativar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/view/desativar/$cli_id\");'><i class='fas fa-sync-alt'></i></div>";
                                    }
                                    else
                                    {
                                        echo "<div class='g_status' title='Ativar' onclick='verificaPermissao(".$permissoes["edit"].",\"".$pagina_link."/view/ativar/$cli_id\");'><i class='fas fa-sync-alt'></i></div>";
                                    }
                                    echo "
                                    <a href='$sis_dominio/admin/login/$cli_url' target='_blank'><div class='g_exibir' title='Acesso Restrito'><i class='fas fa-lock'></i></div></a>
                                </td>
                              </tr>";
                    }
                    echo "</table>";
                    $cnt = "SELECT COUNT(*) FROM cadastro_clientes WHERE ".$nome_query." ";
                    $stmt = $PDO->prepare($cnt);
                    $stmt->bindParam(':fil_nome1', 	$fil_nome1);
                    $stmt->bindParam(':fil_nome2', 	$fil_nome2);					              
					
                    include("../core/mod_includes/php/paginacao.php");
            }
            else
            {
                echo "<br><br><br><br><br>Não há nenhum item cadastrado.";
            }
        }
        if($pagina == 'add')
        {
            echo "	
            <form name='form' id='form' autocomplete='off' enctype='multipart/form-data' method='post' action='cadastro_clientes/view/adicionar'>
                <div class='titulo'> $page &raquo; Adicionar  </div>
                <ul class='nav nav-tabs'>
                        <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                        <li><a data-toggle='tab' 	href='#links'>Links Úteis</a></li>.                        
                        <li><a data-toggle='tab' href='#foto'>Logo</a></li>
                        <li><a data-toggle='tab' href='#analytics'>Analytics</a></li>
					</ul>
					<div class='tab-content'>
						<div id='dados_gerais' class='tab-pane fade in active'>
                            <br><label>Sistema*:</label> <select name='cli_sistema' id='cli_sistema' class='obg'>
                                                    <option value=''>Sistema</option>
                                                    "; 
                                                    $sql = " SELECT * FROM cadastro_sistemas ORDER BY sis_nome";
                                                    $stmt_sis = $PDO->prepare($sql);
                                                    $stmt_sis->execute();
                                                    while($result_sis = $stmt_sis->fetch())
                                                    {
                                                        echo "<option value='".$result_sis['sis_id']."'>".$result_sis['sis_nome']."</option>";
                                                    }
                                                    echo "
                                                </select>
                            <p><label>Nome do Cliente*:</label> <input name='cli_nome' id='cli_nome' placeholder='Nome do Cliente' class='obg'>
                            <p><label>URL*:</label> <input name='cli_url' id='cli_url' placeholder='URL' class='obg'>
                            <p><label>CEP*:</label> <input name='cli_cep' id='cli_cep' placeholder='CEP' class='obg'  maxlength='9' onkeypress='mascaraCEP(this); return SomenteNumero(event);'>
                            <p><label>UF:</label> <select name='cli_uf' id='cli_uf'>
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
                            <p><label>Município:</label> <select name='cli_municipio' id='cli_municipio'>
                            <option value=''>Município</option>
                            </select>
                            <p><label>Bairro:</label> <input name='cli_bairro' id='cli_bairro' placeholder='Bairro' />
                            <p><label>Endereço:</label> <input name='cli_endereco' id='cli_endereco' placeholder='Endereço' />
                            <p><label>Número:</label> <input name='cli_numero' id='cli_numero' placeholder='Número' />
                            <p><label>Complemento:</label> <input name='cli_comp' id='cli_comp' placeholder='Complemento' />
                            <p><label>Telefone:</label> <input name='cli_telefone' id='cli_telefone' placeholder='Telefone' onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);' />
                            <p><label>Whatsapp:</label> <input name='cli_whats' id='cli_whats' placeholder='Whatsapp' onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);' />

                            <p><label>Email:</label> <input name='cli_email' id='cli_email' placeholder='Email'/>
                            <p><label>Site:</label> <input name='cli_site' id='cli_site' placeholder='Site'/>
                            <p><label>Status:</label> 
                            <input type='radio' name='cli_status' value='1' checked> Ativo <br>
                            <input type='radio' name='cli_status' value='0'> Inativo<br>

                            </div>
                            <div id='links' class='tab-pane fade in'>	 
                                <div id='p_scents_links'>
                                    <div class='bloco_links'>
                                        <input type='hidden' name='links[1][lin_id]' id='lin_id'>
                                        <br><label>Nome Link:</label>	<input name='links[1][lin_nome]' id='lin_nome' placeholder='Nome Link'>
                                        <p><label>Link:</label>	<input name='links[1][lin_link]' id='lin_link' placeholder='Link'>
                                        <p><img src='imagens/icon-add.png' id='addLinks' title='Adicionar +' class='botao_dinamico'></p>
                                        <br>
                                    </div>
                                </div>
                            </div>                            
                            <div id='foto' class='tab-pane fade in' style='text-align:center'>
                            <p><label>Foto:</label> <input type='file' name='cli_foto[foto]' id='cli_foto' placeholder='Foto'>
                            </div>   
                            <div id='analytics' class='tab-pane fade in' style='text-align:center'>
                            <p><label>ID acompanhamento:</label> <textarea name='cli_analytics' id='cli_analytics' placeholder='ID acompanhamento'></textarea>
                        </div>                                               
					</div>
					<center>
					<div id='erro' align='center'>&nbsp;</div>
					<input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
					<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_clientes/view'; value='Cancelar'/></center>
					</center>
				</div>
			</form>
            ";
        }
        
        if($pagina == 'edit')
        {            		
			$sql = "SELECT * FROM cadastro_clientes 
                    LEFT JOIN end_uf ON end_uf.uf_id = cadastro_clientes.cli_uf
                    LEFT JOIN end_municipios ON end_municipios.mun_id = cadastro_clientes.cli_municipio
                    WHERE cli_id = :cli_id ";
            $stmt = $PDO->prepare($sql);            
			$stmt->bindParam(':cli_id', $cli_id);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if($rows > 0)
            {
                $result = $stmt->fetch();
				//PEGA DADOS DO SISTEMA
				$sql = "SELECT sis_nome, sis_url FROM cadastro_sistemas WHERE sis_id = :sis_id";
				$stmt_sis = $PDO->prepare($sql);
				$stmt_sis->bindParam(':sis_id', 	$result['cli_sistema']);
				$stmt_sis->execute();
				$result_sis = $stmt_sis->fetch();
				$sis_nome 	= $result_sis['sis_nome'];         
                
                echo "
                <form name='form_cadastro_clientes' id='form_cadastro_clientes' enctype='multipart/form-data' method='post' action='cadastro_clientes/view/editar/$cli_id'>
                    <div class='titulo'> $page &raquo; Editar </div>
                    <ul class='nav nav-tabs'>
					  	<li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
					  	<li><a data-toggle='tab' 	href='#links'>Links Úteis</a></li>                        
                        <li><a data-toggle='tab' href='#foto'>Logo</a></li>
                        <li><a data-toggle='tab' href='#analytics'>Analytics</a></li>
					</ul>
					<div class='tab-content'>
						<div id='dados_gerais' class='tab-pane fade in active'>
							<br><label>Sistema*:</label> <select name='cli_sistema' id='cli_sistema' class='obg'>
														<option value='".$result['cli_sistema']."'>".$sis_nome."</option>
														"; 
														$sql = " SELECT * FROM cadastro_sistemas ORDER BY sis_nome";
														$stmt_sis = $PDO->prepare($sql);
														$stmt_sis->execute();
														while($result_sis = $stmt_sis->fetch())
														{
															echo "<option value='".$result_sis['sis_id']."'>".$result_sis['sis_nome']."</option>";
														}
														echo "
													</select>
							<p><label>Nome do Cliente*:</label> <input name='cli_nome' id='cli_nome' value='".$result['cli_nome']."' placeholder='Nome do Cliente' class='obg'>
							<p><label>URL*:</label> <input name='cli_url' id='cli_url' value='".$result['cli_url']."' placeholder='URL' class='obg'>
							<p><label>CEP*:</label> <input name='cli_cep' id='cli_cep' value='".$result['cli_cep']."' placeholder='CEP' maxlength='9' onkeypress='mascaraCEP(this); return SomenteNumero(event);' />
							<p><label>UF:</label> <select name='cli_uf' id='cli_uf'>
														<option value='".$result['cli_uf']."'>".$result['uf_sigla']."</option>
														"; 
														$sql = " SELECT * FROM end_uf ORDER BY uf_sigla";
														$stmt_uf = $PDO->prepare($sql);
														$stmt_uf->execute();
														while($result_uf = $stmt_uf->fetch())
														{
															echo "<option value='".$result_uf['uf_id']."'>".$result_uf['uf_sigla']."</option>";
														}
														echo "
													</select>
							<p><label>Município:</label> <select name='cli_municipio' id='cli_municipio'>
								<option value='".$result['cli_municipio']."'>".$result['mun_nome']."</option>
							</select>
							<p><label>Bairro:</label> <input name='cli_bairro' id='cli_bairro' value='".$result['cli_bairro']."' placeholder='Bairro' />
							<p><label>Endereço:</label> <input name='cli_endereco' id='cli_endereco' value='".$result['cli_endereco']."' placeholder='Endereço' />
							<p><label>Número:</label> <input name='cli_numero' id='cli_numero' value='".$result['cli_numero']."' placeholder='Número' />
							<p><label>Complemento:</label> <input name='cli_comp' id='cli_comp' value='".$result['cli_comp']."' placeholder='Complemento' />
							<p><label>Telefone:</label> <input name='cli_telefone' id='cli_telefone' value='".$result['cli_telefone']."' placeholder='Telefone' onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);' />
                            <p><label>Whatsapp:</label> <input name='cli_whats' id='cli_whats' value='".$result['cli_whats']."' placeholder='Whatsapp' onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);' />
							<p><label>Email:</label> <input name='cli_email' id='cli_email' value='".$result['cli_email']."' placeholder='Email' />
							<p><label>Site:</label> <input name='cli_site' id='cli_site' value='".$result['cli_site']."' placeholder='Site' />
							<p><label>Status:</label> 
								<input type='radio' name='cli_status' value='1' checked> Ativo <br>
								<input type='radio' name='cli_status' value='0'> Inativo<br>
							
                        </div>
                        <div id='links' class='tab-pane fade in'>
                            <div id='p_scents_links'>
							";
							$sql = "SELECT * FROM cadastro_clientes_links 
									WHERE lin_cliente = :lin_cliente";
							$stmt_link = $PDO->prepare($sql);
							$stmt_link->bindParam(':lin_cliente', $cli_id);
							$stmt_link->execute();
							$rows_link = $stmt_link->rowCount();
							if($rows_link > 0)
							{
								$x=0;
								while($result_link = $stmt_link->fetch())
								{
									$x++;
									echo "
									<div class='bloco_links'>
										<input type='hidden' name='links[$x][lin_id]' id='lin_id' value='".$result_link['lin_id']."'>
										"; if($x > 1){ echo "<br><br><p>";}else{ echo "<br>";} echo "
										   <label>Nome Link:</label><input name='links[$x][lin_nome]' id='lin_nome' value='".$result_link['lin_nome']."' placeholder='Nome Link'> 
										   <p><label>Link:</label><input name='links[$x][lin_link]' id='lin_link' value='".$result_link['lin_link']."' placeholder='Link'> 
										   <br>
										<p><img src='imagens/icon-add.png' id='addLinks' title='Adicionar +' class='botao_dinamico'> <img src='imagens/icon-rmv.png' id='remLinks' title='Remover' class='botao_dinamico'>
                                        
                                    </div>
                                    <br>
									";
								}
							}
							else
							{
								echo "
								<div class='bloco_links'>
									<input type='hidden' name='links[1][lin_id]' id='lin_id'>
									<br><label>Nome Link:</label>	<input name='links[1][lin_nome]' id='lin_nome' placeholder='Nome Link'>
									<p><label>Link:</label>	<input name='links[1][lin_link]' id='lin_link' placeholder='Link'>
									<p><img src='imagens/icon-add.png' id='addLinks' title='Adicionar +' class='botao_dinamico'>
								</div>
								";
							}
							echo "
							</div>
                        </div>						
						<div id='foto' class='tab-pane fade in'>
							<p><label>Foto:</label> ";if($result['cli_foto'] != ''){ echo "<img src='".$result['cli_foto']."' valign='middle' style='max-width:250px'>";} echo " &nbsp; 
							<p><label>Alterar Foto:</label> <input type='file' name='cli_foto[foto]' id='cli_foto'>
                        </div>      
                        <div id='analytics' class='tab-pane fade in' style='text-align:center'>
                            <p><label>ID acompanhamento:</label> <textarea name='cli_analytics' id='cli_analytics' placeholder='ID acompanhamento'>".$result['cli_analytics']."</textarea>
                        </div>           
					</div>
                    <center>
                    <div id='erro' align='center'>&nbsp;</div>
                    <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                    <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_clientes/view'; value='Cancelar'/></center>
                    </center>
                </form>
                ";
            }
        }	
        ?>
    	</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
</body>
</html>
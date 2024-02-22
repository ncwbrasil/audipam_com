<?php
session_start (); 
$pagina_link = 'cadastro_empresas';
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Audipam - Sistema Administrativo Integrado</title>
<meta name="author" content="MogiComp">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="../imagens/favicon.ico">
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<!-- ABAS -->
<link rel="stylesheet" href="../mod_includes/js/abas/bootstrap.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="../mod_includes/js/abas/bootstrap.js"></script>
<!-- ABAS -->
<script src="../mod_includes/js/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="../mod_includes/js/funcoes.js"></script>
<!-- TOOLBAR -->
<link href="../mod_includes/js/toolbar/jquery.toolbars.css" rel="stylesheet" />
<link href="../mod_includes/js/toolbar/bootstrap.icons.css" rel="stylesheet">
<script src="../mod_includes/js/toolbar/jquery.toolbar.js"></script>
<!-- TOOLBAR -->
<link rel="stylesheet" href="../mod_includes/js/janela/jquery-ui.css">
<script src="../mod_includes/js/janela/jquery-ui.js"></script>
</head>
<body>
<?php	
$emp_id = $_GET['emp_id'];

require_once("../mod_includes/php/ctracker.php");
include		('../mod_includes/php/connect.php');

include		('../mod_includes/php/funcoes-jquery.php');
require_once('../mod_includes/php/verificalogin.php');
require_once('../mod_includes/php/verificapermissao.php');
?>

<div class='lateral'>
	<?php include("../mod_menu/menu.php");?>
</div>
<div class='barra'> 
    <?php include("../mod_menu/barra.php");?>
</div>
<div class='centro'>
    <div class='box'>
	<?php
    $page = "Cadastros &raquo; <a href='cadastro_empresas.php?pagina=cadastro_empresas".$autenticacao."'>Empresas</a>";
	//$emp_id = $_GET['emp_id'];
	$emp_tipo = $_POST['emp_tipo'];
	$emp_cpf_cnpj = $_POST['emp_cnpj'].$_POST['emp_cpf'];
	$emp_nome_razao = $_POST['emp_nome_razao'];
	$emp_fantasia = $_POST['emp_fantasia'];
	

	$emp_sigla = $_POST['emp_sigla'];
	$emp_data_fundacao = implode("-",array_reverse(explode("/",$_POST['emp_data_fundacao'])));if($emp_data_fundacao == ''){ $emp_data_fundacao = null;}
	$emp_categ_adm = $_POST['emp_categ_adm'];if($emp_categ_adm == ''){ $emp_categ_adm = null;}
	$emp_esfera = $_POST['emp_esfera'];if($emp_esfera == ''){ $emp_esfera = null;}
	$emp_poder = $_POST['emp_poder'];if($emp_poder == ''){ $emp_poder = null;}
	$emp_orgao = $_POST['emp_orgao'];if($emp_orgao == ''){ $emp_orgao = null;}
	$emp_cep = $_POST['emp_cep'];
	$emp_uf = $_POST['emp_uf'];
	$emp_municipio = $_POST['emp_municipio'];
	$emp_bairro = $_POST['emp_bairro'];
	$emp_endereco = $_POST['emp_endereco'];
	$emp_numero = $_POST['emp_numero'];
	$emp_comp = $_POST['emp_comp'];
	$emp_telefone = $_POST['emp_telefone'];
	$emp_fax = $_POST['emp_fax'];
	$emp_email = $_POST['emp_email'];
	$emp_site = $_POST['emp_site'];
	$emp_status = $_POST['emp_status'];
	$dados = array_filter(array(
		'emp_tipo' 			=> $emp_tipo,
		'emp_cpf_cnpj' 		=> $emp_cpf_cnpj,
		'emp_nome_razao' 	=> $emp_nome_razao,
		'emp_fantasia' 		=> $emp_fantasia,
		'emp_sigla' 		=> $emp_sigla,
		'emp_data_fundacao' => $emp_data_fundacao,
		'emp_categ_adm' 	=> $emp_categ_adm,
		'emp_esfera' 		=> $emp_esfera,
		'emp_poder' 		=> $emp_poder,
		'emp_orgao' 		=> $emp_orgao,
		'emp_cep' 			=> $emp_cep,
		'emp_uf' 			=> $emp_uf,
		'emp_municipio'	 	=> $emp_municipio,
		'emp_bairro' 		=> $emp_bairro,
		'emp_endereco' 		=> $emp_endereco,
		'emp_numero' 		=> $emp_numero,
		'emp_comp' 			=> $emp_comp,
		'emp_telefone' 		=> $emp_telefone,
		'emp_fax' 			=> $emp_fax,
		'emp_email' 		=> $emp_email,
		'emp_site' 			=> $emp_site,
		'emp_status'		=> $emp_status
	),'strlen');
	
	if($action == "adicionar")
    {
		$sql = "SELECT * FROM cadastro_empresas 
				WHERE emp_nome_razao = :emp_nome_razao OR emp_cpf_cnpj = :emp_cpf_cnpj
				ORDER BY emp_id DESC";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':emp_cpf_cnpj', 	$emp_cpf_cnpj);
		$stmt->bindParam(':emp_nome_razao', 	$emp_nome_razao);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
		{
			echo "
				<SCRIPT language='JavaScript'>
					abreMask(
					'<img src=../imagens/x.png> CNPJ ou Razão Social já existente.<br>Por favor verifique os dados.<br><br>'+
					'<input value=\' Ok \' type=\'button\' class=\'close_janela\' onclick=history.back();>' );
				</SCRIPT>
				";
				exit;
		}
        $sql = "INSERT INTO cadastro_empresas SET ".bindFields($dados);
		$stmt = $PDO->prepare($sql);	
        if($stmt->execute($dados))
        {		
			require_once '../mod_includes/php/lib/WideImage.php';
			$emp_id = $PDO->lastInsertId();
			
			//UPLOAD ARQUIVOS
			$caminho = "../admin/empresa_logos/$emp_id/";
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
						$emp_logo	= $caminho;
						$emp_logo .= "logo_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($emp_logo));
						$imnfo = getimagesize($emp_logo);
						$img_w = $imnfo[0];	  // largura
						$img_h = $imnfo[1];	  // altura
						if($img_w > 500 || $img_h > 500)
						{
							$image = WideImage::load($emp_logo);
							$image = $image->resize(500, 500);
							$image->saveToFile($emp_logo);
						}
						$sql = "UPDATE cadastro_empresas SET 
								emp_logo 	 = :emp_logo
								WHERE emp_id = :emp_id ";
						$stmt = $PDO->prepare($sql);
						$stmt->bindParam(':emp_logo',$emp_logo);
						$stmt->bindParam(':emp_id',$emp_id);
						if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
					}					
				}
			}
			//
			
			//DEPATAMENTO - CAMPOS DINÂMICOS
			if(!empty($_POST['departamentos']) && is_array($_POST['departamentos']))
			{
				//LIMPA ARRAY
				foreach($_POST['departamentos'] as $item => $valor) 
				{
					$departamentos_filtrado[$item] = array_filter($valor);
				}
				//
				
				foreach($departamentos_filtrado as $item => $valor) 
				{
					if(!empty($valor))
					{
						$valor['dep_empresa'] = $emp_id;
						$sql = "INSERT INTO cadastro_empresas_departamentos SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//INSERE
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
				}
			}
			
			//CONTATO - CAMPOS DINÂMICOS			
			if(!empty($_POST['contatos']) && is_array($_POST['contatos']))
			{
				//LIMPA ARRAY
				foreach($_POST['contatos'] as $item => $valor) 
				{
					$contatos_filtrado[$item] = array_filter($valor);
				}
				//
				foreach($contatos_filtrado as $item => $valor) 
				{		
					if(!empty($valor))
					{				
						//INVERTE DATA
						if(isset($valor['ctt_data_nasc']))
						{
							$data_nova = implode("-",array_reverse(explode("/",$valor['ctt_data_nasc'])));
							unset($valor['ctt_data_nasc']);
							$valor['ctt_data_nasc'] = $data_nova;
						}
						//
						
						//CRIPTOGRAFA SENHA
						if($valor['ctt_senha'] != '')
						{
							$senha_nova = md5($valor['ctt_senha']);
							unset($valor['ctt_senha']);
							$valor['ctt_senha'] = $senha_nova;						
						}
						//
						
						$valor['ctt_empresa'] = $emp_id;
						$sql = "INSERT INTO cadastro_empresas_contatos SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//INSERE
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
				}
			}
			
			//CONTRATO - CAMPOS DINÂMICOS			
			if(!empty($_POST['contratos']) && is_array($_POST['contratos']))
			{
				//LIMPA ARRAY
				foreach($_POST['contratos'] as $item => $valor) 
				{
					$contratos_filtrado[$item] = array_filter($valor);
				}
				//
				foreach($contratos_filtrado as $item => $valor) 
				{		
					if(!empty($valor))
					{				
						//INVERTE DATA
						if(isset($valor['con_data']))
						{
							$data_nova = implode("-",array_reverse(explode("/",$valor['con_data'])));
							unset($valor['con_data']);
							$valor['con_data'] = $data_nova;
						}
						//
						
						//INVERTE MOEDA
						if(isset($valor['con_valor']))
						{
							$valor_novo = str_replace(",",".",str_replace(".","",$valor['con_valor']));
							unset($valor['con_valor']);
							$valor['con_valor'] = $valor_novo;
						}
						//
						
						
						
						$valor['con_empresa'] = $emp_id;
						$sql = "INSERT INTO cadastro_empresas_contratos SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//INSERE
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
				}
			}
			
			//FORMACAO - CAMPOS DINÂMICOS			
			if(!empty($_POST['formacao']) && is_array($_POST['formacao']))
			{
				//LIMPA ARRAY
				foreach($_POST['formacao'] as $item => $valor) 
				{
					$formacao_filtrado[$item] = array_filter($valor);
				}
				//
				foreach($formacao_filtrado as $item => $valor) 
				{		
					if(!empty($valor))
					{				
						//INVERTE DATA
						if(isset($valor['for_data_vcto']))
						{
							$data_nova = implode("-",array_reverse(explode("/",$valor['for_data_vcto'])));
							unset($valor['for_data_vcto']);
							$valor['for_data_vcto'] = $data_nova;
						}
						//
												
						
						
						
						$valor['for_empresa'] = $emp_id;
						$sql = "INSERT INTO cadastro_empresas_formacao SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//INSERE
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
				}
			}
			
            if(!$erro)
			{
				echo "
				<SCRIPT language='JavaScript'>
					abreMask(
					'<img src=../imagens/ok.png> Cadastro efetuado com sucesso.<br><br>'+
					'<input value=\' Ok \' type=\'button\' class=\'close_janela\'>' );
				</SCRIPT>
					";
			}
			else
			{
				echo "
				<SCRIPT language='JavaScript'>
					abreMask(
					'<img src=../imagens/x.png> Erro ao efetuar cadastro.<br><br>'+
					'<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>');
				</SCRIPT>
					"; 
			}
        }
        else
        {
            echo "
            <SCRIPT language='JavaScript'>
                abreMask(
                '<img src=../imagens/x.png> Erro ao efetuar cadastro.<br><br>'+
                '<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>');
            </SCRIPT>
                "; 
        }	
    }
    
    if($action == 'editar')
    {
		$sql = "UPDATE cadastro_empresas SET ".bindFields($dados)." WHERE emp_id = :emp_id ";
		$stmt = $PDO->prepare($sql); 
		$dados['emp_id'] =  $emp_id;
		if($stmt->execute($dados))
        {
			require_once '../mod_includes/php/lib/WideImage.php';
			//UPLOAD ARQUIVOS
			$caminho = "../admin/empresa_logos/$emp_id/";
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
						$emp_logo	= $caminho;
						$emp_logo .= "logo_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($emp_logo));
						$imnfo = getimagesize($emp_logo);
						$img_w = $imnfo[0];	  // largura
						$img_h = $imnfo[1];	  // altura
						if($img_w > 500 || $img_h > 500)
						{
							$image = WideImage::load($emp_logo);
							$image = $image->resize(500, 500);
							$image->saveToFile($emp_logo);
						}
						$sql = "UPDATE cadastro_empresas SET 
								emp_logo 	 = :emp_logo
								WHERE emp_id = :emp_id ";
						$stmt = $PDO->prepare($sql);
						$stmt->bindParam(':emp_logo',$emp_logo);
						$stmt->bindParam(':emp_id',$emp_id);
						if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
					}					
				}
			}
				
			
			//
			
			
			## CAMPOS DINÂMICOS ##
			
			// CONTATO - EXCLUI OS REMOVIDOS
			if(!empty($_POST['contatos']) && is_array($_POST['contatos']))
			{
				//LIMPA ARRAY
				foreach($_POST['contatos'] as $item => $valor) 
				{
					$contatos_filtrado[$item] = array_filter($valor);
				}
				//
				
				$a_excluir = array();
				foreach($contatos_filtrado as $item) 
				{
					if(isset($item['ctt_id']))
					{
						$a_excluir[] = $item['ctt_id'];
					}
				}
				if(!empty($a_excluir))
				{
					$sql = "DELETE FROM cadastro_empresas_contatos WHERE ctt_empresa = :emp_id AND ctt_id NOT IN (".implode(",",$a_excluir).") ";
					
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':emp_id', $emp_id);
					if($stmt->execute())
					{
						//echo "Excluido <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
				else
				{
					$sql = "DELETE FROM cadastro_empresas_contatos WHERE ctt_empresa = :emp_id ";
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':emp_id', $emp_id);
					if($stmt->execute())
					{
						//echo "Excluido todos <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
			}
			else
			{
				$sql = "DELETE FROM cadastro_empresas_contatos WHERE ctt_empresa = :emp_id ";
				$stmt = $PDO->prepare($sql); 
				$stmt->bindParam(':emp_id', $emp_id);
				if($stmt->execute())
				{
					//echo "Excluido todos <br>";
				}
				else{ $erro=1; $err = $stmt->errorInfo();}
			}
			
			// CONTATO - ATUALIZA OU INSERE NOVOS
			if(!empty($_POST['contatos']) && is_array($_POST['contatos']))
			{
				//LIMPA ARRAY
				foreach($_POST['contatos'] as $item => $valor) 
				{
					$contatos_filtrado[$item] = array_filter($valor);
				}
				//
				foreach(array_filter($contatos_filtrado) as $item => $valor) 
				{
					if(isset($valor['ctt_id']))
					{
						//INVERTE DATA
						if(isset($valor['ctt_data_nasc']))
						{
							$data_nova = implode("-",array_reverse(explode("/",$valor['ctt_data_nasc'])));
							unset($valor['ctt_data_nasc']);
							$valor['ctt_data_nasc'] = $data_nova;
						}
						//
						
						//CRIPTOGRAFA SENHA
						$sql = "SELECT * FROM cadastro_empresas_contatos WHERE ctt_id = :ctt_id ";
						$stmt = $PDO->prepare($sql);
						$stmt->bindParam(':ctt_id',$valor['ctt_id']);
						$stmt->execute();
						$row = $stmt->rowCount();
						if($row > 0)
						{
							$senhacompara = $stmt->fetch(PDO::FETCH_OBJ)->ctt_senha;	
						}
						if($valor['ctt_senha'] == $senhacompara)
						{
														
						}
						else
						{
							$senha_nova = md5($valor['ctt_senha']);
							unset($valor['ctt_senha']);
							$valor['ctt_senha'] = $senha_nova;							
						}
						//
						
						$valor2 = $valor;
						unset($valor2['ctt_id']);
						
						$sql = "UPDATE cadastro_empresas_contatos SET ".bindFields($valor2)." WHERE ctt_id = :ctt_id";
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//echo "Atualizado <br>";
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
					else
					{
						//INVERTE DATA
						if(isset($valor['ctt_data_nasc']))
						{
							$data_nova = implode("-",array_reverse(explode("/",$valor['ctt_data_nasc'])));
							unset($valor['ctt_data_nasc']);
							$valor['ctt_data_nasc'] = $data_nova;
						}
						//
						
						//CRIPTOGRAFA SENHA
						if(isset($valor['ctt_senha']))
						{
							$senha_nova = md5($valor['ctt_senha']);
							unset($valor['ctt_senha']);
							$valor['ctt_senha'] = $senha_nova;						
						}
						//
						
						$valor['ctt_empresa'] = $emp_id;
						$sql = "INSERT INTO cadastro_empresas_contatos SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//echo "Inserido <br>";
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
				}
			}
			
			// DEPARTAMENTOS - EXCLUI OS REMOVIDOS
			if(!empty($_POST['departamentos']) && is_array($_POST['departamentos']))
			{
				//LIMPA ARRAY
				foreach($_POST['departamentos'] as $item => $valor) 
				{
					$departamentos_filtrado[$item] = array_filter($valor);
				}
				//
				$a_excluir = array();
				foreach($departamentos_filtrado as $item) 
				{
					if(isset($item['dep_id']))
					{
						$a_excluir[] = $item['dep_id'];
					}
				}
				if(!empty($a_excluir))
				{
					$sql = "DELETE FROM cadastro_empresas_departamentos WHERE dep_empresa = :emp_id AND dep_id NOT IN (".implode(",",$a_excluir).") ";
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':emp_id', $emp_id);
					if($stmt->execute())
					{
						//echo "Excluido <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
				else
				{
					$sql = "DELETE FROM cadastro_empresas_departamentos WHERE dep_empresa = :emp_id ";
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':emp_id', $emp_id);
					if($stmt->execute())
					{
						//echo "Excluido todos <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
			}
			else
			{
				$sql = "DELETE FROM cadastro_empresas_departamentos WHERE dep_empresa = :emp_id ";
				$stmt = $PDO->prepare($sql); 
				$stmt->bindParam(':emp_id', $emp_id);
				if($stmt->execute())
				{
					//echo "Excluido todos <br>";
				}
				else{ $erro=1; $err = $stmt->errorInfo();}
			}
			// DEPARTAMENTOS - ATUALIZA OU INSERE NOVOS
			if(!empty($_POST['departamentos']) && is_array($_POST['departamentos']))
			{
				//LIMPA ARRAY
				foreach($_POST['departamentos'] as $item => $valor) 
				{
					$departamentos_filtrado[$item] = array_filter($valor);
				}
				//
				
				foreach(array_filter($departamentos_filtrado) as $item => $valor) 
				{					
					if(isset($valor['dep_id']))
					{
						$valor2 = $valor;
						unset($valor2['dep_id']);
						
						$sql = "UPDATE cadastro_empresas_departamentos SET ".bindFields($valor2)." WHERE dep_id = :dep_id";
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//echo "Atualizado <br>";
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
					else
					{
						$valor['dep_empresa'] = $emp_id;
						$sql = "INSERT INTO cadastro_empresas_departamentos SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//echo "Inserido <br>";
						}
						else{ $erro=1; $err = $stmt->errorInfo();}						
					}
				}
			}
			
			
			// CONTATO - EXCLUI OS REMOVIDOS
			if(!empty($_POST['contratos']) && is_array($_POST['contratos']))
			{
				//LIMPA ARRAY
				foreach($_POST['contratos'] as $item => $valor) 
				{
					$contratos_filtrado[$item] = array_filter($valor);
				}
				//
				
				$a_excluir = array();
				foreach($contratos_filtrado as $item) 
				{
					if(isset($item['con_id']))
					{
						$a_excluir[] = $item['con_id'];
					}
				}
				if(!empty($a_excluir))
				{
					$sql = "DELETE FROM cadastro_empresas_contratos WHERE con_empresa = :emp_id AND con_id NOT IN (".implode(",",$a_excluir).") ";
					
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':emp_id', $emp_id);
					if($stmt->execute())
					{
						//echo "Excluido <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
				else
				{
					$sql = "DELETE FROM cadastro_empresas_contratos WHERE con_empresa = :emp_id ";
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':emp_id', $emp_id);
					if($stmt->execute())
					{
						//echo "Excluido todos <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
			}
			else
			{
				$sql = "DELETE FROM cadastro_empresas_contratos WHERE con_empresa = :emp_id ";
				$stmt = $PDO->prepare($sql); 
				$stmt->bindParam(':emp_id', $emp_id);
				if($stmt->execute())
				{
					//echo "Excluido todos <br>";
				}
				else{ $erro=1; $err = $stmt->errorInfo();}
			}
			
			// CONTRATO - ATUALIZA OU INSERE NOVOS
			if(!empty($_POST['contratos']) && is_array($_POST['contratos']))
			{
				//LIMPA ARRAY
				foreach($_POST['contratos'] as $item => $valor) 
				{
					$contratos_filtrado[$item] = array_filter($valor);
				}
				//
				foreach(array_filter($contratos_filtrado) as $item => $valor) 
				{
					if(isset($valor['con_id']))
					{
						//INVERTE DATA
						if(isset($valor['con_data']))
						{
							$data_nova = implode("-",array_reverse(explode("/",$valor['con_data'])));
							unset($valor['con_data']);
							$valor['con_data'] = $data_nova;
						}
						//
						
						//INVERTE MOEDA
						if(isset($valor['con_valor']))
						{
							$valor_novo = str_replace(",",".",str_replace(".","",$valor['con_valor']));
							unset($valor['con_valor']);
							$valor['con_valor'] = $valor_novo;
						}
						//
						
						$valor2 = $valor;
						unset($valor2['con_id']);
						
						$sql = "UPDATE cadastro_empresas_contratos SET ".bindFields($valor2)." WHERE con_id = :con_id";
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//echo "Atualizado <br>";
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
					else
					{
						//INVERTE DATA
						if(isset($valor['con_data']))
						{
							$data_nova = implode("-",array_reverse(explode("/",$valor['con_data'])));
							unset($valor['con_data']);
							$valor['con_data'] = $data_nova;
						}
						//
						
						//INVERTE MOEDA
						if(isset($valor['con_valor']))
						{
							$valor_novo = str_replace(",",".",str_replace(".","",$valor['con_valor']));
							unset($valor['con_valor']);
							$valor['con_valor'] = $valor_novo;
						}
						//
						
						$valor['con_empresa'] = $emp_id;
						$sql = "INSERT INTO cadastro_empresas_contratos SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//echo "Inserido <br>";
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
				}
			}

			// FORMACAO - EXCLUI OS REMOVIDOS
			if(!empty($_POST['formacao']) && is_array($_POST['formacao']))
			{
				//LIMPA ARRAY
				foreach($_POST['formacao'] as $item => $valor) 
				{
					$formacao_filtrado[$item] = array_filter($valor);
				}
				//
				
				$a_excluir = array();
				foreach($formacao_filtrado as $item) 
				{
					if(isset($item['for_id']))
					{
						$a_excluir[] = $item['for_id'];
					}
				}
				if(!empty($a_excluir))
				{
					$sql = "DELETE FROM cadastro_empresas_formacao WHERE for_empresa = :emp_id AND for_id NOT IN (".implode(",",$a_excluir).") ";
					
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':emp_id', $emp_id);
					if($stmt->execute())
					{
						//echo "Excluido <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
				else
				{
					$sql = "DELETE FROM cadastro_empresas_formacao WHERE for_empresa = :emp_id ";
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':emp_id', $emp_id);
					if($stmt->execute())
					{
						//echo "Excluido todos <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
			}
			else
			{
				$sql = "DELETE FROM cadastro_empresas_formacao WHERE for_empresa = :emp_id ";
				$stmt = $PDO->prepare($sql); 
				$stmt->bindParam(':emp_id', $emp_id);
				if($stmt->execute())
				{
					//echo "Excluido todos <br>";
				}
				else{ $erro=1; $err = $stmt->errorInfo();}
			}
			
			// FORMACAO - ATUALIZA OU INSERE NOVOS
			if(!empty($_POST['formacao']) && is_array($_POST['formacao']))
			{
				//LIMPA ARRAY
				foreach($_POST['formacao'] as $item => $valor) 
				{
					$formacao_filtrado[$item] = array_filter($valor);
				}
				//
				foreach(array_filter($formacao_filtrado) as $item => $valor) 
				{
					if(isset($valor['for_id']))
					{
						//INVERTE DATA
						if(isset($valor['for_data_vcto']))
						{
							$data_nova = implode("-",array_reverse(explode("/",$valor['for_data_vcto'])));
							unset($valor['for_data_vcto']);
							$valor['for_data_vcto'] = $data_nova;
						}
						//
						
						$valor2 = $valor;
						unset($valor2['for_id']);		
						
						$sql = "UPDATE cadastro_empresas_formacao SET ".bindFields($valor2)." WHERE for_id = :for_id";
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//echo "Atualizado <br>";
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
					else
					{
						//INVERTE DATA
						if(isset($valor['for_data_vcto']))
						{
							$data_nova = implode("-",array_reverse(explode("/",$valor['for_data_vcto'])));
							unset($valor['for_data_vcto']);
							$valor['for_data_vcto'] = $data_nova;
						}
						//

						$valor['for_empresa'] = $emp_id;
						$sql = "INSERT INTO cadastro_empresas_formacao SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//echo "Inserido <br>";
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
				}
			}     
		}
		else{ $erro=1; $err = $stmt->errorInfo();}
		
        if(!$erro)
		{
			echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../imagens/ok.png> Dados alterados com sucesso.<br><br>'+
				'<input value=\' Ok \' type=\'button\' class=\'close_janela\'>' );
			</SCRIPT>
				";
		}
		else
		{
			echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../imagens/x.png> Erro ao alterar dados.<br>Por favor verifique a exclusão de algum registro relacionado a outra tabela.<br><br>'+
				'<input value=\' Ok \' type=\'button\' class=\'close_janela\'>' );
			</SCRIPT>
			";
		}
    }
    
    if($action == 'excluir')
    {
		unset($_SESSION['action']);
       	$sql = "DELETE FROM cadastro_empresas WHERE emp_id = :emp_id";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':emp_id',$emp_id);
        if($stmt->execute())
        {
            echo "
            <SCRIPT language='JavaScript'>
                abreMask(
                '<img src=../imagens/ok.png> Exclusão realizada com sucesso<br><br>'+
                '<input value=\' OK \' type=\'button\' class=\'close_janela\'>' );
            </SCRIPT>
                ";
        }
        else
        {
            echo "
            <SCRIPT language='JavaScript'>
                abreMask(
                '<img src=../imagens/x.png> Erro ao realizar exclusão.<br>Por favor verifique a exclusão de algum registro relacionado a outra tabela.<br><br>'+
                '<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back(); >');
            </SCRIPT>
            ";
        }
    }
	if($action == 'ativar')
    {
        $sql = "UPDATE cadastro_empresas SET emp_status = :emp_status WHERE emp_id = :emp_id ";
        $stmt = $PDO->prepare($sql);
		$stmt->bindValue(':emp_status',1);
        $stmt->bindParam(':emp_id',$emp_id);
        $stmt->execute();
    }
    if($action == 'desativar')
    {
       	$sql = "UPDATE cadastro_empresas SET emp_status = :emp_status WHERE emp_id = :emp_id ";
        $stmt = $PDO->prepare($sql);
		$stmt->bindValue(':emp_status',0);
        $stmt->bindParam(':emp_id',$emp_id);
        $stmt->execute();
    }
	
	if($action == 'cliente')
	{
		$sql = "UPDATE cadastro_empresas SET emp_cliente = :emp_cliente WHERE emp_id = :emp_id ";
		$stmt = $PDO->prepare($sql);
		$stmt->bindValue(':emp_cliente',1);
        $stmt->bindParam(':emp_id',$emp_id);
        $stmt->execute();
	}
	if($action == 'empresa')
	{
		$sql = "UPDATE cadastro_empresas SET emp_cliente = :emp_cliente WHERE emp_id = :emp_id ";
		$stmt = $PDO->prepare($sql);
		
		$stmt->bindValue(':emp_cliente',null);
        $stmt->bindParam(':emp_id',$emp_id);
        $stmt->execute();
	}
    
	
    $num_por_pagina = 20;
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
		$nome_query = " (emp_nome_razao LIKE :fil_nome1 OR emp_fantasia LIKE :fil_nome2 ) ";
	}
    $sql = "SELECT * FROM cadastro_empresas 
			WHERE ".$nome_query."
			ORDER BY emp_id DESC
            LIMIT :primeiro_registro, :num_por_pagina ";
  	$stmt = $PDO->prepare($sql);
	$stmt->bindParam(':fil_nome1', 	$fil_nome1);
	$stmt->bindParam(':fil_nome2', 	$fil_nome2);
	$stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
	$stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
	$stmt->execute();
	$rows = $stmt->rowCount();
	
    if($pagina == "cadastro_empresas")
    {
        echo "
		<div class='titulo'> $page  </div>
		<div id='botoes'><input value='Nova Empresa' type='button' onclick=javascript:window.location.href='cadastro_empresas.php?pagina=cadastro_empresas_adicionar".$autenticacao."'; /></div>
		<div class='filtro'>
			<form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_empresas.php?pagina=cadastro_empresas".$autenticacao."'>
			<input name='fil_nome' id='fil_nome' value='$fil_nome' placeholder='Nome'>
			<input type='submit' value='Filtrar'> 
			</form>
		</div>
		";
		if ($rows > 0)
		{
			echo "
			<table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
				<tr>
					<td class='titulo_first' align='center' width='1'>Empresa</td>
					<td class='titulo_tabela'></td>
					<td class='titulo_tabela' align='center'>Cliente</td>
					<td class='titulo_tabela' align='center'>Status</td>
					<td class='titulo_last' align='center'>Gerenciar</td>
				</tr>";
				$c=0;
				 while($result = $stmt->fetch())
				{
					$emp_id 			= $result['emp_id'];
					$emp_nome_razao 	= $result['emp_nome_razao'];
					$emp_fantasia 		= $result['emp_fantasia'];
					$emp_tipo 			= $result['emp_tipo'];
					$emp_status 		= $result['emp_status'];
					$emp_cliente		= $result['emp_cliente'];
					$emp_logo 			= $result['emp_logo'];
					if($emp_logo == '')
					{
						$emp_logo = '../imagens/nophoto.png';
					}
					if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;}  
					echo "
					<script type='text/javascript'>
						jQuery(document).ready(function($) {
					
							// Define any icon actions before calling the toolbar
							$('.toolbar-icons a').on('click', function( event ) {
								$(this).click();
								
							});
							$('#normal-button-$emp_id').toolbar({content: '#user-options-$emp_id', position: 'top', hideOnClick: true});
							$('#normal-button-bottom').toolbar({content: '#user-options', position: 'bottom'});
							$('#normal-button-small').toolbar({content: '#user-options-small', position: 'top', hideOnClick: true});
							$('#button-left').toolbar({content: '#user-options', position: 'left'});
							$('#button-right').toolbar({content: '#user-options', position: 'right'});
							$('#link-toolbar').toolbar({content: '#user-options', position: 'top' });
						});
					</script>
					<div id='user-options-$emp_id' class='toolbar-icons' style='display: none;'>
						";
						if($emp_cliente == 1)
						{
							echo "
							<a title='Tornar cliente -> empresa' onclick=\"
								abreMask(
									'Deseja realmente tornar o cliente <b>$emp_nome_razao</b> uma empresa?<br><br>'+
									'<input value=\' Sim \' type=\'button\' onclick=javascript:window.location.href=\'cadastro_empresas.php?pagina=cadastro_empresas&action=empresa&emp_id=$emp_id$autenticacao\';>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
									'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
								\">
								<img border='0' src='../imagens/icon-cliente.png'></i>
							</a>
							";
						}
						else
						{
							echo "
							<a title='Tornar empresa -> cliente' onclick=\"
								abreMask(
									'Deseja realmente tornar a empresa <b>$emp_nome_razao</b> um cliente?<br><br>'+
									'<input value=\' Sim \' type=\'button\' onclick=javascript:window.location.href=\'cadastro_empresas.php?pagina=cadastro_empresas&action=cliente&emp_id=$emp_id$autenticacao\';>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
									'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
								\">
								<img border='0' src='../imagens/icon-cliente.png'></i>
							</a>
							";
						}
						
						if($emp_status == 1)
						{
							echo "<a title='Desativar' href='cadastro_empresas.php?pagina=cadastro_empresas&action=desativar&emp_id=$emp_id$autenticacao'><img border='0' src='../imagens/icon-ativa-desativa.png'></a>";
						}
						else
						{
							echo "<a title='Ativar' href='cadastro_empresas.php?pagina=cadastro_empresas&action=ativar&emp_id=$emp_id$autenticacao'><img border='0' src='../imagens/icon-ativa-desativa.png' ></a>";
						}
						echo "
						<a title='Editar' href='cadastro_empresas.php?pagina=cadastro_empresas_editar&emp_id=$emp_id$autenticacao'><img border='0' src='../imagens/icon-editar.png' ></a>
						<a title='Excluir' onclick=\"
							abreMask(
								'Deseja realmente excluir a empresa <b>$emp_nome_razao</b>?<br><br>'+
								'<input value=\' Sim \' type=\'button\' onclick=javascript:window.location.href=\'cadastro_empresas.php?pagina=cadastro_empresas&action=excluir&emp_id=$emp_id$autenticacao\';>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
								'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
							\">
							<img border='0' src='../imagens/icon-excluir.png' ></i>
						</a>
					</div>
					";
					echo "<tr class='$c1'>
							  <td><div style='background:url($emp_logo); height: 50px; width:50px; background-size:100%; object-fit:cover;  border-radius:100px;'></div>
							  <td>";
								  if($emp_tipo == 'PJ')
								  {
									  echo "$emp_fantasia<br><span class='detalhe'>$emp_nome_razao</span>";
								  }
								  else
								  {
									  if($emp_fantasia != '')
									  {
										echo "$emp_fantasia<br><span class='detalhe'>$emp_nome_razao</span>";
									  }
									  else
									  {
										echo "$emp_nome_razao";
									  }
								  } 
								  echo "
								  </td>
								  <td align=center>";
								  if($emp_cliente == 1)
								  {
									echo "<img border='0' src='../imagens/icon-ativo.png' width='15' height='15'>";
								  }
								  else
								  {
									echo "<img border='0' src='../imagens/icon-inativo.png' width='15' height='15'>";
								  }
								  echo "
								  </td>
								  <td align=center>";
								  if($emp_status == 1)
								  {
									echo "<img border='0' src='../imagens/icon-ativo.png' width='15' height='15'>";
								  }
								  else
								  {
									echo "<img border='0' src='../imagens/icon-inativo.png' width='15' height='15'>";
								  }
								  echo "
								  </td>
							  <td align=center><div id='normal-button-$emp_id' class='settings-button'><img src='../imagens/icon-cog-small.png' /></div></td>
						  </tr>";
				}
				echo "</table>";
				$variavel = "&pagina=cadastro_empresas&fil_nome=$fil_nome".$autenticacao."";
				$cnt = "SELECT COUNT(*) FROM cadastro_empresas WHERE ".$nome_query." ";
				$stmt = $PDO->prepare($cnt);
				$stmt->bindParam(':fil_nome1', 	$fil_nome1);
				$stmt->bindParam(':fil_nome2', 	$fil_nome2);
				include("../mod_includes/php/paginacao.php");
		}
		else
		{
			echo "<br><br><br>Não há nenhuma empresa cadastrada.";
		}
    }
    if($pagina == 'cadastro_empresas_adicionar')
    {
        echo "	
        <form name='form_cadastro_empresas' id='form_cadastro_empresas' enctype='multipart/form-data' method='post' action='cadastro_empresas.php?pagina=cadastro_empresas&action=adicionar&id=$id$autenticacao'>
            <div class='titulo'> $page &raquo; Adicionar  </div>
			<ul class='nav nav-tabs'>
			  <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
			  <li><a data-toggle='tab' 					href='#endereco'>Endereço</a></li>
			  <li><a data-toggle='tab' 					href='#departamentos'>Departamentos</a></li>
			  <li><a data-toggle='tab' 					href='#contatos'>Contatos</a></li>
			  <li><a data-toggle='tab' 					href='#emp_logo'>Logo</a></li>
			  <li><a data-toggle='tab' 					href='#contratos'>Contratos</a></li>
			  <li><a data-toggle='tab' 					href='#certidoes'>Inscrição Conselho de Classe</a></li>
			  <!--<li><a data-toggle='tab' 					href='#prospeccoes'>Prospecções</a></li>-->
			</ul>
			<div class='tab-content'>
				<div id='dados_gerais' class='tab-pane fade in active'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
								<br><label>Tipo:</label> <select name='emp_tipo' id='emp_tipo'>
									<option value=''>Tipo</option>
									<option value='PJ'>Pessoa Jurídica</option>
									<option value='PF'>Pessoa Física</option>
								</select> &nbsp;<input name='emp_cnpj' id='emp_cnpj' placeholder='C.N.P.J'  maxlength='18' onkeypress='mascaraCNPJ(this); return SomenteNumero(event);' style='display:none;'>
								<input name='emp_cpf' id='emp_cpf' placeholder='C.P.F'  maxlength='14' onkeypress='mascaraCPF(this); return SomenteNumero(event);'  style='display:none;'>
								<div id='emp_cpf_cnpj_erro' class='right'>&nbsp;</div>
								<p><label>Nome/Razão Social:</label> <input name='emp_nome_razao' id='emp_nome_razao' placeholder='Nome/Razão Social'>
								<p><label>Nome Fantasia:</label> <input name='emp_fantasia' id='emp_fantasia' placeholder='Nome Fantasia'>
								<p><label>Sigla:</label> <input name='emp_sigla' id='emp_sigla' placeholder='Sigla'>
								<p><label>Data Fundação:</label> <input name='emp_data_fundacao' id='emp_data_fundacao' placeholder='Data Fundação' onkeypress='return mascaraData(this,event);' />
								<p><label>Categoria Administrativa:</label> <select name='emp_categ_adm' id='emp_categ_adm'>
									<option value=''>Categoria Administrativa</option>
									<option value='Pública'>Pública</option>
									<option value='Privada'>Privada</option>
								</select>
								<div id='publico'>
									<label>Esfera:</label> <select name='emp_esfera' id='emp_esfera'>
										<option value=''>Esfera</option>
										<option value='Municipal'>Municipal</option>
										<option value='Estadual'>Estadual</option>
										<option value='Federal'>Federal</option>
									</select>
									<p><label>Poder:</label> <select name='emp_poder' id='emp_poder'>
										<option value=''>Poder</option>
										<option value='Executivo'>Executivo</option>
										<option value='Legislativo'>Legislativo</option>
										<option value='Judiciário'>Judiciário</option>
										<option value='Ministério Público'>Ministério Público</option>
									</select>
									<p><label>Classificação do Órgão:</label> <select name='emp_orgao' id='emp_orgao'>
										<option value=''>Classificação do Órgão</option>
										<option value='Prefeitura'>Prefeitura</option>
										<option value='Câmara'>Câmara</option>
										<option value='Autarquia'>Autarquia</option>
										<option value='Fundação'>Fundação</option>
										<option value='Empresa Pública'>Empresa Pública</option>
										<option value='Sociedade de Economia Mista'>Sociedade de Economia Mista</option>
										<option value='Instituto de Previdência'>Instituto de Previdência</option>
										<option value='Consórcio'>Consórcio</option>
										<option value='Agência Reguladora'>Agência Reguladora</option>
										<option value='Conselho Profissional'>Conselho Profissional</option>
										<option value='Sindicato'>Sindicato</option>
										<option value='Entidade de Classe'>Entidade de Classe</option>
									</select>
								</div>
								<p><label>Telefone:</label> <input name='emp_telefone' id='emp_telefone' placeholder='Telefone (c/ DDD)' onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);' />
								<p><label>Fax:</label> 	<input name='emp_fax' id='emp_fax' placeholder='Fax (c/ DDD)' onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);' />	
								<p><label>Email:</label> <input name='emp_email' id='emp_email' placeholder='Email'>
								<p><label>Site:</label> <input name='emp_site' id='emp_site' placeholder='Site'>
								<p><label>Status:</label> <input type='radio' name='emp_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type='radio' name='emp_status' value='0'> Inativo<br>
							</td>
						</tr>
					</table>
				</div>
				<div id='endereco' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
								<br><label>CEP:</label> <input name='emp_cep' id='emp_cep' placeholder='CEP' maxlength='9' onkeypress='mascaraCEP(this); return SomenteNumero(event);' />
								<p><label>UF:</label> <select name='emp_uf' id='emp_uf'>
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
								<p><label>Município:</label> <select name='emp_municipio' id='emp_municipio'>
									<option value=''>Município</option>
								</select>
								<p><label>Bairro:</label> <input name='emp_bairro' id='emp_bairro' placeholder='Bairro' />
								<p><label>Endereço:</label> <input name='emp_endereco' id='emp_endereco' placeholder='Endereço' />
								<p><label>Número:</label> <input name='emp_numero' id='emp_numero' placeholder='Número' />
								   <label>Complemento:</label> <input name='emp_comp' id='emp_comp' placeholder='Complemento' />
							</td>
						</tr>
					</table>
				</div>
				<div id='departamentos' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
								<div id='p_scents_departamento'>
								<div class='bloco_departamento'>
																		<input type='hidden' name='departamentos[1][dep_id]' id='dep_id'>
									<br><label>Departamento:</label>	<input name='departamentos[1][dep_descricao]' id='dep_descricao' placeholder='Departamento'>
									<p><label>CEP:</label> 				<input name='departamentos[1][dep_cep]' id='dep_cep' class='dep_cep' placeholder='CEP' maxlength='9' onkeypress='mascaraCEP(this); return SomenteNumero(event);' />
									<p><label>UF:</label> 				<select name='departamentos[1][dep_uf]' id='dep_uf' class='dep_uf'>
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
									<p><label>Município:</label> 		<select name='departamentos[1][dep_municipio]' id='dep_municipio' class='dep_municipio'>
																			<option value=''>Município</option>
																		</select>
									<p><label>Bairro:</label> 			<input name='departamentos[1][dep_bairro]' id='dep_bairro' class='dep_bairro' placeholder='Bairro' />
									<p><label>Endereço:</label> 		<input name='departamentos[1][dep_endereco]' id='dep_endereco' class='dep_endereco' placeholder='Endereço' />
									<p><label>Número:</label> 			<input name='departamentos[1][dep_numero]' id='dep_numero' class='dep_numero' placeholder='Número' />
									   <label>Complemento:</label> 		<input name='departamentos[1][dep_comp]' id='dep_comp' placeholder='Complemento' />
									<p><label>Telefone:</label>			<input name='departamentos[1][dep_telefone]' id='dep_telefone' placeholder='Telefone (c/ DDD)' onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);'>
									<p><label>Email:</label>			<input name='departamentos[1][dep_email]' id='dep_email' placeholder='Email'>
									<p><img src='../imagens/icon-add.png' id='addDepartamento' title='Adicionar +' class='botao_dinamico'>
								</div>
							</div>
							</td>
						</tr>
					</table>
				</div>
				<div id='contatos' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
								<div id='p_scents_contato'>
								<div class='bloco_contato'>
									<input type='hidden' name='contatos[1][ctt_id]' id='ctt_id'>
									<br><label>Departamento:</label> 
										<select name='contatos[1][ctt_departamento]' id='ctt_departamento' >
										<option value=''>Departamento</option>
										"; 
										$sql = " SELECT * FROM cadastro_empresas_departamentos 
												 LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cadastro_empresas_departamentos.dep_empresa
												 WHERE emp_id = :emp_id
												 ORDER BY dep_descricao  ";
										$stmt = $PDO->prepare($sql);
										$stmt->bindParam(':emp_id',$emp_id);
										$stmt->execute();
										while($result = $stmt->fetch())
										{
											echo "<option value='".$result['dep_id']."'>".$result['dep_descricao']."</option>";
										}
										echo "
									</select>
									<p><label>Cargo:</label><input 				name='contatos[1][ctt_cargo]' id='ctt_cargo' placeholder='Cargo'>
									<p><label>Nome do Contato:</label><input 	name='contatos[1][ctt_nome]' id='ctt_nome' placeholder='Nome do Contato'>
									<p><label>Email:</label><input 				name='contatos[1][ctt_email]' id='ctt_email' placeholder='Email'>
									<p><label>Telefone:</label><input 			name='contatos[1][ctt_telefone]' id='ctt_telefone' placeholder='Telefone (c/ DDD)' onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);'>
									<label>Ramal:</label><input 				name='contatos[1][ctt_ramal]' id='ctt_ramal' placeholder='Ramal'>
									<p><label>Celular:</label><input 			name='contatos[1][ctt_celular]' id='ctt_celular' placeholder='Celular (c/ DDD)'onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);'>
									<p><label>Data Nascimento:</label><input 	name='contatos[1][ctt_data_nasc]' id='ctt_data_nasc' placeholder='Data Nascimento' onkeypress='return mascaraData(this,event);'>
									<p><label>Login:</label><input 				name='contatos[1][ctt_login]' id='ctt_login' placeholder='Login'>
									<p><label>Senha:</label><input 				name='contatos[1][ctt_senha]' id='ctt_senha' type='password' placeholder='Senha'>
									
									<p><img src='../imagens/icon-add.png' id='addContato' title='Adicionar +' class='botao_dinamico'></p>
								</div>
							</div>
							</td>
						</tr>
					</table>
				</div>
				<div id='emp_logo' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
									<br><label>Logo:</label> <input type='file' name='emp_logo[logo]' id='emp_logo'> 
									<p>
								</div>
							</div>
							</td>
						</tr>
					</table>
				</div>
				<div id='contratos' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
								<div id='p_scents_contrato'>
								<div class='bloco_contrato'>
									<input type='hidden' name='contratos[1][con_id]' id='con_id'>
									<br><label>Tipo:</label> <select name='contratos[1][con_tipo]' id='con_tipo'>
										<option value=''>Tipo</option>
										<option value='PJ'>Pessoa Jurídica</option>
										<option value='PF'>Pessoa Física</option>
									</select> &nbsp;<input name='contratos[1][con_cpf_cnpj]' id='con_cnpj' placeholder='C.N.P.J'  maxlength='18' onkeypress='mascaraCNPJ(this); return SomenteNumero(event);' style='display:none;'>
									&nbsp; <input name='contratos[1][con_cpf_cnpj]' id='con_cpf' placeholder='C.P.F'  maxlength='14' onkeypress='mascaraCPF(this); return SomenteNumero(event);'  style='display:none;'>
									<p><label>Objeto:</label>				<input name='contratos[1][con_objeto]' id='con_objeto' placeholder='Objeto'>
									<p><label>Nome/Razão Social:</label>	<input name='contratos[1][con_razao_social]' id='con_razao_social' placeholder='Nome/Razão Social'>
									<p><label>Data:</label>					<input name='contratos[1][con_data]' class='datepicker'  id='con_data' placeholder='Data' onkeypress='return mascaraData(this,event);'>
									<p><label>Valor:</label>				<input name='contratos[1][con_valor]' id='con_valor' placeholder='Valor' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
									<p><label>Modalidade:</label>			<input name='contratos[1][con_modalidade]' id='con_modalidade' placeholder='Modalidade'>
									
									<p><img src='../imagens/icon-add.png' id='addContrato' title='Adicionar +' class='botao_dinamico'></p>
								</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<div id='certidoes' class='tab-pane fade in'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
								<div id='p_scents_conselho'>
								<div class='bloco_conselho'>
									<input type='hidden' name='formacao[1][for_id]' id='for_id'>
									<p><label>Área:</label>				<input name='formacao[1][for_formacao]' id='for_formacao' placeholder='Área'>
									<p><label>Entidade:</label> <select name='formacao[1][for_entidade]' id='for_entidade'>
										<option value=''>Entidade</option>
										<option value='CRA'>CRA</option>
										<option value='OAB'>OAB</option>
										<option value='CAU'>CAU</option>
										<option value='CRESS'>CRESS</option>
										<option value='CRBIO'>CRBIO</option>
										<option value='CRBM'>CRBM</option>
										<option value='CRC'>CRC</option>
										<option value='CRECI'>CRECI</option>
										<option value='CORECON'>CORECON</option>
										<option value='CREF'>CREF</option>
										<option value='COREN'>COREN</option>
										<option value='CREA'>CREA</option>
										<option value='CRF'>CRF</option>
										<option value='CREFITO'>CREFITO</option>
										<option value='CRM'>CRM</option>
										<option value='CRMV'>CRMV</option>
										<option value='CRN'>CRN</option>
										<option value='CRO'>CRO</option>
										<option value='CRP'>CRP</option>
										<option value='CRQ'>CRQ</option>
										<option value='CORE'>CORE</option>									
									</select>
									<p><label>Inscrição:</label>				<input name='formacao[1][for_inscricao]' id='for_inscricao' placeholder='Inscrição'>
									<p><label>Data Vencimento: </label>					<input name='formacao[1][for_data_vcto]' class='datepicker'  id='for_data_vcto' placeholder='Data Vencimento' onkeypress='return mascaraData(this,event);'>
									<p><img src='../imagens/icon-add.png' id='addconselho' title='Adicionar +' class='botao_dinamico'></p>
								</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<!--<div id='prospeccoes' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
					<tr>
						<td align='left'>
					";
					$sql_icone = "SELECT * FROM cadastro_prospeccoes ORDER BY pro_nome";
					$stmt_icone = $PDO->prepare($sql_icone);
					//$stmt_icone->bindParam(':emp_id',$emp_id);
					$stmt_icone->execute();
					while($result_icone = $stmt_icone->fetch())
					{
						echo "<a href='cadastro_empresas_prospeccoes.php?pagina=cadastro_empresas_prospeccoes&pro_id=".$result_icone['pro_id']."$autenticacao'><div class='icone_pro'>".$result_icone['pro_icone']."<br>".$result_icone['pro_nome']."</div></a>";
					}
					echo "
						</td>
						</tr>
					</table>
				</div>-->
			</div>
			
			<center>
			<div id='erro' align='center'>&nbsp;</div>
			<input type='button' id='bt_cadastro_empresas_sair' value='Salvar e Sair' />&nbsp;&nbsp;&nbsp;&nbsp; 
			<input type='button' id='bt_cadastro_empresas' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
			<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_empresas.php?pagina=cadastro_empresas".$autenticacao."'; value='Cancelar'/></center>
			</center>
        </form>
        ";
    }
    
    if($pagina == 'cadastro_empresas_editar')
    {
		        $sql = "SELECT * FROM cadastro_empresas 
				LEFT JOIN end_uf ON end_uf.uf_id = cadastro_empresas.emp_uf
				LEFT JOIN end_municipios ON end_municipios.mun_id = cadastro_empresas.emp_municipio
				WHERE emp_id = :emp_id";
        $stmt = $PDO->prepare($sql);	
		$stmt->bindParam(':emp_id', $emp_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
        {
			$result = $stmt->fetch();
          	$emp_tipo 			= $result['emp_tipo'];
			$emp_cpf_cnpj 		= $result['emp_cpf_cnpj'];
			$emp_nome_razao 	= $result['emp_nome_razao'];
			$emp_fantasia 		= $result['emp_fantasia'];
			$emp_sigla 			= $result['emp_sigla'];
			$emp_data_fundacao 	= implode("/",array_reverse(explode("-",$result['emp_data_fundacao'])));
			$emp_categ_adm 		= $result['emp_categ_adm'];if($emp_categ_adm == ''){$emp_categ_adm_n = "Categoria Administrativa";}else{$emp_categ_adm_n = $emp_categ_adm;}
			$emp_esfera 		= $result['emp_esfera'];if($emp_esfera == ''){$emp_esfera_n = "Esfera";}else{$emp_esfera_n = $emp_esfera;}
			$emp_poder 			= $result['emp_poder'];if($emp_poder == ''){$emp_poder_n = "Poder";}else{$emp_poder_n = $emp_poder;}
			$emp_orgao 			= $result['emp_orgao'];if($emp_orgao == ''){$emp_orgao_n = "Classificação do Órgão";}else{$emp_orgao_n = $emp_orgao;}
			$emp_cep 			= $result['emp_cep'];
			$emp_uf 			= $result['emp_uf'];
			$uf_sigla 			= $result['uf_sigla'];
			$emp_municipio 		= $result['emp_municipio'];
			$mun_nome	 		= $result['mun_nome'];
			$emp_bairro 		= $result['emp_bairro'];
			$emp_endereco 		= $result['emp_endereco'];
			$emp_numero 		= $result['emp_numero'];
			$emp_comp 			= $result['emp_comp'];
			$emp_telefone 		= $result['emp_telefone'];
			$emp_fax 			= $result['emp_fax'];
			$emp_email 			= $result['emp_email'];
			$emp_site 			= $result['emp_site'];
			$emp_status 		= $result['emp_status'];
			$emp_logo 			= $result['emp_logo'];
			
			echo "
            <form name='form_cadastro_empresas' id='form_cadastro_empresas' enctype='multipart/form-data' method='post' action='cadastro_empresas.php?pagina=cadastro_empresas&action=editar&emp_id=$emp_id$autenticacao'>
                <div class='titulo'> $page &raquo; Editar </div>
				<ul class='nav nav-tabs'>
				  <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
				  <li><a data-toggle='tab' 					href='#endereco'>Endereço</a></li>
				  <li><a data-toggle='tab' 					href='#departamentos'>Departamentos</a></li>
				  <li><a data-toggle='tab' 					href='#contatos'>Contatos</a></li>
				  <li><a data-toggle='tab' 					href='#emp_logo'>Logo</a></li>
				  <li><a data-toggle='tab' 					href='#contratos'>Contratos</a></li>
				  <li><a data-toggle='tab' 					href='#prospeccoes'>Prospecções</a></li>
				  <li><a data-toggle='tab' 					href='#certidoes'>Inscrição Conselho de Classe</a></li>
				</ul>
				<div class='tab-content'>
					<div id='dados_gerais' class='tab-pane fade in active'>
						<table align='center' cellspacing='0' width='100%' class='borda_aba'>
							<tr>
								<td align='left'>
									<br><input type='hidden' name='emp_id' id='emp_id' value='$emp_id'>
									<label>Tipo:</label> <select name='emp_tipo' id='emp_tipo'>
										<option value='$emp_tipo'>$emp_tipo</option>
										<option value='PJ'>Pessoa Jurídica</option>
										<option value='PF'>Pessoa Física</option>
									</select> &nbsp;";
									if($emp_tipo == "PJ")
									{
										echo "<input name='emp_cnpj' id='emp_cnpj' value='$emp_cpf_cnpj' placeholder='C.N.P.J'  maxlength='18' onkeypress='mascaraCNPJ(this); return SomenteNumero(event);'>
											  <input name='emp_cpf' id='emp_cpf' value='' placeholder='C.P.F'  maxlength='14' onkeypress='mascaraCPF(this); return SomenteNumero(event);'  style='display:none;'>
											  ";
									}
									elseif($emp_tipo == "PF")
									{
										echo "<input name='emp_cnpj' id='emp_cnpj' value='' placeholder='C.N.P.J'  maxlength='18' onkeypress='mascaraCNPJ(this); return SomenteNumero(event);' style='display:none;'>
											  <input name='emp_cpf' id='emp_cpf' value='$emp_cpf_cnpj' placeholder='C.P.F'  maxlength='14' onkeypress='mascaraCPF(this); return SomenteNumero(event);'>
											  ";
									}
									echo "
									<div id='emp_cpf_cnpj_erro' class='right'>&nbsp;</div>
									<p><label>Nome/Razão Social:</label> <input name='emp_nome_razao' id='emp_nome_razao' value='$emp_nome_razao' placeholder='Nome/Razão Social'>
									<p><label>Nome Fantasia:</label> <input name='emp_fantasia' id='emp_fantasia' value='$emp_fantasia' placeholder='Nome Fantasia'>
									<p><label>Sigla:</label> <input name='emp_sigla' id='emp_sigla' value='$emp_sigla' placeholder='Sigla'>
									<p><label>Data Fundação:</label> <input name='emp_data_fundacao' id='emp_data_fundacao' value='$emp_data_fundacao' placeholder='Data Fundação' onkeypress='return mascaraData(this,event);' />
									<p><label>Categoria Administrativa:</label> <select name='emp_categ_adm' id='emp_categ_adm'>
										<option value='$emp_categ_adm'>$emp_categ_adm_n</option>
										<option value='Pública'>Pública</option>
										<option value='Privada'>Privada</option>
									</select>
									<div id='publico'";if($emp_categ_adm == "Pública"){echo " style='display:block;' ";} echo ">
										<label>Esfera:</label> <select name='emp_esfera' id='emp_esfera'>
											<option value='$emp_esfera'>$emp_esfera_n</option>
											<option value='Municipal'>Municipal</option>
											<option value='Estadual'>Estadual</option>
											<option value='Federal'>Federal</option>
											<option value=''></option>
										</select>
										<p><label>Poder:</label> <select name='emp_poder' id='emp_poder'>
											<option value='$emp_poder'>$emp_poder_n</option>
											<option value='Executivo'>Executivo</option>
											<option value='Legislativo'>Legislativo</option>
											<option value='Judiciário'>Judiciário</option>
											<option value='Ministério Público'>Ministério Público</option>
											<option value=''></option>
										</select>
										<p><label>Classificação do Órgão:</label> <select name='emp_orgao' id='emp_orgao'>
											<option value='$emp_orgao'>$emp_orgao_n</option>
											<option value='Prefeitura'>Prefeitura</option>
											<option value='Câmara'>Câmara</option>
											<option value='Autarquia'>Autarquia</option>
											<option value='Fundação'>Fundação</option>
											<option value='Empresa Pública'>Empresa Pública</option>
											<option value='Sociedade de Economia Mista'>Sociedade de Economia Mista</option>
											<option value='Instituto de Previdência'>Instituto de Previdência</option>
											<option value='Consórcio'>Consórcio</option>
											<option value='Agência Reguladora'>Agência Reguladora</option>
											<option value='Conselho Profissional'>Conselho Profissional</option>
											<option value='Sindicato'>Sindicato</option>
											<option value='Entidade de Classe'>Entidade de Classe</option>
											<option value=''></option>
										</select>
									</div>
									<p><label>Telefone:</label> <input name='emp_telefone' id='emp_telefone' value='$emp_telefone' placeholder='Telefone (c/ DDD)' onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);' />
									<p><label>Fax:</label> 	<input name='emp_fax' id='emp_fax' value='$emp_fax' placeholder='Fax (c/ DDD)' onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);' />	
									<p><label>Email:</label> <input name='emp_email' id='emp_email' value='$emp_email' placeholder='Email'>
									<p><label>Site:</label> <input name='emp_site' id='emp_site' value='$emp_site' placeholder='Site'>
									<p><label>Status:</label>";
									if($emp_status == 1)
									{
										echo "<input type='radio' name='emp_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											  <input type='radio' name='emp_status' value='0'> Inativo
											 ";
									}
									else
									{
										echo "<input type='radio' name='emp_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											  <input type='radio' name='emp_status' value='0' checked> Inativo
											 ";
									}
									echo "								
								</td>
							</tr>
						</table>
					</div>
					<div id='endereco' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
							<tr>
								<td align='left'>
									<br><label>CEP:</label> <input name='emp_cep' id='emp_cep' value='$emp_cep' placeholder='CEP' maxlength='9' onkeypress='mascaraCEP(this); return SomenteNumero(event);' />
									<p><label>UF:</label> <select name='emp_uf' id='emp_uf'>
																<option value='$emp_uf'>$uf_sigla</option>
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
									<p><label>Município:</label> <select name='emp_municipio' id='emp_municipio'>
										<option value='$emp_municipio'>$mun_nome</option>
									</select>
									<p><label>Bairro:</label> <input name='emp_bairro' id='emp_bairro' value='$emp_bairro' placeholder='Bairro' />
									<p><label>Endereço:</label> <input name='emp_endereco' id='emp_endereco' value='$emp_endereco' placeholder='Endereço' />
									<p><label>Número:</label> <input name='emp_numero' id='emp_numero' value='$emp_numero' placeholder='Número' />
									   <label>Complemento:</label> <input name='emp_comp' id='emp_comp' value='$emp_comp' placeholder='Complemento' />
								</td>
							</tr>
						</table>
					</div>
					<div id='departamentos' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
							<tr>
								<td align='left'>
									<div id='p_scents_departamento'>
									";
									$sql = "SELECT * FROM cadastro_empresas_departamentos
											LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cadastro_empresas_departamentos.dep_empresa											
											LEFT JOIN end_uf ON end_uf.uf_id = cadastro_empresas_departamentos.dep_uf											
											LEFT JOIN end_municipios ON end_municipios.mun_id = cadastro_empresas_departamentos.dep_municipio											
											WHERE dep_empresa = :dep_empresa";
									$stmt = $PDO->prepare($sql);
									$stmt->bindParam(':dep_empresa', $emp_id);
									$stmt->execute();
									$rows = $stmt->rowCount();
									if($rows > 0)
									{
										$x=0;
										while($result = $stmt->fetch())
										{
											$x++;
											echo "
											<div class='bloco_departamento'>
																					<input type='hidden' name='departamentos[$x][dep_id]' id='dep_id' value='".$result['dep_id']."'>
												"; if($x > 1){ echo "<br><br><hr><p>";}else{ echo "<br>";} 
												echo "
												<label>Departamento:</label> 	<input name='departamentos[$x][dep_descricao]' id='dep_descricao' value='".$result['dep_descricao']."'placeholder='Departamento'>
												<p><label>CEP:</label> 			<input name='departamentos[$x][dep_cep]' id='dep_cep' value='".$result['dep_cep']."' class='dep_cep' placeholder='CEP' maxlength='9' onkeypress='mascaraCEP(this); return SomenteNumero(event);' />
												<p><label>UF:</label> 			<select name='departamentos[$x][dep_uf]' id='dep_uf' class='dep_uf' >
																					<option value='".$result['dep_uf']."'>".$result['uf_sigla']."</option>
																					"; 
																					$sql = "  SELECT * FROM end_uf ORDER BY uf_sigla";
																					$stmt_uf = $PDO->prepare($sql);
																					$stmt_uf->execute();
																					while($result_uf = $stmt_uf->fetch())
																					{
																						echo "<option value='".$result_uf['uf_id']."'>".$result_uf['uf_sigla']."</option>";
																					}
																					echo "
																				</select>
												<p><label>Município:</label> 	<select name='departamentos[$x][dep_municipio]' id='dep_municipio' class='dep_municipio'>
																					<option value='".$result['dep_municipio']."'>".$result['mun_nome']."</option>																			
																				</select>
												<p><label>Bairro:</label>		<input name='departamentos[$x][dep_bairro]' id='dep_bairro' class='dep_bairro' value='".$result['dep_bairro']."' placeholder='Bairro'></p>
												<p><label>Endereço:</label>		<input name='departamentos[$x][dep_endereco]' id='dep_endereco' class='dep_endereco' value='".$result['dep_endereco']."' placeholder='Endereço:'></p>
												<p><label>Número:</label>		<input name='departamentos[$x][dep_numero]' id='dep_numero' value='".$result['dep_numero']."' placeholder='Email'></p>
												<label>Complemento:</label>		<input name='departamentos[$x][dep_comp]' id='dep_comp' value='".$result['dep_comp']."' placeholder='Complemento'></p>
												<p><label>Telefone:</label>		<input name='departamentos[$x][dep_telefone]' id='dep_telefone' value='".$result['dep_telefone']."' placeholder='Telefone (c/ DDD)'onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);'></p>
												<p><label>Email:</label>		<input name='departamentos[$x][dep_email]' id='dep_email' value='".$result['dep_email']."' placeholder='Email'></p>
												<p><img src='../imagens/icon-add.png' id='addDepartamento' title='Adicionar +' class='botao_dinamico'> <img src='../imagens/icon-rmv.png' id='remDepartamento' title='Remover' class='botao_dinamico'></p>
											</div>
											";
										}
									}
									else
									{
										echo "
										<div class='bloco_departamento'>
																				<input type='hidden' name='departamentos[1][dep_id]' id='dep_id'>
											<br><label>Departamento:</label>	<input name='departamentos[1][dep_descricao]' id='dep_descricao' placeholder='Departamento'>
											<p><label>CEP:</label> 				<input name='departamentos[1][dep_cep]' id='dep_cep' class='dep_cep' placeholder='CEP' maxlength='9' onkeypress='mascaraCEP(this); return SomenteNumero(event);' />
											<p><label>UF:</label> 				<select name='departamentos[1][dep_uf]' id='dep_uf' class='dep_uf'>
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
											<p><label>Município:</label> 		<select name='departamentos[1][dep_municipio]' id='dep_municipio' class='dep_municipio'>
																					<option value=''>Município</option>
																				</select>
											<p><label>Bairro:</label> 			<input name='departamentos[1][dep_bairro]' id='dep_bairro' class='dep_bairro' placeholder='Bairro' />
											<p><label>Endereço:</label> 		<input name='departamentos[1][dep_endereco]' id='dep_endereco' class='dep_endereco' placeholder='Endereço' />
											<p><label>Número:</label> 			<input name='departamentos[1][dep_numero]' id='dep_numero' class='dep_numero' placeholder='Número' />
											   <label>Complemento:</label> 		<input name='departamentos[1][dep_comp]' id='dep_comp' placeholder='Complemento' />
											<p><label>Telefone:</label>			<input name='departamentos[1][dep_telefone]' id='dep_telefone' placeholder='Telefone (c/ DDD)' onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);'>
											<p><label>Email:</label>			<input name='departamentos[1][dep_email]' id='dep_email' placeholder='Email'>
											<p><img src='../imagens/icon-add.png' id='addDepartamento' title='Adicionar +' class='botao_dinamico'></p>
										</div>
										";
									}
									echo "
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div id='contatos' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
							<tr>
								<td align='left'>
									<div id='p_scents_contato'>
									";
									$sql = "SELECT * FROM cadastro_empresas_contatos 
											LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cadastro_empresas_contatos.ctt_empresa	
											LEFT JOIN cadastro_empresas_departamentos ON cadastro_empresas_departamentos.dep_id = cadastro_empresas_contatos.ctt_departamento
											WHERE ctt_empresa = :ctt_empresa";
									$stmt = $PDO->prepare($sql);
									$stmt->bindParam(':ctt_empresa', $emp_id);
									$stmt->execute();
									$rows = $stmt->rowCount();
									if($rows > 0)
									{
										$x=0;
										while($result = $stmt->fetch())
										{
											$x++;
											echo "
											<div class='bloco_contato'>
												<input type='hidden' name='contatos[$x][ctt_id]' id='ctt_id' value='".$result['ctt_id']."'>
												"; if($x > 1){ echo "<br><br><hr><p>";}else{ echo "<br>";} echo "<label>Departamento:</label> 
													<select name='contatos[$x][ctt_departamento]' id='ctt_departamento' >
													<option value='".$result['ctt_departamento']."'>".$result['dep_descricao']."</option>
													"; 
													$sql = " SELECT * FROM cadastro_empresas_departamentos 
															 LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cadastro_empresas_departamentos.dep_empresa
															 WHERE emp_id = :emp_id
															 ORDER BY dep_descricao  ";
													$stmt_dep = $PDO->prepare($sql);
													$stmt_dep->bindParam(':emp_id',$emp_id);
													$stmt_dep->execute();
													while($result_dep = $stmt_dep->fetch())
													{
														echo "<option value='".$result_dep['dep_id']."'>".$result_dep['dep_descricao']."</option>";
													}
													echo "
												</select>
												<p><label>Cargo:</label><input name='contatos[$x][ctt_cargo]' id='ctt_cargo' value='".$result['ctt_cargo']."' placeholder='Cargo'>
												<p><label>Nome do Contato:</label><input name='contatos[$x][ctt_nome]' id='ctt_nome' value='".$result['ctt_nome']."' placeholder='Nome do Contato'>
												<p><label>Email:</label><input name='contatos[$x][ctt_email]' id='ctt_email' value='".$result['ctt_email']."' placeholder='Email'>
												<p><label>Telefone:</label><input name='contatos[$x][ctt_telefone]' id='ctt_telefone' value='".$result['ctt_telefone']."' placeholder='Telefone (c/ DDD)' onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);'>
												<label>Ramal:</label><input name='contatos[$x][ctt_ramal]' id='ctt_ramal' value='".$result['ctt_ramal']."' placeholder='Ramal'>
												<p><label>Celular:</label><input name='contatos[$x][ctt_celular]' id='ctt_celular' value='".$result['ctt_celular']."' placeholder='Celular (c/ DDD)'onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);'>
												<p><label>Data Nascimento:</label><input name='contatos[$x][ctt_data_nasc]' id='ctt_data_nasc' value='".implode("/",array_reverse(explode("-",$result['ctt_data_nasc'])))."' placeholder='Data Nascimento' onkeypress='return mascaraData(this,event);'>
												<p><label>Login:</label><input 				name='contatos[$x][ctt_login]' id='ctt_login' value='".$result['ctt_login']."'placeholder='Login'>
												<p><label>Senha:</label><input 				name='contatos[$x][ctt_senha]' id='ctt_senha' value='".$result['ctt_senha']."'type='password' placeholder='Senha'>
												<p><img src='../imagens/icon-add.png' id='addContato' title='Adicionar +' class='botao_dinamico'> <img src='../imagens/icon-rmv.png' id='remContato' title='Remover' class='botao_dinamico'>
											</div>
											";
										}
									}
									else
									{
										echo "
										<div class='bloco_contato'>
											<input type='hidden' name='contatos[1][ctt_id]' id='ctt_id'>
											<br><label>Departamento:</label> 
												<select name='contatos[1][ctt_departamento]' id='ctt_departamento' >
												<option value=''>Departamento</option>
												"; 
												$sql = " SELECT * FROM cadastro_empresas_departamentos 
														 LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cadastro_empresas_departamentos.dep_empresa
														 WHERE emp_id = :emp_id
														 ORDER BY dep_descricao  ";
												$stmt = $PDO->prepare($sql);
												$stmt->bindParam(':emp_id',$emp_id);
												$stmt->execute();
												while($result = $stmt->fetch())
												{
													echo "<option value='".$result['dep_id']."'>".$result['dep_descricao']."</option>";
												}
												echo "
											</select>
											<p><label>Cargo:</label>			<input name='contatos[1][ctt_cargo]' id='ctt_cargo' placeholder='Cargo'>
											<p><label>Nome do Contato:</label>	<input name='contatos[1][ctt_nome]' id='ctt_nome' placeholder='Nome do Contato'>
											<p><label>Email:</label>			<input name='contatos[1][ctt_email]' id='ctt_email' placeholder='Email'>
											<p><label>Telefone:</label>			<input name='contatos[1][ctt_telefone]' id='ctt_telefone' placeholder='Telefone (c/ DDD)' onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);'>
											<label>Ramal:</label>				<input name='contatos[1][ctt_ramal]' id='ctt_ramal' placeholder='Ramal'>
											<p><label>Celular:</label>			<input name='contatos[1][ctt_celular]' id='ctt_celular' placeholder='Celular (c/ DDD)'onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);'>
											<p><label>Data Nascimento:</label>	<input name='contatos[1][ctt_data_nasc]' id='ctt_data_nasc' placeholder='Data Nascimento' onkeypress='return mascaraData(this,event);'>
											<p><label>Login:</label>			<input name='contatos[1][ctt_login]' id='ctt_login' placeholder='Login'>
											<p><label>Senha:</label>			<input name='contatos[1][ctt_senha]' id='ctt_senha' type='password' placeholder='Senha'>
											<p><img src='../imagens/icon-add.png' id='addContato' title='Adicionar +' class='botao_dinamico'>
										</div>
										";
									}
									echo "
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div id='emp_logo' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
							<tr>
								<td align='left'>
									<br><label>Logo:</label> ";if($emp_logo != ''){ echo "<img src='$emp_logo' valign='middle' style='max-width:250px'>";} echo " &nbsp; 
									<p><label>Alterar Logo:</label> <input type='file' name='emp_logo[logo]' id='emp_logo'> 									
								</td>
							</tr>
						</table>
					</div>
					<div id='contratos' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
							<tr>
								<td align='left'>
									<div id='p_scents_contrato'>
									";
									$sql = "SELECT * FROM cadastro_empresas_contratos 
											LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cadastro_empresas_contratos.con_empresa	
											WHERE con_empresa = :con_empresa";
									$stmt = $PDO->prepare($sql);
									$stmt->bindParam(':con_empresa', $emp_id);
									$stmt->execute();
									$rows = $stmt->rowCount();
									if($rows > 0)
									{
										$x=0;
										while($result = $stmt->fetch())
										{
											$x++;
											echo "
											<div class='bloco_contrato'>
												<input type='hidden' name='contratos[$x][con_id]' id='con_id' value='".$result['con_id']."'>
												"; if($x > 1){ echo "<br><br><hr><p>";}else{ echo "<br>";} 
												echo "
												<label>Tipo:</label> <select name='contratos[$x][con_tipo]' id='con_tipo'>
													<option value='".$result['con_tipo']."'>".$result['con_tipo']."</option>
													<option value='PJ'>Pessoa Jurídica</option>
													<option value='PF'>Pessoa Física</option>
												</select> &nbsp;";
												if($result['con_tipo'] == "PJ")
												{
													echo "<input name='contratos[$x][con_cpf_cnpj]' id='con_cnpj' value='".$result['con_cpf_cnpj']."' placeholder='C.N.P.J'  maxlength='18' onkeypress='mascaraCNPJ(this); return SomenteNumero(event);'>
														<input name='contratos[$x][con_cpf_cnpj]' id='con_cpf' value='' placeholder='C.P.F'  maxlength='14' onkeypress='mascaraCPF(this); return SomenteNumero(event);'  style='display:none;' disabled>
														";
												}
												elseif($result['con_tipo'] == "PF")
												{
													echo "<input name='contratos[$x][con_cpf_cnpj]' id='con_cnpj' value='' placeholder='C.N.P.J'  maxlength='18' onkeypress='mascaraCNPJ(this); return SomenteNumero(event);' style='display:none;' disabled>
														<input name='contratos[$x][con_cpf_cnpj]' id='con_cpf'  value='".$result['con_cpf_cnpj']."' placeholder='C.P.F'  maxlength='14' onkeypress='mascaraCPF(this); return SomenteNumero(event);'>
														";
												}
												echo "
												<p><label>Objeto:</label><input name='contratos[$x][con_objeto]' id='con_objeto' value='".$result['con_objeto']."' placeholder='Objeto'>
												<p><label>Nome/Razão Social:</label><input name='contratos[$x][con_razao_social]' id='con_razao_social' value='".$result['con_razao_social']."' placeholder='Nome/Razão Social'>
												<p><label>Data:</label><input name='contratos[$x][con_data]' class='datepicker' id='con_data' value='".implode("/",array_reverse(explode("-",$result['con_data'])))."' placeholder='Data' onkeypress='return mascaraData(this,event);'>
												<p><label>Valor:</label>				<input name='contratos[$x][con_valor]' id='con_valor' value='".number_format($result['con_valor'],2,",",".")."' placeholder='Valor' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
												<p><label>Modalidade:</label><input 				name='contratos[$x][con_modalidade]' id='con_modalidade'  value='".$result['con_modalidade']."'placeholder='Modalidade'>
												<p><img src='../imagens/icon-add.png' id='addContrato' title='Adicionar +' class='botao_dinamico'> <img src='../imagens/icon-rmv.png' id='remContrato' title='Remover' class='botao_dinamico'>
											</div>
											";
										}
									}
									else
									{
										echo "
										<div class='bloco_contrato'>
											<input type='hidden' name='contratos[1][con_id]' id='con_id'>
											<br><label>Tipo:</label> <select name='contratos[1][con_tipo]' id='con_tipo'>
												<option value=''>Tipo</option>
												<option value='PJ'>Pessoa Jurídica</option>
												<option value='PF'>Pessoa Física</option>
											</select> &nbsp;<input name='contratos[1][con_cpf_cnpj]' id='con_cnpj' placeholder='C.N.P.J'  maxlength='18' onkeypress='mascaraCNPJ(this); return SomenteNumero(event);' style='display:none;'>
											&nbsp; <input name='contratos[1][con_cpf_cnpj]' id='con_cpf' placeholder='C.P.F'  maxlength='14' onkeypress='mascaraCPF(this); return SomenteNumero(event);'  style='display:none;'>
											
											<p><label>Objeto:</label>				<input name='contratos[1][con_objeto]' id='con_objeto' placeholder='Objeto'>
											<p><label>Nome/Razão Social:</label>	<input name='contratos[1][con_razao_social]' id='con_razao_social' placeholder='Nome/Razão Social'>
											<p><label>Data:</label>					<input name='contratos[1][con_data]' class='datepicker' id='con_data' placeholder='Data' onkeypress='return mascaraData(this,event);'>
											<p><label>Valor:</label>				<input name='contratos[1][con_valor]' id='con_valor' placeholder='Valor' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
											<p><label>Modalidade:</label>			<input name='contratos[1][con_modalidade]' id='con_modalidade' placeholder='Modalidade'>
											<p><img src='../imagens/icon-add.png' id='addContrato' title='Adicionar +' class='botao_dinamico'>
										</div>
										";
									}
									echo "
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div id='prospeccoes' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
						";
						$sql_icone = "SELECT * FROM cadastro_prospeccoes ORDER BY pro_nome";
						$stmt_icone = $PDO->prepare($sql_icone);
						//$stmt_icone->bindParam(':emp_id',$emp_id);
						$stmt_icone->execute();
						while($result_icone = $stmt_icone->fetch())
						{
							echo "<a href='cadastro_empresas_prospeccoes.php?pagina=cadastro_empresas_prospeccoes&emp_id=$emp_id&pro_id=".$result_icone['pro_id']."$autenticacao'><div class='icone_pro'>".$result_icone['pro_icone']."<br>".$result_icone['pro_nome']."</div></a>";
						}
						echo "
							</td>
							</tr>
						</table>
					</div>
					<div id='certidoes' class='tab-pane fade in'>
						<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
							<tr>
								<td align='left'>
									<div id='p_scents_conselho'>
									";
									$sql = "SELECT * FROM cadastro_empresas_formacao 
											WHERE for_empresa = :for_empresa";
									$stmt = $PDO->prepare($sql);
									$stmt->bindParam(':for_empresa', $emp_id);
									$stmt->execute();
									$rows = $stmt->rowCount();
									if($rows > 0)
									{
										$x=0;
										while($result = $stmt->fetch())
										{
											$x++;
											echo "
											<div class='bloco_conselho'>
												<input type='hidden' name='formacao[$x][for_id]' id='for_id' value='".$result['for_id']."'>
												"; if($x > 1){ echo "<br><br><hr><p>";}else{ echo "<br>";} 
												echo "
												<p><label>Área:</label><input name='formacao[$x][for_formacao]' id='for_formacao' value='".$result['for_formacao']."' placeholder='Formação'>
												<p><label>Entidade:</label> <select name='formacao[$x][for_entidade]' id='for_entidade'>
													<option value='".$result['for_entidade']."'>".$result['for_entidade']."</option>
													<option value='CRA'>CRA</option>
													<option value='OAB'>OAB</option>
													<option value='CAU'>CAU</option>
													<option value='CRESS'>CRESS</option>
													<option value='CRBIO'>CRBIO</option>
													<option value='CRBM'>CRBM</option>
													<option value='CRC'>CRC</option>
													<option value='CRECI'>CRECI</option>
													<option value='CORECON'>CORECON</option>
													<option value='CREF'>CREF</option>
													<option value='COREN'>COREN</option>
													<option value='CREA'>CREA</option>
													<option value='CRF'>CRF</option>
													<option value='CREFITO'>CREFITO</option>
													<option value='CRM'>CRM</option>
													<option value='CRMV'>CRMV</option>
													<option value='CRN'>CRN</option>
													<option value='CRO'>CRO</option>
													<option value='CRP'>CRP</option>
													<option value='CRQ'>CRQ</option>
													<option value='CORE'>CORE</option>
												</select>												
												<p><label>Inscrição:</label>				<input name='formacao[$x][for_inscricao]' id='for_inscricao' value='".$result['for_inscricao']."' placeholder='Inscrição'>
												<p><label>Data Vencimento:</label><input name='formacao[$x][for_data_vcto]' class='datepicker' id='for_data_vcto' value='".implode("/",array_reverse(explode("-",$result['for_data_vcto'])))."' placeholder='Data' onkeypress='return mascaraData(this,event);'>
												<p><img src='../imagens/icon-add.png' id='addconselho' title='Adicionar +' class='botao_dinamico'> <img src='../imagens/icon-rmv.png' id='remconselho' title='Remover' class='botao_dinamico'>
											</div>
											";
										}
									}
									else
									{
										echo "
										<div class='bloco_conselho'>
											<input type='hidden' name='formacao[1][for_id]' id='for_id'>
											<p><label>Área:</label>				<input name='formacao[1][for_formacao]' id='for_formacao' placeholder='Área'>
											<p><label>Entidade:</label> <select name='formacao[1][for_entidade]' id='for_entidade'>
												<option value=''>Entidade</option>
												<option value='CRA'>CRA</option>
												<option value='OAB'>OAB</option>
												<option value='CAU'>CAU</option>
												<option value='CRESS'>CRESS</option>
												<option value='CRBIO'>CRBIO</option>
												<option value='CRBM'>CRBM</option>
												<option value='CRC'>CRC</option>
												<option value='CRECI'>CRECI</option>
												<option value='CORECON'>CORECON</option>
												<option value='CREF'>CREF</option>
												<option value='COREN'>COREN</option>
												<option value='CREA'>CREA</option>
												<option value='CRF'>CRF</option>
												<option value='CREFITO'>CREFITO</option>
												<option value='CRM'>CRM</option>
												<option value='CRMV'>CRMV</option>
												<option value='CRN'>CRN</option>
												<option value='CRO'>CRO</option>
												<option value='CRP'>CRP</option>
												<option value='CRQ'>CRQ</option>
												<option value='CORE'>CORE</option>									
											</select>
											<p><label>Inscrição:</label>				<input name='formacao[1][for_inscricao]' id='for_inscricao' placeholder='Inscrição'>
											<p><label>Data Vencimento: </label>					<input name='formacao[1][for_data_vcto]' class='datepicker'  id='for_data_vcto' placeholder='Data Vencimento' onkeypress='return mascaraData(this,event);'>
									
											<p><img src='../imagens/icon-add.png' id='addconselho' title='Adicionar +' class='botao_dinamico'>
										</div>
										";
									}
									echo "
									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<center>
				<div id='erro' align='center'>&nbsp;</div>
				<input type='button' id='bt_cadastro_empresas_sair' value='Salvar e Sair' />&nbsp;&nbsp;&nbsp;&nbsp; 
				<input type='button' id='bt_cadastro_empresas_a' onclick='alteraActionEmpresa(".$emp_id.")' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
				<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_empresas.php?pagina=cadastro_empresas$autenticacao'; value='Cancelar'/></center>
				</center>
            </form>
            ";
        }
    }	
	?>
    </div>
</div>
<?php
include('../mod_rodape/rodape.php');
?>
</body>
</html>
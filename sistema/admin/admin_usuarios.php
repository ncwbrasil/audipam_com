<?php
session_start (); 
$pagina_link = 'admin_usuarios';
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
<script type='text/javascript' src='../mod_includes/js/jcrop/jquery.Jcrop.min.js'></script>
<script type='text/javascript' src='../mod_includes/js/jcrop/script_perfil.js'></script>
<link type='text/css' href='../mod_includes/js/jcrop/jquery.Jcrop.min.css' rel='stylesheet'  />

</head>
<body>
<?php	
require_once("../mod_includes/php/ctracker.php");
include		('../mod_includes/php/connect.php');
include		('../mod_includes/php/funcoes-jquery.php');
require_once('../mod_includes/php/verificalogin.php');
require_once('../mod_includes/php/verificapermissao.php');
include		("../mod_topo/altera_foto_perfil.php");
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
    $page = "Administradores &raquo; <a href='admin_usuarios.php?pagina=admin_usuarios".$autenticacao."'>Usuários</a>";
	$usu_id = $_GET['usu_id'];
	$usu_empresa = $_POST['usu_empresa'];
	$usu_setor = $_POST['usu_setor'];
	$usu_vinculacao = $_POST['usu_vinculacao'];
	$usu_nome = trim($_POST['usu_nome']);
	$usu_datanasc = implode("-",array_reverse(explode("/",$_POST['usu_datanasc'])));
	$usu_sexo = $_POST['usu_sexo'];
	$usu_cep = $_POST['usu_cep'];
	$usu_uf = $_POST['usu_uf'];
	$usu_municipio = $_POST['usu_municipio'];
	$usu_bairro = $_POST['usu_bairro'];
	$usu_endereco = $_POST['usu_endereco'];
	$usu_numero = $_POST['usu_numero'];
	$usu_comp = $_POST['usu_comp'];
	$usu_telefone = $_POST['usu_telefone'];
	$usu_celular = $_POST['usu_celular'];
	$usu_email = $_POST['usu_email'];
	$usu_cargo = $_POST['usu_cargo'];
	$usu_data_admissao = implode("-",array_reverse(explode("/",$_POST['usu_data_admissao'])));
	$usu_data_demissao = implode("-",array_reverse(explode("/",$_POST['usu_data_demissao'])));
	$usu_login = $_POST['usu_login'];
	$usu_senha = md5($_POST['usu_senha']);
	$usu_status = $_POST['usu_status'];
	$usu_notificacao = $_POST['usu_notificacao'];
	$usu_homologador = $_POST['usu_homologador'];
	$sql = "SELECT * FROM admin_usuarios WHERE usu_id = :usu_id ";
	$stmt = $PDO->prepare($sql);
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
	$dados = array_filter(array(
		'usu_empresa' 		=> $usu_empresa,
		'usu_setor' 		=> $usu_setor,
		'usu_vinculacao' 	=> $usu_vinculacao,
		'usu_nome' 			=> $usu_nome,
		'usu_datanasc' 		=> $usu_datanasc,
		'usu_sexo' 			=> $usu_sexo,
		'usu_cep' 			=> $usu_cep,
		'usu_uf' 			=> $usu_uf,
		'usu_municipio' 	=> $usu_municipio,
		'usu_bairro' 		=> $usu_bairro,
		'usu_endereco' 		=> $usu_endereco,
		'usu_numero' 		=> $usu_numero,
		'usu_comp' 			=> $usu_comp,
		'usu_telefone' 		=> $usu_telefone,
		'usu_celular' 		=> $usu_celular,
		'usu_email' 		=> $usu_email,
		'usu_cargo' 		=> $usu_cargo,
		'usu_data_admissao' => $usu_data_admissao,
		'usu_data_demissao' => $usu_data_demissao,
		'usu_login' 		=> $usu_login,
		'usu_senha' 		=> $usu_senha,
		'usu_status' 		=> $usu_status,
		'usu_notificacao'	=> $usu_notificacao,
		'usu_homologador'	=> $usu_homologador
	),'strlen');
	if($action == "adicionar")
    {
        $sql = "INSERT INTO admin_usuarios SET ".bindFields($dados);
		$stmt = $PDO->prepare($sql);	
        if($stmt->execute($dados))
        {		
			$usu_id = $PDO->lastInsertId();

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
												
						
						
						
						$valor['for_usuario'] = $usu_id;
						$sql = "INSERT INTO admin_usuarios_formacao SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//INSERE
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
				}
			}
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
    
    if($action == 'editar')
    {
        $sql = "UPDATE admin_usuarios SET ".bindFields($dados)." WHERE usu_id = :usu_id ";
		$stmt = $PDO->prepare($sql); 
		$dados['usu_id'] =  $usu_id;
		if($stmt->execute($dados))
        {
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
					$sql = "DELETE FROM admin_usuarios_formacao WHERE for_usuario = :usu_id AND for_id NOT IN (".implode(",",$a_excluir).") ";
					
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':usu_id', $usu_id);
					if($stmt->execute())
					{
						//echo "Excluido <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
				else
				{
					$sql = "DELETE FROM admin_usuarios_formacao WHERE for_usuario = :usu_id ";
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':usu_id', $usu_id);
					if($stmt->execute())
					{
						//echo "Excluido todos <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
			}
			else
			{
				$sql = "DELETE FROM admin_usuarios_formacao WHERE for_usuario = :usu_id ";
				$stmt = $PDO->prepare($sql); 
				$stmt->bindParam(':usu_id', $usu_id);
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
																							
						$sql = "UPDATE admin_usuarios_formacao SET ".bindFields($valor2)." WHERE for_id = :for_id";
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

						$valor['for_usuario'] = $usu_id;
						$sql = "INSERT INTO admin_usuarios_formacao SET ".bindFields($valor);
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
        $sql = "SELECT * FROM admin_usuarios WHERE usu_id = :usu_id";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':usu_id',$usu_id);
		$stmt->execute();
        $result = $stmt->fetch();
        $usu_login = $result['usu_login'];
        $usu_nome = $result['usu_nome'];
        if($usu_login == "administrador")
        {
            echo "
            <SCRIPT language='JavaScript'>
                abreMask(
                '<img src=../imagens/x.png> O usuário <b>$usu_nome</b> não pode ser alterado.<br><br>'+
                '<input value=\' Ok \' type=\'button\' onclick=javascript:window.location.href=\'admin_usuarios.php?pagina=admin_usuarios$autenticacao\'; >');
            </SCRIPT>
            ";exit;
        }
        
        $sql = "DELETE FROM admin_usuarios WHERE usu_id = :usu_id";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':usu_id',$usu_id);
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
                '<img src=../imagens/x.png> Erro ao realizar exclusão.<br><br>'+
                '<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back(); >');
            </SCRIPT>
            ";
        }
    }
    if($action == 'ativar')
    {
        $sql = "UPDATE admin_usuarios SET usu_status = :usu_status WHERE usu_id = :usu_id ";
        $stmt = $PDO->prepare($sql);
		$stmt->bindValue(':usu_status',1);
        $stmt->bindParam(':usu_id',$usu_id);
        $stmt->execute();
    }
    if($action == 'desativar')
    {
        $sql = "SELECT * FROM admin_usuarios WHERE usu_id = :usu_id";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':usu_id',$usu_id);
		$stmt->execute();
        $result = $stmt->fetch();
        $usu_login = $result['usu_login'];
        $usu_nome = $result['usu_nome'];
        if($usu_login == "administrador")
        {
            echo "
            <SCRIPT language='JavaScript'>
                abreMask(
                '<img src=../imagens/x.png> O administrador <b>$usu_nome</b> não pode ser alterado.<br><br>'+
                '<input value=\' Ok \' type=\'button\' onclick=javascript:window.location.href=\'admin_usuarios.php?pagina=admin_usuarios$autenticacao\'; >');
            </SCRIPT>
            ";exit;
        }
    
        $sql = "UPDATE admin_usuarios SET usu_status = :usu_status WHERE usu_id = :usu_id ";
        $stmt = $PDO->prepare($sql);
		$stmt->bindValue(':usu_status',0);
        $stmt->bindParam(':usu_id',$usu_id);
        $stmt->execute();
    }
    $num_por_pagina = 10;
    if(!$pag){$primeiro_registro = 0; $pag = 1;}
    else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
    $sql = "SELECT * FROM admin_usuarios 
            LEFT JOIN admin_setores ON admin_setores.set_id = admin_usuarios.usu_setor
            ORDER BY usu_id ASC
            LIMIT :primeiro_registro, :num_por_pagina ";
  	$stmt = $PDO->prepare($sql);
	$stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
	$stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
	$stmt->execute();
    $rows = $stmt->rowCount();
    if($pagina == "admin_usuarios")
    {
        echo "
		<div class='titulo'> $page  </div>
		<div id='botoes'><input value='Novo Usuário' type='button' onclick=javascript:window.location.href='admin_usuarios.php?pagina=admin_usuarios_adicionar".$autenticacao."'; /></div>
		";
		if ($rows > 0)
		{
			echo "
			<table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
				<tr>
					<td class='titulo_first'>Foto</td>
					<td class='titulo_tabela'>Nome</td>
					<td class='titulo_tabela'>Setor</td>
					<td class='titulo_tabela'>Login</td>
					<td class='titulo_tabela' align='center'>Status</td>
					<td class='titulo_tabela' align='center'>Recebe notificação<br> via email?</td>
					<td class='titulo_tabela' align='center'>Homologador?</td>
					<td class='titulo_last' align='center'>Gerenciar</td>
				</tr>";
				$c=0;
				 while($result = $stmt->fetch())
				{
					
					$usu_id 	= $result['usu_id'];
					$set_nome 	= $result['set_nome'];
					$usu_nome 	= $result['usu_nome'];
					$usu_foto 	= $result['usu_foto'];
					$usu_login 	= $result['usu_login'];
					$usu_status = $result['usu_status'];
					$usu_notificacao = $result['usu_notificacao'];
					$usu_homologador = $result['usu_homologador'];
					if($usu_foto == '')
					{
						$usu_foto = '../imagens/perfil.png';
					}
					
					if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;}  
					echo "
					<script type='text/javascript'>
						jQuery(document).ready(function($) {
					
							// Define any icon actions before calling the toolbar
							$('.toolbar-icons a').on('click', function( event ) {
								$(this).click();
								
							});
							$('#normal-button-$usu_id').toolbar({content: '#user-options-$usu_id', position: 'top', hideOnClick: true});
							$('#normal-button-bottom').toolbar({content: '#user-options', position: 'bottom'});
							$('#normal-button-small').toolbar({content: '#user-options-small', position: 'top', hideOnClick: true});
							$('#button-left').toolbar({content: '#user-options', position: 'left'});
							$('#button-right').toolbar({content: '#user-options', position: 'right'});
							$('#link-toolbar').toolbar({content: '#user-options', position: 'top' });
						});
					</script>
					<div id='user-options-$usu_id' class='toolbar-icons' style='display: none;'>
						";
						if($usu_status == 1)
						{
							echo "<a title='Desativar' href='admin_usuarios.php?pagina=admin_usuarios&action=desativar&usu_id=$usu_id$autenticacao'><img border='0' src='../imagens/icon-ativa-desativa.png'></a>";
						}
						else
						{
							echo "<a title='Ativar' href='admin_usuarios.php?pagina=admin_usuarios&action=ativar&usu_id=$usu_id$autenticacao'><img border='0' src='../imagens/icon-ativa-desativa.png'></a>";
						}
						echo "
						<a title='Editar' href='admin_usuarios.php?pagina=admin_usuarios_editar&usu_id=$usu_id$autenticacao'><img border='0' src='../imagens/icon-editar.png'></a>
						<a title='Excluir' onclick=\"
							abreMask(
								'Deseja realmente excluir o administrador <b>$usu_nome</b>?<br><br>'+
								'<input value=\' Sim \' type=\'button\' onclick=javascript:window.location.href=\'admin_usuarios.php?pagina=admin_usuarios&action=excluir&usu_id=$usu_id$autenticacao\';>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
								'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
							\">
							<img border='0' src='../imagens/icon-excluir.png'></i>
						</a>
					</div>
					";
					echo "<tr class='$c1'>
							  <td><div style='background:url(".$usu_foto."); height: 50px; width:50px; object-fit:cover; background-size:100%; border-radius:100px;'></div></td>
							  <td>$usu_nome</td>
							  <td>$set_nome</td>
							  <td>$usu_login</td>
							  <td align=center>";
							  if($usu_status == 1)
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
							  if($usu_notificacao == 1)
							  {
								echo "<img border='0' src='../imagens/ok.png' width='15' height='15'>";
							  }
							  else
							  {
								echo "<img border='0' src='../imagens/x.png' width='15' height='15'>";
							  }
							  echo "
							  </td>
							  <td align=center>";
							  if($usu_homologador == 1)
							  {
								echo "<img border='0' src='../imagens/ok.png' width='15' height='15'>";
							  }
							  else
							  {
								echo "<img border='0' src='../imagens/x.png' width='15' height='15'>";
							  }
							  echo "
							  </td>
							  <td align=center><div id='normal-button-$usu_id' class='settings-button'><img src='../imagens/icon-cog-small.png' /></div></td>
						  </tr>";
				}
				echo "</table>";
				$variavel = "&pagina=admin_usuarios".$autenticacao."";
				$cnt = "SELECT COUNT(*) FROM admin_usuarios ";
				$stmt = $PDO->prepare($cnt);
				include("../mod_includes/php/paginacao.php");
		}
		else
		{
			echo "<br><br><br>Não há nenhum administrador cadastrado.";
		}
    }
    if($pagina == 'admin_usuarios_adicionar')
    {
		include		("../mod_topo/foto_perfil.php");
        echo "	
        <form name='form_admin_usuarios' id='form_admin_usuarios' enctype='application/x-www-form-urlencoded' method='post' action='admin_usuarios.php?pagina=admin_usuarios&action=adicionar&id=$id$autenticacao'>
			<div class='titulo'> $page &raquo; Adicionar  </div>
			<ul class='nav nav-tabs'>
			  <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
			  <li><a data-toggle='tab' 					href='#foto'>Foto</a></li>
			  <li><a data-toggle='tab' 					href='#formacao'>Formação Graduação</a></li>			  
			</ul>
			<div class='tab-content'>
				<div id='dados_gerais' class='tab-pane fade in active'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
					<tr>
						<td align='left'>
						<label>Empresa:</label> <select name='usu_empresa' id='usu_empresa' >
							<option value=''>Empresa</option>
							"; 
							$sql = "SELECT * FROM agenda_gerenciar
									INNER JOIN cadastro_empresas ON cadastro_empresas.emp_id =  agenda_gerenciar.age_empresa
									ORDER BY emp_fantasia";
							$stmt_cat = $PDO->prepare($sql);
							$stmt_cat->execute();
							while($result_cat = $stmt_cat->fetch())
							{
								echo "<option value='".$result_cat['emp_id']."'>".$result_cat['emp_fantasia']."</option>";
							}
							echo "
						</select>
						 <p><label>Setor:</label> <select name='usu_setor' id='usu_setor'>
							<option value=''>Setor</option>";
							$sql = " SELECT * FROM admin_setores ORDER BY set_nome";
							$stmt = $PDO->prepare($sql);
							$stmt->execute();
							while($result = $stmt->fetch())
							{
								echo "<option value='".$result['set_id']."'>".$result['set_nome']."</option>";
							}
							echo "
						</select>
						<p>
						<label>Vinculação:</label> <select name='usu_vinculacao' id='usu_vinculacao'>
							<option value=''>Vinculação</option>
							<option value='Contratado'>Contratado</option>
							<option value='Freelancer'>Freelancer</option>
						</select>
						<p>
						<label>Nome do Usuário:</label> <input name='usu_nome' id='usu_nome' placeholder='Nome do Usuário'>
						<p>
						<label>Data Nascimento:</label> <input name='usu_datanasc' id='usu_datanasc' placeholder='Data Nascimento' onkeypress='return mascaraData(this,event);'>
						<p>
						<label>Sexo:</label> <select name='usu_sexo' id='usu_sexo'>
							<option value=''>Sexo</option>
							<option value='Masculino'>Masculino</option>
							<option value='Feminino'>Feminino</option>
						</select>
						<p><label>Cargo:</label> <input name='usu_cargo' id='usu_cargo' placeholder='Cargo' />
						<p><label>Data Admissão:</label> <input name='usu_data_admissao' id='usu_data_admissao' placeholder='Data Admissão' onkeypress='return mascaraData(this,event);' />
						<p><label>Data Demissão:</label> <input name='usu_data_demissao' id='usu_data_demissao' placeholder='Data Demissão' onkeypress='return mascaraData(this,event);' />
						<p><label>CEP:</label> <input name='usu_cep' id='usu_cep' placeholder='CEP' maxlength='9' onkeypress='mascaraCEP(this); return SomenteNumero(event);' />
						<p><label>UF:</label> <select name='usu_uf' id='usu_uf'>
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
						<p><label>Município:</label> <select name='usu_municipio' id='usu_municipio'>
							<option value=''>Município</option>
						</select>
						<p><label>Bairro:</label> <input name='usu_bairro' id='usu_bairro' placeholder='Bairro' />
						<p><label>Endereço:</label> <input name='usu_endereco' id='usu_endereco' placeholder='Endereço' />
						<p><label>Número:</label> <input name='usu_numero' id='usu_numero' placeholder='Número' />
							<label>Complemento:</label> <input name='usu_comp' id='usu_comp' placeholder='Complemento' />
						<p><label>Telefone:</label> <input name='usu_telefone' id='usu_telefone' placeholder='Telefone (c/ DDD)' onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);' />
						<p><label>Celular:</label> <input name='usu_celular' id='usu_celular' placeholder='Celular (c/ DDD)' onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);' />	
						<p><label>Email:</label> <input name='usu_email' id='usu_email' placeholder='Email'>	
						<p><label>Login:</label> <input type='text' name='usu_login' id='usu_login' placeholder='Login' autocomplete='off'>
						<p><label>Senha:</label> <input type='password' name='usu_senha' id='usu_senha' placeholder='Senha'>
						<p>
						<p><label>Status:</label> <input type='radio' name='usu_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type='radio' name='usu_status' value='0'> Inativo<br>
						<p><label>Recebe Notificação?</label> <input type='radio' name='usu_notificacao' value='1' checked> Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type='radio' name='usu_notificacao' value='0'> Não<br>
						<p><label>Homologador?</label> <input type='radio' name='usu_homologador' value='1' checked> Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type='radio' name='usu_homologador' value='0'> Não<br>					
					</td>
					</tr>
					</table>
				</div>
				<div id='foto' class='tab-pane fade in'>
					<div class='formtitulo'>Foto do Perfil</div>
					<a id='box_foto_perfil'>";
					if($usu_foto != ''){echo "<img src='$usu_foto' border='0' width='250' />";}else{echo "<img src='../imagens/perfil.png' border='0' width='250' />";}
					echo "
					</a>
				</div>
				<div id='formacao' class='tab-pane fade in'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
								<div id='p_scents_formacao'>
								<div class='bloco_formacao'>
									<input type='hidden' name='formacao[1][for_id]' id='for_id'>
									<p><label>Formação:</label>				<input name='formacao[1][for_formacao]' id='for_formacao' placeholder='Formação'>
									<p><label>Instituição de Ensino:</label>				<input name='formacao[1][for_ies]' id='for_ies' placeholder='Instituição de Ensino'>
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
									<p><img src='../imagens/icon-add.png' id='addformacao' title='Adicionar +' class='botao_dinamico'></p>
								</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>

			<center>
			<div id='erro' align='center'>&nbsp;</div>
			<input type='button' id='bt_admin_usuarios' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
			<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='admin_usuarios.php?pagina=admin_usuarios".$autenticacao."'; value='Cancelar'/></center>
			</center>               
        </form>
        ";
    }
    
    if($pagina == 'admin_usuarios_editar')
    {
        $sql = "SELECT * FROM admin_usuarios 
				LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = admin_usuarios.usu_empresa
				LEFT JOIN admin_setores ON admin_setores.set_id = admin_usuarios.usu_setor
				LEFT JOIN end_uf ON end_uf.uf_id = admin_usuarios.usu_uf
				LEFT JOIN end_municipios ON end_municipios.mun_id = admin_usuarios.usu_municipio
				WHERE usu_id = :usu_id";
        $stmt = $PDO->prepare($sql);	
		$stmt->bindParam(':usu_id', $usu_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
        {
			$result = $stmt->fetch();
            $usu_foto 			= $result['usu_foto'];
			$usu_setor 			= $result['usu_setor'];
			$usu_vinculacao	 	= $result['usu_vinculacao'];if($usu_vinculacao == ''){$usu_vinculacao = 'Vinculação';}
			$emp_id 			= $result['emp_id'];
			$emp_fantasia 		= $result['emp_fantasia'];
			$set_nome 			= $result['set_nome'];
			$usu_nome 			= $result['usu_nome'];
			$usu_email 			= $result['usu_email'];
			$usu_datanasc 		= implode("/",array_reverse(explode("-",$result['usu_datanasc'])));
			$usu_sexo 			= $result['usu_sexo'];if($usu_sexo == ''){$usu_sexo = 'Sexo';}
			$usu_cep 			= $result['usu_cep'];
			$usu_uf 			= $result['usu_uf'];
			$uf_sigla 			= $result['uf_sigla'];if($uf_sigla == ''){$uf_sigla = 'UF';}
			$usu_municipio 		= $result['usu_municipio'];
			$mun_nome 			= $result['mun_nome'];if($mun_nome == ''){$mun_nome = 'Município';}
			$usu_bairro 		= $result['usu_bairro'];
			$usu_endereco 		= $result['usu_endereco'];
			$usu_numero 		= $result['usu_numero'];
			$usu_comp 			= $result['usu_comp'];
			$usu_telefone 		= $result['usu_telefone'];
			$usu_celular 		= $result['usu_celular'];
			$usu_cargo 			= $result['usu_cargo'];
			$usu_data_admissao 	= implode("/",array_reverse(explode("-",$result['usu_data_admissao'])));
			$usu_data_demissao 	= implode("/",array_reverse(explode("-",$result['usu_data_demissao'])));
			$usu_imagem 		= $result['usu_imagem'];
			$usu_login 			= $result['usu_login'];
			$usu_senha 			= $result['usu_senha'];
			$usu_status 		= $result['usu_status'];
			$usu_notificacao 	= $result['usu_notificacao'];
			$usu_homologador 	= $result['usu_homologador'];
			include		("../mod_topo/foto_perfil.php");
            
            echo "
            <form name='form_admin_usuarios' id='form_admin_usuarios' enctype='application/x-www-form-urlencoded' method='post' action='admin_usuarios.php?pagina=admin_usuarios&action=editar&usu_id=$usu_id$autenticacao'>
				<div class='titulo'> $page &raquo; Editar</div>
				<ul class='nav nav-tabs'>
					<li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
					<li><a data-toggle='tab' 					href='#foto'>Foto</a></li>
					<li><a data-toggle='tab' 					href='#formacao'>Formação Graduação</a></li>			  
				</ul>
				<div class='tab-content'>
					<div id='dados_gerais' class='tab-pane fade in active'>
						<table align='center' cellspacing='0' width='100%'>
							<tr>
								<td align='left'>									
									<div class='quadro'>
										<div class='formtitulo'>Dados Gerais</div>
										<label>Empresa:</label> <select name='usu_empresa' id='usu_empresa' >
											<option value='$emp_id'>$emp_fantasia</option>
											"; 
											$sql = "SELECT * FROM agenda_gerenciar
													INNER JOIN cadastro_empresas ON cadastro_empresas.emp_id =  agenda_gerenciar.age_empresa
													ORDER BY emp_fantasia";
											$stmt_cat = $PDO->prepare($sql);
											$stmt_cat->execute();
											while($result_cat = $stmt_cat->fetch())
											{
												echo "<option value='".$result_cat['emp_id']."'>".$result_cat['emp_fantasia']."</option>";
											}
											echo "
										</select>
										<p><label>Setor:</label> <select name='usu_setor' id='usu_setor'>
											<option value='$usu_setor'>$set_nome</option>";
											$sql = " SELECT * FROM admin_setores ORDER BY set_nome";
											$stmt = $PDO->prepare($sql);
											$stmt->execute();
											while($result = $stmt->fetch())
											{
												echo "<option value='".$result['set_id']."'>".$result['set_nome']."</option>";
											}
											echo "
										</select>
										<p><label>Vinculação:</label> <select name='usu_vinculacao' id='usu_vinculacao'>
											<option value='$usu_vinculacao'>$usu_vinculacao</option>
											<option value='Contratado'>Contratado</option>
											<option value='Freelancer'>Freelancer</option>
										</select>
										<p><label>Nome do Usuário:</label> <input name='usu_nome' id='usu_nome' value='$usu_nome' placeholder='Nome do Usuário'>
										<p><label>Data Nascimento:</label> <input name='usu_datanasc' id='usu_datanasc' value='$usu_datanasc' placeholder='Data Nascimento' onkeypress='return mascaraData(this,event);'>
										<p><label>Sexo:</label><select name='usu_sexo' id='usu_sexo'>
											<option value='$usu_sexo'>$usu_sexo</option>
											<option value='Masculino'>Masculino</option>
											<option value='Feminino'>Feminino</option>
										</select>
									</div>
									<p>
									<div class='quadro'>
										<div class='formtitulo'>Dados Profissionais</div>
										<p><label>Cargo:</label> <input name='usu_cargo' id='usu_cargo' value='$usu_cargo' placeholder='Cargo' />
										<p><label>Data Admissão:</label> <input name='usu_data_admissao' id='usu_data_admissao' value='$usu_data_admissao' placeholder='Data Admissão' onkeypress='return mascaraData(this,event);' />
										<label>Data Demissão:</label> <input name='usu_data_demissao' id='usu_data_demissao' value='$usu_data_demissao' placeholder='Data Demissão' onkeypress='return mascaraData(this,event);' />
									</div>
									<p>
									<div class='quadro'>
										<div class='formtitulo'>Endereço e Contato</div>
										<p><label>CEP:</label> <input name='usu_cep' id='usu_cep' value='$usu_cep' placeholder='CEP' maxlength='9' onkeypress='mascaraCEP(this); return SomenteNumero(event);' />
										<p><label>UF:</label> <select name='usu_uf' id='usu_uf'>
											<option value='$usu_uf'>$uf_sigla</option>
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
										<p><label>Município:</label><select name='usu_municipio' id='usu_municipio'>
											<option value='$usu_municipio'>$mun_nome</option>
										</select>
										<p><label>Bairro:</label><input name='usu_bairro' id='usu_bairro' value='$usu_bairro' placeholder='Bairro' />
										<p><label>Endereço:</label><input name='usu_endereco' id='usu_endereco' value='$usu_endereco' placeholder='Endereço' />
										<p><label>Número:</label><input name='usu_numero' id='usu_numero' value='$usu_numero' placeholder='Número' />
										<label>Complemento:</label><input name='usu_comp' id='usu_comp' value='$usu_comp' placeholder='Complemento' />
										<p><label>Telefone:</label><input name='usu_telefone' id='usu_telefone' value='$usu_telefone' placeholder='Telefone (c/ DDD)' onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);' />
										<p><label>Celular:</label><input name='usu_celular' id='usu_celular' value='$usu_celular' placeholder='Celular (c/ DDD)' onkeypress='mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);' />	
										<p><label>Email:</label><input name='usu_email' id='usu_email' value='$usu_email' placeholder='Email'>						
									</div>
									<p>
									<div class='quadro'>
										<div class='formtitulo'>Dados de Acesso</div>
										<p><label>Login:</label><input type='text' name='usu_login' id='usu_login' value='$usu_login' placeholder='Login' autocomplete='off'>
										<p><label>Senha:</label><input type='password' name='usu_senha' id='usu_senha' value='$usu_senha' placeholder='Senha'>
										<p><label>Status:</label>";
										if($usu_status == 1)
										{
											echo "<input type='radio' name='usu_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<input type='radio' name='usu_status' value='0'> Inativo
												";
										}
										else
										{
											echo "<input type='radio' name='usu_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<input type='radio' name='usu_status' value='0' checked> Inativo
												";
										}
										echo "
										<p><label>Recebe Notificação?:</label>";
										if($usu_notificacao == 1)
										{
											echo "<input type='radio' name='usu_notificacao' value='1' checked> Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<input type='radio' name='usu_notificacao' value='0'> Não
												";
										}
										else
										{
											echo "<input type='radio' name='usu_notificacao' value='1'> Sim  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<input type='radio' name='usu_notificacao' value='0' checked> Não
												";
										}
										echo "
										<p><label>Homologador?:</label>";
										if($usu_homologador == 1)
										{
											echo "<input type='radio' name='usu_homologador' value='1' checked> Sim &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<input type='radio' name='usu_homologador' value='0'> Não
												";
										}
										else
										{
											echo "<input type='radio' name='usu_homologador' value='1'> Sim  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<input type='radio' name='usu_homologador' value='0' checked> Não
												";
										}
										echo "
									<br><br>
									</div>
									
								</td>
							</tr>
						</table>
					</div>
					<div id='foto' class='tab-pane fade in'>
						<div class='formtitulo'>Foto do Perfil</div>
						<a id='box_foto_perfil'>";
						if($usu_foto != ''){echo "<img src='$usu_foto' border='0' width='250' />";}else{echo "<img src='../imagens/perfil.png' border='0' width='250' />";}
						echo "
						</a>
					</div>
					<div id='formacao' class='tab-pane fade in'>
						<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
							<tr>
								<td align='left'>
									<div id='p_scents_formacao'>
									";
									$sql = "SELECT * FROM admin_usuarios_formacao 
											WHERE for_usuario = :for_usuario";
									$stmt = $PDO->prepare($sql);
									$stmt->bindParam(':for_usuario', $usu_id);
									$stmt->execute();
									$rows = $stmt->rowCount();
									if($rows > 0)
									{
										$x=0;
										while($result = $stmt->fetch())
										{
											$x++;
											echo "
											<div class='bloco_formacao'>
												<input type='hidden' name='formacao[$x][for_id]' id='for_id' value='".$result['for_id']."'>
												"; if($x > 1){ echo "<br><br><hr><p>";}else{ echo "<br>";} 
												echo "
												<p><label>Formação:</label><input name='formacao[$x][for_formacao]' id='for_formacao' value='".$result['for_formacao']."' placeholder='Formação'>
												<p><label>Instituição de Ensino:</label>				<input name='formacao[$x][for_ies]' id='for_ies' value='".$result['for_ies']."' placeholder='Instituição de Ensino'>
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
												<p><img src='../imagens/icon-add.png' id='addformacao' title='Adicionar +' class='botao_dinamico'> <img src='../imagens/icon-rmv.png' id='remformacao' title='Remover' class='botao_dinamico'>
											</div>
											";
										}
									}
									else
									{
										echo "
										<div class='bloco_formacao'>
											<input type='hidden' name='formacao[1][for_id]' id='for_id'>
											<p><label>Formação:</label>				<input name='formacao[1][for_formacao]' id='for_formacao' placeholder='Formação'>
											<p><label>Tipo:</label> <select name='formacao[1][for_entidade]' id='for_entidade'>
												<option value=''>Tipo</option>
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
											<p><label>Data Vencimento:</label>					<input name='formacao[1][for_data_vcto]' class='datepicker' id='for_data_vcto' placeholder='Data Vencimento' onkeypress='return mascaraData(this,event);'>
											<p><img src='../imagens/icon-add.png' id='addformacao' title='Adicionar +' class='botao_dinamico'>
										</div>
										";
									}
									echo "
									</div>
								</td>
							</tr>
						</table>
					</div>
					<br><br>
					<center>
					<div id='erro' align='center'>&nbsp;</div>
					<input type='button' id='bt_admin_usuarios' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
					<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='admin_usuarios.php?pagina=admin_usuarios$autenticacao'; value='Cancelar'/></center>
					</center>
				</div>
				
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
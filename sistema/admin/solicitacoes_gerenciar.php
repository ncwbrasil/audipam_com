<?php
session_start (); 
$pagina_link = 'solicitacoes_gerenciar';
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
<script type="text/javascript" src="../mod_includes/js/tooltip.js"></script>
<script src="../mod_includes/js/funcoes.js"></script>
<!-- TOOLBAR -->
<link href="../mod_includes/js/toolbar/jquery.toolbars.css" rel="stylesheet" />
<link href="../mod_includes/js/toolbar/bootstrap.icons.css" rel="stylesheet">
<script src="../mod_includes/js/toolbar/jquery.toolbar.js"></script>
<!-- TOOLBAR -->
<!--   TimeLine   -->
<link rel="stylesheet" href="../mod_includes/js/timeline/style-timeline.css"> <!-- Resource style -->
<script src="../mod_includes/js/timeline/modernizr.js"></script> <!-- Modernizr -->
<script src="../mod_includes/js/timeline/main.js"></script> <!-- Modernizr -->
<!--   TimeLine   -->
<link rel="stylesheet" href="../mod_includes/js/janela/jquery-ui.css">
<script src="../mod_includes/js/janela/jquery-ui.js"></script>


</head>
<body>
<?php	
$sol_id = $_GET['sol_id'];
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
    $page = "Solicitações &raquo; <a href='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar".$autenticacao."'>Gerenciar</a>";
	$sts_id = $_GET['sts_id'];
	$sol_tipo 				= "Interna";
	$sol_cliente 			= $_POST['sol_cliente'];
	$sol_contrato 			= $_POST['sol_contrato'];
	$sol_item_contrato 		= $_POST['sol_item_contrato'];
	$sol_categoria	 		= $_POST['sol_categoria'];
	$sol_servico 			= $_POST['sol_servico'];
	$sol_breve_historico 	= $_POST['sol_breve_historico'];
	$sol_memorial 			= $_POST['sol_memorial'];
	$sol_data 				= implode("-",array_reverse(explode("/",$_POST['sol_data'])));
	$sol_hora 				= $_POST['sol_hora'];
	$dados = array_filter(array(
		'sol_tipo' 				=> $sol_tipo,
		'sol_cliente' 			=> $sol_cliente,
		'sol_interno' 			=> $_SESSION['usuario_id'],
		'sol_contrato' 			=> $sol_contrato,
		'sol_item_contrato' 	=> $sol_item_contrato,
		'sol_categoria' 		=> $sol_categoria,
		'sol_servico' 			=> $sol_servico,
		'sol_breve_historico' 	=> $sol_breve_historico,
		'sol_memorial' 			=> $sol_memorial,
		'sol_data_cadastro' 	=> $sol_data." ".$sol_hora
	));

# ADICIONAR NOVA SOLICITACAO
	if($action == "adicionar")
    {
        $sql = "INSERT INTO cliente_solicitacoes SET ".bindFields($dados);
		$stmt = $PDO->prepare($sql);	
        if($stmt->execute($dados))
        {		
			
			$sol_id = $PDO->lastInsertId();
			
			//UPLOAD ARQUIVOS
			$caminho = "../admin/solicitacoes_anexos/$sol_id/";
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
						$sol_anexo	= $caminho;
						$sol_anexo .= "anexo_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($sol_anexo));
						$sql = "UPDATE cliente_solicitacoes SET 
								sol_anexo 	 = :sol_anexo
								WHERE sol_id = :sol_id ";
						$stmt = $PDO->prepare($sql);
						$stmt->bindParam(':sol_anexo',$sol_anexo);
						$stmt->bindParam(':sol_id',$sol_id);
						if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
					}					
				}
			}
			//
			
			// STATUS SOLICITACAO
			$sql = "INSERT INTO cliente_status_solicitacoes  SET sts_solicitacao = :sts_solicitacao, sts_status = :sts_status ";
			$stmt = $PDO->prepare($sql);	
			$stmt->bindParam(':sts_solicitacao',$sol_id);
			$stmt->bindValue(':sts_status',1);
			//$stmt->bindParam(':sts_observacao',$sts_observacao);
			if($stmt->execute())
			{
				
				$sts_id = $PDO->lastInsertId();
				
				//UPLOAD ARQUIVOS
				$caminho = "../admin/status_anexos/$sts_id/";
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
							$sts_foto	= $caminho;
							$sts_foto .= "foto_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
							move_uploaded_file($nomeTemporario, ($sts_foto));
							$sql = "UPDATE cliente_status_solicitacoes SET 
									sts_foto 	 = :sts_foto
									WHERE sts_id = :sts_id ";
							$stmt = $PDO->prepare($sql);
							$stmt->bindParam(':sts_foto',$sts_foto);
							$stmt->bindParam(':sts_id',$sts_id);
							if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
						}					
					}
				}
			}else{ $erro=1; $err = $stmt->errorInfo();}
        }
		else{ $erro=1; $err = $stmt->errorInfo();}
		
      	if(!$erro)
		{
			echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../imagens/ok.png> Solicitação registrada com sucesso!<br><br>'+
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
	
# EDITAR SOLICITACAO
	if($action == "editar")
    {
		unset($dados['sol_tipo']);
		unset($dados['sol_cliente']);
		unset($dados['sol_interno']);
		unset($dados['sol_contrato']);
		unset($dados['sol_item_contrato']);
        $sql = "UPDATE cliente_solicitacoes SET ".bindFields($dados)." WHERE sol_id = :sol_id ";
		
		$stmt = $PDO->prepare($sql);
		$dados['sol_id'] = $sol_id;
		if($stmt->execute($dados))
        {					
        }
		else{ $erro=1; $err = $stmt->errorInfo();}		
      	if(!$erro)
		{
			echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../imagens/ok.png> Alteração realizada com sucesso!<br><br>'+
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

# EDITAR STATUS
	if($action == "editar_status")
    {
		$sts_id = $_GET['sts_id'];
		$sts_data 				= implode("-",array_reverse(explode("/",$_POST['sts_data'])));
		$sts_hora 				= $_POST['sts_hora'];
		$dados = array_filter(array(
			'sts_data' 				=> $sts_data." ".$sts_hora
		));
        $sql = "UPDATE cliente_status_solicitacoes SET ".bindFields($dados)." WHERE sts_id = :sts_id ";
		$stmt = $PDO->prepare($sql);
		$dados['sts_id'] = $sts_id;
		if($stmt->execute($dados))
        {					
        }
		else{ $erro=1; $err = $stmt->errorInfo();}		
      	if(!$erro)
		{
			echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../imagens/ok.png> Alteração realizada com sucesso!<br><br>'+
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

# AGENDAR SOLICITACAO	
	if($action == "agendar")
    {
		//$agi_agenda = 1;
		$agi_agenda = $_POST['agi_agenda'];
		$agi_data = implode("-",array_reverse(explode("/",$_POST['agi_data'])));
		$agi_horario_inicial = $_POST['agi_horario_inicial'];
		$agi_horario = $_POST['agi_horario'];
		$agi_forma_atendimento = $_POST['agi_forma_atendimento'];
		$agi_descricao = str_replace('"','',str_replace(array("\r\n", "\r", "\n"), "<br />", $_POST['agi_descricao']));
	
		$dados = array_filter(array(
			'agi_agenda' 			=> $agi_agenda,
			'agi_data' 				=> $agi_data,
			'agi_horario_inicial' 	=> $agi_horario_inicial,
			'agi_horario' 			=> $agi_horario,
			'agi_forma_atendimento' => $agi_forma_atendimento,
			'agi_descricao' 		=> $agi_descricao
		));
		
        $sql = "INSERT INTO agenda_gerenciar_itens SET ".bindFields($dados);
		$stmt = $PDO->prepare($sql);	
        if($stmt->execute($dados))
        {		
			$agi_id = $PDO->lastInsertId();
			// ATUALIZA STATUS SOLICITACAO
			$sql = "SELECT * FROM cliente_solicitacoes 
					LEFT JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_id = cliente_solicitacoes.sol_contato
					LEFT JOIN cliente_status_solicitacoes h1 ON h1.sts_solicitacao = cliente_solicitacoes.sol_id 
					WHERE h1.sts_id = (SELECT MAX(h2.sts_id) FROM cliente_status_solicitacoes h2 where h2.sts_solicitacao = h1.sts_solicitacao) AND
						  sol_id = :sol_id
					GROUP BY sol_id
					";
			$stmt = $PDO->prepare($sql);
			$stmt->bindParam(':sol_id', 	$sol_id);
			$stmt->execute();
			$rows = $stmt->rowCount();
			if($rows > 0)
			{
				$result = $stmt->fetch();
				$sol_id 	= $result['sol_id'];
				$sol_tipo 	= $result['sol_tipo'];
				$ano 		= $result['ano'];
				$ctt_email	= $result['ctt_email'];
				$ctt_nome	= $result['ctt_nome'];
				$sts_status	= $result['sts_status'];
				
				if($sts_status == 2 || $sts_status == 1)
				{
					$sql = "INSERT INTO cliente_status_solicitacoes SET 
							sts_solicitacao = :sol_id, 
							sts_status = :sts_status, 
							sts_observacao = :sts_observacao,
							sts_usuario = :sts_usuario";
					$stmt = $PDO->prepare($sql);	
					$sts_observacao = "Agendado para ".$_POST['agi_data']." às ".$_POST['agi_horario_inicial']."";
					$stmt->bindParam(':sol_id', $sol_id);
					$stmt->bindValue(':sts_status', 3);
					$stmt->bindParam(':sts_observacao', $sts_observacao);
					$stmt->bindParam(':sts_usuario', $_SESSION['usuario_id']);
					if($stmt->execute())
					{
						//echo "Inserido <br>";
						
						// ENVIA EMAIL
						if($sol_tipo == "Externa")
						{ include("../mail/admin_envia_status_atualizado.php"); }
					}
					else{ $erro=1; $err = $stmt->errorInfo();}	
				}
			}
			
			//UPLOAD ARQUIVOS
			$caminho = "../admin/agenda_anexos/$agi_id/";
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
						$agi_anexo	= $caminho;
						$agi_anexo .= "anexo_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($agi_anexo));
						$sql = "UPDATE agenda_gerenciar_itens SET 
								agi_anexo 	 = :agi_anexo
								WHERE agi_id = :agi_id ";
						$stmt = $PDO->prepare($sql);
						$stmt->bindParam(':agi_anexo',$agi_anexo);
						$stmt->bindParam(':agi_id',$agi_id);
						if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
					}					
				}
			}
			//
			
			//TECNICOS - CAMPOS DINÂMICOS
			if(!empty($_POST['tecnico']) && is_array($_POST['tecnico']))
			{
				//LIMPA ARRAY
				foreach($_POST['tecnico'] as $item => $valor) 
				{
					$tecnico_filtrado[$item] = array_filter($valor);
				}
				//
				foreach($tecnico_filtrado as $item => $valor) 
				{
					if(!empty($valor))
					{
						$valor['agu_agenda_item'] = $agi_id;
						$sql = "INSERT INTO agenda_gerenciar_usuario SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//INSERE
							
							// ALERTA
							$sql = "INSERT INTO social_alertas (
									ale_usuario, 
									ale_descricao, 
									ale_lida, 
									ale_arquivado,
									ale_link) VALUES (
									:ale_usuario, 
									:ale_descricao, 
									:ale_lida,
									:ale_arquivado,
									:ale_link
									) ";
							$stmt_alerta = $PDO->prepare($sql);	
							$ale_descricao = "Você tem uma nova tarefa agendada para ".$_POST['agi_data']." às ".$agi_horario_inicial." <b>".$agi_cliente_nome."</b>";
							$ale_link = "social_agenda.php?pagina=agenda";
							$stmt_alerta->bindParam(':ale_usuario',$valor['agu_usuario']);
							$stmt_alerta->bindParam(':ale_descricao',$ale_descricao);
							$stmt_alerta->bindValue(':ale_lida',0);
							$stmt_alerta->bindValue(':ale_arquivado',0);
							$stmt_alerta->bindParam(':ale_link',$ale_link);
							if($stmt_alerta->execute())
							{		
							
								$sql_usu = "SELECT usu_email, usu_nome FROM admin_usuarios WHERE usu_id = :usu_id";
								$stmt_usu = $PDO->prepare($sql_usu);	
								$stmt_usu->bindParam(':usu_id',$valor['agu_usuario']);
								$stmt_usu->execute();
								$rows_usu = $stmt_usu->rowCount();
								if($rows_usu > 0)
								{
									$result = $stmt_usu->fetch();
									$usu_email = $result['usu_email'];
									$usu_nome = $result['usu_nome'];
								}
							
								include("../mail/admin_envia_tarefa_tecnico.php");
							}
							else{ $erro=1; $err = $stmt_alerta->errorInfo();}
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
				}
			}
			
			//VEÍCULOS - CAMPOS DINÂMICOS			
			if(!empty($_POST['veiculo']) && is_array($_POST['veiculo']))
			{
				//LIMPA ARRAY
				foreach($_POST['veiculo'] as $item => $valor) 
				{
					$veiculo_filtrado[$item] = array_filter($valor);
				}
				//
				foreach($veiculo_filtrado as $item => $valor) 
				{		
					if(!empty($valor))
					{	
					
						$valor['agc_agenda_item'] = $agi_id;
						$sql = "INSERT INTO agenda_gerenciar_carro SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//INSERE
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
				}
			}
			
			//SERVIDORES - CAMPOS DINÂMICOS
			if(!empty($_POST['servidor']) && is_array($_POST['servidor']))
			{
				//LIMPA ARRAY
				foreach($_POST['servidor'] as $item => $valor) 
				{
					$servidor_filtrado[$item] = array_filter($valor);
				}
				//
				foreach($servidor_filtrado as $item => $valor) 
				{
					if(!empty($valor))
					{
						$valor['ags_agenda_item'] = $agi_id;
						$sql = "INSERT INTO agenda_gerenciar_servidores SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//INSERE														
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
				}
			}
			
			
			
			// VINCULA SOLICITACAO -> AGENDA
			$sql = "INSERT INTO cliente_solicitacoes_agenda SET soa_solicitacao = :sol_id, soa_agenda_item = :agi_id";
			$stmt = $PDO->prepare($sql);	
			$stmt->bindParam(':sol_id', $sol_id);
			$stmt->bindParam(':agi_id', $agi_id);
			if($stmt->execute())
			{
				//echo "Inserido <br>";
			}
			else{ $erro=1; $err = $stmt->errorInfo();}	
		}
		else
		{
			$erro=1;
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
	
	if($action == 'excluir_status')
    {
       	$sts_id = $_GET['sts_id'];
		$sql = "DELETE FROM cliente_status_solicitacoes WHERE sts_id = :sts_id";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':sts_id',$sts_id);
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
    
# ADICIONAR NOVO STATUS
	if($action == "novo_status")
    {
		$sts_solicitacao = $sol_id;
		$sts_status 		= $_POST['sts_status'];
		$sts_observacao 	= $_POST['sts_observacao'];
		$sts_fb_tecnico 	= $_POST['sts_fb_tecnico'];
		$sts_fb_servidor 	= $_POST['sts_fb_servidor'];
		$sts_fb_data 		= implode("-",array_reverse(explode("/",$_POST['sts_fb_data'])));
		$sts_fb_hora 		= $_POST['sts_fb_hora'];
		$sts_fb_forma_atendimento = $_POST['sts_fb_forma_atendimento'];
		$sts_usuario = $_SESSION['usuario_id'];
		
		$dados = array_filter(array(
			'sts_solicitacao' 	=> $sts_solicitacao,
			'sts_status' 		=> $sts_status,
			'sts_observacao' 	=> $sts_observacao,
			'sts_usuario' 		=> $sts_usuario,
			'sts_fb_tecnico' 	=> $sts_fb_tecnico,
			'sts_fb_servidor' 	=> $sts_fb_servidor,
			'sts_fb_data' 		=> $sts_fb_data,
			'sts_fb_hora' 		=> $sts_fb_hora,
			'sts_fb_forma_atendimento' 	=> $sts_fb_forma_atendimento
		));
        $sql = "INSERT INTO cliente_status_solicitacoes SET ".bindFields($dados);
		$stmt = $PDO->prepare($sql);	
        if($stmt->execute($dados))
        {	
			$sts_id = $PDO->lastInsertId();
			
			//UPLOAD ARQUIVOS
			$caminho = "../admin/status_anexos/$sts_id/";
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
						$sts_anexo	= $caminho;
						$sts_anexo .= "anexo_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($sts_anexo));
						$sql = "UPDATE cliente_status_solicitacoes SET 
								sts_anexo 	 = :sts_anexo
								WHERE sts_id = :sts_id ";
						$stmt = $PDO->prepare($sql);
						$stmt->bindParam(':sts_anexo',$sts_anexo);
						$stmt->bindParam(':sts_id',$sts_id);
						if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
					}					
				}
			}

			foreach($_FILES as $key => $files)
			{
				$files_test = array_filter($files['name']);				
				if(!empty($files_test))
				{
					if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
					if(!empty($files["name"]["anexo2"]))
					{
						$nomeArquivo 	= $files["name"]["anexo2"];
						$nomeTemporario = $files["tmp_name"]["anexo2"];
						$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
						$sts_anexo2	= $caminho;
						$sts_anexo2 .= "anexo_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($sts_anexo2));
						$sql = "UPDATE cliente_status_solicitacoes SET 
								sts_anexo2 	 = :sts_anexo2
								WHERE sts_id = :sts_id ";
						$stmt = $PDO->prepare($sql);
						$stmt->bindParam(':sts_anexo2',$sts_anexo2);
						$stmt->bindParam(':sts_id',$sts_id);
						if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
					}					
				}
			}

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
						$sts_foto	= $caminho;
						$sts_foto .= "foto_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($sts_foto));
						$sql = "UPDATE cliente_status_solicitacoes SET 
								sts_foto 	 = :sts_foto
								WHERE sts_id = :sts_id ";
						$stmt = $PDO->prepare($sql);
						$stmt->bindParam(':sts_foto',$sts_foto);
						$stmt->bindParam(':sts_id',$sts_id);
						if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
					}					
				}
			}

			foreach($_FILES as $key => $files)
			{
				$files_test = array_filter($files['name']);				
				if(!empty($files_test))
				{
					if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
					if(!empty($files["name"]["foto2"]))
					{
						$nomeArquivo 	= $files["name"]["foto2"];
						$nomeTemporario = $files["tmp_name"]["foto2"];
						$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
						$sts_foto2	= $caminho;
						$sts_foto2 .= "foto_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($sts_foto2));
						$sql = "UPDATE cliente_status_solicitacoes SET 
								sts_foto2 	 = :sts_foto2
								WHERE sts_id = :sts_id ";
						$stmt = $PDO->prepare($sql);
						$stmt->bindParam(':sts_foto2',$sts_foto2);
						$stmt->bindParam(':sts_id',$sts_id);
						if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
					}					
				}
			}
			//
		
			
			$sql = "SELECT * FROM cliente_solicitacoes 
					INNER JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_id = cliente_solicitacoes.sol_contato
					WHERE sol_id = :sol_id";
			$stmt = $PDO->prepare($sql);	
			$stmt->bindParam(':sol_id', $sol_id);
			$stmt->execute();
			$rows = $stmt->rowCount();
			if($rows > 0)
			{
				$result = $stmt->fetch();
				$sol_id 	= $result['sol_id'];
				$sol_tipo	= $result['sol_tipo'];
				$ano 		= $result['ano'];
				$ctt_email	= $result['ctt_email'];
				$ctt_nome	= $result['ctt_nome'];
				if($sol_tipo == "Externa")
				{ include("../mail/admin_envia_status_atualizado.php");}
			}
			
			if($sts_status == 6)
			{
				// ALERTA
				$sql = "SELECT * FROM admin_usuarios
						WHERE usu_homologador = :usu_homologador AND usu_status = :usu_status ";
				$stmt = $PDO->prepare($sql);
				$email = '';
				$stmt->bindValue(":usu_homologador",1);
				$stmt->bindValue(":usu_status",1);
				$stmt->execute();
				$rows = $stmt->rowCount();
				if($rows > 0)
				{
					while($result = $stmt->fetch())
					{
						$sql = "INSERT INTO social_alertas (
								ale_usuario, 
								ale_descricao, 
								ale_lida, 
								ale_arquivado,
								ale_link) VALUES (
								:ale_usuario, 
								:ale_descricao, 
								:ale_lida,
								:ale_arquivado,
								:ale_link
								) ";
						$stmt_alerta = $PDO->prepare($sql);	
						$ale_descricao = "Você tem uma nova solicitação para homologar, protocolo N° ".$ano.".".str_pad($sol_id,6,0,STR_PAD_LEFT)."";
						$ale_link = "solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar_exibir&sol_id=$sol_id";
						$stmt_alerta->bindParam(':ale_usuario',$result['usu_id']);
						$stmt_alerta->bindParam(':ale_descricao',$ale_descricao);
						$stmt_alerta->bindValue(':ale_lida',0);
						$stmt_alerta->bindValue(':ale_arquivado',0);
						$stmt_alerta->bindParam(':ale_link',$ale_link);
						if($stmt_alerta->execute())
						{		
						}
						else{ $erro=1; $err = $stmt_alerta->errorInfo();}
					}
				}
			}
			
        }
		else{ $erro=1; $err = $stmt->errorInfo();}
		
      	if(!$erro && $email_enviado == 1)
		{
			echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../imagens/ok.png> Alteração de status OK. <br>'+
				'<img src=../imagens/ok.png> Foi enviado um email para o cliente informando sobre a atualização de status.<br><br>'+
				'<input value=\' Ok \' type=\'button\' class=\'close_janela\'>' );
			</SCRIPT>
				";
		}
		elseif(!$erro && !$email_enviado)
		{
			echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../imagens/ok.png> Alteração de status OK. <br>'+
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
	
	if($action == "homologacao")
    {
		$sts_id = $_GET['sts_id'];
		$sts_solicitacao = $sol_id;
		$sts_fb_tecnico 	= $_POST['sts_fb_tecnico'];
		$sts_fb_servidor 	= $_POST['sts_fb_servidor'];
		$sts_fb_data 		= implode("-",array_reverse(explode("/",$_POST['sts_fb_data'])));
		$sts_fb_hora 		= $_POST['sts_fb_hora'];
		$sts_fb_forma_atendimento = $_POST['sts_fb_forma_atendimento'];
		$sts_usuario = $_SESSION['usuario_id'];
		
		$dados = array_filter(array(			
			'sts_usuario' 		=> $sts_usuario,
			'sts_fb_tecnico' 	=> $sts_fb_tecnico,
			'sts_fb_servidor' 	=> $sts_fb_servidor,
			'sts_fb_data' 		=> $sts_fb_data,
			'sts_fb_hora' 		=> $sts_fb_hora,
			'sts_fb_forma_atendimento' 	=> $sts_fb_forma_atendimento
		));
        $sql = "UPDATE cliente_status_solicitacoes SET ".bindFields($dados)." WHERE sts_id = $sts_id ";
		$stmt = $PDO->prepare($sql);		
        if($stmt->execute($dados))
        {			
			
        }
		else{ $erro=1; $err = $stmt->errorInfo();}
		
      	if(!$erro && $email_enviado == 1)
		{
			echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../imagens/ok.png> Alteração de status OK. <br>'+
				'<img src=../imagens/ok.png> Foi enviado um email para o cliente informando sobre a atualização de status.<br><br>'+
				'<input value=\' Ok \' type=\'button\' class=\'close_janela\'>' );
			</SCRIPT>
				";
		}
		elseif(!$erro && !$email_enviado)
		{
			echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../imagens/ok.png> Alteração de status OK. <br>'+
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
	
	if($action == "envia_whatsapp")
    {
		$sol_id = $_GET['sol_id'];
		$user = $_POST['user'];		
		$sql = "SELECT * FROM admin_usuarios
				WHERE usu_id = :usu_id				
				";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':usu_id', 	$user);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
		{
			while($result = $stmt->fetch())
			{
				$usu_celular = $result['usu_celular'];
				$usu_nome 	= $result['usu_nome'];
				
			}
		}

		$sql = "SELECT *, Year(sol_data_cadastro) as ano FROM cliente_solicitacoes 
				LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cliente_solicitacoes.sol_cliente
				LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = cliente_solicitacoes.sol_interno			
				LEFT JOIN cliente_status_solicitacoes h1 ON h1.sts_solicitacao = cliente_solicitacoes.sol_id 
				WHERE h1.sts_id = (SELECT MAX(h2.sts_id) FROM cliente_status_solicitacoes h2 where h2.sts_solicitacao = h1.sts_solicitacao) AND
					sol_id = :sol_id
				GROUP BY sol_id
				";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':sol_id', 	$sol_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
		{
			while($result = $stmt->fetch())
			{
				switch($sts_status)
				{
					case 1 : $sts_status = "Registrado";break;
					case 2 : $sts_status = "Em Análise";break;
					case 3 : $sts_status = "Agendado";break;
					case 4 : $sts_status = "Em Execução";break;
					case 5 : $sts_status = "Cancelado";break;
					case 6 : $sts_status = "Em Homologação";break;
					case 7 : $sts_status = "Concluído";break;
				}				
				$protocolo = $result['ano'].".".str_pad($result['sol_id'],6,"0",STR_PAD_LEFT);
				$cliente = $result['emp_fantasia'];
				$obs 	= urlencode($result['sts_observacao']);
				
				?>
				<script>
					window.open("https://api.whatsapp.com/send?phone=55<?php echo $usu_celular;?>&text=Olá%20*<?php echo $usu_nome;?>*,%20segue%20o%20último%20status%20da%20solicitação%20abaixo:%0A%0A*Protocolo:*%20_<?php echo $protocolo;?>_%0A*Cliente:*%20_<?php echo $cliente;?>_%0A*Status:*%20<?php echo $obs;?>","_blank");
				</script>
				<?php				
			}
		}
		
		
      	if(!$erro)
		{
			echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../imagens/ok.png> Comando enviado para whatsapp com sucesso!<br>Lembre-se de permitir Popups caso seu navegador pergunte.<br><br>'+
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
		$nome_query = " (a2.emp_nome_razao LIKE :fil_nome1 OR a2.emp_fantasia LIKE :fil_nome2 ) ";
	}
	$fil_protocolo = $_REQUEST['fil_protocolo'];
	if($fil_protocolo == '')
	{
		$protocolo_query = " 1 = 1 ";
	}
	else
	{
		$fil_protocolo1 = "%".$fil_protocolo."%";
		$protocolo_query = " (sol_id LIKE :fil_protocolo) ";
	}
	$fil_data_inicio = implode('-',array_reverse(explode('/',$_REQUEST['fil_data_inicio'])));
	$fil_data_fim = implode('-',array_reverse(explode('/',$_REQUEST['fil_data_fim'])));
	if($fil_data_inicio == '' && $fil_data_fim == '')
	{
		$data_query = " 1 = 1 ";
	}
	elseif($fil_data_inicio != '' && $fil_data_fim == '')
	{
		
		$data_query = " sol_data_cadastro >= :fil_data_inicio ";
	}
	elseif($fil_data_inicio == '' && $fil_data_fim != '')
	{
		$fil_data_fim_h = $fil_data_fim." 23:59:59";
		$data_query = " sol_data_cadastro <= :fil_data_fim ";
	}
	elseif($fil_data_inicio != '' && $fil_data_fim != '')
	{
			$fil_data_fim_h = $fil_data_fim." 23:59:59";
		$data_query = " sol_data_cadastro BETWEEN :fil_data_inicio AND :fil_data_fim ";
	}
	$fil_status = $_REQUEST['fil_status'];
	if($fil_status == '')
	{
		$status_query = " 1 = 1 ";
		$fil_status_n = "Status";
	}
	else
	{
		$status_query = " (sts_status = :fil_status) ";
		switch($fil_status)
		{
			case 1 : $fil_status_n = "Registrado";break;
			case 2 : $fil_status_n = "Em Análise";break;
			case 3 : $fil_status_n = "Agendado";break;
			case 4 : $fil_status_n = "Em Execução";break;
			case 5 : $fil_status_n = "Cancelado";break;
			case 6 : $fil_status_n = "Em Homologação";break;
			case 7 : $fil_status_n = "Concluído";break;
		}
	}
	$fil_categoria = $_REQUEST['fil_categoria'];
	if($fil_categoria == '')
	{
		$categoria_query = " 1 = 1 ";
		$fil_categoria_n = "Categoria";
	}
	else
	{
		$categoria_query = " (sol_categoria = :fil_categoria) ";
		switch($fil_categoria)
		{
			case 1 : $fil_categoria_n = "Prazos / Notificações / Calendário de Obrigações";break;
			case 2 : $fil_categoria_n = "Assessoramento / Participação em Licitação";break;
			case 3 : $fil_categoria_n = "Treinamento / Capacitação / Workshop";break;
			case 4 : $fil_categoria_n = "Reunião de Trabalho";break;
			case 5 : $fil_categoria_n = "Gestão de Contratos";break;
			case 6 : $fil_categoria_n = "Pareceres & Estudos Técnicos";break;
			case 7 : $fil_categoria_n = "Atendimento Técnico Presencial";break;
			case 8 : $fil_categoria_n = "Suporte Técnico à Distância";break;
			case 9 : $fil_categoria_n = "Digitalização e Gestão Eletrônica de Documentos";break;
			case 10 : $fil_categoria_n = "Atualização de Base de Dados / Manutenção de Software";break;
			case 11 : $fil_categoria_n = "Prospecção";break;
		}
	}
	
    $sql = "SELECT *, Year(sol_data_cadastro) as ano, a1.emp_logo as logo_contratado, a2.emp_logo as emp_logo, a2.emp_fantasia as emp_fantasia FROM cliente_solicitacoes 
			LEFT JOIN ( cadastro_contratos 
				LEFT JOIN cadastro_contratos_gestor ON cadastro_contratos_gestor.ges_contrato = cadastro_contratos.con_id 
				LEFT JOIN cadastro_contratos_itens ON cadastro_contratos_itens.ite_contrato = cadastro_contratos.con_id
				LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
				LEFT JOIN aux_servicos ON aux_servicos.ser_id = cadastro_contratos.con_servico )
			ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato
			LEFT JOIN aux_categoria_solicitacao ON aux_categoria_solicitacao.cas_id = cliente_solicitacoes.sol_categoria
			LEFT JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_id = cliente_solicitacoes.sol_contato
			LEFT JOIN cadastro_empresas a2 ON a2.emp_id = cliente_solicitacoes.sol_cliente
			LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = cliente_solicitacoes.sol_interno
			LEFT JOIN (cliente_solicitacoes_agenda 
				LEFT JOIN (agenda_gerenciar_itens 
					LEFT JOIN agenda_gerenciar_usuario ON agenda_gerenciar_usuario.agu_agenda_item = agenda_gerenciar_itens.agi_id
					LEFT JOIN ( agenda_gerenciar 
						LEFT JOIN cadastro_empresas a1 ON a1.emp_id = agenda_gerenciar.age_empresa)
					ON agenda_gerenciar.age_id = agenda_gerenciar_itens.agi_agenda)
				ON agenda_gerenciar_itens.agi_id =  cliente_solicitacoes_agenda.soa_agenda_item)
			ON cliente_solicitacoes_agenda.soa_solicitacao = cliente_solicitacoes.sol_id
			LEFT JOIN cliente_status_solicitacoes h1 ON h1.sts_solicitacao = cliente_solicitacoes.sol_id 
			WHERE h1.sts_id = (SELECT MAX(h2.sts_id) FROM cliente_status_solicitacoes h2 where h2.sts_solicitacao = h1.sts_solicitacao) AND
				  ".$protocolo_query." AND ".$nome_query." AND ".$data_query." AND ".$status_query." AND ".$categoria_query."
			GROUP BY sol_id
			ORDER BY sol_id DESC
            LIMIT :primeiro_registro, :num_por_pagina ";
  	$stmt = $PDO->prepare($sql);
	$stmt->bindParam(':fil_protocolo', 	$fil_protocolo1);
	$stmt->bindParam(':fil_nome1', 	$fil_nome1);
	$stmt->bindParam(':fil_nome2', 	$fil_nome2);
	$stmt->bindParam(':fil_data_inicio', 	$fil_data_inicio);
	$stmt->bindParam(':fil_data_fim', 		$fil_data_fim_h);
	$stmt->bindParam(':fil_status', 		$fil_status);
	$stmt->bindParam(':fil_categoria', 		$fil_categoria);
	$stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
	$stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
	$stmt->execute();
    $rows = $stmt->rowCount();
	
	if($pagina == "solicitacoes_gerenciar")
    {
        echo "
		<div class='titulo'> $page  </div>
		<div id='botoes'><input value='Nova Solicitação' type='button' onclick=javascript:window.location.href='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar_adicionar".$autenticacao."'; /></div>
		<div class='filtro'>
			<form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar".$autenticacao."'>
			<input name='fil_protocolo' id='fil_protocolo' value='$fil_protocolo' placeholder='Protocolo'>
			<input name='fil_nome' id='fil_nome' value='$fil_nome' placeholder='Nome'>
			<input type='text' name='fil_data_inicio' id='fil_data_inicio' placeholder='Data Início' value='".implode('/',array_reverse(explode('-',$fil_data_inicio)))."' onkeypress='return mascaraData(this,event);'>
			<input type='text' name='fil_data_fim' id='fil_data_fim' placeholder='Data Fim' value='".implode('/',array_reverse(explode('-',$fil_data_fim)))."' onkeypress='return mascaraData(this,event);'>
			<select name='fil_status' id='fil_status'>
				<option value='$fil_status'>$fil_status_n</option>
				<option value='1'>Registrado</option>
				<option value='2'>Em Análise</option>
				<option value='3'>Agendado</option>
				<option value='4'>Em Execução</option>
				<option value='5'>Cancelado</option>
				<option value='6'>Em Homologação</option>
				<option value='7'>Concluído</option>													
			</select>
			<select name='fil_categoria' id='fil_categoria'>
				<option value='$fil_categoria'>$fil_categoria_n</option>
				"; 
				$sql = " SELECT * FROM aux_categoria_solicitacao ORDER BY cas_id";
				$stmt_cat = $PDO->prepare($sql);
				$stmt_cat->execute();
				while($result_cat = $stmt_cat->fetch())
				{
					echo "<option value='".$result_cat['cas_id']."'>".$result_cat['cas_descricao']."</option>";
				}
				echo "													
			</select>
			<input type='submit' value='Filtrar'> 
			</form>
		</div>
		";
		if ($rows > 0)
		{
			echo "
			<table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
				<tr>
					<td class='titulo_first'>Protocolo</td>					
					<td class='titulo_tabela'>Contratado</td>
					<td class='titulo_tabela' align='center' width='1'>Cliente</td>
					<td class='titulo_tabela'></td>
					<td class='titulo_tabela'>Solicitante</td>
					<td class='titulo_tabela' align='center'>Responsável</td>
					<td class='titulo_tabela' align='center'>Status</td>
					<td class='titulo_tabela' align='center'>Categoria</td>
					<td class='titulo_tabela' align='center'>Feedback</td>
					";
					if($_SESSION['usuario_id'] == 1 || $_SESSION['usuario_id'] == 2)
					{
						echo "<td class='titulo_tabela' align='center'>Views</td>";
					}
					echo "
					<td class='titulo_tabela' align='center'>Wpp</td>					
					<td class='titulo_tabela' align='center'>Imprimir</td>					
					<td class='titulo_last' align='center'>Gerenciar</td>
				</tr>";
				$c=0;
				while($result = $stmt->fetch())
				{
					$sol_id 			= $result['sol_id'];
					$ano 				= $result['ano'];
					$emp_tipo 			= $result['emp_tipo'];
					$emp_nome_razao 	= $result['emp_nome_razao'];
					$emp_fantasia 		= $result['emp_fantasia'];
					$con_objeto 		= $result['con_objeto'];
					$sol_tipo 			= $result['sol_tipo'];
					$cas_icone 			= $result['cas_icone'];
					$cas_descricao 			= $result['cas_descricao'];
					$solicitante 		= $result['ctt_nome'].$result['usu_nome'];
					$sol_data 			= implode("/",array_reverse(explode("-",substr($result['sol_data_cadastro'],0,10))));
					$sol_hora 			= substr($result['sol_data_cadastro'],11,5);
					$sol_breve_historico= truncate($result['sol_breve_historico'],100);
					$sts_data 			= implode("/",array_reverse(explode("-",substr($result['sts_data'],0,10))));
					$sts_fb_tecnico 	= $result['sts_fb_tecnico'];
					$sts_status			= $result['sts_status'];
					$sts_id				= $result['sts_id'];
					switch($sts_status)
					{
						case 1 : $sts_status = "Registrado";break;
						case 2 : $sts_status = "Em Análise";break;
						case 3 : $sts_status = "Agendado";break;
						case 4 : $sts_status = "Em Execução";break;
						case 5 : $sts_status = "Cancelado";break;
						case 6 : $sts_status = "Em Homologação";break;
						case 7 : $sts_status = "Concluído";break;
					}
					$emp_logo 			= $result['emp_logo'];
					if($emp_logo == '')
					{
						$emp_logo = '../imagens/nophoto.png';
					}
					if($sts_fb_tecnico == '')
					{
						if($_SESSION['homologador'] == 1 && $sts_status == "Concluído")
						{
							$feedback = "<a title='Homologar' href='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar_homologar&sol_id=$sol_id&sts_id=$sts_id$autenticacao'><i class='fas fa-check-square' style='color: #F00; font-size:25px;'></i></a>";							
						}
						else
						{
							$feedback = '<i class="fas fa-check-square" style="color: #F00; font-size:25px;"></i>';
						}						
					}
					else
					{
						$feedback = '<i class="fas fa-check-square" style="color: green; font-size:25px;"></i>';
					}
					$logo_contratado 			= $result['logo_contratado'];
					$sql = "SELECT * FROM agenda_gerenciar_usuario 
							INNER JOIN admin_usuarios ON admin_usuarios.usu_id = agenda_gerenciar_usuario.agu_usuario
							INNER JOIN (agenda_gerenciar_itens 
								INNER JOIN cliente_solicitacoes_agenda ON cliente_solicitacoes_agenda.soa_agenda_item = agenda_gerenciar_itens.agi_id)
							ON agenda_gerenciar_itens.agi_id =  agenda_gerenciar_usuario.agu_agenda_item
							WHERE soa_solicitacao = :soa_solicitacao AND agu_responsavel = :agu_responsavel
							";
					$stmt_responsavel = $PDO->prepare($sql);
					$agu_responsavel = "Sim";
					$stmt_responsavel->bindParam(':soa_solicitacao', 	$sol_id);
					$stmt_responsavel->bindParam(':agu_responsavel', 	$agu_responsavel);
					$stmt_responsavel->execute();
					$rows_responsavel = $stmt_responsavel->rowCount();
					$responsavel = "";
					if($rows_responsavel > 0)
					{
						while($result_responsavel = $stmt_responsavel->fetch())
						{
							$responsavel = $result_responsavel['usu_nome'];
						}
					}


					// MONTA LOG DE ACESSO
					$sql = "SELECT COUNT(log_id) as qtd, usu_nome, usu_foto FROM log_solicitacoes_usuarios 
							INNER JOIN admin_usuarios ON admin_usuarios.usu_id = log_solicitacoes_usuarios.log_usuario						
							INNER JOIN cliente_solicitacoes ON cliente_solicitacoes.sol_id = log_solicitacoes_usuarios.log_solicitacao
							WHERE log_solicitacao = :log_solicitacao
							GROUP BY log_usuario
							ORDER BY qtd DESC
							";
					$stmt_log = $PDO->prepare($sql);
					$stmt_log->bindParam(':log_solicitacao', 	$sol_id);
					$stmt_log->execute();
					$rows_log = $stmt_log->rowCount();	
					$log = "";				
					if($rows_log > 0)
					{
						while($result_log = $stmt_log->fetch())
						{
							$log .= "<img src='".$result_log['usu_foto']."' class='foto_perfil' valign='middle' style='width:30px'> ".$result_log['qtd']." <p> ";						
						}
					}					
					
					if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;}  
					echo "
					<script type='text/javascript'>
						jQuery(document).ready(function($) {
					
							// Define any icon actions before calling the toolbar
							$('.toolbar-icons a').on('click', function( event ) {
								$(this).click();
								
							});
							$('#normal-button-$sol_id').toolbar({content: '#user-options-$sol_id', position: 'top', hideOnClick: true});
							$('#normal-button-bottom').toolbar({content: '#user-options', position: 'bottom'});
							$('#normal-button-small').toolbar({content: '#user-options-small', position: 'top', hideOnClick: true});
							$('#button-left').toolbar({content: '#user-options', position: 'left'});
							$('#button-right').toolbar({content: '#user-options', position: 'right'});
							$('#link-toolbar').toolbar({content: '#user-options', position: 'top' });
						});
					</script>
					<div id='user-options-$sol_id' class='toolbar-icons' style='display: none;'>
						<a title='Editar' href='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar_editar&sol_id=$sol_id$autenticacao'><img border='0' src='../imagens/icon-editar.png' ></a>						
					</div>
					";
					
					echo "<tr class='$c1'>
							  <td><a href='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar_exibir&sol_id=$sol_id$autenticacao' style='font-size:16px;'>$ano.$sol_id</a></td>
							 
							 ";
							 ?>
							 <td><img src="<?php echo $logo_contratado;?>" width="50" ></td>
							 <td><img src="<?php echo $emp_logo;?>" width="50"></div></td>
							 <?php echo "
							  <td>$emp_fantasia<br><span class='detalhe'>$con_objeto<br>$sol_breve_historico</span></td>
							  <td>$solicitante</td>
							  <td  align=center>$responsavel</td> 
							  <td align=center>$sts_status<br><span class='detalhe'>$sts_data</span></td>
							  <td align=center><img src='".$cas_icone."' width='25' title='".$cas_descricao."'></td>
							  <td align=center>$feedback</td>
							  ";
							  
							if($_SESSION['usuario_id'] == 1 || $_SESSION['usuario_id'] == 2)
							{
								echo "
							  <td align='center'><i class='far fa-eye' style='font-size:20px;' ";if($log != ''){ echo "onmouseover=\"toolTip('".addslashes($log)."');\"";} echo " onmouseout=\"toolTip();\"></i></td>
							  ";
							}
							echo "
							<td align='center'><i class='fab fa-whatsapp' style='font-size:25px; color:green;' onclick='window_whatsapp(\"".$result['sol_id']."\",\"".$autenticacao."\");'></td>
							<td align='center'><i class='fas fa-print' style='font-size:25px;' onclick=javascript:window.open('relatorio_individual_imprimir.php?sol_id=".$result['sol_id']."$autenticacao'); ></td>
							<td align=center><div id='normal-button-$sol_id' class='settings-button'><img src='../imagens/icon-cog-small.png' /></div></td>
						  </tr>";
				}
				echo "</table>";
				$variavel = "&pagina=solicitacoes_gerenciar&fil_protocolo=$fil_protocolo&fil_nome=$fil_nome&fil_data_inicio=$fil_data_inicio&fil_data_fim=$fil_data_fim".$autenticacao."";
				
				$cnt = "SELECT COUNT(*) FROM cliente_solicitacoes 
						LEFT JOIN aux_categoria_solicitacao ON aux_categoria_solicitacao.cas_id = cliente_solicitacoes.sol_categoria
						LEFT JOIN cadastro_empresas a2 ON a2.emp_id = cliente_solicitacoes.sol_cliente
						LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = cliente_solicitacoes.sol_interno
						LEFT JOIN (cliente_solicitacoes_agenda 
							LEFT JOIN (agenda_gerenciar_itens 
								LEFT JOIN agenda_gerenciar_usuario ON agenda_gerenciar_usuario.agu_agenda_item = agenda_gerenciar_itens.agi_id
								LEFT JOIN ( agenda_gerenciar 
									LEFT JOIN cadastro_empresas a1 ON a1.emp_id = agenda_gerenciar.age_empresa)
								ON agenda_gerenciar.age_id = agenda_gerenciar_itens.agi_agenda)
							ON agenda_gerenciar_itens.agi_id =  cliente_solicitacoes_agenda.soa_agenda_item)
						ON cliente_solicitacoes_agenda.soa_solicitacao = cliente_solicitacoes.sol_id
						LEFT JOIN cliente_status_solicitacoes h1 ON h1.sts_solicitacao = cliente_solicitacoes.sol_id 
						WHERE h1.sts_id = (SELECT MAX(h2.sts_id) FROM cliente_status_solicitacoes h2 where h2.sts_solicitacao = h1.sts_solicitacao) AND
							 ".$protocolo_query." AND ".$nome_query." AND ".$data_query." AND ".$status_query." AND ".$categoria_query."
						";
				$stmt = $PDO->prepare($cnt);
				$stmt->bindParam(':fil_protocolo', 	$fil_protocolo1);
				$stmt->bindParam(':fil_nome1', 	$fil_nome1);
				$stmt->bindParam(':fil_nome2', 	$fil_nome2);
				$stmt->bindParam(':fil_data_inicio', 	$fil_data_inicio);
				$stmt->bindParam(':fil_data_fim', 		$fil_data_fim_h);
				$stmt->bindParam(':fil_categoria', 		$fil_categoria);
				$stmt->bindParam(':fil_status', 		$fil_status);
				include("../mod_includes/php/paginacao.php");
		}
		else
		{
			echo "<br><br><br><br>Não há nenhuma solicitação registrada.";
		}
		
    }
	if($pagina == "solicitacoes_gerenciar_adicionar")
	{
		echo "	
        <form name='form_solicitacoes_gerenciar' id='form_solicitacoes_gerenciar' enctype='multipart/form-data' method='post' action='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar&action=adicionar$autenticacao'>
            <div class='titulo'> $page &raquo; Adicionar  </div>
			<table align='center' cellspacing='0' width='100%'>
				<tr>
					<td align='left'>
						<p><label>Empresa:</label> 
						<div class='suggestion' style='width:80%;'>
							<input name='sol_cliente' id='sol_cliente' placeholder='ID' autocomplete='off'  type='hidden' />
							<input style='width:100%;' name='sol_cliente_nome' id='sol_cliente_nome' type='text' placeholder='Digite o nome ou CNPJ/CPF' autocomplete='off' />
							<div class='suggestionsBox' id='suggestions' style='display: none;'>
								<div class='suggestionList' id='autoSuggestionsList'>
									&nbsp;
								</div>
							</div>
						</div>
						<br><br>
						<p><label>Contrato:</label> <select name='sol_contrato' id='sol_contrato'>
							<option value=''>Selecione o Contrato</option>							
						</select>
						<div id='detalhes_contrato'> &nbsp;
							<label>Serviço:</label> <div class='servico'>&nbsp;</div> &nbsp;
							<label>Objeto:</label> <div class='objeto'>&nbsp;</div>
						</div>	
						<div id='serv'>
							
						</div>	
						<p>
						<label>Item do Contrato:</label> <select name='sol_item_contrato' id='sol_item_contrato'>
																<option value=''>Item do Contrato</option>
															</select>
						<p><label>Categoria:</label> <select name='sol_categoria' id='sol_categoria'>
															<option value=''>Categoria</option>
															"; 
															$sql = " SELECT * FROM aux_categoria_solicitacao ORDER BY cas_id";
															$stmt = $PDO->prepare($sql);
															$stmt->execute();
															while($result = $stmt->fetch())
															{
																echo "<option value='".$result['cas_id']."'>".$result['cas_descricao']."</option>";
															}
															echo "
														</select>
						<p><label>Data:</label> <input name='sol_data' class='datepicker' id='sol_data' value='".date("d/m/Y")."' placeholder='Data'  onkeypress='return mascaraData(this,event);'  />
						   <label>Hora:</label> <input name='sol_hora' id='sol_hora' value='".date("H:i")."' placeholder='Hora'  onkeypress='return mascaraHorario(this,event);'  />
						<p><label>Breve Histórico:</label> <textarea name='sol_breve_historico' id='sol_breve_historico' placeholder='Breve Histórico'></textarea>
						<p><label>Memorial:</label> <textarea name='sol_memorial' id='sol_memorial' placeholder='Memorial'></textarea>
						<p><label>Anexo:</label> <input type='file' name='sol_anexo[anexo]' id='sol_anexo'> 
						<p><label>Figura 1:</label> <input type='file' name='sts_foto[foto]' id='sts_foto'> 
						<br>
						<center>
						<div id='erro' align='center'>&nbsp;</div>
						<input type='button' id='bt_solicitacoes_gerenciar' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
						<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar".$autenticacao."'; value='Cancelar'/></center>
						</center>
					</td>
				</tr>
			</table>
        </form>
        ";
	}
	
	if($pagina == "solicitacoes_gerenciar_editar")
	{
		$sql = "SELECT *, Year(sol_data_cadastro) as ano,s1.ser_descricao as s1,s2.ser_descricao as s2 FROM cliente_solicitacoes 
				LEFT JOIN ( cadastro_contratos 
					LEFT JOIN cadastro_contratos_gestor ON cadastro_contratos_gestor.ges_contrato = cadastro_contratos.con_id 
					LEFT JOIN cadastro_contratos_itens ON cadastro_contratos_itens.ite_contrato = cadastro_contratos.con_id
					LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
					LEFT JOIN aux_servicos as s1 ON s1.ser_id = cadastro_contratos.con_servico )
				ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato
				LEFT JOIN aux_servicos as s2 ON s2.ser_id = cliente_solicitacoes.sol_servico
				LEFT JOIN aux_categoria_solicitacao ON aux_categoria_solicitacao.cas_id = cliente_solicitacoes.sol_categoria
				LEFT JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_id = cliente_solicitacoes.sol_contato
				LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cliente_solicitacoes.sol_cliente
				LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = cliente_solicitacoes.sol_interno
				LEFT JOIN cliente_status_solicitacoes h1 ON h1.sts_solicitacao = cliente_solicitacoes.sol_id 
				WHERE h1.sts_id = (SELECT MAX(h2.sts_id) FROM cliente_status_solicitacoes h2 where h2.sts_solicitacao = h1.sts_solicitacao) AND
					  sol_id = :sol_id
				GROUP BY sol_id";
        $stmt = $PDO->prepare($sql);	
		$stmt->bindParam(':sol_id', $sol_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
        {
			$result = $stmt->fetch();
          	$sol_id 			= $result['sol_id'];
			$ano 				= $result['ano'];
			$con_numero_processo= $result['con_numero_processo'];
			$con_ano_processo 	= $result['con_ano_processo'];
			$mod_descricao 		= $result['mod_descricao'];
			$ite_descricao		= $result['ite_descricao'];
			$ite_tipo 			= $result['ite_tipo'];
			$sol_tipo 			= $result['sol_tipo'];
			$sol_servico 		= $result['sol_servico'];
			$ser_descricao 		= $result['s2'];
			$emp_tipo 			= $result['emp_tipo'];
			$emp_nome_razao 	= $result['emp_nome_razao'];
			$emp_fantasia 		= $result['emp_fantasia'];
			$solicitante		= $result['ctt_nome'].$result['usu_nome'];
			$sol_categoria 		= $result['sol_categoria'];
			$cas_descricao 		= $result['cas_descricao'];
			$sol_tipo 			= $result['sol_tipo'];
			$sol_data = implode("/",array_reverse(explode("-",substr($result['sol_data_cadastro'],0,10))));
			$sol_hora = substr($result['sol_data_cadastro'],11,5);
			$sol_cliente	 		= $result['sol_cliente'];
			$sol_contrato	 		= $result['sol_contrato'];
			$sol_item_contrato	 	= $result['sol_item_contrato'];
			$sol_breve_historico 	= nl2br($result['sol_breve_historico']);
			$sol_memorial 			= nl2br($result['sol_memorial']);
			$sol_anexo	 			= $result['sol_anexo'];
			$sts_status 			= $result['sts_status'];
			$ctt_email	 			= $result['ctt_email'];
			$ctt_nome	 			= $result['ctt_nome'];
			switch($sts_status)
			{
				case 1 : $sts_status = "Registrado";break;
				case 2 : $sts_status = "Em Análise";break;
				case 3 : $sts_status = "Agendado";break;
				case 4 : $sts_status = "Em Execução";break;
				case 5 : $sts_status = "Cancelado";break;
				case 6 : $sts_status = "Em Homologação";break;
				case 7 : $sts_status = "Concluído";break;
			}
			echo "	
			<form name='form_solicitacoes_gerenciar' id='form_solicitacoes_gerenciar' enctype='multipart/form-data' method='post' action='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar&action=editar&sol_id=$sol_id$autenticacao'>
				<div class='titulo'> $page &raquo; Editar  </div>
				<table align='center' cellspacing='0' width='100%'>
					<tr>
						<td align='left'>
							<p><label>Empresa:</label> 
							<div class='suggestion' style='width:80%;'>
								<input name='sol_cliente' id='sol_cliente' value='$sol_cliente' placeholder='ID' autocomplete='off'  type='hidden' />
								<input style='width:100%;' name='sol_cliente_nome_r' id='sol_cliente_nome_r' value='$emp_nome_razao' readonly type='text' placeholder='Digite o nome ou CNPJ/CPF' autocomplete='off' />
								<div class='suggestionsBox' id='suggestions' style='display: none;'>
									<div class='suggestionList' id='autoSuggestionsList'>
										&nbsp;
									</div>
								</div>
							</div>
							<br><br>
							<p><label>Contrato:</label> <select name='sol_contrato' id='sol_contrato'>
								<option value='$sol_contrato'>$con_numero_processo/$con_ano_processo ($mod_descricao)</option>							
							</select>
							<div id='detalhes_contrato'>
								<label>Serviço:</label> <div class='servico'>&nbsp;</div>
								<label>Objeto:</label> <div class='objeto'>&nbsp;</div>
							</div>	
							";
							if($sol_servico != '')
							{
								echo "
								<div id='serv' style='display:block;'>
									<label>Serviço:</label> <select name='sol_servico' id='sol_servico'>
										<option value='$sol_servico'>$ser_descricao</option>
										"; 
										$sql = " SELECT * FROM aux_servicos WHERE ser_status = :ser_status ORDER BY ser_descricao ";
										$stmt = $PDO->prepare($sql);
										$stmt->bindValue(':ser_status',1);
										$stmt->execute();
										while($result = $stmt->fetch())
										{
											echo "<option value='".$result['ser_id']."'>".$result['ser_descricao']."</option>";
										}
										echo "
									</select>
								</div>
								";
							}
							else
							{
								echo "
								<div id='serv'>
									
								</div>
								";
							}
							echo "
							<p>
							<label>Item do Contrato:</label> <select name='sol_item_contrato' id='sol_item_contrato'>
																	<option value='$sol_item_contrato'>$ite_descricao/$ite_tipo</option>
																</select>
							<p><label>Categoria:</label> <select name='sol_categoria' id='sol_categoria'>
															<option value='$sol_categoria'>$cas_descricao</option>
															"; 
															$sql = " SELECT * FROM aux_categoria_solicitacao ORDER BY cas_id";
															$stmt = $PDO->prepare($sql);
															$stmt->execute();
															while($result = $stmt->fetch())
															{
																echo "<option value='".$result['cas_id']."'>".$result['cas_descricao']."</option>";
															}
															echo "
														</select>
							<p><label>Data:</label> <input name='sol_data' id='sol_data' value='$sol_data' placeholder='Data'  onkeypress='return mascaraData(this,event);'  />
							   <label>Hora:</label> <input name='sol_hora' id='sol_hora' value='$sol_hora' placeholder='Hora'  onkeypress='return mascaraHorario(this,event);'  />
							<p><label>Breve Histórico:</label> <textarea name='sol_breve_historico' id='sol_breve_historico' placeholder='Breve Histórico'>$sol_breve_historico</textarea>
							<p><label>Memorial:</label> <textarea name='sol_memorial' id='sol_memorial' placeholder='Memorial'>$sol_memorial</textarea>
							<br>
							<center>
							<div id='erro' align='center'>&nbsp;</div>
							<input type='button' id='bt_solicitacoes_gerenciar' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
							<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar".$autenticacao."'; value='Cancelar'/></center>
							</center>
						</td>
					</tr>
				</table>
			</form>
			";
		}
	}
	
	if($pagina == "solicitacoes_gerenciar_exibir")
	{
		?>
        <script language="javascript">
		jQuery(document).ready(function()
		{
			jQuery('html, body').animate({scrollTop:$(document).height()-$(window).height()}, 1500);
		});
		</script>
        <?php

		// LOG DE ACESSO
		$sql = "INSERT INTO log_solicitacoes_usuarios SET
				log_solicitacao = :log_solicitacao,
				log_usuario = :log_usuario
				";
		$stmt = $PDO->prepare($sql);	
		$stmt->bindParam(':log_solicitacao', $sol_id);
		$stmt->bindParam(':log_usuario', $_SESSION['usuario_id']);
		$stmt->execute();
		

		
		$sql = "SELECT *, Year(sol_data_cadastro) as ano,s1.ser_descricao as s1,s2.ser_descricao as s2 FROM cliente_solicitacoes 
				LEFT JOIN ( cadastro_contratos 
					LEFT JOIN cadastro_contratos_gestor ON cadastro_contratos_gestor.ges_contrato = cadastro_contratos.con_id 
					LEFT JOIN cadastro_contratos_itens ON cadastro_contratos_itens.ite_contrato = cadastro_contratos.con_id
					LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
					LEFT JOIN aux_servicos as s1 ON s1.ser_id = cadastro_contratos.con_servico )
				ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato
				LEFT JOIN aux_servicos as s2 ON s2.ser_id = cliente_solicitacoes.sol_servico
				LEFT JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_id = cliente_solicitacoes.sol_contato
				LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cliente_solicitacoes.sol_cliente
				LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = cliente_solicitacoes.sol_interno
				LEFT JOIN cliente_status_solicitacoes h1 ON h1.sts_solicitacao = cliente_solicitacoes.sol_id 
				WHERE h1.sts_id = (SELECT MAX(h2.sts_id) FROM cliente_status_solicitacoes h2 where h2.sts_solicitacao = h1.sts_solicitacao) AND
					  sol_id = :sol_id
				GROUP BY sol_id";
        $stmt = $PDO->prepare($sql);	
		$stmt->bindParam(':sol_id', $sol_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
        {
			$result = $stmt->fetch();
          	$sol_id 			= $result['sol_id'];
			$ano 				= $result['ano'];
			$con_numero_processo= $result['con_numero_processo'];
			$con_ano_processo 	= $result['con_ano_processo'];
			$con_memorial 		= $result['con_memorial'];
			$mod_descricao 		= $result['mod_descricao'];
			$ite_descricao		= $result['ite_descricao'];
			$ite_tipo 			= $result['ite_tipo'];
			$sol_tipo 			= $result['sol_tipo'];
			$ser_descricao		= $result['s1'].$result['s2'];
			$emp_tipo 			= $result['emp_tipo'];
			$emp_nome_razao 	= $result['emp_nome_razao'];
			$emp_fantasia 		= $result['emp_fantasia'];
			$solicitante		= $result['ctt_nome'].$result['usu_nome'];
			$sol_tipo 			= $result['sol_tipo'];
			$sol_data = implode("/",array_reverse(explode("-",substr($result['sol_data_cadastro'],0,10))));
			$sol_hora = substr($result['sol_data_cadastro'],11,5);
			$sol_breve_historico 	= nl2br($result['sol_breve_historico']);
			$sol_memorial 			= nl2br($result['sol_memorial']);
			$sol_anexo	 			= $result['sol_anexo'];
			$sts_status 			= $result['sts_status'];
			
			$ctt_email	 			= $result['ctt_email'];
			$ctt_nome	 			= $result['ctt_nome'];
			switch($sts_status)
			{
				case 1 : $sts_status = "Registrado";break;
				case 2 : $sts_status = "Em Análise";break;
				case 3 : $sts_status = "Agendado";break;
				case 4 : $sts_status = "Em Execução";break;
				case 5 : $sts_status = "Cancelado";break;
				case 6 : $sts_status = "Em Homologação";break;
				case 7 : $sts_status = "Concluído";break;
			}
			
			
			// ATUALIZA STATUS
			if($sts_status == "Registrado" && $sol_tipo == "Externa")
			{
				$sql = "INSERT INTO cliente_status_solicitacoes SET sts_solicitacao = :sol_id, sts_status = :sts_status, sts_observacao = :sts_observacao, sts_usuario = :sts_usuario";
				$stmt = $PDO->prepare($sql);	
				$sts_observacao = "Sua solicitação já foi visualizada por nossa equipe.";
				$stmt->bindParam(':sol_id', $sol_id);
				$stmt->bindValue(':sts_status', 2);
				$stmt->bindParam(':sts_observacao', $sts_observacao);
				$stmt->bindParam(':sts_usuario', $_SESSION['usuario_id']);
				if($stmt->execute())
				{
					//echo "Inserido <br>";
					
					// ENVIA EMAIL
					include("../mail/admin_envia_status_atualizado.php");
					if(!$erro)
					{
						echo "
						<SCRIPT language='JavaScript'>
							abreMask(
							'<img src=../imagens/ok.png> Alteração de status OK. <br>'+
							'<img src=../imagens/ok.png> Foi enviado um email para o cliente informando sobre a atualização de status.<br><br>'+
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
				else{ $erro=1; $err = $stmt->errorInfo();}	
			}



			
			echo "
				<div class='titulo'> $page &raquo; Exibir </div>
				<img class='hand' title='Imprimir Solicitação' style='float:right; margin:0 5px;' src='../imagens/icon-pdf.png' onclick=javascript:window.open('solicitacao_imprimir.php?sol_id=$sol_id$autenticacao');>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<img class='hand' title='Imprimir O.S.' style='float:right; margin:0 5px;' src='../imagens/icon-obs.png' onclick=javascript:window.open('solicitacao_os.php?sol_id=$sol_id$autenticacao');> 
				<ul class='nav nav-tabs'>
				  <li class='active'><a data-toggle='tab' 	href='#historico'>Histórico da Solicitação</a></li>
				  <li><a data-toggle='tab' 					href='#dados'>Dados da Solicitação</a></li>
				</ul>
				<div class='tab-content'>
					<div id='historico' class='tab-pane fade in active'>
						<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
							<tr>
								<td align='left'>
								";
								$sql = "SELECT * FROM cliente_solicitacoes 
										LEFT JOIN ( cadastro_contratos 
											LEFT JOIN cadastro_contratos_gestor ON cadastro_contratos_gestor.ges_contrato = cadastro_contratos.con_id 
											LEFT JOIN cadastro_contratos_itens ON cadastro_contratos_itens.ite_contrato = cadastro_contratos.con_id 
											LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
											LEFT JOIN aux_servicos ON aux_servicos.ser_id = cadastro_contratos.con_servico)
										ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato
										LEFT JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_id = cliente_solicitacoes.sol_contato
										LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cliente_solicitacoes.sol_cliente
										LEFT JOIN (cliente_status_solicitacoes 
											LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = cliente_status_solicitacoes.sts_usuario)
										ON cliente_status_solicitacoes.sts_solicitacao = cliente_solicitacoes.sol_id 
										WHERE sol_id = :sol_id
										GROUP BY sts_id
										ORDER BY sts_data ASC";
								$stmt = $PDO->prepare($sql);	
								$stmt->bindParam(':sol_id', $sol_id);
								$stmt->execute();
								$rows = $stmt->rowCount();
								if($rows > 0)
								{
									echo "<section id='cd-timeline' class='cd-container'>";
									while($result = $stmt->fetch())
									{
										$sol_id 		= $result['sol_id'];
										$sts_observacao = nl2br($result['sts_observacao']);
										$sts_data		= implode("/",array_reverse(explode("-",substr($result['sts_data'],0,10))));
										$sts_hora		= substr($result['sts_data'],11,5);
										$sts_id	 		= $result['sts_id'];
										$sts_status 	= $result['sts_status'];
										$sts_usuario 	= $result['sts_usuario'];
										$sts_anexo		= $result['sts_anexo'];
										$sts_anexo2		= $result['sts_anexo2'];
										$sts_foto		= $result['sts_foto'];
										$sts_foto2		= $result['sts_foto2'];
										$usu_nome 		= $result['usu_nome'];
										$ctt_nome 		= $result['ctt_nome'];
										if($sts_usuario == '')
										{
											$usuario = $ctt_nome;
										}
										else
										{
											$usuario = $usu_nome;
										}
										switch($sts_status)
										{
											case 1 : $sts_status = "Registrado";break;
											case 2 : $sts_status = "Em Análise";break;
											case 3 : $sts_status = "Agendado";break;
											case 4 : $sts_status = "Em Execução";break;
											case 5 : $sts_status = "Cancelado";break;
											case 6 : $sts_status = "Em Homologação";break;
											case 7 : $sts_status = "Concluído";break;
										}
										echo "
										<div class='cd-timeline-block'>
											<div class='cd-timeline-img cd-location'>
												<img src='../imagens/cd-icon-location.svg' alt='Location'>
											</div> <!-- cd-timeline-img -->
								
											<div class='cd-timeline-content'>
												<h2></h2>
												<a onclick=\"
													abreMask(
														'Deseja realmente excluir este status?<br><br>'+
														'<input value=\' Sim \' type=\'button\' onclick=javascript:window.location.href=\'solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar_exibir&action=excluir_status&sol_id=$sol_id&sts_id=$sts_id$autenticacao\';>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
														'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
													\">
													<img src='../imagens/icon-excluir.png' style='float:right; margin-top:-15px; margin-right:-15px; width:25px;'>
												</a>	
												<a href='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar_editar_status&sol_id=$sol_id&sts_id=$sts_id$autenticacao'>
													<img src='../imagens/icon-editar.png' style='float:right; margin-top:-15px; margin-right:0px; width:25px;'>
												</a>											
												<p><b>Usuário:</b> ".$usuario."
												<p><b>Status:</b> ".$sts_status."
												<p><b>Observações:</b> ".$sts_observacao."
												";if($sts_anexo != ''){ echo "<p><b>Anexo:</b> <a href='$sts_anexo ' target='_blank'><img src='../imagens/icon-anexo.png' valign='middle' border='0'></a>";} echo " &nbsp;							
												";if($sts_anexo2 != ''){ echo "<p><b>Anexo 2:</b> <a href='$sts_anexo2 ' target='_blank'><img src='../imagens/icon-anexo.png' valign='middle' border='0'></a>";} echo " &nbsp;							
												";if($sts_foto != ''){ echo "<p><b>Figura 1:</b> <a href='$sts_foto ' target='_blank'><img src='../imagens/icon-anexo.png' valign='middle' border='0'></a>";} echo " &nbsp;							
												";if($sts_foto2 != ''){ echo "<p><b>Figura 2:</b> <a href='$sts_foto2 ' target='_blank'><img src='../imagens/icon-anexo.png' valign='middle' border='0'></a>";} echo " &nbsp;							
												<span class='cd-date'>".$sts_data."<br>às ".$sts_hora."</span>
											</div> <!-- cd-timeline-content -->
										</div> <!-- cd-timeline-block -->
										
										";
									}
									echo "</section>";
								}
								echo "
									<form name='form_novo_status' id='form_novo_status' enctype='multipart/form-data' method='post' action='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar_exibir&action=novo_status&sol_id=$sol_id".$autenticacao."'>
									<div class='subquadro' style='width:94%; margin-left:2%;'>
										<div class='status'>
											<p class='subtitle'><input type='button' id='bt_status' value='Adicionar Novo Status' /></p>
											<div class='conteudo'>
												<select name='sts_status' id='sts_status'>
													<option value=''>Status</option>
													<option value='1'>Registrado</option>
													<option value='2'>Em Análise</option>
													<option value='3'>Agendado</option>
													<option value='4'>Em Execução</option>
													<option value='5'>Cancelado</option>
													<option value='6'>Em Homologação</option>
													<option value='7'>Concluído</option>													
												</select>
												<p>Caso queria utilizar texto de um parecer, selecione abaixo:<br>
												<select name='sts_parecer' id='sts_parecer' class='sts_parecer' >
													<option value=''>Assunto Parecer</option>
													"; 
													$sql = "SELECT * FROM aux_assuntos_pareceres ORDER BY ass_descricao ASC";
													$stmt = $PDO->prepare($sql);
													$stmt->execute();
													while($result = $stmt->fetch())
													{
														echo "<option value='".$result['ass_id']."'>".$result['ass_descricao']."</option>";
													}
													echo "
												</select>
												<p><textarea name='sts_observacao' style='width:47%;' id='sts_observacao' placeholder='Observação'></textarea>
												<textarea name='sts_memorial' style='width:47%; height:200px; float:right;' readonly id='sts_memorial' placeholder='Memorial / T.R.'>$con_memorial</textarea>
												<p>Anexo:<br><input type='file' name='sts_anexo[anexo]' id='sts_anexo'> 
												<p>Anexo 2:<br><input type='file' name='sts_anexo2[anexo2]' id='sts_anexo2'> 
												<p>Figura 1:<br><input type='file' name='sts_foto[foto]' id='sts_foto'> 
												<p>Figura 2:<br><input type='file' name='sts_foto2[foto2]' id='sts_foto2'> 
												<p>
												<div class='homologacao' style='display:none;'>
												<p class='titulo'>Homologação / Feedback</p>													
													<select name='sts_fb_tecnico' id='sts_fb_tecnico' style='width:150px;'>
														<option value=''>Técnico</option>
														"; 
														$sql = "SELECT * FROM admin_usuarios ORDER BY usu_nome ASC";
														$stmt = $PDO->prepare($sql);
														$stmt->execute();
														while($result = $stmt->fetch())
														{
															echo "<option value='".$result['usu_nome']."'>".$result['usu_nome']."</option>";
														}
														echo "
													</select>													
													<select name='sts_fb_servidor' id='sts_fb_servidor' style='width:150px;' >
														<option value=''>Servidor</option>
														"; 
														$sql = "SELECT * FROM cadastro_empresas_contatos 
																LEFT JOIN (cadastro_empresas 
																	LEFT JOIN cliente_solicitacoes ON cliente_solicitacoes.sol_cliente = cadastro_empresas.emp_id )
																ON cadastro_empresas.emp_id = cadastro_empresas_contatos.ctt_empresa
																WHERE sol_id = :sol_id
																ORDER BY ctt_nome ASC";
														$stmt = $PDO->prepare($sql);
														$stmt->bindParam(":sol_id",$sol_id);
														$stmt->execute();
														while($result = $stmt->fetch())
														{
															echo "<option value='".$result['ctt_nome']."'>".$result['ctt_nome']."</option>";
														}
														echo "
													</select>
													<input name='sts_fb_data' id='sts_fb_data' class='datepicker' placeholder='Data' style='width:100px;'/>
													<input name='sts_fb_hora' id='sts_fb_hora' placeholder='Horário' style='width:100px;' onkeypress='return mascaraHorario(this,event);'>
													<select name='sts_fb_forma_atendimento' id='sts_fb_forma_atendimento' >
														<option value=''>Forma de Atendimento</option>
														"; 
														$sql = "SELECT * FROM aux_formas_atendimento 
																ORDER BY fat_descricao ASC";
														$stmt = $PDO->prepare($sql);
														$stmt->execute();
														while($result = $stmt->fetch())
														{
															echo "<option value='".$result['fat_descricao']."'>".$result['fat_descricao']."</option>";
														}
														echo "
													</select>
												</div>
												<center>
												<div id='erro' align='center'>&nbsp;</div>
												<input type='button' id='bt_novo_status' value='Salvar' />
												</center>
											</div>
										</div>
									</div>
									</form>
									<br><br>
								</td>
							</tr>
						</table>
					</div>
					<div id='dados' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%' class='borda_aba'>
							<tr>
								<td align='left'>
									<br>
									<label>Protocolo:</label> 				<div class='sol_exibir'>$ano.$sol_id</div>
									<label>Tipo de Solicitação:</label> 	<div class='sol_exibir'>$sol_tipo</div>
									<label>Cliente/Empresa:</label>         <div class='sol_exibir'>
									";
										if($emp_tipo == 'PJ')
									  {
										  echo "$emp_fantasia (<span class='detalhe'>$emp_nome_razao</span>)";
									  }
									  else
									  {
										  if($emp_fantasia != '')
										  {
											echo "$emp_fantasia (<span class='detalhe'>$emp_nome_razao</span>)";
										  }
										  else
										  {
											echo "$emp_nome_razao";
										  }
									  } 
									echo " </div>
									<label>Solicitante:</label>  			<div class='sol_exibir'>$solicitante</div>
									<label>Data da Solicitação:</label>  	<div class='sol_exibir'>$sol_data às $sol_hora</div>
									<label>Status Atual:</label>  			<div class='sol_exibir'>$sts_status</div>
									<label>Contrato:</label>  				<div class='sol_exibir'>";if($con_numero_processo == "" && $con_ano_processo == ""){echo "&nbsp;";}else{ echo "$con_numero_processo/$con_ano_processo ($mod_descricao)";} echo "</div>
									<label>Item do Contrato:</label>  		<div class='sol_exibir'>";if($ite_descricao == "" && $ite_tipo == ""){echo "&nbsp;";}else{ echo "$ite_descricao ($ite_tipo)";} echo "</div>
									<label>Serviço:</label>  				<div class='sol_exibir'>$ser_descricao &nbsp;</div>
									<label>Breve Histórico:</label>  		<div class='sol_exibir'>$sol_breve_historico</div>
									<label>Memorial:</label>  				<div class='sol_exibir'>$sol_memorial</div>
									";if($sol_anexo != ''){ echo "<label>Anexo:</label> <div class='sol_exibir'><a href='$sol_anexo ' target='_blank'><img src='../imagens/icon-anexo.png' valign='middle' border='0'></a></div>";} echo " &nbsp;							
								</td>
							</tr>
						</table>						
					</div>
				</div>
				<br>
				<center>	
				";
				if($sts_status == "Em Análise" || $sts_status == "Registrado")
				{
					echo "<input type='button' onclick=javascript:window.location.href='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar_agendar&sol_id=$sol_id$autenticacao'; value='Agendar Solicitação'/> &nbsp;&nbsp;&nbsp;&nbsp;";
				}
				echo "
				<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar$autenticacao'; value='Voltar'/></center>
				</center>
            ";
        }
	}
	if($pagina == "solicitacoes_gerenciar_homologar")
	{				
			echo "								
			<form name='form_novo_status' id='form_novo_status' enctype='multipart/form-data' method='post' action='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar&action=homologacao&sol_id=$sol_id&sts_id=$sts_id".$autenticacao."'>														
				<p class='titulo'>Homologação / Feedback</p>													
				<p><label>Ténico:</label> <select name='sts_fb_tecnico' id='sts_fb_tecnico' style='width:82%;' >
					<option value=''>Técnico</option>
					"; 
					$sql = "SELECT * FROM admin_usuarios ORDER BY usu_nome ASC";
					$stmt = $PDO->prepare($sql);
					$stmt->execute();
					while($result = $stmt->fetch())
					{
						echo "<option value='".$result['usu_nome']."'>".$result['usu_nome']."</option>";
					}
					echo "
				</select>													
				<p><label>Servidor:</label> <select name='sts_fb_servidor' id='sts_fb_servidor' style='width:82%;' >
					<option value=''>Servidor</option>
					"; 
					$sql = "SELECT * FROM cadastro_empresas_contatos 
							LEFT JOIN (cadastro_empresas 
								LEFT JOIN cliente_solicitacoes ON cliente_solicitacoes.sol_cliente = cadastro_empresas.emp_id )
							ON cadastro_empresas.emp_id = cadastro_empresas_contatos.ctt_empresa
							WHERE sol_id = :sol_id
							ORDER BY ctt_nome ASC";
					$stmt = $PDO->prepare($sql);
					$stmt->bindParam(":sol_id",$sol_id);
					$stmt->execute();
					while($result = $stmt->fetch())
					{
						echo "<option value='".$result['ctt_nome']."'>".$result['ctt_nome']."</option>";
					}
					echo "
				</select>
				<p><label>Data:</label> <input name='sts_fb_data' id='sts_fb_data' class='datepicker' style='width:80%;'  placeholder='Data'/>
				<p><label>Horário:</label> <input name='sts_fb_hora' id='sts_fb_hora' placeholder='Horário' style='width:80%;'  onkeypress='return mascaraHorario(this,event);'>
				<p><label>Forma Atendimento:</label> <select name='sts_fb_forma_atendimento' style='width:82%;'  id='sts_fb_forma_atendimento' >
					<option value=''>Forma de Atendimento</option>
					"; 
					$sql = "SELECT * FROM aux_formas_atendimento 
							ORDER BY fat_descricao ASC";
					$stmt = $PDO->prepare($sql);
					$stmt->execute();
					while($result = $stmt->fetch())
					{
						echo "<option value='".$result['fat_descricao']."'>".$result['fat_descricao']."</option>";
					}
					echo "
				</select>			
				<center>
				<div id='erro' align='center'>&nbsp;</div>
				<input type='button' id='bt_novo_status' value='Salvar' /> &nbsp;&nbsp;&nbsp;&nbsp;
				<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar$autenticacao'; value='Voltar'/></center>
			
				</center>			
			</form>
				

			</center>
            ";        
	}
    if($pagina == "solicitacoes_gerenciar_agendar")
	{
		 echo "	
		<form name='form_agenda_gerenciar_itens' id='form_agenda_gerenciar_itens' enctype='multipart/form-data' method='post' action='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar_exibir&action=agendar&sol_id=$sol_id$autenticacao'>
            <div class='titulo'> $page &raquo; Agendar  </div>
			<ul class='nav nav-tabs'>
			  <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
			  <li><a data-toggle='tab' href='#tecnicos'>Técnicos</a></li>
			  <li><a data-toggle='tab' href='#veiculos'>Veículos</a></li>
			  <li><a data-toggle='tab' href='#servidores'>Servidores</a></li>			  
			</ul>
			
			<div class='tab-content'>
				<div id='dados_gerais' class='tab-pane fade in active'>
					<table align='center' cellspacing='0' width='100%' class='borda_aba'>
						<tr>
							<td align='left'>
								<br>
								<label>Agenda:</label> 
										<select name='agi_agenda' id='agi_agenda' >
										<option value=''>Agenda</option>
										"; 
										$sql = "SELECT * FROM agenda_gerenciar
												LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = agenda_gerenciar.age_empresa
												ORDER BY emp_nome_razao ASC";
										$stmt = $PDO->prepare($sql);
										$stmt->execute();
										while($result = $stmt->fetch())
										{
											echo "<option value='".$result['age_id']."'>".$result['emp_fantasia']."</option>";
										}
										echo "
									</select>
								<p><label>Data:</label> <input name='agi_data' id='agi_data' placeholder='Data'  onkeypress='return mascaraData(this,event);'>
								<p><label>Horário Inicial:</label> <input name='agi_horario_inicial' id='agi_horario_inicial' placeholder='Horário Inicial' onkeypress='return mascaraHorario(this,event);'>
								<p><label>Horário:</label> <input name='agi_horario' id='agi_horario' placeholder='Horário' >
								<p><label>Forma de Atendimento:</label> 
										<select name='agi_forma_atendimento' id='agi_forma_atendimento' >
										<option value=''>Forma de Atendimento</option>
										"; 
										$sql = "SELECT * FROM aux_formas_atendimento 
												ORDER BY fat_descricao ASC";
										$stmt = $PDO->prepare($sql);
										$stmt->execute();
										while($result = $stmt->fetch())
										{
											echo "<option value='".$result['fat_id']."'>".$result['fat_descricao']."</option>";
										}
										echo "
									</select>
								<p><label>Descrição:</label> <textarea name='agi_descricao' id='agi_descricao' placeholder='Descrição'></textarea>
								<p><label>Anexo:</label> <input type='file' name='agi_anexo[anexo]' id='agi_anexo'> 
									
							</td>
						</tr>
					</table>
			  	</div>
				<div id='tecnicos' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
								<div id='p_scents_tecnico'>
								<div class='bloco_tecnico'>
									<input type='hidden' name='tecnico[1][agu_id]' id='agu_id'>
									<br><label>Técnico:</label> 
										<select name='tecnico[1][agu_usuario]' id='agu_usuario' class='agu_usuario' >
										<option value=''>Técnico</option>
										"; 
										$sql = "SELECT * FROM admin_usuarios WHERE usu_status = :usu_status ORDER BY usu_nome ASC";
										$stmt = $PDO->prepare($sql);
										$stmt->bindValue(":usu_status",1);
										$stmt->execute();
										while($result = $stmt->fetch())
										{
											echo "<option value='".$result['usu_id']."'>".$result['usu_nome']."</option>";
										}
										echo "
									</select>
									<label>Responsável?</label>	
									   <select name='tecnico[1][agu_responsavel]' id='agu_responsavel' class='agu_responsavel'>
									   		<option value=''>Responsável?</option>
											<option value='Sim'>Sim</option>
											<option value='Não'>Não</option>
									   </select>
									<p>
									<img src='../imagens/icon-add.png' id='addTecnico' title='Adicionar +' class='botao_dinamico'>
									<br><br>
								</div>
								</div>
							</td>
						</tr>
					</table>
			  	</div>
			 	<div id='veiculos' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
								<div id='p_scents_veiculo'>
								<div class='bloco_veiculo'>
									<input type='hidden' name='veiculo[1][agc_id]' id='agc_id'>
									<br><label>Veículo:</label> 
										<select name='veiculo[1][agc_carro]' id='agc_carro'>
										<option value=''>Veículo</option>
										"; 
										$sql = "SELECT * FROM cadastro_carros WHERE car_status = :car_status ORDER BY car_descricao ASC";
										$stmt = $PDO->prepare($sql);
										$stmt->bindValue(":car_status",1);
										$stmt->execute();
										while($result = $stmt->fetch())
										{
											echo "<option value='".$result['car_id']."'>".$result['car_descricao']."</option>";
										}
										echo "
									</select>
									<p>
									<img src='../imagens/icon-add.png' id='addVeiculo' title='Adicionar +' class='botao_dinamico'>
									<br>
								</div>
								</div>
							</td>
						</tr>
					</table>
			  	</div>
				<div id='servidores' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
								<div id='p_scents_servidor'>
								<div class='bloco_servidor'>
									<input type='hidden' name='servidor[1][ags_id]' id='ags_id'>
									<br><label>Servidor:</label> 
										<select name='servidor[1][ags_servidor]' id='ags_servidor' class='ags_servidor' >
										<option value=''>Servidor</option>
										"; 
										$sql = "SELECT * FROM cadastro_empresas_contatos 
												LEFT JOIN (cadastro_empresas 
													LEFT JOIN cliente_solicitacoes ON cliente_solicitacoes.sol_cliente = cadastro_empresas.emp_id )
												ON cadastro_empresas.emp_id = cadastro_empresas_contatos.ctt_empresa
												WHERE sol_id = :sol_id
												ORDER BY ctt_nome ASC";
										$stmt = $PDO->prepare($sql);
										$stmt->bindParam(":sol_id",$sol_id);
										$stmt->execute();
										while($result = $stmt->fetch())
										{
											echo "<option value='".$result['ctt_id']."'>".$result['ctt_nome']."</option>";
										}
										echo "
									</select>
									<label>Responsável?</label>	
									   <select name='servidor[1][ags_responsavel]' id='ags_responsavel' class='ags_responsavel'>
									   		<option value=''>Responsável?</option>
											<option value='Sim'>Sim</option>
											<option value='Não'>Não</option>
									   </select>
									<p>
									<img src='../imagens/icon-add.png' id='addServidor' title='Adicionar +' class='botao_dinamico'>
									<br><br>
								</div>
								</div>
							</td>
						</tr>
					</table>
			  	</div>				
			</div>   			
			<center>
			<div id='erro' align='center'>&nbsp;</div>
			<input type='button' id='bt_agenda_gerenciar_itens' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
			<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='agenda_gerenciar_itens.php?pagina=agenda_gerenciar_itens&age_id=$age_id".$autenticacao."'; value='Cancelar'/></center>
			</center>
        </form>
		";
    }	
	
	if($pagina == "solicitacoes_gerenciar_editar_status")
	{
		$sts_id = $_GET['sts_id'];
		$sql = "SELECT * FROM cliente_status_solicitacoes 
				WHERE sts_id = :sts_id
				";
        $stmt = $PDO->prepare($sql);	
		$stmt->bindParam(':sts_id', $sts_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
        {
			$result = $stmt->fetch();
     		$sts_data = implode("/",array_reverse(explode("-",substr($result['sts_data'],0,10))));
			$sts_hora = substr($result['sts_data'],11,5);
			echo "	
			<form name='form_editar_status' id='form_editar_status' enctype='multipart/form-data' method='post' action='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar_exibir&action=editar_status&sol_id=$sol_id&sts_id=$sts_id$autenticacao'>
				<div class='titulo'> $page &raquo; Editar Data  </div>
				<table align='center' cellspacing='0' width='100%'>
					<tr>
						<td align='left'>
							<p><label>Data:</label> <input name='sts_data' id='sts_data' value='$sts_data' placeholder='Data' onkeypress='return mascaraData(this,event);' />
							   <label>Hora:</label> <input name='sts_hora' id='sts_hora' value='$sts_hora' placeholder='Hora' onkeypress='return mascaraHorario(this,event);' />
							<br>
							<center>
							<div id='erro' align='center'>&nbsp;</div>
							<input type='submit' id='bt_editar_status' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
							<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='solicitacoes_gerenciar.php?pagina=solicitacoes_gerenciar".$autenticacao."'; value='Cancelar'/></center>
							</center>
						</td>
					</tr>
				</table>
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
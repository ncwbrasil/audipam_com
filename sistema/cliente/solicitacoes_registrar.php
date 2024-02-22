<?php
session_start (); 
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Audipam - Sistema Administrativo Integrado</title>
<meta name="author" content="MogiComp">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="../imagens/favicon.ico">
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../mod_includes/js/jquery-1.8.3.min.js"></script>
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
require_once("../mod_includes/php/ctracker.php");
include		('../mod_includes/php/connect.php');
include		('../mod_includes/php/funcoes-jquery.php');
require_once('../mod_includes/php/verificalogincliente.php');

?>
<div class='lateral'>
	<?php include("mod_menu/menu.php");?>
</div>
<div class='barra'> 
<div class='msg'>
	<?php //include("mod_menu/barra.php");?>
</div>
</div>
<div class='centro'>
	<div class='box'>
    <?php
	$sol_tipo 				= "Externa";
	$sol_contrato 			= $_POST['sol_contrato'];
	$sol_item_contrato 		= $_POST['sol_item_contrato'];
	$sol_breve_historico 	= $_POST['sol_breve_historico'];
	$sol_memorial 			= $_POST['sol_memorial'];
	$sol_data_cadastro		= date("Y-m-d")." ".date("H:i");
	$dados = array_filter(array(
		'sol_tipo' 				=> $sol_tipo,
		'sol_cliente' 			=> $_SESSION['cliente_id'],
		'sol_contato' 			=> $_SESSION['contato_id'],
		'sol_contrato' 			=> $sol_contrato,
		'sol_item_contrato' 	=> $sol_item_contrato,
		'sol_breve_historico' 	=> $sol_breve_historico,
		'sol_memorial' 			=> $sol_memorial,
		'sol_data_cadastro' 	=> $sol_data_cadastro
	));
	
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
			if($stmt->execute()){}else{ $erro=1; $err = $stmt->errorInfo();}
        }
		else{ $erro=1; $err = $stmt->errorInfo();}
		
      	if(!$erro)
		{
			
			// PEGA DADOS PARA EMAIL
			$sql = "SELECT *, Year(sol_data_cadastro) as ano FROM cliente_solicitacoes 
					INNER JOIN ( cadastro_contratos 
						LEFT JOIN cadastro_contratos_gestor ON cadastro_contratos_gestor.ges_contrato = cadastro_contratos.con_id 
						LEFT JOIN cadastro_contratos_itens ON cadastro_contratos_itens.ite_contrato = cadastro_contratos.con_id
						LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
						LEFT JOIN aux_servicos ON aux_servicos.ser_id = cadastro_contratos.con_servico )
					ON cadastro_contratos.con_id = cliente_solicitacoes.sol_contrato
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
				$ano 				= $result['ano'];
				$ctt_nome = $result['ctt_nome'];
				$emp_nome_razao = $result['emp_nome_razao'];
				$con_numero_processo= $result['con_numero_processo'];
				$con_ano_processo 	= $result['con_ano_processo'];
				$mod_descricao 		= $result['mod_descricao'];
				$ite_descricao		= $result['ite_descricao'];
				$ite_tipo 			= $result['ite_tipo'];
			}
			//
			
			// ALERTA
			$sql = "SELECT * FROM admin_usuarios
					WHERE usu_notificacao = :usu_notificacao AND usu_email <> :usu_email AND usu_status = :usu_status ";
			$stmt = $PDO->prepare($sql);
			$email = '';
			$stmt->bindValue(":usu_notificacao",1);
			$stmt->bindParam(":usu_email",$email);
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
					$ale_descricao = "Uma nova solicitação foi realizada, protocolo N° ".$ano.".".str_pad($sol_id,6,0,STR_PAD_LEFT)."";
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
			
			include("../mail/cliente_envia_solicitacao.php");
			/*echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../imagens/ok.png> A sua solicitação foi registrada com sucesso!<br>Por favor, aguarde enquanto nossa equipe analisa.<br><br>'+
				'<input value=\' Ok \' type=\'button\' class=\'close_janela\'>' );
			</SCRIPT>
				";*/
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
    
	if($pagina == "solicitacoes_registrar")
	{
		echo "	
        <form name='form_solicitacoes_registrar' id='form_solicitacoes_registrar' enctype='multipart/form-data' method='post' action='solicitacoes_registrar.php?pagina=solicitacoes_registrar&action=adicionar$autenticacao'>
            <div class='titulo'> Registrar Solicitação  </div>
			<table align='center' cellspacing='0' width='100%'>
				<tr>
					<td align='left'>
						<p><label>Contrato:</label> <select name='sol_contrato' id='sol_contrato'>
							<option value=''>Selecione o Contrato</option>";
							$sql = "SELECT * FROM cadastro_contratos
									INNER JOIN ( cadastro_contratos_gestor 
										INNER JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_id = cadastro_contratos_gestor.ges_contato )
									ON cadastro_contratos_gestor.ges_contrato = cadastro_contratos.con_id
									INNER JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
									WHERE ges_contato = :ges_contato AND con_status = :con_status
									ORDER BY con_ano_processo DESC, con_numero_processo DESC";
							$stmt = $PDO->prepare($sql);
							$stmt->bindParam(':ges_contato', $_SESSION['contato_id']);
							$stmt->bindValue(':con_status', 1);
							$stmt->execute();
							while($result = $stmt->fetch())
							{
								echo "<option value='".$result['con_id']."'>".$result['con_numero_processo']."/".$result['con_ano_processo']." (".$result['mod_descricao'].")</option>";
							}
							echo "
						</select>
						<div id='detalhes_contrato'>
							<label>Serviço:</label> <div class='servico'>&nbsp;</div>
							<label>Objeto:</label> <div class='objeto'>&nbsp;</div>
						</div>	
						<label>Item do Contrato:</label> <select name='sol_item_contrato' id='sol_item_contrato'>
																<option value=''>Item do Contrato</option>
															</select>
						<p><label>Breve Histórico:</label> <textarea name='sol_breve_historico' id='sol_breve_historico' placeholder='Breve Histórico'></textarea>
						<p><label>Memorial:</label> <textarea name='sol_memorial' id='sol_memorial' placeholder='Memorial'></textarea>
						<p><label>Anexo:</label> <input type='file' name='sol_anexo[anexo]' id='sol_anexo'> 
						<br>
						<center>
						<div id='erro' align='center'>&nbsp;</div>
						<input type='button' id='bt_solicitacoes_registrar' value='Registrar' />&nbsp;&nbsp;&nbsp;&nbsp; 
						<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='admin.php?pagina=admin".$autenticacao."'; value='Cancelar'/></center>
						</center>
					</td>
				</tr>
			</table>
        </form>
        ";
	}
	?>
    </div>
</div>
<script src='../mod_includes/js/w8/scripts.js'></script>
<?php
include('../mod_rodape/rodape.php');
?>

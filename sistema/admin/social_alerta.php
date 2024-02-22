<?php
session_start();
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
require_once("../mod_includes/php/ctracker.php");
include		('../mod_includes/php/connect.php');
include		('../mod_includes/php/funcoes-jquery.php');
require_once('../mod_includes/php/verificalogin.php');
include		('../mod_includes/php/funcoes.php');

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
		$sql = "SELECT * FROM social_alertas 
				WHERE ale_usuario = :ale_usuario AND ale_arquivado <> :ale_arquivado
				ORDER BY ale_data DESC";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':ale_usuario',$_SESSION['usuario_id']);
		$stmt->bindValue(':ale_arquivado',1);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
		{
			while($result = $stmt->fetch())
			{
				$link='';
				$ale_id	   = $result['ale_id'];
				$descricao = $result['ale_descricao'];
				$data 	   = implode("/",array_reverse(explode("-",substr($result['ale_data'],0,10))));
				if($data == date("d/m/Y"))
				{
					$data = "Hoje";
					
					$data_ini = strtotime($result['ale_data']);
					$data_fim = strtotime(date("Y-m-d H:i:s"));
					
					$diferenca = $data_fim - $data_ini; 
					if($diferenca < 3600) 
					{ 
						$hora = (int)floor( $diferenca / (60)); 
						$data_final = "há $hora minuto(s)";
					}
					else
					{
						$hora = (int)floor( $diferenca / (60 * 60)); 
						$data_final = "há $hora hora(s)";
					}
					
				}
				else
				{
					$hora = substr($result['ale_data'],11,5);
					$data_final = "$data às $hora";
				}
				$lida = $result['ale_lida'];
				$link = $result['ale_link'];
					echo "
					<a href='"; if($link != ''){ echo $link.$autenticacao;}else{ echo "#";} echo "'>
					<div class='alertaBox "; if($lida == 0){ echo "n_lida";} echo "'>
						<div class='infos' onclick='alertaMarcarLida(".$ale_id.",this);'>
								<span class='data'>$data_final</span>
								-
								<span class='descricao'>$descricao</span>
						</div>
						<div class='acao'>
							<a href='#' class='x' id='aaaa' name='aaaa' value='aaa' onclick='alertaArquivar(".$ale_id.",this);' title='Arquivar notificação'>x</a>
							<br>
							<a href='#' class='arq' onclick='alertaMarcarLida(".$ale_id.",this);' title='Marcar como lido'>º</a>
						</div>
					</div>
					</a>
					";
			}
		}
		else
		{
			echo "Não há alertas.";
		}
		
		?>
 	</div>
</div>
<?php
include('../mod_rodape/rodape.php');
?>
</body>
</html>
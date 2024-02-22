<?php
include('connect.php');

$usuario = $_POST['usuario'];
$sql_alerta = "SELECT * FROM social_alertas
			   LEFT JOIN cadastro_usuarios t1 ON t1.usu_id = social_alertas.ale_destinatario
			   LEFT JOIN cadastro_usuarios t2 ON t2.usu_id = social_alertas.ale_remetente
			   WHERE ale_destinatario = :ale_destinatario AND ale_lida = :ale_lida AND ale_arquivado = :ale_arquivado";
$stmt_alerta = $PDO->prepare($sql_alerta);
$stmt_alerta->bindParam(":ale_destinatario",$usuario);
$stmt_alerta->bindValue(":ale_lida",0);
$stmt_alerta->bindValue(":ale_arquivado",0);
$stmt_alerta->execute();
$rows_alerta = $stmt_alerta->rowCount();

if($rows_alerta > 0)
{
	echo $rows_alerta;
	while($result = $stmt_alerta->fetch())
	{
		if($result['ale_exibida'] != 1)
		{
			echo '<script>$.notify("'.strip_tags($result['ale_descricao']).'", { title: "'.$result['mor_nome'].$result['mof_nome'].$result['fun_nome'].'",icon: "'.$result['mor_foto'].$result['mof_foto'].$result['fun_foto'].'" }).click(function(){alertaMarcarLida('.$result['ale_id'].',this); setTimeout(function() {location.href = "'.$result['ale_link'].'"},100);});</script>';
			$sql = "UPDATE social_alertas SET ale_exibida = 1 
					WHERE ale_id = :ale_id AND ale_lida = :ale_lida AND ale_arquivado = :ale_arquivado";
			$stmt_notify = $PDO->prepare($sql);
			$stmt_notify->bindParam(':ale_id',$result['ale_id']);
			$stmt_notify->bindValue(':ale_lida',0);
			$stmt_notify->bindValue(':ale_arquivado',0);
			$stmt_notify->execute();
		}
	}
}

?>
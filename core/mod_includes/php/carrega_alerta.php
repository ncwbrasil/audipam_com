<?php
include('connect.php');
$usuario = $_POST['usuario'];
$sql_alerta = "SELECT * FROM social_alertas
			   LEFT JOIN cadastro_usuarios t1 ON t1.usu_id = social_alertas.ale_destinatario
			   LEFT JOIN cadastro_usuarios t2 ON t2.usu_id = social_alertas.ale_remetente
			   WHERE ale_destinatario = :ale_destinatario AND ale_arquivado = :ale_arquivado
			   ORDER BY ale_id DESC
			   LIMIT 0, 5";
$stmt_alerta = $PDO->prepare($sql_alerta);
$stmt_alerta->bindParam(":ale_destinatario",$usuario);
$stmt_alerta->bindValue(":ale_arquivado",0);
$stmt_alerta->execute();
$rows_alerta = $stmt_alerta->rowCount();

if($rows_alerta > 0)
{
	while($result_alerta = $stmt_alerta->fetch())
	{
		$foto=$nome=$descricao=$foto02=$ale_link="";
		
		if($result_alerta['usu_foto'] != '')
		{
			$foto = $result_alerta['usu_foto'];
		}
		else
		{
			$foto = "../core/imagens/perfil.png";
		}
		$nome = $result_alerta['usu_nome'];
		$desc = explode("<br>",$result_alerta['ale_descricao']);
		$descricao = $desc[0];
		$ale_id = $result_alerta['ale_id'];
		$ale_link = $result_alerta['ale_link'];
		$ale_lida = $result_alerta['ale_lida'];
		$data = implode("/",array_reverse(explode("-",substr($result_alerta['ale_data_cadastro'],0,10))));
		
		
		
		
		$data_ini = strtotime($result_alerta['ale_data_cadastro']);
		$data_fim = strtotime(date("Y-m-d H:i:s"));
		
		$diferenca = $data_fim - $data_ini; 
		
		if($diferenca < 3600) 
		{
			$hora = (int)floor( $diferenca / (60)); 
			$data_final = "há $hora minuto(s)";
		}
		elseif($diferenca >= 3600 && $diferenca < 86400) 
		{
			$hora = (int)floor( $diferenca / (60 * 60)); 
			$data_final = "há $hora hora(s)";
		}
		else
		{
			$dias = (int)floor($diferenca / (60 * 60 * 24));
			$data_final = "há $dias dia(s)";
		}
		
		
		echo "
		<a onclick='alertaMarcarLida(".$ale_id.",this);' href='$ale_link'>
		<div class='bloco_alerta ";if($ale_lida == 0){echo "n_lida";} echo "'>
			<div class='imagem01'>
			<div class='foto_perfil' style='width:50px; height:50px; background:url($foto) center center; background-size: cover; border-radius:50px;' border='0'></div>
			<!--<img src='imagem.php?arquivo=../core/".$foto."&altura=40&largura=40&marca=mini' class='perfil'>-->
			</div>
			<div class='descricao_alerta'>
				<span class='bold'>$nome</span> <br> $descricao
				<br>
				<p class='data'>".$data_final."</p>
			</div>
			<!--<div class='imagem02'>
				<img src='imagem.php?arquivo=../app/$foto02&largura=30&altura=30&marca=mini' >
			</div>-->
		</div>
		</a>
		";
	}
	echo "
	</div>
	<div class='todas'><a href='social_alerta'>Ver todas notificações</a></div>
	";
}
else
{
	echo "<center><br>Não há notificações</center>";
}
?>
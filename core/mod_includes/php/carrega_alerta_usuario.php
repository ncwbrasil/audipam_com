<?php
include('connect.php');
$usr = $_POST['usr'];
$sql_alerta = "SELECT *, a1.usu_foto as foto_usuario, a1.usu_nome as nome_usuario FROM social_alertas_usuarios
			   LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = social_alertas_usuarios.ale_usuario
			   LEFT JOIN solicitacoes_gerenciar ON solicitacoes_gerenciar.sol_id = social_alertas_usuarios.ale_publicacao
			   LEFT JOIN cadastro_usuarios a1 ON a1.usu_id = social_alertas_usuarios.ale_remetente_user
			   LEFT JOIN cadastro_clientes ON cadastro_clientes.cli_id = social_alertas_usuarios.ale_remetente_cidade
			   WHERE ale_usuario = :ale_usuario AND ale_arquivado = :ale_arquivado
			   ORDER BY ale_id DESC
			   LIMIT 0, 5";
$stmt_alerta = $PDO->prepare($sql_alerta);
$stmt_alerta->bindParam(":ale_usuario",$usr);
$stmt_alerta->bindValue(":ale_lida",0);
$stmt_alerta->bindValue(":ale_arquivado",0);
$stmt_alerta->execute();
$rows_alerta = $stmt_alerta->rowCount();

if($rows_alerta > 0)
{
	while($result_alerta = $stmt_alerta->fetch())
	{
		$foto01=$foto_cliente=$nome=$descricao=$foto02=$ale_link="";
		if($result_alerta['usu_foto'] != '')
		{
			$foto01 = $result_alerta['usu_foto']."";
		}
		elseif($result_alerta['cli_logo'] != '')
		{
			$foto01 = $result_alerta['cli_logo']."";
		}
		else
		{
			$foto01 = "imagens/perfil.png";
		}
		
		$nome = $result_alerta['nome_usuario'].$result_alerta['cli_fantasia'];
		$descricao = $result_alerta['ale_descricao'];
		$foto02 = $result_alerta['sol_foto'];
		$ale_link = $result_alerta['ale_link'];
		$ale_lida = $result_alerta['ale_lida'];
		
		$data = implode("/",array_reverse(explode("-",substr($result_alerta['ale_data'],0,10))));
		if($data == date("d/m/Y"))
		{
			$data = "Hoje";
			
			$data_ini = strtotime($result_alerta['ale_data']);
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
			$data_ini = strtotime($result_alerta['ale_data']);
			$data_fim = strtotime(date("Y-m-d H:i:s"));
			$diferenca = $data_fim - $data_ini; 
			$dias = (int)floor($diferenca / (60 * 60 * 24));
			$data_final = "há $dias dia(s)";
		}
		
		echo "
		<a href='$ale_link'>
		<div class='bloco_alerta ";if($ale_lida == 0){echo "n_lida";} echo "'>
			<div class='imagem01'>
				";
				if($result_alerta['usu_facebook'] == 1)
				{
					echo "<img src='".$foto01."' width='30'>";
				}
				else
				{
					echo "<img src='imagem.php?arquivo=".$foto01."&altura=30&largura=30&marca=mini' class='perfil'>";
				}
				echo "
			</div>
			<div class='descricao_alerta'>
				<span class='bold'>$nome</span> $descricao
				<br>
				<p class='data'>$data_final</p>
			</div>
			<div class='imagem02'>
				<img src='imagem.php?arquivo=$foto02&largura=30&altura=30&marca=mini' >
			</div>
		</div>
		</a>
		";
	}
	echo "
	<div class='todas'><a href='social_alerta'>Ver todas notificações</a></div>
	";
}
else
{
	echo "<center><br>Não há notificações</center>";
}
?>
<?php
include('connect.php');
require_once("funcoes.php");
sec_session_start(); 
$data = $_POST['data'];
$sql = "SELECT * FROM operacao_reservas 
		LEFT JOIN ( cadastro_usuarios 
			LEFT JOIN ( cadastro_empresas 
				LEFT JOIN cadastro_torres ON cadastro_torres.tor_id = cadastro_empresas.emp_torre )
			ON cadastro_empresas.emp_id = cadastro_usuarios.usu_empresa
			)
		ON cadastro_usuarios.usu_id = operacao_reservas.res_usuario
		LEFT JOIN aux_area_comum ON aux_area_comum.are_id = operacao_reservas.res_area_comum
		WHERE res_cliente = :res_cliente AND res_data = :res_data
		ORDER BY res_hora_inicio ASC ";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':res_cliente', $_SESSION['cliente_id']);
$stmt->bindParam(':res_data', $data);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows>0)
{
	$eventos = "";
	echo "<br><p class='titulo'> RESERVAS PARA ".implode("/",array_reverse(explode("-",$data)))."</p>";
	while($result = $stmt->fetch())
	{
		$res_id 			= $result['res_id'];
		$bloco 			= $result['tor_guarita'];
		$ap 			= $result['emp_conjunto'];
		$nome 			= $result['usu_nome'];
		if($result["res_status"] == "Em Avaliação")
		{
			$color = "#FF6600";
		}
		elseif($result["res_status"] == "Aprovado")
		{
			$color = "#00CC66";
		}
		elseif($result["res_status"] == "Reprovado")
		{
			$color = "#FF0000";
		}
		if(strtotime($result['res_data']) < strtotime(date("Y-m-d")))
		{
			$color = "#AAAAAA";
		}
		# RECUPERA DADOS TORRE / AP #
		
		$evento .= "
		<div class='bloco_reservas'>
			<div style='float:right;'><span class='bold'>Status:</span> <span style='background-color:".$color."; color:#FFF;'> &nbsp ".$result["res_status"]." &nbsp </span></div>
			<span class='bold'>Área reservada:</span> ".$result["are_nome"]."<br>
			<span class='bold'>Horário:</span> ".substr($result["res_hora_inicio"],0,5)." às ".substr($result["res_hora_fim"],0,5)."<br>
			<span class='bold'>Torre / Conjunto:</span> ".$bloco." / ".$ap." <br>
			<span class='bold'>Solicitante:</span> ".$nome." 
			<div class='att_convidados'><i class='fas fa-sync-alt'></i></div>
			<div class='exibir_convidados' id='$res_id'> <i class='far fa-eye'></i> Exibir convidados </div>
			<div class='convidados'>
			";
			$sql = "SELECT * FROM operacao_reservas_convidados 
					LEFT JOIN (operacao_reservas 
						LEFT JOIN ( cadastro_usuarios 
							LEFT JOIN ( cadastro_empresas 
								LEFT JOIN cadastro_torres ON cadastro_torres.tor_id = cadastro_empresas.emp_torre )
							ON cadastro_empresas.emp_id = cadastro_usuarios.usu_empresa
							)
						ON cadastro_usuarios.usu_id = operacao_reservas.res_usuario
					LEFT JOIN aux_area_comum ON aux_area_comum.are_id = operacao_reservas.res_area_comum )
					ON operacao_reservas.res_id = operacao_reservas_convidados.con_reserva
					WHERE con_reserva = :con_reserva";
            $stmt_convidados = $PDO->prepare($sql);
			$stmt_convidados->bindParam(':con_reserva', $res_id);
			$stmt_convidados->execute();
			$rows_convidados = $stmt_convidados->rowCount();    	
            if($rows_convidados > 0)
            {
				$evento .= "
				<table align='center' width='100%' border='0' cellspacing='0' cellpadding='10'>
					<tr>
						<td class='titulo_tabela' align='left'>Nome</td>
						<td class='titulo_tabela'>CPF</td>	
						<td align='center' class='titulo_tabela'>Dar Entrada</td>									
					</tr>";
					$c=0;
					while($result_convidados = $stmt_convidados->fetch())
					{
						$con_id 	= $result_convidados['con_id'];
						$con_nome 	= $result_convidados['con_nome'];
						$con_cpf	= $result_convidados['con_cpf'];
						$con_entrou	= $result_convidados['con_entrou'];
						if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;}  
						$evento .= "<tr class='$c1'>
									<td>$con_nome</td>
									<td>$con_cpf</td>
									<td align='center'>
									";
									if($con_entrou != 1)
									{
										$evento .= "<div class='g_acesso' style='float:none;' title='Novo visitante' onclick='window.open(\"cadastro_visitantes_dinamico/add?cpf=".$con_cpf."&nome=".$con_nome."&local=".$result["are_nome"]."&con_id=$con_id\" , \"Novo Visitante\" , \"width=500,height=650,scrollbars=NO,location=no,titlebar=no,menubar=no,left=500,top=0\") ;'><i class='far fa-user'></i></div>";
									}
									$evento .= "</td>											  
								</tr>";
					}
					$evento .= "
				</table>
				";
			}
			$evento .= "
			</div>
		</div><br>";
	}

	echo $evento;
}
else
{
	echo "<br><p class='titulo'> NÃO HÁ RESERVAS PARA ".implode("/",array_reverse(explode("-",$data)))."</p>";
	
}

?>
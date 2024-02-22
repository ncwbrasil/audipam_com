<?php
include('connect.php');
require_once("funcoes.php");
sec_session_start(); 
$morador = $_POST['morador'];
$sql = "SELECT * FROM operacao_reservas 
		LEFT JOIN ( cadastro_usuarios 
			LEFT JOIN ( cadastro_empresas 
				LEFT JOIN cadastro_torres ON cadastro_torres.tor_id = cadastro_empresas.emp_torre )
			ON cadastro_empresas.emp_id = cadastro_usuarios.usu_empresa
			)
		ON cadastro_usuarios.usu_id = operacao_reservas.res_usuario
		LEFT JOIN aux_area_comum ON aux_area_comum.are_id = operacao_reservas.res_area_comum
		WHERE res_cliente = :res_cliente AND (res_status = :res_status1 OR res_status = :res_status2)";
$stmt = $PDO->prepare($sql);
$res_status1 = "Em Avaliação";
$res_status2 = "Aprovado";

$stmt->bindParam(':res_cliente', $_SESSION['cliente_id']);
$stmt->bindParam(':res_status1', $res_status1);
$stmt->bindParam(':res_status2', $res_status2);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows>0)
{
	$result = $stmt->fetchAll();
	foreach($result as $row)
	{
		if($row["res_status"] == "Em Avaliação")
		{
			$color = "#FF6600";
		}
		elseif($row["res_status"] == "Aprovado")
		{
			$color = "#00CC66";
		}
		elseif($row["res_status"] == "Reprovado")
		{
			$color = "#FF0000";
		}
		if(strtotime($row['res_data']) < strtotime(date("Y-m-d")))
		{
			$color = "#AAAAAA";
		}
	 $data[] = array(
	  'id'   	=> $row["res_id"],
	  'title'   => $row["are_nome"],
	  'start'   => $row["res_data"]." ".$row["res_hora_inicio"],
	  'end'   	=> $row["res_data"]." ".$row["res_hora_fim"],
	  'ap'  	=> "(Torre ".$row["tor_guarita"]." - AP ".$row["mor_ap"].")",
	  'color'   => $color
	  
	  
	 );
	}
	
	echo json_encode($data);
}
else
{

}

?>
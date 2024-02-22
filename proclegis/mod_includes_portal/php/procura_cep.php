<?php
include('connect.php');
$cep = $_POST['cep'];
$up = $_POST['up'];
$sql = "SELECT * FROM end_enderecos
	   LEFT JOIN (end_bairros 
		   LEFT JOIN (end_municipios 
				LEFT JOIN end_uf
				ON end_uf.uf_id = end_municipios.mun_uf)
		   ON end_municipios.mun_id = end_bairros.bai_municipio)
	   ON end_bairros.bai_id = end_enderecos.end_bairro
	   WHERE end_cep = :cep";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':cep', $cep);
$stmt->execute();
$rows = $stmt->rowCount();
if($up == 'uf')
{
	if($rows>0)
	{
		while($result = $stmt->fetch())
		{
			echo "<option value='".$result['uf_id']."' selected>".$result['uf_sigla']."</option>";
		}
	}
	else
	{
		echo "<option value=''>UF</option>";
		$sql = " SELECT * FROM end_uf ORDER BY uf_sigla";
		$stmt = $PDO->prepare($sql);
		$stmt->execute();
		while($result = $stmt->fetch())
		{
			echo "<option value='".$result['uf_id']."'>".$result['uf_sigla']."</option>";
		}
	}
}

if($up == 'municipio')
{
	if($rows>0)
	{
		while($result = $stmt->fetch())
		{
			echo "<option value='".$result['mun_id']."' selected>".$result['mun_nome']."</option>";
		}
	}
	else
	{
		echo "<option value=''>Municípios</option>";
	}
}

if($up == 'bairro')
{
	if($rows>0)
	{
		while($result = $stmt->fetch())
		{
			echo $result['bai_nome'];
		}
	}
	else
	{
		echo "";
	}
}

if($up == 'endereco')
{
	if($rows>0)
	{
		while($result = $stmt->fetch())
		{
			echo $result['end_endereco'];
		}
	}
	else
	{
		echo "";
	}
}

?>
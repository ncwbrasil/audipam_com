<?php
include_once("../../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../../core/mod_includes/php/connect.php");
$tipo_autor = $_POST['tipo_autor'];

$sql = "SELECT * FROM aux_autoria_tipo_autor WHERE id = :id";
$stmt = $PDO_PROCLEGIS->prepare($sql);
$stmt->bindParam(':id', $tipo_autor);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows>0)
{
	while($result = $stmt->fetch())
	{
		$tipo = $result['descricao'];
	}

	if($tipo == "Parlamentar")
	{
		$sql = "SELECT * FROM  cadastro_parlamentares";
		$stmt = $PDO_PROCLEGIS->prepare($sql);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows>0)
		{
			echo "<label>Selecione o autor:</label>";
			echo "<div style='float:left; width:70%; margin-bottom:20px;'>";
			while($result = $stmt->fetch())
			{
				echo "<input type='hidden' name='parlamentar' value='".$result['id']."'>";
				echo "<input type='radio' class='autor' name='autor' value='".$result['nome']."'>".$result['nome']."<br>";
			}
			echo "</div><p>";
		}		
	}
	elseif($tipo == "Bancada Parlamentar")
	{
		$sql = "SELECT * FROM  cadastro_bancadas";
		$stmt = $PDO_PROCLEGIS->prepare($sql);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows>0)
		{
			echo "<label>Selecione o autor:</label>";
			echo "<div style='float:left; width:70%; margin-bottom:20px;'>";
			while($result = $stmt->fetch())
			{
				echo "<input type='hidden' name='bancada' value='".$result['id']."'>";			
				echo "<input type='radio' class='autor' name='autor' value='".$result['nome']."'>".$result['nome']."<br>";
			}
			echo "</div><p>";
		}		
	}
	elseif($tipo == "Bloco Parlamentar")
	{
		$sql = "SELECT * FROM  aux_bancadas_blocos";
		$stmt = $PDO_PROCLEGIS->prepare($sql);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows>0)
		{
			echo "<label>Selecione o autor:</label>";
			echo "<div style='float:left; width:70%; margin-bottom:20px;'>";
			while($result = $stmt->fetch())
			{
				echo "<input type='hidden' name='bloco' value='".$result['id']."'>";			
				
				echo "<input type='radio' class='autor' name='autor' value='".$result['nome']."'>".$result['nome']."<br>";
			}
			echo "</div><p>";
		}		
	}
	elseif($tipo == "Frente Parlamentar")
	{
		$sql = "SELECT * FROM  aux_bancadas_frentes";
		$stmt = $PDO_PROCLEGIS->prepare($sql);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows>0)
		{
			echo "<label>Selecione o autor:</label>";
			echo "<div style='float:left; width:70%; margin-bottom:20px;'>";
			while($result = $stmt->fetch())
			{
				echo "<input type='hidden' name='frente' value='".$result['id']."'>";	
				echo "<input type='radio' class='autor' name='autor' value='".$result['nome']."'>".$result['nome']."<br>";
			}
			echo "</div><p>";
		}		
	}
	elseif($tipo == "Comissão")
	{
		$sql = "SELECT * FROM  cadastro_comissoes";
		$stmt = $PDO_PROCLEGIS->prepare($sql);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows>0)
		{
			echo "<label>Selecione o autor:</label>";
			echo "<div style='float:left; width:70%; margin-bottom:20px;'>";
			while($result = $stmt->fetch())
			{
				echo "<input type='hidden' name='comissao' value='".$result['id']."'>";	
				echo "<input type='radio' class='autor' name='autor' value='".$result['sigla']." - ".$result['nome']."'>".$result['sigla']." - ".$result['nome']."<br>";
			}
			echo "</div><p>";
		}		
	}
	elseif($tipo == "Órgão")
	{
		$sql = "SELECT * FROM  aux_materias_orgaos";
		$stmt = $PDO_PROCLEGIS->prepare($sql);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows>0)
		{
			echo "<label>Selecione o autor:</label>";
			echo "<div style='float:left; width:70%; margin-bottom:20px;'>";
			while($result = $stmt->fetch())
			{
				echo "<input type='hidden' name='orgao' value='".$result['id']."'>";	
				echo "<input type='radio' class='autor' name='autor' value='".$result['sigla']." - ".$result['nome']."'>".$result['sigla']." - ".$result['nome']."<br>";
			}
			echo "</div><p>";
		}		
	}
	else
	{
			
	}
}
else
{
	echo "";
}
?>
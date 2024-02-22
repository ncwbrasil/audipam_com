<?php
session_start();
include('connect.php');

$legislatura = $_POST['legislatura'];

$sql = "SELECT *,cadastro_parlamentares.id as parlamentar_id, cadastro_parlamentares.nome as parlamentar_nome FROM cadastro_parlamentares
		LEFT JOIN ( cadastro_parlamentares_mandatos 
			LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_parlamentares_mandatos.legislatura )
		ON cadastro_parlamentares_mandatos.parlamentar =  cadastro_parlamentares.id 
		LEFT JOIN ( cadastro_parlamentares_filiacoes 
			LEFT JOIN aux_parlamentares_partidos ON aux_parlamentares_partidos.id = cadastro_parlamentares_filiacoes.partido )
		ON cadastro_parlamentares_filiacoes.parlamentar =  cadastro_parlamentares.id 
		WHERE cadastro_parlamentares_mandatos.legislatura = :legislatura
		GROUP BY cadastro_parlamentares.id
		ORDER BY cadastro_parlamentares.nome";
$stmt_int = $PDO_PROCLEGIS->prepare($sql);     
$stmt_int->bindParam(":legislatura", $legislatura);                                                          
$stmt_int->execute();
$rows_int = $stmt_int->rowCount();
if($rows_int > 0)
{
	while($result_int = $stmt_int->fetch())
	{
		echo "
		<a href='vereador/".$result_int['parlamentar_id']."'>
		<div class='blocos'>
			<div class='vereadores-foto' style='background:url(".str_replace("../","",$result_int['foto']).") top center no-repeat; background-size: 80%; border-radius:60px; width:80px; height:80px; border:1px solid #CCC;' border='0'></div>
			<div class='vereadores-nome'><span class='bold'>".$result_int['parlamentar_nome']."</span><br>".$result_int['sigla']."</div>
		</div>
		</a>";
	}                
}
else
{
	echo "Não há vereadores cadastrados para esta legislatura.";
}
      
?>
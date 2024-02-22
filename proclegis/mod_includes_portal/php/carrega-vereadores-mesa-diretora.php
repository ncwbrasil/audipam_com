<?php
session_start();
include('connect.php');

$sessao = $_POST['sessao'];

$sql = "SELECT *, cadastro_mesa_diretora.id as id,
				aux_parlamentares_legislaturas.numero as numero,
				YEAR(aux_parlamentares_legislaturas.data_inicio) as data_inicio, 
				YEAR(aux_parlamentares_legislaturas.data_fim) as data_fim,
				aux_mesa_diretora_sessoes.numero as numero_sessao,
				YEAR(aux_mesa_diretora_sessoes.data_inicio) as data_inicio_sessao, 
				YEAR(aux_mesa_diretora_sessoes.data_fim) as data_fim_sessao,
				aux_mesa_diretora_cargos.descricao as descricao_cargo,
				cadastro_parlamentares.nome as parlamentar_nome,
				cadastro_parlamentares.id as parlamentar_id                    
		FROM cadastro_mesa_diretora 
		
		LEFT JOIN aux_parlamentares_legislaturas ON aux_parlamentares_legislaturas.id = cadastro_mesa_diretora.legislatura
		LEFT JOIN aux_mesa_diretora_sessoes ON aux_mesa_diretora_sessoes.id = cadastro_mesa_diretora.sessao      
		LEFT JOIN (cadastro_mesa_diretora_composicao 
			LEFT JOIN aux_mesa_diretora_cargos ON aux_mesa_diretora_cargos.id = cadastro_mesa_diretora_composicao.cargo
			LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = cadastro_mesa_diretora_composicao.parlamentar  )
		ON cadastro_mesa_diretora_composicao.mesa_diretora = cadastro_mesa_diretora.id
		                        
		WHERE cadastro_mesa_diretora.sessao = :sessao 			
		ORDER BY aux_mesa_diretora_cargos.id ASC";
$stmt_int = $PDO_PROCLEGIS->prepare($sql);     
$stmt_int->bindParam(":sessao", $sessao);                                                          
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
			<div class='vereadores-nome'><span class='bold'>".$result_int['parlamentar_nome']."</span><br>".$result_int['descricao_cargo']."</div>
		</div>
		</a>";
	}  
}
else
{
	echo "Não há mesa diretora cadastrada para este período.";
}
              

?>
<?php
session_start();
include('connect.php');

$periodo = $ano = $_POST['periodo'];


$sql = "SELECT *, cadastro_comissoes.id as comissao_id 
				, cadastro_comissoes.nome as comissao_nome
				, cadastro_comissoes.descricao as comissao_descricao
				, aux_comissoes_tipos.nome as comissao_tipo 
				, aux_comissoes_tipos.natureza as comissao_natureza                    
		FROM cadastro_comissoes 
		LEFT JOIN aux_comissoes_tipos ON aux_comissoes_tipos.id = cadastro_comissoes.tipo      
		LEFT JOIN cadastro_comissoes_composicao ON cadastro_comissoes_composicao.comissao = cadastro_comissoes.id		                        
		/*WHERE cadastro_comissoes_composicao.periodo = :periodo 	*/
		WHERE YEAR(cadastro_comissoes.data_criacao) = :ano 	
		GROUP BY cadastro_comissoes.id
		ORDER BY cadastro_comissoes.nome ASC";
$stmt_int = $PDO_PROCLEGIS->prepare($sql);     
//$stmt_int->bindParam(":periodo", $periodo);                                                          
$stmt_int->bindParam(":ano", $ano);                                                          
$stmt_int->execute();
$rows_int = $stmt_int->rowCount();
if($rows_int > 0)
{
	while($result_int = $stmt_int->fetch())
	{
		echo "
		<a href='comissao/".$result_int['comissao_id']."'>
		<div class='blocos'>
			<span class='bold'>".$result_int['comissao_nome']."</span> - ".$result_int['comissao_natureza']."<br>
			<span class='bold'>Tipo:</span> ".$result_int['comissao_tipo']."<br>
			".$result_int['comissao_descricao']."
		</div>
		</a>";
	}  
}
else
{
	echo "Não há comissões cadastrada para este período.";
}
              

?>
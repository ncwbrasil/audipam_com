<?php
include('connect.php');
require_once("funcoes.php");
sec_session_start(); 
?>

<?php
$busca = str_replace(".","",str_replace("-","",$_POST['busca']));

$sql = "SELECT * FROM admin_setores_permissoes
		LEFT JOIN admin_submodulos ON admin_submodulos.sub_id = admin_setores_permissoes.sep_submodulo
		INNER JOIN admin_modulos ON admin_modulos.mod_id = admin_setores_permissoes.sep_modulo 
		INNER JOIN ( admin_setores 
			INNER JOIN cadastro_usuarios 
			ON cadastro_usuarios.usu_setor = admin_setores.set_id )
		ON admin_setores.set_id = admin_setores_permissoes.sep_setor
		WHERE (mod_nome LIKE :mod_nome OR sub_nome LIKE :sub_nome)
		GROUP BY sub_id  
		ORDER BY mod_ordem, sub_ordem ASC
		";
$sub_nome = "%".$busca."%";
$mod_nome = "%".$busca."%";
$stmt = $PDO->prepare($sql);

//$stmt->bindParam(':sep_setor', $_SESSION['setor_id'] );
$stmt->bindParam(':mod_nome', $mod_nome );
$stmt->bindParam(':sub_nome', $sub_nome );
$stmt->execute();
$rows = $stmt->rowCount();

if($rows>0)
{
	while($result = $stmt->fetch())
	{
		echo "
		<a class='search' href='".$result['sub_link']."/view'>
		<div class='result'>
		<span>".$result['mod_nome']." &raquo; ".$result['sub_nome']."</span>
		</div>
		</a>
		";
	}
	
}
else
{
	echo "<div class='result'>
			Nada encontrado :(
		  </div>"; 
}
?>
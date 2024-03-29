<?php
//VARIAVEIS
$null = '';
$inicio 	= date("Y-m")."-01";                
$hoje 		= date("Y-m-d")." 23:59:59";
$mes_passado_ini = date("Y-m",strtotime("-1 month"))."-01";
$mes_passado_fim = date("Y-m-d",strtotime("-1 month"));
$atual = $diferenca = $dif = $valor_old = $pc = "";

$sql = "SELECT count(id) AS valor FROM cadastro_sessoes_plenarias 
        WHERE ativo <> :ativo     
";
$stmt = $PDO_PROCLEGIS->prepare($sql);
$stmt->bindValue(':ativo', 0);  
$stmt->execute();
$rows = $stmt->rowCount();
if($rows > 0)
{   
    while($result = $stmt->fetch())
    {
        $atual = $result['valor'];                
    }
}

?>
<div class='quad-int'><i class="nc-icon nc-vector"></i>
    <i class="fas fa-microphone-alt blue"></i> 
    <div class='txt'>
        Sessões Plenárias <br>
        <span class='n'><?php echo str_pad($atual,2,"0",STR_PAD_LEFT);?></span> <br>
        
    </div>		
    <div class='more'>
        <a href='cadastro_sessoes_plenarias/view'><i class="fas fa-cog"></i> Gerenciar</a>
    </div>
</div>
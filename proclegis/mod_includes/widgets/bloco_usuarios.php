<?php
//VARIAVEIS
$null = '';
$inicio 	= date("Y-m")."-01";                
$hoje 		= date("Y-m-d")."";
$mes_passado_ini = date("Y-m",strtotime("-1 month"))."-01";
$mes_passado_fim = date("Y-m-d",strtotime("-1 month"));

$atual = $diferenca = $dif = $valor_old = $pc = "";

$sql = "SELECT COUNT(usu_id) as valor FROM cadastro_usuarios  ";
$stmt = $PDO_PROCLEGIS->prepare($sql);
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
    <i class="fas fa-user blue"></i> 
    <div class='txt'>
        Usuários<br>
        <span class='n'><?php echo str_pad($atual,2,"0",STR_PAD_LEFT);?></span> <br>
        <?php //echo $dif;?> <?php //echo $pc;?>
    </div>		
    <div class='more'>
        <a href='cadastro_usuarios/view'><i class="fas fa-cog"></i> Gerenciar</a>
    </div>
</div>
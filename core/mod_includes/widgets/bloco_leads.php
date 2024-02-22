<?php
//VARIAVEIS
$null = '';
$inicio 	= date("Y-m")."-01";                
$hoje 		= date("Y-m-d")."";
$mes_passado_ini = date("Y-m",strtotime("-1 month"))."-01";
$mes_passado_fim = date("Y-m-d",strtotime("-1 month"));

$atual = $diferenca = $dif = $valor_old = $pc = "";

$sql = "SELECT COUNT(lea_id) as valor FROM cadastro_leads  ";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(":inicio", $inicio);
$stmt->bindParam(":hoje", $hoje);
$stmt->execute();
$rows = $stmt->rowCount();

if($rows > 0)
{
    $sql_rec_compara = "SELECT COUNT(lea_id) as valor FROM cadastro_leads  ";
    $stmt_rec_compara = $PDO->prepare($sql_rec_compara);    
    $stmt_rec_compara->bindParam(":mes_passado_ini", $mes_passado_ini);
    $stmt_rec_compara->bindParam(":mes_passado_fim", $mes_passado_fim);
    $stmt_rec_compara->execute();
    $rows_rec_compara = $stmt_rec_compara->rowCount();;
    if($rows_rec_compara > 0)
    {
        while($result_rec_compara = $stmt_rec_compara->fetch())
        {
            $valor_old = $result_rec_compara['valor'];
            
        }
    }
    while($result = $stmt->fetch())
    {
        $atual = $result['valor'];
        $diferenca = $result['valor'] - $valor_old;

        // PORCENTAGEM
        $pc = (($atual/$valor_old)-1)*100;
        if($valor_old == 0)
        {
            $pc = 0;
        }
        if($pc > 0)
        {
            $pc = "<span class='verde'>(".number_format($pc,1,",",".")."%)</span>";
        }
        elseif($pc < 0)
        {
            $pc = "<span class='vermelho'>(".number_format($pc,1,",",".")."%)</span>";
        }
        else
        {
            $pc = "(".number_format($pc,1,",",".")."%)";
        }


        if($diferenca > 0)
        {
            $dif = "<span class='verde'>&#9650; ".$diferenca."</span>";
        }
        elseif($diferenca <0)
        {
            $dif = "<span class='vermelho'>&#9660; ".$diferenca."</span>";
        }
        elseif($diferenca == 0)
        {
            $dif = "<span>&harr; R$ ".$diferenca."</span>";
        }
    }
}

?>
<div class='quad-int'><i class="nc-icon nc-vector"></i>
    <i class="fas fa-filter blue"></i> 
    <div class='txt'>
        Leads<br>
        <span class='n'><?php echo $atual;?></span> <br>
        <?php //echo $dif;?> <?php //echo $pc;?>
    </div>		
    <div class='more'>
        <a href='cadastro_leads/view'><i class="fas fa-cog"></i> Gerenciar</a>
    </div>
</div>
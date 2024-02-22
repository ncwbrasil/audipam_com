<?php
//VARIAVEIS
$null = '';
$inicio 	= date("Y-m")."-01";                
$hoje 		= date("Y-m-d")." 23:59:59";
$mes_passado_ini = date("Y-m",strtotime("-1 month"))."-01";
$mes_passado_fim = date("Y-m-d",strtotime("-1 month"));
$atual = $diferenca = $dif = $valor_old = $pc = "";

$sql = "SELECT SUM(des_valor_pago) AS valor FROM financeiro_despesas  
        WHERE des_data_pagamento  BETWEEN :inicio AND :hoje
";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(":inicio", $inicio);
$stmt->bindParam(":hoje", $hoje);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows > 0)
{
    $sql_dec_compara = "SELECT SUM(des_valor_pago) AS valor FROM financeiro_despesas  
                        WHERE des_data_pagamento BETWEEN  :mes_passado_ini AND :mes_passado_fim
                        ";
    $stmt_dec_compara = $PDO->prepare($sql_dec_compara);
    $stmt_dec_compara->bindParam(":mes_passado_ini", $mes_passado_ini);
    $stmt_dec_compara->bindParam(":mes_passado_fim", $mes_passado_fim);
    $stmt_dec_compara->execute();
    $rows_dec_compara = $stmt_dec_compara->rowCount();;
    if($rows_dec_compara > 0)
    {
        while($result_dec_compara = $stmt_dec_compara->fetch())
        {
            $valor_old = $result_dec_compara['valor'];
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
        if($pc < 0)
        {
            $pc = "<span class='verde'>(".number_format($pc,1,",",".")."%)</span>";
        }
        elseif($pc > 0)
        {
            $pc = "<span class='vermelho'>(".number_format($pc,1,",",".")."%)</span>";
        }
        else
        {
            $pc = "(".number_format($pc,1,",",".")."%)";
        }

        if($diferenca < 0)
        {
            $dif = "<span class='verde'>&#9660; R$ ".number_format($diferenca,2,',','.')."</span>";
        }
        elseif($diferenca > 0)
        {
            $dif = "<span class='vermelho'>&#9650; R$ ".number_format($diferenca,2,',','.')."</span>";
        }
        elseif($diferenca == 0)
        {
            $dif = "<span>&harr; R$ ".number_format($diferenca,2,',','.')."</span>";
        }
    }
}

?>
<div class='quad-int'><i class="nc-icon nc-vector"></i>
    <i class="fas fa-dollar-sign red"></i> 
    <div class='txt'>
        Despesas<br>
        <span class='n'>R$ <?php echo number_format($atual,2,",",".");?></span> <br>
        <?php echo $dif;?> <?php echo $pc;?>
    </div>		
    <div class='more'>
        <a href='relatorios_despesas/view'><i class="fas fa-cog"></i> Gerenciar</a>
    </div>
</div>
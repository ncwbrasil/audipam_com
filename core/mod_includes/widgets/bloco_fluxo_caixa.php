<?php
//VARIAVEIS
$null = '';
$inicio 	= date("Y-m")."-01";                
$hoje 		= date("Y-m-d")." 23:59:59";
$mes_passado_ini = date("Y-m",strtotime("-1 month"))."-01";
$mes_passado_fim = date("Y-m-d",strtotime("-1 month"));
$atual = $diferenca = $dif = $valor_old = $pc = "";

$sql = "SELECT SUM(valor) as valor FROM ( 
        SELECT SUM(fat_valor_pago) AS valor FROM financeiro_faturas 
        WHERE fat_valor_pago > 0 AND fat_data_pagamento BETWEEN :inicio1 AND :hoje1
        UNION ALL
        SELECT SUM(-des_valor_pago) as valor FROM financeiro_despesas
        WHERE des_data_pagamento BETWEEN :inicio2 AND :hoje2
    ) as valor 
    ";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(":inicio1", $inicio);
$stmt->bindParam(":hoje1", $hoje);
$stmt->bindParam(":inicio2", $inicio);
$stmt->bindParam(":hoje2", $hoje);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows > 0)
{
    $sql_flx_compara = "SELECT SUM(valor) as valor FROM ( 
                    SELECT SUM(fat_valor_pago) AS valor FROM financeiro_faturas 
                    WHERE fat_valor_pago > 0 AND fat_data_pagamento BETWEEN :mes_passado_ini1 AND :mes_passado_fim1
                    UNION ALL
                    SELECT SUM(-des_valor_pago) as valor FROM financeiro_despesas
                    WHERE des_data_pagamento BETWEEN :mes_passado_ini2 AND :mes_passado_fim2
                ) as valor 
                    ";
    $stmt_flx_compara = $PDO->prepare($sql_flx_compara);
    $stmt_flx_compara->bindParam(":mes_passado_ini1", $mes_passado_ini);
    $stmt_flx_compara->bindParam(":mes_passado_fim1", $mes_passado_fim);
    $stmt_flx_compara->bindParam(":mes_passado_ini2", $mes_passado_ini);
    $stmt_flx_compara->bindParam(":mes_passado_fim2", $mes_passado_fim);
    $stmt_flx_compara->execute();
    $rows_flx_compara = $stmt_flx_compara->rowCount();;
    if($rows_flx_compara > 0)
    {
        while($result_flx_compara = $stmt_flx_compara->fetch())
        {
            $valor_old = $result_flx_compara['valor'];
        }
    }
    while($result = $stmt->fetch())
    {
        
        $atual = $result['valor'];
        //echo "Atual: ".$atual."<br>"."Passado: ".$valor_old;
        $diferenca = $result['valor'] - $valor_old;

        // PORCENTAGEM
        $pc = ((($atual/$valor_old)-1)*100);        
        if(($atual < 0 && $valor_old < 0) || ($atual > 0 && $valor_old < 0))
        {
            $pc = ($pc)*(-1);            
        }
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
            $dif = "<span class='verde'>&#9650; R$ ".number_format($diferenca,2,',','.')."</span>";
        }
        elseif($diferenca <0)
        {
            $dif = "<span class='vermelho'>&#9660; R$ ".number_format($diferenca,2,',','.')."</span>";
        }
        elseif($diferenca == 0)
        {
            $dif = "<span>&harr; R$ ".number_format($diferenca,2,',','.')."</span>";
        }
    }
}

?>
<div class='quad-int'><i class="nc-icon nc-vector"></i>
    <i class="fas fa-exchange-alt lar"></i> 
    <div class='txt'>
        Fluxo de Caixa<br>
        <span class='n'>R$ <?php echo number_format($atual,2,",",".");?></span> <br>
        <?php echo $dif;?> <?php echo $pc;?>
    </div>		
    <div class='more'>
        <a href='relatorios_fluxo_caixa/view'><i class="fas fa-cog"></i> Gerenciar</a>
    </div>
</div>
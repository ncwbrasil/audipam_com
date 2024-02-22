<?php
//VARIAVEIS
$null = '';
$inicio 	= date("Y-m")."-01";                
$hoje 		= date("Y-m-d")." 23:59:59";
$mes_passado_ini = date("Y-m",strtotime("-1 month"))."-01";
$mes_passado_fim = date("Y-m-d",strtotime("-1 month"));
$atual = $diferenca = $dif = $valor_old = $pc = "";

$sql = "SELECT SUM(lse_valor) AS valor FROM cadastro_leads_servicos 
        LEFT JOIN cadastro_leads ON cadastro_leads.lea_id = cadastro_leads_servicos.lse_lead
        WHERE lse_tipo = :lse_tipo AND lea_reprovado <> :lea_reprovado
";
$stmt = $PDO->prepare($sql);
$lse_tipo = "Avulso";
$stmt->bindParam(":lse_tipo", $lse_tipo);
$stmt->bindParam(":inicio", $inicio);
$stmt->bindParam(":hoje", $hoje);
$stmt->bindValue(':lea_reprovado', 			1);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows > 0)
{
    $sql_rec_compara = "SELECT SUM(lse_valor) AS valor FROM cadastro_leads_servicos     
                        LEFT JOIN cadastro_leads ON cadastro_leads.lea_id = cadastro_leads_servicos.lse_lead
                        WHERE lse_valor = :lse_valor AND lea_reprovado <> :lea_reprovado
                        ";
    $stmt_rec_compara = $PDO->prepare($sql_rec_compara);
    $lse_tipo = "Avulso";
    $stmt_rec_compara->bindParam(":lse_tipo", $lse_tipo);    
    $stmt_rec_compara->bindParam(":mes_passado_ini", $mes_passado_ini);
    $stmt_rec_compara->bindParam(":mes_passado_fim", $mes_passado_fim);
    $stmt_rec_compara->bindValue(':lea_reprovado', 			1);
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
    <i class="fab fa-first-order-alt green"></i> 
    <div class='txt'>
        Leads | Avulsos<br>
        <span class='n'>R$ <?php echo number_format($atual,2,",",".");?></span> <br>
        <?php //echo $dif;?> <?php //echo $pc;?>
    </div>		
    <div class='more'>
        <a href='cadastro_leads/view'><i class="fas fa-cog"></i> Gerenciar</a>
    </div>
</div>
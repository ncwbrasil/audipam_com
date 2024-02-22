<?php
//VARIAVEIS
$null = '';
$inicio 	= date("Y-m")."-01";                
$hoje 		= date("Y-m-d")." 23:59:59";
$mes_passado_ini = date("Y-m",strtotime("-1 month"))."-01";
$mes_passado_fim = date("Y-m-d",strtotime("-1 month"));

$atual = $diferenca = $dif = $valor_old = $pc = "";

$sql = "SELECT valor/servicos as ticket FROM
        (
        SELECT SUM(ser_valor) AS valor FROM cadastro_servicos WHERE ser_valor > :ser_valor AND ser_data_inicio BETWEEN :inicio AND :hoje
        ) as valor,
        (
        SELECT COUNT(ser_id) AS servicos FROM cadastro_servicos WHERE ser_valor > :ser_valor2 AND ser_data_inicio BETWEEN :inicio2 AND :hoje2
        ) as servicos 
";
$stmt = $PDO->prepare($sql);
$stmt->bindValue(":ser_valor", 0);
$stmt->bindValue(":ser_valor2", 0);
$stmt->bindParam(":null", $null);
$stmt->bindParam(":inicio", $inicio);
$stmt->bindParam(":hoje", $hoje);
$stmt->bindParam(":inicio2", $inicio);
$stmt->bindParam(":hoje2", $hoje);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows > 0)
{
    $sql_rec_compara = "
                    SELECT valor/servicos as ticket FROM
                    (
                    SELECT SUM(ser_valor) AS valor FROM cadastro_servicos WHERE ser_valor > :ser_valor AND ser_data_inicio BETWEEN :mes_passado_ini AND :mes_passado_fim
                    ) as valor,
                    (
                    SELECT COUNT(ser_id) AS servicos FROM cadastro_servicos WHERE ser_valor > :ser_valor2 AND ser_data_inicio BETWEEN :mes_passado_ini2 AND :mes_passado_fim2
                    ) as servicos                     
                    ";
    $stmt_rec_compara = $PDO->prepare($sql_rec_compara);
    $stmt_rec_compara->bindValue(":ser_valor", 0);
    $stmt_rec_compara->bindValue(":ser_valor2", 0);
    $stmt_rec_compara->bindParam(":null", $null);
    $stmt_rec_compara->bindParam(":mes_passado_ini", $mes_passado_ini);
    $stmt_rec_compara->bindParam(":mes_passado_fim", $mes_passado_fim);
    $stmt_rec_compara->bindParam(":mes_passado_ini2", $mes_passado_ini);
    $stmt_rec_compara->bindParam(":mes_passado_fim2", $mes_passado_fim);
    $stmt_rec_compara->execute();
    $rows_rec_compara = $stmt_rec_compara->rowCount();;
    if($rows_rec_compara > 0)
    {
        while($result_rec_compara = $stmt_rec_compara->fetch())
        {
            $valor_old = $result_rec_compara['ticket'];
            
        }
    }
    while($result = $stmt->fetch())
    {
        $atual = $result['ticket'];
        $diferenca = $result['ticket'] - $valor_old;

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
    <i class="fas fa-ticket-alt blue"></i> 
    <div class='txt'>
        Ticket Médio<br>
        <span class='n'>R$ <?php echo number_format($atual,2,",",".");?></span> <br>
        <?php echo $dif;?> <?php echo $pc;?>
    </div>		
    <div class='more'>
        <a href='relatorios_receitas/view'><i class="fas fa-cog"></i> Gerenciar</a>
    </div>
</div>
<?php
//VARIAVEIS
$null = '';
$inicio 	= date("Y-m")."-01";                
$hoje 		= date("Y-m-d")." 23:59:59";
$mes_passado_ini = date("Y-m",strtotime("-1 month"))."-01";
$mes_passado_fim = date("Y-m-d",strtotime("-1 month"));
$atual = $diferenca = $dif = $valor_old = $pc = "";


$sql = "SELECT SUM(fat_valor) AS valor FROM financeiro_faturas 
        LEFT JOIN aux_forma_pagamento ON aux_forma_pagamento.fpg_id = financeiro_faturas.fat_forma_pagamento 
        LEFT JOIN (cadastro_servicos 
                LEFT JOIN aux_tipo_servico 
                ON aux_tipo_servico.tps_id = cadastro_servicos.ser_tipo_servico 
                LEFT JOIN cadastro_clientes 
                ON cadastro_clientes.cli_id = cadastro_servicos.ser_cliente 
                )
            ON cadastro_servicos.ser_id = financeiro_faturas.fat_servico
            LEFT JOIN financeiro_tramitacao h1 ON h1.ftr_fatura = financeiro_faturas.fat_id 
            WHERE h1.ftr_id = (SELECT MAX(h2.ftr_id) FROM financeiro_tramitacao h2 where h2.ftr_fatura = h1.ftr_fatura) AND
            (ftr_status = :ftr_status1 OR ftr_status = :ftr_status2 OR ftr_status = :ftr_status3 OR ftr_status = :ftr_status4 OR ftr_status = :ftr_status5) AND 
            DATE_FORMAT(fat_data_vencimento,'%Y-%m') = :fat_data_vencimento";																				 
$stmt = $PDO->prepare($sql);
$fat_data_vencimento = date("Y-m");
$ftr_status1 = "Em aberto";
$ftr_status2 = "Aguardando";
$ftr_status3 = "Enviado";
$ftr_status4 = "Recebido";
$ftr_status5 = "Vencido";


$stmt->bindParam(":ftr_status1", $ftr_status1);
$stmt->bindParam(":ftr_status2", $ftr_status2);
$stmt->bindParam(":ftr_status3", $ftr_status3);
$stmt->bindParam(":ftr_status4", $ftr_status4);
$stmt->bindParam(":ftr_status5", $ftr_status5);
$stmt->bindParam(":fat_data_vencimento", $fat_data_vencimento);
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
    <i class="far fa-clock purple"></i> 
    <div class='txt'>
        Previstos<br>
        <span class='n'>R$ <?php echo number_format($atual,2,",",".");?></span> <br>
        
    </div>		
    <div class='more'>
        <a href='relatorios_previstos/view'><i class="fas fa-cog"></i> Gerenciar</a>
    </div>
</div>
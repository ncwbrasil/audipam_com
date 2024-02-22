<div id='chartTipoEnsaio' style='width:48%; float:left; margin-left:2%;'>
    <!-- DASHBOARD -->
        <?php
        // QUERY PARA GRAFICO LINHAS POR UNIDADE POR MES
        $tipo = array();
        $sql = "SELECT tps_nome, COUNT(ser_id) as cnt FROM cadastro_servicos
                LEFT JOIN aux_tipo_servico ON  aux_tipo_servico.tps_id = cadastro_servicos.ser_tipo_servico
                GROUP By tps_nome
                ";
        $stmt_grafico = $PDO->prepare($sql);        
        if($stmt_grafico->execute())
        {
            while($result_grafico = $stmt_grafico->fetch())
            {
                $tipo[$result_grafico['tps_nome']] = $result_grafico['cnt'];                            
            }                
        }        
        
        ?>
    <!-- FIM DASHBOARD -->
    
</div>
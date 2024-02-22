<?php	
# FILTRO

#
?>

<div id='chartReceitaDespesa'>
    <?php 
    // echo "
    // <div class='filter' >
    // <form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='home'>
    // <select name='fil_mesesfluxoLocker' id='fil_mesesfluxoLocker'>
    //     <option value='$mesesfluxoLocker'>últimos ".$mesesfluxoLocker." dias</option>
    //     <option value='15'>últimos 15 dias</option>
    //     <option value='30'>últimos 30 dias</option>  
    //     <option value='60'>últimos 60 dias</option>               
    // </select>
    // <input type='submit' value='Filtrar'> 
    // </form>
    // </div>
    // ";
    ?>
    
    <!-- DASHBOARD -->
        <?php
        // QUERY PARA GRAFICO LINHAS POR UNIDADE POR MES
        $sql = "SELECT DATE_FORMAT(fat_data_pagamento,'%Y-%m') as data, COUNT(fat_id) as qtd FROM financeiro_faturas 
                WHERE DATE_FORMAT(fat_data_pagamento,'%Y-%m') BETWEEN :inicio AND :fim
                GROUP BY DATE_FORMAT(fat_data_pagamento,'%Y-%m')
                ORDER BY DATE_FORMAT(fat_data_pagamento,'%Y-%m') ASC";
        $stmt = $PDO->prepare($sql);
        $inicio = date("Y-m",strtotime("-12 month"));
        $fim =	date("Y-m");
        $stmt->bindParam(':inicio', $inicio);
        $stmt->bindParam(':fim', $fim);
        $stmt->execute();
        $rows = $stmt->rowCount();

        $leg_g4 = array();
        $receita = array();	
        $despesa = array();		
    
        //while($result = $stmt->fetch())
        for($x=1;$x<=12;$x++) //apagar para fluxo de 12 ultimos meses
        {
            // $data = $result['data'];
            // $data = substr($data, -2);
            // switch ($data)
            switch ($x)  //apagar para fluxo de 12 ultimos meses
            {
                case 1: $data = "Jan"; break;
                case 2: $data = "Fev"; break;
                case 3: $data = "Mar"; break;
                case 4: $data = "Abr"; break;
                case 5: $data = "Mai"; break;
                case 6: $data = "Jun"; break;
                case 7: $data = "Jul"; break;
                case 8: $data = "Ago"; break;
                case 9: $data = "Set"; break;
                case 10: $data = "Out"; break;
                case 11: $data = "Nov"; break;
                case 12: $data = "Dez"; break;
            }
            $leg_g4[] 	= $data;                        
            $result['data'] = date("Y-").str_pad($x,2,0,STR_PAD_LEFT); //apagar para fluxo de 12 ultimos meses

            $sql = "SELECT COALESCE(SUM(fat_valor_pago),0) as valor FROM financeiro_faturas 
                    WHERE DATE_FORMAT(fat_data_pagamento,'%Y-%m') = :data 
                    ";
            $stmt_ext = $PDO->prepare($sql);            
            $stmt_ext->bindParam(':data', $result['data']);            
            $stmt_ext->execute();
            $receita[] 	= $stmt_ext->fetch(PDO::FETCH_OBJ)->valor;

            $sql = "SELECT COALESCE(SUM(des_valor_pago),0) as valor FROM financeiro_despesas 
                    WHERE DATE_FORMAT(des_data_pagamento,'%Y-%m') = :data 
                    ";
            $stmt_ext = $PDO->prepare($sql);            
            $stmt_ext->bindParam(':data', $result['data']);            
            $stmt_ext->execute();
            $despesa[] 	= $stmt_ext->fetch(PDO::FETCH_OBJ)->valor;                       
        }
        $leg_g4 = json_encode($leg_g4);
        $receita = implode(",",array_values($receita ));
        $despesa = implode(",",array_values($despesa));
       

        
        ?>
    <!-- FIM DASHBOARD -->
    
</div>
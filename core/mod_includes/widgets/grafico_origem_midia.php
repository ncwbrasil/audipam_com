<div id='chartOrigemMidia' style='width:48%; float:left;'>
    <!-- DASHBOARD -->
        <?php
        // QUERY PARA GRAFICO LINHAS POR UNIDADE POR MES
        $origem = array();
        $sql = "SELECT lea_origem, COUNT(lea_id) as cnt FROM cadastro_leads
                GROUP By lea_origem
                ";
        $stmt_grafico = $PDO->prepare($sql);        
        if($stmt_grafico->execute())
        {
            while($result_grafico = $stmt_grafico->fetch())
            {
                $origem[$result_grafico['lea_origem']] = $result_grafico['cnt'];                            
            }                
        }        
       

        
        ?>
    <!-- FIM DASHBOARD -->
    
</div>
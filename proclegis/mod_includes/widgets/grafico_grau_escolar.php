<div id='chartGrauEscolar' style='width:48%; float:left;'>
    <!-- DASHBOARD -->
        <?php        
        $grau_escolar = array();
        $sql = "SELECT sug_grau_escolar, COUNT(sug_id) as cnt FROM cadastro_sugestoes
                GROUP By sug_grau_escolar
                ";
        $stmt_grafico = $PDO->prepare($sql);        
        if($stmt_grafico->execute())
        {
            while($result_grafico = $stmt_grafico->fetch())
            {
                $grau_escolar[$result_grafico['sug_grau_escolar']] = $result_grafico['cnt'];                            
            }                
        }        
       

        
        ?>
    <!-- FIM DASHBOARD -->
    
</div>
<div id='chartMunicipio' style='width:48%; float:left; margin-left:2%;'>
    <!-- DASHBOARD -->
        <?php        
        $municipio = array();
        $sql = "SELECT sug_municipio, COUNT(sug_id) as cnt FROM cadastro_sugestoes
                GROUP By sug_municipio
                ";
        $stmt_grafico = $PDO->prepare($sql);        
        if($stmt_grafico->execute())
        {
            while($result_grafico = $stmt_grafico->fetch())
            {
                $municipio[$result_grafico['sug_municipio']] = $result_grafico['cnt'];                            
            }                
        }        
        
        ?>
    <!-- FIM DASHBOARD -->
    
</div>
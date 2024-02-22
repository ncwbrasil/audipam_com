<div id='chartSexo' style='width:48%; float:left; margin-top:2%;'>
    <!-- DASHBOARD -->
        <?php        
        $sexo = array();
        $sql = "SELECT sug_sexo, COUNT(sug_id) as cnt FROM cadastro_sugestoes
                GROUP By sug_sexo
                ";
        $stmt_grafico = $PDO->prepare($sql);        
        if($stmt_grafico->execute())
        {
            while($result_grafico = $stmt_grafico->fetch())
            {
                $sexo[$result_grafico['sug_sexo']] = $result_grafico['cnt'];                            
            }                
        }        
       

        
        ?>
    <!-- FIM DASHBOARD -->
    
</div>



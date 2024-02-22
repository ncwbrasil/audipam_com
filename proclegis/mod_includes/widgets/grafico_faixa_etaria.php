<div id='chartIdade' style='width:48%; float:left; margin-top:2%; margin-left:2%; margin-bottom:2%;'>
    <!-- DASHBOARD -->
        <?php        
        $idade = array();
        $valor = array();
        $idade12 = $idade13 = $idade18 = $idade25 = $idade35 = $idade45 = $idade55 = $idade65 = 0;
        $sql = "SELECT COUNT(sug_id) as cnt FROM cadastro_sugestoes
                WHERE sug_idade <= :sug_idade			
                ";
        $stmt_grafico = $PDO->prepare($sql);
        $stmt_grafico->bindValue(":sug_idade", 12);
        if($stmt_grafico->execute())
        {
            while($result_grafico = $stmt_grafico->fetch())
            {
                $idade12 = $result_grafico['cnt'];                            
            }                
        } 
        
        $sql = "SELECT COUNT(sug_id) as cnt FROM cadastro_sugestoes
                WHERE sug_idade BETWEEN :sug_idade1 AND :sug_idade2			
                ";
        $stmt_grafico = $PDO->prepare($sql);
        $stmt_grafico->bindValue(":sug_idade1", 13);
        $stmt_grafico->bindValue(":sug_idade2", 17);
        if($stmt_grafico->execute())
        {
            while($result_grafico = $stmt_grafico->fetch())
            {
                $idade13 = $result_grafico['cnt'];                            
            }                
        }
        
        $sql = "SELECT COUNT(sug_id) as cnt FROM cadastro_sugestoes
                WHERE sug_idade BETWEEN :sug_idade1 AND :sug_idade2			
                ";
        $stmt_grafico = $PDO->prepare($sql);
        $stmt_grafico->bindValue(":sug_idade1", 18);
        $stmt_grafico->bindValue(":sug_idade2", 24);
        if($stmt_grafico->execute())
        {
            while($result_grafico = $stmt_grafico->fetch())
            {
                $idade18 = $result_grafico['cnt'];                            
            }                
        }
    
        $sql = "SELECT COUNT(sug_id) as cnt FROM cadastro_sugestoes
                WHERE sug_idade BETWEEN :sug_idade1 AND :sug_idade2			
                ";
        $stmt_grafico = $PDO->prepare($sql);
        $stmt_grafico->bindValue(":sug_idade1", 25);
        $stmt_grafico->bindValue(":sug_idade2", 34);
        if($stmt_grafico->execute())
        {
            while($result_grafico = $stmt_grafico->fetch())
            {
                $idade25 = $result_grafico['cnt'];                            
            }                
        }
    
        $sql = "SELECT COUNT(sug_id) as cnt FROM cadastro_sugestoes
                WHERE sug_idade BETWEEN :sug_idade1 AND :sug_idade2			
                ";
        $stmt_grafico = $PDO->prepare($sql);
        $stmt_grafico->bindValue(":sug_idade1", 35);
        $stmt_grafico->bindValue(":sug_idade2", 44);
        if($stmt_grafico->execute())
        {
            while($result_grafico = $stmt_grafico->fetch())
            {
                $idade35 = $result_grafico['cnt'];                            
            }                
        }
    
        $sql = "SELECT COUNT(sug_id) as cnt FROM cadastro_sugestoes
                WHERE sug_idade BETWEEN :sug_idade1 AND :sug_idade2			
                ";
        $stmt_grafico = $PDO->prepare($sql);
        $stmt_grafico->bindValue(":sug_idade1", 45);
        $stmt_grafico->bindValue(":sug_idade2", 54);
        if($stmt_grafico->execute())
        {
            while($result_grafico = $stmt_grafico->fetch())
            {
                $idade45 = $result_grafico['cnt'];                            
            }                
        }
    
        $sql = "SELECT COUNT(sug_id) as cnt FROM cadastro_sugestoes
                WHERE sug_idade BETWEEN :sug_idade1 AND :sug_idade2			
                ";
        $stmt_grafico = $PDO->prepare($sql);
        $stmt_grafico->bindValue(":sug_idade1", 55);
        $stmt_grafico->bindValue(":sug_idade2", 64);
        if($stmt_grafico->execute())
        {
            while($result_grafico = $stmt_grafico->fetch())
            {
                $idade55 = $result_grafico['cnt'];                            
            }                
        }
        $sql = "SELECT COUNT(sug_id) as cnt FROM cadastro_sugestoes
                WHERE sug_idade >= :sug_idade1			
                ";
        $stmt_grafico = $PDO->prepare($sql);
        $stmt_grafico->bindValue(":sug_idade1", 65);                    
        if($stmt_grafico->execute())
        {
            while($result_grafico = $stmt_grafico->fetch())
            {
                $idade65 = $result_grafico['cnt'];                            
            }                
        }     
       

        
        ?>
    <!-- FIM DASHBOARD -->
    
</div>


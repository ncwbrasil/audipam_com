<?php
    $_SESSION['cliente_url'] = $_POST['cliente_url']; 
    $_SESSION['sistema_url'] = $_POST['sistema_url']; 

    include_once("../../core/mod_includes/php/connect.php");
    $tipo = $_POST['tp']; 
    $ano = $_POST['an']; 
    $acao = $_POST['acao']; 

    if($acao == 'carrega_numero_proposicao'){
        $sql = "SELECT MAX(numero) as numero
        FROM cadastro_proposicoes WHERE tipo = :tipo AND ano = :ano";
        $stmt = $PDO_PROCLEGIS->prepare($sql);    
        $stmt->bindParam(':tipo', 	$tipo);
        $stmt->bindValue(':ano', $ano);
        $stmt->execute();
        $rows = $stmt->rowCount();
        if($rows > 0)
        {
            $result = $stmt->fetch();

            if ($result['numero'] == ''){
                $numero= "000001"; 
            }
            else {
                $numero = $result['numero']+1; 
            }

            $sql_tipo = "SELECT * FROM aux_proposicoes_tipos WHERE id = :id"; 
            $stmt_tipo = $PDO_PROCLEGIS->prepare($sql_tipo);    
            $stmt_tipo->bindValue(':id', $tipo);
            $stmt_tipo->execute();
            $rows_tipo = $stmt_tipo->rowCount();
            if($rows_tipo > 0)
            {
                $result_tipo = $stmt_tipo->fetch(); 

                $hoje = date('Y-m-d'); 
                $dias = $result_tipo['dias_uteis']; 
   
                $prazo_final  = date('Y-m-d', strtotime("+$dias weekdays",strtotime($hoje)));                        
                
                $sql_prazo = "SELECT * FROM aux_agenda_data_inativa WHERE din_data_completa >= :data_final
                ORDER BY din_data_completa ASC"; 
                $stmt_prazo = $PDO_PROCLEGIS->prepare($sql_prazo);    
                $stmt_prazo->bindValue(':data_final', $prazo_final);
                $stmt_prazo->execute();
                $rows_prazo = $stmt_prazo->rowCount();
                if($rows_prazo > 0)
                {   
                    while($result_datas = $stmt_prazo->fetch()){
                        if($result_datas['din_data_completa'] == $prazo_final){
                            $prazo_final = date('Y-m-d', strtotime("+1 days",strtotime($prazo_final)));
                            $dia_semana = date('l', strtotime($prazo_final)); 

                            if($dia_semana == 'Saturday'){
                                $prazo_final = date('Y-m-d', strtotime("+2 days",strtotime($prazo_final)));
                                $dia_semana = date('l', strtotime($prazo_final)); 
                            }
                            else if ($dia_semana == 'Sunday'){
                                $prazo_final = date('Y-m-d', strtotime("+1 days",strtotime($prazo_final)));
                                $dia_semana = date('l', strtotime($prazo_final)); 
                            }
                        }else {
                            break; 
                        }
                   }
                   echo "
                    <p><label>Número*:</label> <input name='numero' id='numero' placeholder='Número' value='$numero' class='obg'>
                    <p><label>Prazo Final*:</label> <input name='prazo_final' id='prazo_final' placeholder='Prazo Final' value='".date('d/m/Y',strtotime($prazo_final))."' class='obg' readonly>                   
                   ";

                }
                else {
                    echo "
                    <p><label>Número*:</label> <input name='numero' id='numero' placeholder='Número' value='$numero' class='obg'>
                    <p><label>Prazo Final*:</label> <input name='prazo_final' id='prazo_final' placeholder='Prazo Final' value='".date('d/m/Y',strtotime($prazo_final))."' class='obg' readonly>                   
                   ";

                }        
            }    
        }
    }

    if($acao == 'carrega_numero_materia'){
        $sql = "SELECT MAX(numero) as numero
        FROM cadastro_materias WHERE tipo = :tipo AND ano = :ano";
        $stmt = $PDO_PROCLEGIS->prepare($sql);    
        $stmt->bindParam(':tipo', 	$tipo);
        $stmt->bindValue(':ano', $ano);
        $stmt->execute();
        $rows = $stmt->rowCount();
        if($rows > 0)
        {
            $result = $stmt->fetch();
            if ($result['numero'] == ''){
                echo $numero = "000001"; 
            }
            else {
                echo $numero = $result['numero']+1; 
            }
        }
    }

    if($acao == 'carrega_ano_materia'){
        $sql = "SELECT ano FROM cadastro_materias 
        WHERE tipo = :tipo
        GROUP BY ano";
        $stmt = $PDO_PROCLEGIS->prepare($sql);    
        $stmt->bindParam(':tipo', 	$tipo);
        $stmt->execute();
        $rows = $stmt->rowCount();
        if($rows > 0)
        {
            while ($result = $stmt->fetch()){
                echo "<option value ='".$result['ano']."'>".$result['ano']."</option>"; 
            }
        }
    }

    if($acao == 'carrega_numero_materia_proposicao'){
        $sql = "SELECT * FROM cadastro_materias 
        WHERE tipo = :tipo AND ano = :ano";
        $stmt = $PDO_PROCLEGIS->prepare($sql);    
        $stmt->bindParam(':tipo', 	$tipo);
        $stmt->bindParam(':ano', 	$ano);
        $stmt->execute();
        $rows = $stmt->rowCount();
        if($rows > 0)
        {
            while ($result = $stmt->fetch()){
                echo "<option value ='".$result['id']."'> Nº ".$result['numero']."</option>"; 
            }
        }
    }
?>

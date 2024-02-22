<?php	
# FILTRO
$ano = date("Y");
$mes = date("m");

$fil_meses = $_REQUEST['fil_meses'];
if($fil_meses == '')
{
    $meses = "30";	
    $inicio = date("Y-m-d",strtotime("-".$meses." days"));
    $fim =	date("Y-m-d");
} 
else
{
    $meses = $fil_meses;
    $inicio = date("Y-m-d",strtotime("-".$meses." days"));
    $fim =	date("Y-m-d");	
}
?>

<div id='chartPrograma'>
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
        $sql = "SELECT pro_nome, COUNT(sug_id) as qtd FROM cadastro_sugestoes
                LEFT JOIN aux_programa_governo ON aux_programa_governo.pro_id = cadastro_sugestoes.sug_programa
                GROUP BY pro_nome
                ";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':inicio', $inicio);
        $stmt->bindParam(':fim', $fim);
        $stmt->execute();
        $rows = $stmt->rowCount();

        $leg_g4 = array();
        $qtd_g4 = array();	
        // $qtd_ext = array();	
        // $qtd_int = array();		
        while($result = $stmt->fetch())
        {
            
            $pro_nome = $result['pro_nome'];
           
            
            $leg_g4[] 	= $pro_nome;
            $qtd_g4[] 	= $result['qtd'];					
        }

        
        $leg_g4 = json_encode($leg_g4);
        $qtd_g4 = implode(",",array_values($qtd_g4));
       

        
        ?>
    <!-- FIM DASHBOARD -->
    
</div>
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

<div id='chartSugestoes'>
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
        $sql = "SELECT DATE_FORMAT(sug_data_cadastro,'%Y-%m-%d') as data, COUNT(sug_id) as qtd FROM cadastro_sugestoes
                WHERE DATE_FORMAT(sug_data_cadastro,'%Y-%m-%d') BETWEEN :inicio AND :fim 
                GROUP BY DATE_FORMAT(sug_data_cadastro,'%Y-%m-%d')
                ORDER BY DATE_FORMAT(sug_data_cadastro,'%Y-%m-%d') ASC";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':inicio', $inicio);
        $stmt->bindParam(':fim', $fim);
        $stmt->execute();
        $rows = $stmt->rowCount();

        $leg_g3 = array();
        $qtd_g3 = array();	
        // $qtd_ext = array();	
        // $qtd_int = array();		
        while($result = $stmt->fetch())
        {
            $data = $result['data'];
            $data = substr($data, -5);
            
            $leg_g3[] 	= implode("/",array_reverse(explode("-",$data)));
            $qtd_g3[] 	= $result['qtd'];					
        }

        
        $leg_g3 = json_encode($leg_g3);
        $qtd_g3 = implode(",",array_values($qtd_g3));
       

        
        ?>
    <!-- FIM DASHBOARD -->
    
</div>
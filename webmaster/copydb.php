<script>
abreMask(
'Estamos criando e preparando todo o ambiente do cliente =) <br> Por favor aguarde, isso pode levar alguns minutos. <br><br>'+
'<img src=\'../core/imagens/carregando.gif\' >');
</script>
<?php
ob_flush();
flush();

/**
* 
* Code was adapted from SitePoint
* http://www.sitepoint.com/forums/showthread.php?697857-Copy-mysql-table-from-one-server-to-another-through-php&s=b5b25e09ff44749d2e49e0d7c1640fd8&p=4680578&viewfull=1#post4680578
* 
*/

// Prevent script from timing out
set_time_limit(0);


// define( 'MYSQL_HOST', 'localhost' );
// define( 'MYSQL_PORT', '3306' );
// define( 'MYSQL_USER', 'root' );
// define( 'MYSQL_PASSWORD', 'M0507c1106#12' );
// define( 'MYSQL_DB_NAME_SOURCE', 'audipamc_proclegis_cmmc');
// define( 'MYSQL_DB_NAME_DEST', 'audipamc_proclegis_'.$cli_url);
// try
// {
//     $PDO_SOURCE 	    = new PDO( 'mysql:host=' . MYSQL_HOST . ';port=' . MYSQL_PORT . ';dbname=' . MYSQL_DB_NAME_SOURCE, MYSQL_USER, MYSQL_PASSWORD );
//     $PDO_SOURCE->query("USE audipamc_proclegis_cmmc");
    
// }
// catch ( PDOException $e )
// {
//     echo 'Erro ao conectar com o MySQL: ' . $e->getMessage();
// }
// $PDO_SOURCE->exec("SET CHARACTER SET utf8");
// $PDO_SOURCE->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

// try
// {
//     $PDO_DEST 	    = new PDO( 'mysql:host=' . MYSQL_HOST . ';port=' . MYSQL_PORT . ';dbname=' . MYSQL_DB_NAME_DEST, MYSQL_USER, MYSQL_PASSWORD );
//     $PDO_DEST->query("USE audipamc_proclegis_'.$cli_url");
    
// }
// catch ( PDOException $e )
// {
//     echo 'Erro ao conectar com o MySQL: ' . $e->getMessage();
// }
// $PDO_DEST->exec("SET CHARACTER SET utf8");
// $PDO_DEST->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

echo "<pre>";

// // Source server of the table to be duplicated
// $sourceHost = '192.168.0.11:3306';
// $sourceUser = 'root';
// $sourcePassword = 'm0507c1106';
// $sourceDatabase = 'audipam_proclegis';

// // Destination server to duplicate the table
// $sourceHost = '192.168.0.11:3306';
// $sourceUser = 'root';
// $sourcePassword = 'm0507c1106';
// $sourceDatabase = 'audipam_proclegis_teste';


// Connect to source server
// $source = mysql_connect($sourceHost, $sourceUser, $sourcePassword);
// mysql_select_db($sourceDatabase, $source);

// // Connect to destination server
// $destination = mysql_connect($destinationHost, $destinationUser, $destinationPassword); // connect server 2	
// mysql_select_db($destinationDatabase, $destination); // select database 2
$PDO_SOURCE->query("USE audipamc_proclegis_cmmc");
$tables = $PDO_SOURCE->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

foreach($tables as $key => $value)
{
    // Table to be duplicated
    $table = $value;
    
    // Get the table structure from the source and create it on destination server
    $tableInfo = $PDO_SOURCE->query("SHOW CREATE TABLE $table  ")->fetch(); // get structure from table on server 1

    $PDO_DEST->query("SET FOREIGN_KEY_CHECKS=0; $tableInfo[1] "); // use found structure to make table on server 2

   

    // if($table == "admin_modulos" || 
    //    $table == "admin_submodulos" || 
    //    $table == "dashboard_widgets" || 
    //    $table == "end_bairros" || 
    //    $table == "end_enderecos" || 
    //    $table == "end_municipios" || 
    //    $table == "end_uf"
    //    )
    // {
        // Copy data from source to destination
        $x=0;
        $valores=array();

        $result = $PDO_SOURCE->query("SELECT * FROM $table  "); // select all content		
        $linhas = $result->rowCount();
        
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) // Para da linhas da tabela
        {
            $x++;
            $colunas = implode(", ", array_keys($row));
            array_push($valores, "('".implode("', '", array_map( 'addslashes',array_values($row)))."')");
            
            if($linhas > 1000)
            {
                // Em caso de tabelas com muitas linhas, não faz nada, para inserir várias linhas de uma vez posteriormente
            }
            else
            {
                // Insere dados no destino
                $PDO_DEST->query("SET FOREIGN_KEY_CHECKS=0;  INSERT INTO $table (" . $colunas . ") VALUES " . implode(",", $valores) . " ");
                $x=0;
                $valores=array();
                
            }
            
        }
       
        // Insere 50000 registros de cada vez para tabelas com mais de 1000 linhas
        if($linhas > 1000)
        {            
            // Divide array de registros a cada 50000 linhas
            $dividido = array_chunk($valores, 50000, true);
            foreach($dividido as $key => $val)
            {
                // Insere dados no destino
                $PDO_DEST->query("SET FOREIGN_KEY_CHECKS=0;  INSERT INTO $table (" . $colunas . ") VALUES " . implode(",", $val) . " ");
            }
        }
        
        
        // while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

        //     $PDO_DEST->query("SET FOREIGN_KEY_CHECKS=0; INSERT INTO $table (" . implode(", ", array_keys($row)) . ") VALUES ('" . implode("', '", array_values($row)) . "')");
        // }
    //}
   
}






// Close connections
// mysql_close($source);
// mysql_close($destination);
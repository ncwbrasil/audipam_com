<?php require_once("ctracker.php"); 
require_once("parametros.php"); 
//error_reporting(1); 
date_default_timezone_set('America/Sao_Paulo'); 

// if($_SESSION['cliente_url'] == "")
// {
//     $cli_url = isset($_GET['cli_url']) ? $_GET['cli_url'] : '';
//     if($cli_url != "")
//     {
//         $_SESSION['cliente_url'] = $cli_url;
//     }
// }
// if($_SESSION['sistema_url'] == "")
// {
//     $sis_url = isset($_GET['sis_url']) ? $_GET['sis_url'] : '';
//     if($sis_url != "")
//     {
//         $_SESSION['sistema_url'] = $sis_url;
//     }
//     if($sis_url == "" && $_SESSION['sistema_url'] == "")
//     {
//         $ex = explode('/', $_SERVER['REQUEST_URI']);
//         $sis_url = $ex[count($ex)-3];	
//         $db = $sis_url;
//         $_SESSION['sistema_url'] = $sis_url;
//     }
// }


$segs = explode('.', $_SERVER['HTTP_HOST']);
$cli_url = $segs[0]; if( $segs[0]){ $_SESSION['cliente_url'] = $cli_url;}
$ex = explode('/', $_SERVER['REQUEST_URI']);
$sis_url = $ex[1]; if( $ex[1]){ $_SESSION['sistema_url'] = $sis_url;}


// $sis_url = $segs[1]; if( $segs[1]){ $_SESSION['sistema_url'] = $sis_url;}


//FUNÇÃO JSON PARA O APP
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: GET, OPTIONS, POST');
// header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
// class Json{
// 	public function encode($vetor){ 
// 		echo json_encode($vetor, 128); 
// 	}
// }

// $JSON = new JSON;

//define( 'MYSQL_HOST', 'bd-mogicomp.sytes.net' );
// define( 'MYSQL_HOST', '192.168.0.11' );
// define( 'MYSQL_PORT', '3030' );
// define( 'MYSQL_USER', 'root' );
// define( 'MYSQL_PASSWORD', 'm0507c1106' );
// define( 'MYSQL_DB_NAME', 'audipam_sistemas' );
// define( 'MYSQL_DB_NAME_TRANSPARENCIA', 'audipam_transparencia' );

define( 'MYSQL_HOST', 'localhost' );
define( 'MYSQL_PORT', '3306' );
define( 'MYSQL_USER', 'audipamcom_mogicomp' );
define( 'MYSQL_PASSWORD', 'M0507c1106#' );
define( 'MYSQL_DB_NAME', 'audipamcom_sistema' );


// define( 'MYSQL_USER_ROOT', 'root' );
// define( 'MYSQL_PASSWORD_ROOT', 'M0507c1106#12' );
// define( 'MYSQL_DB_NAME_SOURCE', 'audipamcom_proclegis_cmmc');
// define( 'MYSQL_DB_NAME_DEST', 'audipamcom_proclegis_'.$cli_url);
// try
// {
//     $PDO_SOURCE 	    = new PDO( 'mysql:host=' . MYSQL_HOST . ';port=' . MYSQL_PORT . ';dbname=' . MYSQL_DB_NAME_SOURCE, MYSQL_USER_ROOT, MYSQL_PASSWORD_ROOT );
//     $PDO_SOURCE->query("USE audipamcom_proclegis_cmmc");
    
// }
// catch ( PDOException $e )
// {
//     echo 'Erro ao conectar com o MySQL: ' . $e->getMessage();
// }
// $PDO_SOURCE->exec("SET CHARACTER SET utf8");
// $PDO_SOURCE->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

// try
// {
//     $PDO_DEST 	    = new PDO( 'mysql:host=' . MYSQL_HOST . ';port=' . MYSQL_PORT . ';dbname=' . MYSQL_DB_NAME_DEST, MYSQL_USER_ROOT, MYSQL_PASSWORD_ROOT );
//     $PDO_DEST->query("USE audipamcom_proclegis_'.$cli_url");
    
// }
// catch ( PDOException $e )
// {
//     echo 'Erro ao conectar com o MySQL: ' . $e->getMessage();
// }
// $PDO_DEST->exec("SET CHARACTER SET utf8");
// $PDO_DEST->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );







try
{
    $PDO				= new PDO( 'mysql:host=' . MYSQL_HOST . ';port=' . MYSQL_PORT . ';dbname=' . MYSQL_DB_NAME, MYSQL_USER, MYSQL_PASSWORD );
}
catch ( PDOException $e )
{
    echo 'Erro ao conectar com o MySQL: ' . $e->getMessage();
}


$PDO->exec("SET CHARACTER SET utf8");
// $PDO_TRANSPARENCIA->exec("SET CHARACTER SET utf8");
$PDO->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );

?>
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
//         $sis_url = $ex[count($ex)-2];	
//         $db = $sis_url;
//         $_SESSION['sistema_url'] = $sis_url;
//     }
// }
$segs = explode('.', $_SERVER['HTTP_HOST']);
$cli_url = $segs[0]; if( $segs[0]){ $_SESSION['cliente_url'] = $cli_url;}
$ex = explode('/', $_SERVER['REQUEST_URI']);
$sis_url = $ex[1]; if( $ex[1]){ $_SESSION['sistema_url'] = $sis_url;}

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
if($_SESSION['cliente_url'])
{
    define( 'MYSQL_DB_NAME_PL', 'audipamcom_'.$_SESSION['sistema_url'].'_'.$_SESSION['cliente_url'] );
}
define( 'MYSQL_HOST', 'localhost' );
define( 'MYSQL_PORT', '3306' );
define( 'MYSQL_USER', 'audipamcom_mogicomp' );
define( 'MYSQL_PASSWORD', 'M0507c1106#' );
define( 'MYSQL_DB_NAME', 'audipamcom_sistema' );

try
{
    $PDO				= new PDO( 'mysql:host=' . MYSQL_HOST . ';port=' . MYSQL_PORT . ';dbname=' . MYSQL_DB_NAME, MYSQL_USER, MYSQL_PASSWORD );
    if($_SESSION['cliente_url'])
    {
        $PDO_PROCLEGIS 	    = new PDO( 'mysql:host=' . MYSQL_HOST . ';port=' . MYSQL_PORT . ';dbname=' . MYSQL_DB_NAME_PL, MYSQL_USER, MYSQL_PASSWORD );
    }
}
catch ( PDOException $e )
{
    echo 'Erro ao conectar com o MySQL: ' . $e->getMessage();
}
$PDO->exec("SET CHARACTER SET utf8");
$PDO->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
if($_SESSION['cliente_url'])
{
    $PDO_PROCLEGIS->exec("SET CHARACTER SET utf8");
    $PDO_PROCLEGIS->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
}
?>
<?php 
require_once("ctracker.php"); 
require_once("parametros.php"); 
error_reporting(1); 
date_default_timezone_set('America/Sao_Paulo'); 

// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
	header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
		header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

	exit(0);
}

class Json{
	public function encode($vetor){ 
		echo json_encode($vetor, 128); 
	}
}

$JSON = new JSON;

define( 'MYSQL_HOST', 'audipam.com.br' );
define( 'MYSQL_PORT', '3306' );
define( 'MYSQL_USER', 'audipamc_mogicomp' );
define( 'MYSQL_PASSWORD', 'M0507c1106#' );
define( 'MYSQL_DB_NAME', 'audipamc_sistema' );

$cli_url = $_GET['cli_url']; 

// define( 'MYSQL_HOST', '192.168.0.11' );
// define( 'MYSQL_PORT', '3030' );
// define( 'MYSQL_USER', 'root' );
// define( 'MYSQL_PASSWORD', 'm0507c1106' );
// define( 'MYSQL_DB_NAME', 'audipam_sistemas' );

if($cli_url=='' || $cli_url == Null){

	try
	{
		$PDO				= new PDO( 'mysql:host=' . MYSQL_HOST . ';port=' . MYSQL_PORT . ';dbname=' . MYSQL_DB_NAME, MYSQL_USER, MYSQL_PASSWORD );
	}
	catch ( PDOException $e )
	{
		echo 'Erro ao conectar com o MySQL: ' . $e->getMessage();
	}
	$PDO->exec("SET CHARACTER SET utf8");
	$PDO->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );

}else {
	define( 'MYSQL_DB_NAME_PL', 'audipamc_proclegis_'.$cli_url);
	try
	{
		$PDO				= new PDO( 'mysql:host=' . MYSQL_HOST . ';port=' . MYSQL_PORT . ';dbname=' . MYSQL_DB_NAME, MYSQL_USER, MYSQL_PASSWORD );
		$PDO_PROCLEGIS 	    = new PDO( 'mysql:host=' . MYSQL_HOST . ';port=' . MYSQL_PORT . ';dbname=' . MYSQL_DB_NAME_PL, MYSQL_USER, MYSQL_PASSWORD );
	}
	catch ( PDOException $e )
	{
		echo 'Erro ao conectar com o MySQL: ' . $e->getMessage();
	}
	$PDO->exec("SET CHARACTER SET utf8");
	$PDO_PROCLEGIS->exec("SET CHARACTER SET utf8");
	$PDO->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
	$PDO_PROCLEGIS->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
	
}


if(isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
    $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));
}

?>
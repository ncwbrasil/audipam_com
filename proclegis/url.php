<!-- <base href="http://<?php echo $_SERVER['HTTP_HOST'];?>/audipam_sistemas/proclegis/" /> -->


<?php 

$segs = explode('.', $_SERVER['HTTP_HOST']);
$cli_url = $segs[0];

?>

<base href="https://<?php echo $segs[0];?>.<?php echo $segs[1];?>.<?php echo $segs[2];?>.<?php echo $segs[3];?>/proclegis/" />

<?php
// $cli_url = isset($cli_url) ? $cli_url : '';
// if($cli_url == "")
// {
    
//     $ex = explode('/', $_SERVER['REQUEST_URI']);
//     $cli_url = $ex[count($ex)-1];	

//     //$db = $cli_url;
// }


// //LOCAL
// $sis_url = isset($sis_url) ? $sis_url : '';
// if($sis_url == "")
// {
//     $ex = explode('/', $_SERVER['REQUEST_URI']);
//     $sis_url = $ex[count($ex)-4];	

//     $db = $sis_url;
// }



?>

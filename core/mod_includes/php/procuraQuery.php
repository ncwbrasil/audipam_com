<meta 	http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
include('connect.php');
require_once("funcoes.php");
sec_session_start(); 

$query = $_POST['query'];
$num = $_POST['num'];
$start = $_POST['start'];


$query = urlencode($query);

$useragent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
$ch = curl_init ("");
curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/search?hl=pt-br&tbo=d&site=&source=hp&sa=N&num='.$num.'&start='.$start.'&q='.$query.'&filter=0');
//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
//curl_setopt($ch, CURLOPT_PROXY, '34.90.145.120:3128');
curl_setopt($ch, CURLOPT_USERAGENT, $useragent); // set user agent
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_COOKIEJAR, "cookies.txt"); // cookies storage / here the changes have been made
curl_setopt($ch, CURLOPT_COOKIEFILE, "cookies.txt");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // false for https
curl_setopt($ch, CURLOPT_ENCODING, "gzip"); // the page encoding

$output = curl_exec ($ch);
echo $output;
$result_tmp = explode('<div id="main">',$output);
$result = $result_tmp[1];

$result_tmp = explode('<footer>',$result);

$result = $result_tmp[0];

echo $result;
curl_close($ch);

		




?>
<?php
session_start (); 
error_reporting(0);
date_default_timezone_set('America/Sao_Paulo');

include		('../mod_includes/php/connect.php');

$pagina = $_GET['pagina'];
$fil_orgao = $_REQUEST['fil_orgao'];
if($fil_orgao == '')
{
	$orgao_query = " 1 = 1 ";
	$fil_orgao_n = "Classificação do órgão";
}
else
{
	$orgao_query = " emp_orgao = '".$fil_orgao."' ";
	$fil_orgao_n = $fil_orgao;	
}
$fil_uf = $_REQUEST['fil_uf'];
if($fil_orgao == '')
{
	$uf_query = " 1 = 1 ";
	$fil_uf_n = "UF";
}
else
{
	$uf_query = " emp_uf = '".$fil_uf."' ";
	$fil_uf_n = $fil_uf;	
}
$sql = "SELECT * FROM cadastro_empresas
		LEFT JOIN end_uf ON end_uf.uf_id = cadastro_empresas.emp_uf
		LEFT JOIN end_municipios ON end_municipios.mun_id = cadastro_empresas.emp_municipio
		WHERE ".$orgao_query." AND ".$uf_query." 
		ORDER BY emp_fantasia ASC  
		";
$stmt = $PDO->prepare($sql);	
$stmt->execute();
$rows = $stmt->rowCount();

$fil_empresa = $_REQUEST['fil_empresa'];
if($fil_orgao == '')
{
	$empresa_query = " 1 = 1 ";
	$fil_empresa_n = "Classificação do órgão";
}
else
{
	$empresa_query = " emp_id = '".$fil_empresa."' ";
	$fil_empresa_n = $fil_empresa;
	
}
$sql = "SELECT * FROM cadastro_empresas
		LEFT JOIN end_uf ON end_uf.uf_id = cadastro_empresas.emp_uf
		LEFT JOIN end_municipios ON end_municipios.mun_id = cadastro_empresas.emp_municipio
		WHERE ".$empresa_query." 
		ORDER BY emp_fantasia ASC  
		";
$stmt_rem = $PDO->prepare($sql);	
$stmt_rem->execute();
$rows_rem = $stmt_rem->rowCount();
$result_rem = $stmt_rem->fetch();

//header("Content-Type: text/html; charset=utf-8", true); 


/* Carrega seu HTML */



// define('MPDF_PATH', '../mod_includes/js/mpdf/');
// include(MPDF_PATH.'mpdf.php');
// $mpdf = new mPDF(
//  '',    // mode - default ''
//  'A4',    // format - A4, for example, default ''
//  11,     // font size - default 0
//  'quicksand',    // default font family
//  10,    // margin_left
//  10,    // margin right
//  10,     // margin top
//  5,    // margin bottom
//  5,     // margin header
//  5,     // margin footer
//  'P');  // L - landscape, P - portrait);
// $mpdf->SetFont('quicksand');
// $mpdf->SetTitle('Audipam | Mala Direta');
// $mpdf->useOddEven = false;
// $mpdf->SetHTMLHeader(''); 
// $mpdf->SetHTMLFooter('');

// $mpdf->allow_charset_conversion=true;
// $mpdf->charset_in='UTF-8';
	
ob_start();  //inicia o buffer
?>
<style>

.body			{ font-family:"quicksand"; font-size:11px; }
.uso_correios 	{ border: 1px solid #999;  padding:15px; border-radius:10px; width:100%; background:#FFF;}
.logos 			{ margin:0 auto; margin-bottom:80px;text-align:left; padding: 0 0 15px 0;}
.logos img 		{ max-height:90px; height:90px;}
.titulo			{ font-size:13px;}
table			{ font-family:"quicksand"; font-size:11px; width:100%;}
.left 			{ float:left; transform: rotate(90deg);}
.right 			{ float:right;}
div.destinatario 			{ margin-left:0px; margin-bottom:140px;  font-size:11px;  border: 1px solid #999; padding:15px; border-radius:10px; width:60%; background:#FFF;}
div.remetente 		{ margin-left:0px; margin-bottom:126px;  font-size:11px; border: 1px solid #999; padding:15px; border-radius:10px; width:60%; background:#FFF;}
</style>
<?php 

if($rows > 0)
{
	while($result = $stmt->fetch())
	{
		
		echo "	
		<br><br><br><br><br><br><br><br><br><br><br>
		<div class='remetente'>
			Remetente <p>
			".$result_rem['emp_nome_razao']." <br>
			".$result_rem['emp_endereco'].", ".$result_rem['emp_numero']." ".$result_rem['emp_comp']." <br>
			".$result_rem['emp_bairro']." - ".$result_rem['mun_nome']."/".$result_rem['uf_sigla']." <br>
			".$result_rem['emp_cep']."
		</div>					
		<div class='logos'>
			<img class='left' src=\"".$result_rem['emp_logo']."\" height='90'>
			<img class='right' src=\"../imagens/maladireta_selo.png\" height='90'>
		</div>
		<div class='destinatario'>
			Destinatário <p>
			".$result['emp_nome_razao']." <br>
			".$result['emp_endereco'].", ".$result['emp_numero']." ".$result['emp_comp']." <br>
			".$result['emp_bairro']." - ".$result['mun_nome']."/".$result['uf_sigla']." <br>
			".$result['emp_cep']."
		</div>
		<!--<img src=\"../imagens/maladireta_topo.png\" >-->
		<div class='uso_correios'>
			<b class='titulo'>Para uso dos Correios</b><br>
			<table cellpadding='1' >
				<tr>
					<td width='25%'><input type='checkbox'> Mudou-se </td>
					<td width='25%'><input type='checkbox'> Endereço insuficiente </td>
					<td width='25%'>Data</td>
					<td width='25%'>Reiniciado Serviço Postal em:</td>
				</tr>
				<tr>
					<td><input type='checkbox'> Desconhecido </td>
					<td><input type='checkbox'> Não existe o nº indicado </td>
					<td>_______/_______/_____________</td>
					<td>_______/_______/_____________</td>
				</tr>
				<tr>
					<td><input type='checkbox'> Recusado </td>
					<td><input type='checkbox'> Não procurado </td>
					<td colspan='2'>Assinatura entregador</td>
				</tr>
				<tr>
					<td><input type='checkbox'> Falecido </td>
					<td><input type='checkbox'> Ausente </td>
					<td colspan='2'>___________________________________________________________________</td>						
				</tr>
			</table>							
		</div>		
		<div class='page_break'></div>
		";		

		//$mpdf->WriteHTML(utf8_decode("$html"));
		//$mpdf->AddPage();
	}

}	

$html = ob_get_clean();
$html = utf8_encode($html);				


/* Carrega a classe DOMPdf */
require_once("../mod_includes/js/dompdf/dompdf_config.inc.php");

/* Cria a instância */
$dompdf = new DOMPDF();

/* Carrega seu HTML */
$dompdf->load_html(utf8_decode("$html"));

/* Renderiza */
$dompdf->render();

/* Exibe */
$dompdf->stream(
    "Relatorio_".str_pad($orc_id,6,'0',STR_PAD_LEFT).".pdf", /* Nome do arquivo de saída */
    array(
        "Attachment" => true /* Para download, altere para true */
    )
);

exit();
?>
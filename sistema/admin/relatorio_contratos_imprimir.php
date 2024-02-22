<?php
session_start (); 
error_reporting(0);
date_default_timezone_set('America/Sao_Paulo');

include		('../mod_includes/php/connect.php');

$pagina = $_GET['pagina'];
	$fil_empresa = $_REQUEST['fil_empresa'];
	if($fil_empresa == '')
	{
		$empresa_query = " 1 = 1 ";
		$fil_empresa_n = "Empresa";
	}
	else
	{
		$empresa_query = " con_contratado = '".$fil_empresa."' ";
		$sql_fat = "SELECT * FROM cadastro_empresas  WHERE emp_id = :fil_empresa ";
		$stmt_fat = $PDO->prepare($sql_fat);
		$stmt_fat->bindParam(":fil_empresa",$fil_empresa);
		$stmt_fat->execute();
		$result_fat = $stmt_fat->fetch();
		$fil_empresa_n = $result_fat['emp_fantasia'];
	}
	$fil_cliente = $_REQUEST['fil_cliente'];
	if($fil_cliente == '')
	{
		$cliente_query = " 1 = 1 ";
		$fil_cliente_n = "Cliente";
	}
	else
	{
		$cliente_query = " con_contratante = '".$fil_cliente."' ";
		$sql_cli = "SELECT * FROM cadastro_empresas WHERE emp_id = :fil_cliente ";
		$stmt_cli = $PDO->prepare($sql_cli);
		$stmt_cli->bindParam(":fil_cliente",$fil_cliente);
		$stmt_cli->execute();
		$result_cli = $stmt_cli->fetch();
		$fil_cliente_n = $result_cli['emp_nome_razao'];
	}
	
	$fil_dia_nf = $_REQUEST['fil_dia_nf'];
	if($fil_dia_nf == '')
	{
		$nf_query = " 1 = 1 ";
	}
	else
	{
		$nf_query = " con_dia_emissao_nf = '".str_pad($fil_dia_nf,2,"0",STR_PAD_LEFT)."' ";
	}
	
	$fil_data_inicio_enc = implode('-',array_reverse(explode('/',$_REQUEST['fil_data_inicio_enc'])));
	$fil_data_fim_enc = implode('-',array_reverse(explode('/',$_REQUEST['fil_data_fim_enc'])));
	if($fil_data_inicio_enc == '' && $fil_data_fim_enc == '')
	{
		$data_query = " 1 = 1 ";
	}
	elseif($fil_data_inicio_enc != '' && $fil_data_fim_enc == '')
	{
		$data_query = " con_final_vig >= '$fil_data_inicio_enc' ";
	}
	elseif($fil_data_inicio_enc == '' && $fil_data_fim_enc != '')
	{
		$data_query = " con_final_vig <= '$fil_data_fim_enc 23:59:59' ";
	}
	elseif($fil_data_inicio_enc != '' && $fil_data_fim_enc != '')
	{
		$data_query = " con_final_vig BETWEEN '$fil_data_inicio_enc' AND '$fil_data_fim_enc 23:59:59' ";
	}
	$fil_con_status = $_REQUEST['fil_con_status'];
	if($fil_con_status == '')
	{
		$status_query = " 1 = 1 ";
		$fil_con_status_n = "Status";
	}
	else
	{
		$status_query = " con_status = '".$fil_con_status."' ";
	}

 $sql = "SELECT * FROM cadastro_contratos
			LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cadastro_contratos.con_contratante
			LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
			WHERE  ".$empresa_query." AND ".$cliente_query."  AND ".$nf_query."  AND ".$data_query."  AND ".$status_query." 
			GROUP BY con_id  
			ORDER BY con_final_vig ASC
			
			";
  	$stmt = $PDO->prepare($sql);
	$stmt->execute();
    $rows = $stmt->rowCount();

//header("Content-Type: text/html; charset=utf-8", true); 
ob_start();  //inicia o buffer
?>
<!--<img src='../imagens/topopdf.png'>-->
<style>
.topo 			{ margin:0 auto; text-align:center; padding: 0 0 15px 0;}
.rodape 		{ margin:0 auto; text-align:left; padding: 15px 0 0 0; font-family:"Calibri";}
.rod			{ color: #999; font-size:13px; font-family:"Calibri"; }

.body	{ font-family:"Calibri"; font-size:11px;}
table	{ font-family:"Calibri"; font-size:11px;}
.titulo	{ font-size:20px; font-family:"sharpmedium";color:#0F72BD; font-weight:bold; text-align:center; margin-bottom:15px; }
.title	{ font-size:20px; font-family:"sharpmedium";color:#0F72BD; font-weight:bold; text-align:center; margin-bottom:15px; }
.label 	{ width:18%; font-weight:bold; float:left; text-align: right; margin-bottom:10px;}
.info 	{ width:80%; float:right; margin-bottom:10px;}
.detalhe{ color:#999; }

.linhapar			{ background:#FAFAFA; }
.linhaimpar			{ background:#FFFFFF; }
.bordatabela		{ border: 1px solid #DADADA; font-size:11px; color:#666;  -moz-border-radius:2px 2px 0px 0px; -webkit-border-radius:2px 2px 0px 0px; border-radius:2px 2px 0px 0px;}
.titulo_tabela	{ font-size:13px; font-family:"Calibri"; border:0; color:#333; background:#EEE;}
.titulo_first	{ font-size:13px; font-family:"Calibri"; border:0; color:#333; background:#EEE; -moz-border-radius:5px 0px 0px 0px; -webkit-border-radius:5px 0px 0px 0px; border-radius:5px 0px 0px 0px;}
.titulo_last	{ font-size:13px; font-family:"Calibri"; border:0; color:#333; background:#EEE; -moz-border-radius:0px 5px 0px 0px; -webkit-border-radius:0px 5px 0px 0px; border-radius:0px 5px 0px 0px;}


.azul			{ color:#0F72BD;}
.laranja		{ color:#F60; font-weight:bold;}
.verde			{ color:#81C566; font-weight:bold;}
.vermelho		{ color:#900; font-weight:bold;}



</style>
<?php	

	echo "
	<div class='body'>
		<div class='titulo'>Relatório de Contratos</span> </div>
				";
				
				if($rows > 0)
				{

					while($result = $stmt->fetch())
					{
						
						if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
						echo "
							  	<img src='".$result['emp_logo']."' height='30' valign='middle'> <span class='title'>".$result['emp_nome_razao']."</span> <br>
								<b>Objeto do Contrato:</b> ".$result['con_objeto']."<br>
								<b>Contrato:</b> ".$result['con_numero_processo']."/".$result['con_ano_processo']." (".$result['mod_descricao'].")"."<br>
								<b>Encerramento:</b> ".implode("/",array_reverse(explode("-",$result['con_final_vig'])))." <br>
								<b>Dia Emissão NF:</b> ".$result['con_dia_emissao_nf']."<br>
								<b>Periodicidade:</b> ".$result['con_periodicidade']."<br>
								<b>Valor Global:</b> R$ ".number_format($result['con_valor_global_atual'],2,',','.')." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Valor Mensal Vigente:</b> R$ ".number_format($result['con_valor_unitario_atual'],2,',','.')."<br>
								<b>Status:</b> ".$result['con_status']."<br><p>
						";
					}
				}
				echo "	
			</div>
	";
$html = ob_get_clean();
$html = utf8_encode($html);

// define('MPDF_PATH', '../mod_includes/js/mpdf/');
// include(MPDF_PATH.'mpdf.php');
// $mpdf = new mPDF(
//  '',    // mode - default ''
//  'A4',    // format - A4, for example, default ''
//  0,     // font size - default 0
//  '',    // default font family
//  10,    // margin_left
//  10,    // margin right
//  30,     // margin top
//  23,    // margin bottom
//  5,     // margin header
//  5,     // margin footer
//  'P');  // L - landscape, P - portrait);
// $mpdf->SetTitle('Audipam | Relatório de Contratos');
// $mpdf->useOddEven = false;
// $mpdf->SetHTMLHeader('<div class="topo"><img src=../imagens/logo_branco.png width="300"><br><br></div>'); 
// $mpdf->SetHTMLFooter('');

// $mpdf->allow_charset_conversion=true;
// $mpdf->charset_in='UTF-8';
// $mpdf->WriteHTML(utf8_decode("$html"));
// //$mpdf->AddPage();

// $mpdf->Output('Orçamento_'.str_pad($orc_id,6,'0',STR_PAD_LEFT).'.pdf','I');
// exit();
require_once("../mod_includes/js/dompdf/dompdf_config.inc.php");

/* Cria a instância */
$dompdf = new DOMPDF();

/* Carrega seu HTML */
$dompdf->load_html(utf8_decode("$html"));

/* Renderiza */
$dompdf->render();

/* Exibe */
$dompdf->stream(
    "Orçamento_".str_pad($orc_id,6,'0',STR_PAD_LEFT).".pdf", /* Nome do arquivo de saída */
    array(
        "Attachment" => true /* Para download, altere para true */
    )
);

exit();

?>
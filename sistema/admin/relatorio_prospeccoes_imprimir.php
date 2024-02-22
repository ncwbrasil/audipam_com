<?php
session_start (); 
error_reporting(0);
date_default_timezone_set('America/Sao_Paulo');

include		('../mod_includes/php/connect.php');

$pagina = $_GET['pagina'];
$emp_id = $_REQUEST['emp_id'];

$rodape = '<div class="rodape" style="width:100%;">'.date("d/m/Y").'</div><div class="rodape" style="width:45%;float:left;"></div><div  class="rodape" style="width:45%;float:right;"></div>';

 $sql = "SELECT * FROM cadastro_empresas_prospeccoes 
		 LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cadastro_empresas_prospeccoes.epr_empresa
		 LEFT JOIN cadastro_prospeccoes ON cadastro_prospeccoes.pro_id = cadastro_empresas_prospeccoes.epr_icone
		 LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = cadastro_empresas_prospeccoes.epr_usuario
		 LEFT JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_id = cadastro_empresas_prospeccoes.epr_contato
		 WHERE epr_empresa = :epr_empresa 
	
		";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':epr_empresa', 	$emp_id);
$stmt->execute();
$rows = $stmt->rowCount();

//header("Content-Type: text/html; charset=utf-8", true); 

ob_start();  //inicia o buffer

?>
<!--<img src='../imagens/topopdf.png'>-->
<style>
.topo 			{ margin:0 auto; text-align:center; padding: 0 0 15px 0;}
.rodape 		{ font-family:"Calibri"; color:#777; font-size:11px; text-align:center;}
.rod			{ color: #999; font-size:13px; font-family:"Calibri"; }

.body	{ font-family:"Calibri"; font-size:11px; line-height:30px;}
table	{ font-family:"Calibri"; font-size:11px;}
.titulo	{ font-size:20px; font-family:"sharpmedium";color:#0F72BD; font-weight:bold; text-align:center; margin-bottom:15px; }
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
		<div class='titulo'>Relatório de Prospecções</span> </div>
				";
			
				if($rows > 0)
				{
					$x=1;
					while($result = $stmt->fetch())
					{
						if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;} 
						if($x == 1)
						{
							echo "<img src='".$result['emp_logo']."' height='30' valign='middle'> <span class='title'>".$result['emp_nome_razao']."</span><br><p><br>
							";
						}
						echo "
							  	<b>Prospecção:</b> ".$result['pro_icone']." ".$result['pro_nome']."<br>
								<b>Responsável:</b> ".$result['usu_nome']."<br>
								<b>Nome empresa:</b> ".$result['epr_nome_empresa']."<br>
								<b>Contato:</b> ".$result['ctt_nome']."<br>
								<b>Data:</b> ".implode("/",array_reverse(explode("-",$result['epr_data'])))."<br>
								<b>Breve histórico:</b> ".$result['epr_breve_historico']."<br>
								<hr>
								";
						$x++;
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
// $mpdf->SetTitle($emp_fantasia_n);
// $mpdf->useOddEven = false;
// $mpdf->SetHTMLHeader('<div class="topo"><img src='.$emp_logo.' width="300"><br><br></div>'); 
// $mpdf->SetHTMLFooter($rodape);

// $mpdf->allow_charset_conversion=true;
// $mpdf->charset_in='UTF-8';
// $mpdf->WriteHTML(utf8_decode("$html"));
// //$mpdf->AddPage();

// $mpdf->Output('Relatorio_'.str_pad($orc_id,6,'0',STR_PAD_LEFT).'.pdf','I');
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
    "Relatorio_".str_pad($orc_id,6,'0',STR_PAD_LEFT).".pdf", /* Nome do arquivo de saída */
    array(
        "Attachment" => true /* Para download, altere para true */
    )
);

exit();

?>
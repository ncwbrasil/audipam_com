<script>
//abreMask('<div class="piscar"><img src="../imagens/carregando.gif" border="0"> Enviando email...</div>');
//blink('.piscar');
</script>
<?php
// CONFERIR DATA
		$agora = time();
		$data = getdate($agora);
		$dia_semana = $data[wday];
		$dia_mes = $data[mday];
		$mes = $data[mon];
		$ano = $data[year];
		switch ($dia_semana)
		{
	        case 0:
                $dia_semana = "Domingo";
	        break;
	        case 1:
                $dia_semana = "Segunda-feira";
	        break;
	        case 2:
                $dia_semana = "Ter�a-feira";
	        break;
	        case 3:
                $dia_semana = "Quarta-feira";
	        break;
	        case 4:
                $dia_semana = "Quinta-feira";
	        break;
	        case 5:
                $dia_semana = "Sexta-feira";
	        break;
	        case 6:
                $dia_semana = "S�bado";
	        break;
		}
		switch ($mes)
		{
        	case 1:
                $mes = "Janeiro";
        	break;
        	case 2:
                $mes = "Fevereiro";
        	break;
        	case 3:
                $mes = "Mar�o";
        	break;
        	case 4:
                $mes = "Abril";
        	break;
        	case 5:
                $mes = "Maio";
        	break;
        	case 6:
                $mes = "Junho";
        	break;
        	case 7:
                $mes = "Julho";
        	break;
        	case 8:
                $mes = "Agosto";
        	break;
        	case 9:
                $mes = "Setembro";
        	break;
        	case 10:
                $mes = "Outubro";
        	break;
        	case 11:
                $mes = "Novembro";
        	break;
        	case 12:
                $mes = "Dezembro";
 			break;
		}
		$datap = $dia_semana.', '.$dia_mes.' de '.$mes.' de '.$ano;


// Inclui o arquivo class.phpmailer.php localizado na pasta phpmailer
require_once("../mod_includes/php/phpmailer/class.phpmailer.php");
 
// Inicia a classe PHPMailer
$mail = new PHPMailer();
// Define os dados do servidor e tipo de conex�o
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
$mail->IsSMTP();
$mail->Host = "mail.mogicomp.com.br"; // Endere�o do servidor SMTP (caso queira utilizar a autentica��o, utilize o host smtp.seudom�nio.com.br)
$mail->SMTPAuth = true; // Usa autentica��o SMTP? (opcional)
$mail->Username = 'autenticacao@mogicomp.com.br'; // Usu�rio do servidor SMTP
$mail->Password = 'info2012mogi'; // Senha do servidor SMTP


// Define o remetente
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
$mail->From = "mogicomp@mogicomp.com.br"; // Seu e-mail
$mail->Sender = "autenticacao@mogicomp.com.br"; // Seu e-mail
$mail->FromName = "MogiComp - S.A.I."; // Seu nome

 
// Define os destinat�rio(s)
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
$mail->AddAddress('gustavo@mogicomp.com.br');
$mail->AddCC('marcelo@mogicomp.com.br');

// Define os dados t�cnicos da Mensagem
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
$mail->IsHTML(true); // Define que o e-mail ser� enviado como HTML

//$mail->CharSet = 'iso-8859-1'; // Charset da mensagem (opcional)
 
// Define a mensagem (Texto e Assunto)
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
$mail->Subject  = "Serviço(s) Vencido(s)"; // Assunto da mensagem


$mail->Body = "
				<head>
				<style type='text/css'>
				.margem 	{ padding-left:20px; padding-right:20px;}
				.margem2 	{ padding-top:20px; padding-bottom:20px; padding-left:20px; padding-right:20px;}
				a:link 		{}
				a:visited 	{}
				a:hover 	{ text-decoration: underline; color:#F90F00;}
				a:active 	{ text-decoration: none;}
				hr 			{ color: #00B1C0;}
				.titulo		{ font-family:Calibri; color:#0C5A8C; font-size:15px; text-align:left; font-weight:normal; } 
				.texto		{ font-family:Calibri; color:#727272; font-size:13px; text-align:justify; font-weight:normal; }
				.interna	{ font-family:Calibri; color:#727272; border: 1px dashed #CCC; line-height:20px; min-width:600px; font-size:13px; text-align:justify; font-weight:normal; }
				.interna td	{ padding:4px 10px; }
				.rodape		{ font-family:Calibri; color:#727272; font-size:11px; text-align:justify; font-weight:normal; }
				.titulo_tabela	{ font-size:15px; color:#FFF; background:#666;}
				.red { color: #900;}
				
				</style>
				</head>
				<body class='fundo'>
					<table class='texto' align='center' border='0' width='100%' cellspacing='0' cellpadding='0'>
					<tr>
						<td align='left'>
							<!--$_SERVER[SERVER_NAME]-->
							<img src='https://sweetpics.com.br/core/imagens/logo_h.png'><br>
							<span class='titulo'>
								<br><b>Olá administrador, </b>
							</span><br><br>
							".$datap."<br>
							Os seguintes serviços foram <u>encerrados</u>:<br><br>
							<table class='interna' cellspacing='0'>
							<tr>
								<td class='titulo_tabela'>ID</td>
								<td class='titulo_tabela'>Cliente</td>
								<td class='titulo_tabela'>Serviço</td>
								<td class='titulo_tabela'>Data Início</td>
								<td class='titulo_tabela'>Data Fim</td>
								<td class='titulo_tabela'>Valor</td>
							</tr>
							<tr>
								<td><b>".$ser_id."</b></td>
								<td><b>".utf8_decode($clientes)."</b></td>
								<td><b>".utf8_decode($servicos)."</b></td>
								<td><b>".$data_inicio."</b></td>
								<td><b>".$data_fim."</b></td>
								<td><b>".$valores."</b></td>
							</tr>
							</table>
							<br>
							
							<hr>
							<span class='rodape'>
								Este é um email gerado automaticamente pelo sistema, portanto, por favor não responda-o.<br><br>
								As informações contidas nesta mensagem e nos arquivos anexados são para uso restrito, sendo seu sigilo protegido por lei, não havendo ainda garantia legal quanto à integridade de seu conteúdo. Caso não seja o destinatário, por favor desconsidere essa mensagem. O uso indevido dessas informações será tratado conforme as normas da empresa e a legislação em vigor
							</font>
						</td>
					</tr>
					</table>
				</body>
						";
						/*$mail->AltBody = 'Este � o corpo da mensagem de teste, em Texto Plano! \r\n 
						<IMG src="http://seudom�nio.com.br/imagem.jpg" alt=":)"  class="wp-smiley"> ';*/
						 
						// Define os anexos (opcional)
						// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
						//$mail->AddAttachment("/home/login/documento.pdf", "novo_nome.pdf");  // Insere um anexo
						 
						// Envia o e-mail
						$enviado = $mail->Send();
												
						// Limpa os destinat�rios e os anexos
						$mail->ClearAllRecipients();
						$mail->ClearAttachments();
						 if ($enviado)
						 {
							 
						 }
						 else
						 {
							 echo "Informa��es do erro: " . $mail->ErrorInfo;
						 }
						// Exibe uma mensagem de resultado
						/*if ($enviado) {
						echo("<SCRIPT LANGUAGE='JavaScript'>
							alert('Sua mensagem foi enviada com sucesso. Em breve entraremos em contato. Obrigado')
							window.location.href = 'home.php';
							</SCRIPT>");
						} else {
						echo("<SCRIPT LANGUAGE='JavaScript'>
							alert('Falha ao enviar mensagem. Tente novamente')
							javascrip:history.back();
							</SCRIPT>");
						echo "Informa��es do erro: 
						" . $mail->ErrorInfo;
						}*/


?>
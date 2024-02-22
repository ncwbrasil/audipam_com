<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS, POST');
header('Access-Control-Allow-Headers: origin, x-requested-with,Content-Type, Content-Range, Content-Disposition, Content-Description');

include_once("mod_includes/php/connect.php");
include_once("mod_includes/php/funcoes.php");

$acao = $_GET['action']; 

if($acao == 'listarMensagens'){
	$usu_email = $_GET['usu_email']; 

	$sql = "SELECT * FROM app_usuarios
	WHERE usu_email = :usu_email";
	$stmt = $PDO->prepare($sql);
	$stmt->bindValue(':usu_email', $usu_email);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if($rows > 0)
	{
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$sql_msg = "SELECT * FROM app_conversas	
		LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = app_conversas.con_parlamentar
		WHERE con_usuario = :con_usuario";
		$stmt_msg = $PDO_PROCLEGIS->prepare($sql_msg);
		$stmt_msg->bindValue(':con_usuario', $result['usu_id']);
		$stmt_msg->execute();
		$rows_msg = $stmt_msg->rowCount();
		if($rows_msg > 0)
		{
			for($i = 0; $i < $stmt_msg->rowCount(); $i++){
				$result_msg = $stmt_msg->fetch(PDO::FETCH_ASSOC);
				$msg_mensa="<p> <span class='subtitulo'>".$result_msg['nome']." </span><br> ".$result_msg['apelido']." <br>
				<span class='data'>Última mensagem enviada: ".date('d/m/Y H:i', strtotime($result_msg['con_data_cadastro']))."</span></p>"; 
				$dados['dados'][$i]['msg_mensagem'] = $msg_mensa;
				$dados['dados'][$i]['msg_id'] = $result_msg['con_id'];
			}
			echo JSON::encode($dados);
		}
		else {
			echo 'false'; 
		}
	}
	else
	{
		echo "false";
	}
}

if ($acao == 'listarMensagensParlamentar'){

	$con_id = $_GET['con_id']; 
	$sql_msg = "SELECT * FROM app_conversas
	LEFT JOIN app_mensagens ON app_mensagens.msg_conversa = app_conversas.con_id
	WHERE con_id = :con_id";
	$stmt_msg = $PDO_PROCLEGIS->prepare($sql_msg);
	$stmt_msg->bindValue(':con_id', $con_id);
	$stmt_msg->execute();
	$rows_msg = $stmt_msg->rowCount();
	if($rows_msg > 0)
	{
		for($i = 0; $i < $stmt_msg->rowCount(); $i++){
			$result_msg = $stmt_msg->fetch(PDO::FETCH_ASSOC);
			$msg_mensa="<p>".$result_msg['msg_mensagem']." <br>
				<span class='data'>".date('d/m/Y H:i', strtotime($result_msg['con_data_cadastro']))."</span></p>"; 

			$dados['dados'][$i]['msg_mensagem'] = $msg_mensa;
			$dados['dados'][$i]['msg_id'] = $result_msg['msg_id'];
		}
		echo JSON::encode($dados);
	}
	else {
		echo 'false'; 
	}

}

if ($acao == 'cadastrarMensagem'){

	$usu_email = $_POST['msg_email']; 
	$usu_token = $_POST['usu_token']; 
	$con_parlamentar = $_POST['msg_parlamentar']; 
	$msg_mensagem = $_POST['msg_mensagem']; 
	$msg_assunto = $_POST['msg_assunto']; 
	$ln = $_POST['usu_lng']; 
	$lt = $_POST['usu_lat']; 
	//$msg_foto = $_POST['msg_foto'];ß

	$msg_foto = 'Foto_Parlamentar.png';


	// // DEFINIÇÃO DA FUNÇÃO
	// function base64_to_jpeg( $base64_string, $output_file ) 
	// {
	// 	$ifp = fopen( $output_file, "wb" ); 
	// 	fwrite( $ifp, base64_decode( $base64_string) ); 
	// 	fclose( $ifp ); 
	// 	return( $output_file ); 
	// }   

	// //GERA IMAGEM A PARTIR DO BASE 64

	// $base64 = str_replace('data:image/jpeg;base64,','',$msg_foto); 
	// $patch =  "uploads/mensagens/foto_".md5(mt_rand(1,10000).date('YmdHis').$nomeArquivo).".jpg";
	// base64_to_jpeg($base64, $patch);	
	
	$sql = "SELECT * FROM app_usuarios WHERE usu_email = :usu_email";
	$stmt = $PDO->prepare($sql);
	$stmt->bindValue(':usu_email', $usu_email);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if($rows > 0){
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$sql_con= "SELECT * FROM app_conversas 
		LEFT JOIN cadastro_parlamentares ON cadastro_parlamentares.id = app_conversas.con_parlamentar
		WHERE con_usuario = :con_usuario AND con_parlamentar =:con_parlamentar";
		$stmt_con = $PDO_PROCLEGIS->prepare($sql_con);
		$stmt_con->bindValue(':con_usuario', $result['usu_id']);
		$stmt_con->bindValue(':con_parlamentar', $con_parlamentar);
		$stmt_con->execute();
		$rows_con = $stmt_con->rowCount();	
		if ($rows_con > 0) {
			$result_con = $stmt_con->fetch(PDO::FETCH_ASSOC);
			$sql_msg = "INSERT INTO app_mensagens SET
			msg_conversa = :msg_conversa, 
			msg_remetente = :msg_remetente, 
			msg_mensagem = :msg_mensagem,
			msg_assunto = :msg_assunto,  
			msg_foto = :msg_foto, 
			msg_lat = :msg_lat, 
			msg_lng = :msg_lng";
			$stmt_msg = $PDO_PROCLEGIS->prepare($sql_msg);
			$stmt_msg->bindValue(':msg_conversa', $result_con['con_id']);
			$stmt_msg->bindValue(':msg_remetente', 1);
			$stmt_msg->bindValue(':msg_mensagem', $msg_mensagem);
			$stmt_msg->bindValue(':msg_assunto', $msg_assunto);
			$stmt_msg->bindValue(':msg_lat', $lt);
			$stmt_msg->bindValue(':msg_lng', $ln);
			$stmt_msg->bindValue(':msg_foto', $msg_foto);
			if ($stmt_msg->execute()) {
				 
				//$email_parlamentar = $result_con['con_id']; 
				//$email_parlamentar = "teste@audipam.com.br"; 
				//enviaEmail($msg_assunto, $msg_mensagem, $patch, $usu_email,$email_parlamentar); 

				echo "true"; 
			}
			else {
				echo "false1"; 
			}

		}else {
			$sql_cad = "INSERT INTO app_conversas SET
			con_usuario = :con_usuario, 
			con_parlamentar = :con_parlamentar";
			$stmt_cad = $PDO_PROCLEGIS->prepare($sql_cad);
			$stmt_cad->bindValue(':con_usuario', $result['usu_id']);
			$stmt_cad->bindValue(':con_parlamentar', $con_parlamentar);
			if ($stmt_cad->execute()) {

				$msg_conversa = $PDO_PROCLEGIS->lastInsertId();
				$sql_msg = "INSERT INTO app_mensagens SET
				msg_conversa = :msg_conversa, 
				msg_remetente = :msg_remetente, 
				msg_mensagem = :msg_mensagem,
				msg_assunto = :msg_assunto, 
				msg_foto = :msg_foto,
				msg_lat = :msg_lat, 
				msg_lng = :msg_lng";
				$stmt_msg = $PDO_PROCLEGIS->prepare($sql_msg);
				$stmt_msg->bindValue(':msg_conversa', $msg_conversa);
				$stmt_msg->bindValue(':msg_remetente', 1);
				$stmt_msg->bindValue(':msg_mensagem', $msg_mensagem);
				$stmt_msg->bindValue(':msg_assunto', $msg_assunto);
				$stmt_msg->bindValue(':msg_lat', $lt);
				$stmt_msg->bindValue(':msg_lng', $ln);
				$stmt_msg->bindValue(':msg_foto', $msg_foto);
				if ($stmt_msg->execute()) {
					$sql_par= "SELECT * FROM  cadastro_parlamentares
					WHERE con_parlamentar =:con_parlamentar";
					$stmt_par = $PDO_PROCLEGIS->prepare($sql_par);
					$stmt_par->bindValue(':con_parlamentar', $con_parlamentar);
					$stmt_par->execute();
					$rows_par = $stmt_par->rowCount();	
					if ($rows_par > 0) {
						//$email_parlamentar = $result_con['con_id']; 
						//$email_parlamentar = "teste@audipam.com.br"; 
						//enviaEmail($msg_assunto, $msg_mensagem, $patch, $usu_email,$email_parlamentar); 
					}
					
					echo "true"; 
				}
				else {
					echo "false2"; 
				}
			}
			else {
				echo "false3"; 
			}
		}	
	}
	else {
		echo "false4"; 
	}
}


if ($acao == 'cadastraOuvidoria'){

	$usu_email = $_POST['msg_email']; 
	$usu_token = $_POST['usu_token']; 
	$msg_mensagem = $_POST['msg_mensagem']; 
	$msg_assunto = $_POST['msg_assunto']; 
	$ln = $_POST['usu_lng']; 
	$lt = $_POST['usu_lat']; 
	//$msg_foto = $_POST['msg_foto'];

	$msg_foto ='Foto_Parlamentar.png';


	// // DEFINIÇÃO DA FUNÇÃO
	// function base64_to_jpeg( $base64_string, $output_file ) 
	// {
	// 	$ifp = fopen( $output_file, "wb" ); 
	// 	fwrite( $ifp, base64_decode( $base64_string) ); 
	// 	fclose( $ifp ); 
	// 	return( $output_file ); 
	// }   

	// //GERA IMAGEM A PARTIR DO BASE 64

	// $base64 = str_replace('data:image/jpeg;base64,','',$msg_foto); 
	// $patch =  "uploads/mensagens/foto_".md5(mt_rand(1,10000).date('YmdHis').$nomeArquivo).".jpg";
	// base64_to_jpeg($base64, $patch);	
	
	$sql = "SELECT * FROM app_usuarios WHERE usu_email = :usu_email";
	$stmt = $PDO->prepare($sql);
	$stmt->bindValue(':usu_email', $usu_email);
	$stmt->execute();
	$rows = $stmt->rowCount();
	if($rows > 0){

		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$sql_msg = "INSERT INTO app_ouvidoria SET
		msg_remetente_nome = :msg_remetente_nome, 
		msg_remetente_email = :msg_remetente_email,
		msg_mensagem = :msg_mensagem,
		msg_assunto = :msg_assunto, 
		msg_foto = :msg_foto,
		msg_lat = :msg_lat, 
		msg_lng = :msg_lng";
		$stmt_msg = $PDO_PROCLEGIS->prepare($sql_msg);
		$stmt_msg->bindValue(':msg_remetente_nome', $result['usu_nome']);
		$stmt_msg->bindValue(':msg_remetente_email', $result['usu_email']);
		$stmt_msg->bindValue(':msg_mensagem', $msg_mensagem);
		$stmt_msg->bindValue(':msg_assunto', $msg_assunto);
		$stmt_msg->bindValue(':msg_lat', $lt);
		$stmt_msg->bindValue(':msg_lng', $ln);
		$stmt_msg->bindValue(':msg_foto', $msg_foto);
		if ($stmt_msg->execute()) {
			echo "true"; 
		}
		else {
			echo "false2"; 
		}
	}
	else {
		echo "false4"; 
	}
}


function enviaEmail($msg_assunto, $msg_mensagem, $patch, $usu_email, $email_parlamentar){

	date_default_timezone_set('America/Sao_Paulo');	
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
			$dia_semana = "Terça-feira";
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
			$dia_semana = "Sábado";
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
			$mes = "Março";
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
	require("mod_includes/php/phpmailer/class.phpmailer.php");
	 
	// Inicia a classe PHPMailer
	$mail = new PHPMailer();
	// Define os dados do servidor e tipo de conexão
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->IsSMTP();
	$mail->Host = "mail.mogicomp.com.br"; // Endereço do servidor SMTP (caso queira utilizar a autenticação, utilize o host smtp.seudomínio.com.br)
	$mail->SMTPAuth = false; // Usa autenticação SMTP? (opcional)
	$mail->Username = 'autenticacao@mogicomp.com.br'; // Usuário do servidor SMTP
	$mail->Password = 'Infomogi123#'; // Senha do servidor SMTP
	//$mail->SMTPDebug = 1;
		 
	// Define o remetente
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->From = "jcandido89@gmail.com"; // Seu e-mail
	$mail->Sender = "autenticacao@mogicomp.com.br"; // Seu e-mail
	$mail->FromName = "teste"; // Seu nome
	
	// Define os destinatário(s)
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	//$mail->AddAddress('marcelo@mogicomp.com.br');
	$mail->AddAddress('jorge@mogicomp.com.br');
			
	// Define os dados técnicos da Mensagem
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->IsHTML(true); // Define que o e-mail será enviado como HTML
	
	$mail->CharSet = 'utf-8'; // Charset da mensagem (opcional)
	 
	// Define a mensagem (Texto e Assunto)
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$assunto = 'Formulário de Contato - '.$msg_assunto;
	$mail->Subject  = '=?utf-8?B?'.base64_encode($assunto).'?='; // Assunto da mensagem
	$mail->Body = "
	<head>
		<style type='text/css'>
			.margem 		{ padding-top:20px; padding-bottom:20px; padding-left:20px;padding-right:20px;}
			a:link 			{}
			a:visited 		{}
			a:hover 		{ text-decoration: underline; color:#2C4E67; }
			a:active 		{ text-decoration: none; }
			.texto			{ font-family:'Calibri'; color:#666; font-size:14px; text-align:justify; font-weight:normal;}
			hr				{ border:none; border-top:1px solid #2C4E67;}
			.rodape			{ font-family:Calibri; color:#727272; font-size:12px; text-align:justify; font-weight:normal; }
					
		</style>
	</head>
	<body>
		<table style='font-family:Calibri;' align='center' border='0' width='100%' cellspacing='0' cellpadding='0'>
		<tr>
			<td align='left'>
				<table class='texto'>
					<tr>
						<td align='right'>
							<b>E-mail:</b>
						</td>
						<td align='left'>
							$email
						</td>
					</tr>
					<tr>
						<td align='right'>
							<b>Assunto:</b>
						</td>
						<td align='left'>
							$msg_assunto
						</td>
					</tr>
					<tr>
						<td align='right'>
							<b>Mensagem:</b>
						</td>
						<td align='left' valign='top'>
							$msg_mensagem
						</td>
					</tr>
				</table>
				<hr>
				<span class='rodape'>
					<font size='1' color='#2C4E67'><b>Mensagem enviada:</b></font> ".$datap."<br>
					Este é um email gerado automaticamente pelo sistema.<br><br>
					As informações contidas nesta mensagem e nos arquivos anexados são para uso restrito, sendo seu sigilo protegido por lei, não havendo ainda garantia legal quanto à integridade de seu conteúdo. Caso não seja o destinatário, por favor desconsidere essa mensagem. O uso indevido dessas informações será tratado conforme as normas da empresa e a legislação em vigor.
				</font>
			</td>
		</tr>
		</table>
	</body>
	";
	/*$mail->AltBody = 'Este é o corpo da mensagem de teste, em Texto Plano! \r\n 
	<IMG src="http://seudomínio.com.br/imagem.jpg" alt=":)"  class="wp-smiley"> ';*/
	
	// Define os anexos (opcional)
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	//$mail->AddAttachment("https://audipam.com.br/webservice/$patch", "Anexo");  // Insere um anexo
	
	// Envia o e-mail
	$enviado = $mail->Send();
	
	// Limpa os destinatários e os anexos
	$mail->ClearAllRecipients();
	$mail->ClearAttachments();

	// Exibe uma mensagem de resultado
	if ($enviado)
	{
		echo "true";
	}
	else
	{
		echo "False Mensagem".$mail->ErrorInfo;	
	}
}

?>
<?php

/*

Copyright (c) 2009 Anant Garg (anantgarg.com | inscripts.com)

This script may be used for non-commercial purposes only. For any
commercial purposes, please contact the author at 
anant.garg@inscripts.com

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

*/

//define ('DBPATH','localhost');
function emoticons($text) {
    $icons = array(
            ':)'    =>  ' <img src="../imagens/emoticon-smyle.png" /> ',
            '(Y)'   =>  ' <img src="../imagens/emoticon-joia.png" /> ',
            '(y)'   =>  ' <img src="../imagens/emoticon-joia.png" /> ',
            '(satan)'   =>  ' <img src="../imagens/emoticon-satan.png" /> '
    );
    return strtr($text, $icons);
}

define ('DBPATH','mogicomp.com.br:3306');
define ('DBUSER','mogicomp_admin');
define ('DBPASS','info2012mogi');
define ('DBNAME','mogicomp_mogicomp');

/*define ('DBPATH','bd-mogicomp.sytes.net:3030');
define ('DBUSER','root');
define ('DBPASS','m0507c1106');
define ('DBNAME','mogicomp');*/
session_write_close();
session_start();

global $dbh;

$dbh = mysql_connect(DBPATH,DBUSER,DBPASS);
mysql_select_db(DBNAME,$dbh);

if ($_GET['action'] == "chatheartbeat") { chatHeartbeat(); } 
if ($_GET['action'] == "sendchat") { sendChat(); } 
if ($_GET['action'] == "closechat") { closeChat(); } 
if ($_GET['action'] == "startchatsession") { startChatSession(); } 

if (!isset($_SESSION['chatHistory'])) {
	$_SESSION['chatHistory'] = array();	
}

if (!isset($_SESSION['openChatBoxes'])) {
	$_SESSION['openChatBoxes'] = array();	
}

function chatHeartbeat() {
	
	$sql = "select * from social_mensagens 
			LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = social_mensagens.msg_remetente
			where (msg_destinatario = '".mysql_real_escape_string($_SESSION['usuario_id'])."' AND msg_lida = 0) order by msg_id ASC";
	$query = mysql_query($sql);
	$items = '';

	$chatBoxes = array();

	while ($chat = mysql_fetch_array($query)) {

		/*if (!isset($_SESSION['openChatBoxes'][$chat['msg_remetente']]) && isset($_SESSION['chatHistory'][$chat['msg_remetente']])) {
			$items = $_SESSION['chatHistory'][$chat['msg_remetente']];
		}*/

		$chat['msg_mensagem'] = sanitize(utf8_encode($chat['msg_mensagem']));
		$chat['usu_nome'] = sanitize(utf8_encode($chat['usu_nome']));

		$items .= <<<EOD
					   {
			"s": "0",
			"f": "{$chat['msg_remetente']}",
			"n": "{$chat['usu_nome']}",
			"m": "{$chat['msg_mensagem']}"
	   },
EOD;

	if (!isset($_SESSION['chatHistory'][$chat['msg_remetente']])) {
		$_SESSION['chatHistory'][$chat['msg_remetente']] = '';
	}

	$_SESSION['chatHistory'][$chat['msg_remetente']] .= <<<EOD
						   {
			"s": "0",
			"f": "{$chat['msg_remetente']}",
			"n": "{$chat['usu_nome']}",
			"m": "{$chat['msg_mensagem']}"
	   },
EOD;
		
		unset($_SESSION['tsChatBoxes'][$chat['msg_remetente']]);
		$_SESSION['openChatBoxes'][$chat['msg_remetente']] = $chat['msg_data'];
	}

	if (!empty($_SESSION['openChatBoxes'])) {
	foreach ($_SESSION['openChatBoxes'] as $chatbox => $time) {
		if (!isset($_SESSION['tsChatBoxes'][$chatbox])) {
			$now = time()-strtotime($time);
			$time = date('d/m - H:i', strtotime($time));

			$msg_mensagem = "enviado em $time";
			if ($now > 180) {
				$items .= <<<EOD
{
"s": "2",
"f": "$chatbox",
"m": "{$msg_mensagem}"
},
EOD;

	if (!isset($_SESSION['chatHistory'][$chatbox])) {
		$_SESSION['chatHistory'][$chatbox] = '';
	}

	$_SESSION['chatHistory'][$chatbox] .= <<<EOD
		{
"s": "2",
"f": "$chatbox",
"m": "{$msg_mensagem}"
},
EOD;
			$_SESSION['tsChatBoxes'][$chatbox] = 1;
		}
		}
	}
}

	$sql = "update social_mensagens set msg_lida = 1 where msg_destinatario = '".mysql_real_escape_string($_SESSION['usuario_id'])."' and msg_lida = 0";
	$query = mysql_query($sql);

	if ($items != '') {
		$items = substr($items, 0, -1);
	}
header('Content-type: application/json');
?>
{
		"items": [
			<?php echo $items;?>
        ]
}

<?php
			exit(0);
}

function chatBoxSession($chatbox) {
	
	$items = '';
	
	if (isset($_SESSION['chatHistory'][$chatbox])) {
		$items = $_SESSION['chatHistory'][$chatbox];
	}

	return $items;
}

function startChatSession() {
	$items = '';
	if (!empty($_SESSION['openChatBoxes'])) {
		foreach ($_SESSION['openChatBoxes'] as $chatbox => $void) {
			$items .= chatBoxSession($chatbox);
		}
	}


	if ($items != '') {
		$items = substr($items, 0, -1);
	}

header('Content-type: application/json');
?>
{
		"username": "<?php echo $_SESSION['n'];?>",
		"items": [
			<?php echo $items;?>
        ]
}

<?php


	exit(0);
}

function sendChat() {
	$msg_remetente = $_SESSION['usuario_id'];
	$msg_destinatario = $_POST['msg_destinatario'];
	$msg_mensagem = $_POST['msg_mensagem'];

	$_SESSION['openChatBoxes'][$_POST['msg_destinatario']] = date('Y-m-d H:i:s', time());
	
	$messagesan = sanitize($msg_mensagem);

	if (!isset($_SESSION['chatHistory'][$_POST['msg_destinatario']])) {
		$_SESSION['chatHistory'][$_POST['msg_destinatario']] = '';
	}

	$_SESSION['chatHistory'][$_POST['msg_destinatario']] .= <<<EOD
					   {
			"s": "1",
			"f": "{$msg_destinatario}",
			"m": "{$messagesan}"
	   },
EOD;


	unset($_SESSION['tsChatBoxes'][$_POST['msg_destinatario']]);

	$sql = "insert into social_mensagens (msg_remetente,msg_destinatario,msg_mensagem,msg_data) values ('".mysql_real_escape_string($msg_remetente)."', '".mysql_real_escape_string($msg_destinatario)."','".mysql_real_escape_string(utf8_decode(emoticons($msg_mensagem)))."',NOW())";
	$query = mysql_query($sql);
	echo "1";
	exit(0);
}

function closeChat() {

	unset($_SESSION['openChatBoxes'][$_POST['chatbox']]);
	
	echo "1";
	exit(0);
}

function sanitize($text) {
	$text = htmlspecialchars($text, ENT_QUOTES);
	$text = str_replace("\n\r","\n",$text);
	$text = str_replace("\r\n","\n",$text);
	$text = str_replace("\n","<br>",$text);
	return $text;
}
//mysql_close($dbh);
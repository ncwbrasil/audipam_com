<?php
//error_reporting(E_ALL);
# Include the Autoloader (see "Libraries" for install instructions)

require '../../vendor/autoload.php';


use Mailgun\Mailgun;
# Instantiate the client.
// $mgClient = new Mailgun('8573419590335a0b0cdfe574fe112d2e-e31dc3cc-65ed68e7');
// $domain = "audipam.com.br";
// # Make the call to the client.
// $result = $mgClient->sendMessage($domain, array(
// 	'from'	=> 'Excited User <mailgun@audipam.com.br>',
// 	'to'	=> 'Baz <gustavo@mogicomp.com.br>',
// 	'subject' => 'Hello',
// 	'text'	=> 'Testing some Mailgun awesomness!'
// ));


// First, instantiate the SDK with your API credentials
$mg = Mailgun::create('8573419590335a0b0cdfe574fe112d2e-e31dc3cc-65ed68e7'); // For US servers
$mg = Mailgun::create('8573419590335a0b0cdfe574fe112d2e-e31dc3cc-65ed68e7', 'https://api.mailgun.net/v3/audipam.com.br'); // For EU servers
// Now, compose and send your message.
// $mg->messages()->send($domain, $params);
$result = $mg->messages()->send('audipam.com.br', [
	'from'	=> 'Processo Legislativo <naoresponda@audipam.com.br>',
	'to'	=> $am_email,
	'subject' => 'Nova Tramitação Designada - Matéria Legislativa',
	'template'    	=> 'proclegis_nova_tramitacao_interna',
	"v:numero"		=> "$numero",
    "v:ano" 		=> "$ano",
    "v:cli_url" 	=> $_SESSION['cliente_url'],
    "v:sis_url" 	=> $_SESSION['sistema_url'],
    "v:id_materia" 	=> $id,
	"v:email" 		=> $am_email
]);


?>
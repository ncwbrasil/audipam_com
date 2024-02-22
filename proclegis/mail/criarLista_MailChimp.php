<?php

include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");




// $contact           = new stdClass;
// $contact->company  = "php.com.br";
// $contact->address1 = "Rua das Flores";
// $contact->city     = "Lajeado";
// $contact->state    = "RS";
// $contact->zip      = "95900000";
// $contact->country  = "BR";
// $contact->phone    = "";
// $campaign_defaults             = new stdClass;
// $campaign_defaults->from_name  = "php.com.br";
// $campaign_defaults->from_email = "php@email.com";
// $campaign_defaults->subject    = "teste";
// $campaign_defaults->language   = "pt-br";
// $opcoes                      = new stdClass;
// $opcoes->name                = "Lista do tutorial";
// $opcoes->contact             = $contact;
// $opcoes->permission_reminder = "Você está recebendo esse e-mail pois está na lista do tutorial do mailchimp";
// $opcoes->campaign_defaults   = $campaign_defaults;
// $opcoes->email_type_option   = true;
// $opcoes = json_encode($opcoes);
// $ch = curl_init();
// // Você precisa substituir o X
// curl_setopt($ch, CURLOPT_URL, "https://us6.api.mailchimp.com/3.0/lists");
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt($ch, CURLOPT_POSTFIELDS, $opcoes);
// curl_setopt($ch, CURLOPT_POST, 1);
// // Você precisa colocar a sua chave da api
// curl_setopt($ch, CURLOPT_USERPWD, "mailchimp" . ":" . "eaa1851b888f73abb62346cbda458853-us6");
// $headers = array();
// $headers[] = "Content-Type: application/x-www-form-urlencoded";
// curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
// $result = curl_exec($ch);
// if (curl_errno($ch))
// {
//     echo 'Error:' . curl_error($ch);
// }    
// curl_close ($ch);
// $result = json_decode($result);
// var_dump($result);






// Adicionar um usuário em ma lista
$membro                = new stdClass;
$membro->email_address = "gustavo@mogicomp.com.br";
$membro->status        = "subscribed";
$membro->tags          = ["cliente", "cliente ouro"];
$membro                = json_encode($membro);
// Você precisa colocar o seu ID
$id_lista = "fe8e9a70fd";
$ch = curl_init();
// Atualizar a URL
curl_setopt($ch, CURLOPT_URL, "https://us6.api.mailchimp.com/3.0/lists/{$id_lista}/members");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $membro);
curl_setopt($ch, CURLOPT_POST, 1);
// Usar sua chave
curl_setopt($ch, CURLOPT_USERPWD, "mailchimp" . ":" . "eaa1851b888f73abb62346cbda458853-us6");
$headers = array();
$headers[] = "Content-Type: application/x-www-form-urlencoded";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close ($ch);
var_dump($result);

require_once('../../vendor/autoload.php');

$mailchimp = new MailchimpMarketing\ApiClient();

$mailchimp->setConfig([
  'apiKey' => 'eaa1851b888f73abb62346cbda458853-us6',
  'server' => 'us6'
]);

$response = $mailchimp->lists->createList();
print_r($response);

?>
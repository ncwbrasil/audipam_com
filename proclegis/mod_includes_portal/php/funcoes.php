<?php
function encriptar($texto, $chave)
{
    $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($texto, $cipher, $chave, $options=OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, $chave, $as_binary=true);
    return $ciphertext = base64_encode($iv.$hmac.$ciphertext_raw);
}

function desencriptar($textoCodificado, $chave)
{
    $c = base64_decode($textoCodificado);
    $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, $sha2len=32);
    $ciphertext_raw = substr($c, $ivlen+$sha2len);
    $texto_original = openssl_decrypt($ciphertext_raw, $cipher, $chave, $options=OPENSSL_RAW_DATA, $iv);
    $calcmac = hash_hmac('sha256', $ciphertext_raw, $chave, $as_binary=true);
    if (hash_equals($hmac, $calcmac)) {//PHP 5.6+ timing attack safe comparison
        return $texto_original;
    }
}
function generatePassword($qtyCaraceters = 8)
{
    //Letras minúsculas embaralhadas
    $smallLetters = str_shuffle('abcdefghijklmnopqrstuvwxyz');
 
    //Letras maiúsculas embaralhadas
    $capitalLetters = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
 
    //Números aleatórios
    $numbers = (((date('Ymd') / 12) * 24) + mt_rand(800, 9999));
    $numbers .= 1234567890;
 
    //Caracteres Especiais
    $specialCharacters = str_shuffle('-_=');
 
    //Junta tudo
    $characters = $capitalLetters.$smallLetters.$numbers.$specialCharacters;
 
    //Embaralha e pega apenas a quantidade de caracteres informada no parâmetro
    $password = substr(str_shuffle($characters), 0, $qtyCaraceters);
 
    //Retorna a senha
    return $password;
}
function rBlankLines($str) {  
    $str = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\r\n", $str);  
    return $str;
}

function bindFields($fields){
	
    end($fields);
    $lastField = key($fields);

    $bindString = ' ';
    foreach($fields as $field => $data){ 
            $bindString .= $field . '=:' . $field; 
            $bindString .= ($field === $lastField ? ' ' : ',');
    }
    return $bindString;
}
function selectLoginUsuario($PDO, $email, $senha)
{
    $sql = "SELECT * FROM cadastro_usuarios 
			INNER JOIN admin_setores ON admin_setores.set_id = cadastro_usuarios.usu_setor
            LEFT JOIN aux_autoria_autores ON aux_autoria_autores.usuario = cadastro_usuarios.usu_id
            WHERE usu_email = :email AND usu_senha = :senha
		";
	$stmt = $PDO->prepare( $sql );

	$stmt->bindParam( ':email', $email );
	$stmt->bindParam( ':senha', $senha );
	$stmt->execute();
	$rows = $stmt->rowCount();
	if ($rows > 0)
	{
        while($field = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $result[] = $field;
        }
        
    }
    
    return $result;
}
function selectLoginUsuarioWebmaster($PDO, $email, $senha)
{
    $sql = "SELECT * FROM cadastro_usuarios 
			INNER JOIN admin_setores ON admin_setores.set_id = cadastro_usuarios.usu_setor
            WHERE usu_email = :email AND usu_senha = :senha
		";
	$stmt = $PDO->prepare( $sql );

	$stmt->bindParam( ':email', $email );
	$stmt->bindParam( ':senha', $senha );
	$stmt->execute();
	$rows = $stmt->rowCount();
	if ($rows > 0)
	{
        while($field = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $result[] = $field;
        }
     
    }
    
    return $result;
}
function insertLogUsuario($PDO, $dados)
{

    $dados = array_filter($dados);

    $sql = " INSERT INTO log_login_usuarios SET ".bindFields($dados)." ";
		
    $stmt = $PDO->prepare( $sql );
    $stmt->execute($dados);
    $rows = $stmt->rowCount();
    return $rows;
}



function alertaWeb($PDO, $remetente, $destinatario, $ale_descricao, $ale_link)
{
      
    $sql = "SELECT * FROM cadastro_usuarios
            LEFT JOIN admin_setores ON admin_setores.set_id = cadastro_usuarios.usu_setor
            WHERE usu_id IN ( $destinatario )";
    $stmt = $PDO->prepare($sql);

    $stmt->execute();
    $rows = $stmt->rowCount();
    if($rows > 0 )
    {
        while($result = $stmt->fetch())
        {
            $sql_alerta = "INSERT INTO social_alertas SET 
                            ale_remetente = :ale_remetente,  
                            ale_destinatario = :ale_destinatario,
                            ale_descricao = :ale_descricao, 
                            ale_lida = :ale_lida,
                            ale_arquivado = :ale_arquivado,
                            ale_link = :ale_link ";            
            $stmt_alerta = $PDO->prepare($sql_alerta);
            $stmt_alerta->bindParam(':ale_remetente', $remetente);
            $stmt_alerta->bindParam(':ale_destinatario', $result['usu_id']);
            $stmt_alerta->bindParam(':ale_descricao', $ale_descricao);
            $stmt_alerta->bindValue(':ale_lida', 0);
            $stmt_alerta->bindValue(':ale_arquivado', 0);
            $stmt_alerta->bindParam(':ale_link', $ale_link);
            if($stmt_alerta->execute()){}else{$erro=1;}				                    
        }
        if($erro != 1)
        {
            return "true";
        }
        else
        {
            return "false";
        }
    }
}


if(!function_exists('sec_session_start'))
{
    function sec_session_start() 
    {
       	$session_name = 'sec_session_id'; // Define um nome padrão de sessão
        $secure = false; // Defina como true (verdadeiro) caso esteja utilizando https.
        $httponly = true; // Isto impede que o javascript seja capaz de acessar a id de sessão. 

        ini_set('session.use_only_cookies', 1); // Força as sessões a apenas utilizarem cookies. 
        $cookieParams = session_get_cookie_params(); // Recebe os parâmetros atuais dos cookies.
        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 
        session_name($session_name); // Define o nome da sessão como sendo o acima definido.
        session_start(); // Inicia a sessão php.
        //session_regenerate_id(true); // regenerada a sessão, deleta a outra.
    }
}
function reverteData($campo)
{
    if (strpos($campo, '/') === false) 
    {
        $nova_data = implode("/",array_reverse(explode("-",$campo)));
    }
    else
    {
        $nova_data = implode("-",array_reverse(explode("/",$campo)));
    }
    return $nova_data;
}
function rearrange( $arr ){
    foreach( $arr as $key => $all ){
        foreach( $all as $i => $val ){
            $new[$i][$key] = $val;    
        }    
    }
    return $new;
}
function verificaPermissao($acao,$permissoes,$pagina)
{
	$_SESSION['negado'] = 0;
	if($permissoes[$acao] != 1)
	{
		$_SESSION['negado'] = 1;
		exit;
		return false;
	}
	else
	{
		return true;
	}
}

function geradorTags($valor)
{
	$array1 = array( "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç",
					 "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç",
					 "/", "- ", ",", "?", "&", "º", "ª", "|", "'", "(", ")", ":");

	$array2 = array( "a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c",
					 "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C",
					 "", "", "", "", "", "", "", "", "", "", "", "");

	$tags = str_replace($array1, $array2, utf8_decode($valor));
	$tags = strtolower($tags);
	$tags = str_replace(' ', '-', $tags);
	
	return $tags;
}


function limpaStringAll($VString)
{
    $VNovo="";
    for ($i = 0; $i < mb_strlen($VString); $i++)
    {
        if (preg_match ("/[a-zA-Z0-9]/", substr($VString, $i, 1)) == 1)
        $VNovo .= substr($VString, $i, 1);
    }
    return $VNovo;
    
}


function limpaString($valor)
{
	$array1 = array( "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç",
					 "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç",
					 "/", "- ", ",", "?", "&", "º", "ª", "|", "'", "(", ")", ":");

	$array2 = array( "a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c",
					 "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C",
					 "", "", "", "", "", "", "", "", "", "", "", "");

	$tags = str_replace($array1, $array2, utf8_decode($valor));
	
	return $tags;
}

function bindFields2($fields){
    end($fields);
    $lastField = key($fields);

    $bindString = ' ';
    foreach($fields as $field => $data){ 
            $bindString .= ':' . $data; 
            $bindString .= ($field === $lastField ? ' ' : ',');
    }
    return $bindString;
}
function truncate( $string, $length, $truncateAfter = TRUE, $truncateString = '...' ) {
    if( strlen( $string ) <= $length ) {
        return $string;
    }
    $position = ( $truncateAfter == TRUE ? strrpos( substr( $string, 0, $length ), ' ' ) :
                                            strpos( substr( $string, $length ), ' ' ) + $length );
    return substr( $string, 0, $position ) . $truncateString;
}
function RetirarAcentos($frase) {
    $frase = str_replace(array("à","á","â","ã","ä","è","é","ê","ë","ì","í","î","ï","ò","ó","ô","õ","ö","ù","ú","û","ü","À","Á","Â","Ã","Ä","È","É","Ê","Ë","Ì","Í","Î","Ò","Ó","Ô","Õ","Ö","Ù","Ú","Û","Ü","ç","Ç","ñ","Ñ"),
                         array("a","a","a","a","a","e","e","e","e","i","i","i","i","o","o","o","o","o","u","u","u","u","A","A","A","A","A","E","E","E","E","I","I","I","O","O","O","O","O","U","U","U","U","c","C","n","N"), $frase);
 
    return $frase;                           
}
//########## FUNCAO PROXIMO DIA UTIL ###########
function getDayOfWeek($timestamp){
  	$date = getdate($timestamp);
    $diaSemana = $date['weekday'];
    if(preg_match('/(sunday|domingo)/mi',$diaSemana))
        $diaSemana = 'Domingo';
    else if(preg_match('/(monday|segunda)/mi',$diaSemana))
        $diaSemana = 'Segunda';
    else if(preg_match('/(tuesday|terça)/mi',$diaSemana))
        $diaSemana = 'Terça';
    else if(preg_match('/(wednesday|quarta)/mi',$diaSemana))
        $diaSemana = 'Quarta';
    else if(preg_match('/(thursday|quinta)/mi',$diaSemana))
        $diaSemana = 'Quinta';
    else if(preg_match('/(friday|sexta)/mi',$diaSemana))
        $diaSemana = 'Sexta';
    else if(preg_match('/(saturday|sábado)/mi',$diaSemana))
        $diaSemana = 'Sábado';
         
    return $diaSemana;
}

function diaUtil($data){
    while(true){
        if(getDayOfWeek($data) == 'Sábado'){
 
            $data = $data + (86400 * 2);
            return diaUtil($data);
             
        }else if(getDayOfWeek($data) == 'Domingo'){
             
            $data = $data + (86400 * 1);
            return diaUtil($data);
             
        }
		else
		{
            return $data;
        }
             
    }
}
function getIp()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
	{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

//############# FIM FUNCAO PROXIMO DIA UTIL ###############
?>
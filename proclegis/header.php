<?php
	
	include_once('url.php');
	include_once("../core/mod_includes/php/funcoes.php");
	include_once("mod_includes_portal/php/connect.php");
	
	//print_r($_SESSION);
	if($cli_url && $_SESSION['cliente'] == "")
	{
		$_SESSION['cliente'] = $cli_url;
		?><script>document.location.href= "../proclegis/";</script><?php
	}
	# PEGA NOME SISTEMA E CLIENTE #
	$sql = "SELECT * FROM cadastro_clientes
			INNER JOIN cadastro_sistemas ON cadastro_sistemas.sis_id = cadastro_clientes.cli_sistema 
			LEFT JOIN end_uf ON end_uf.uf_id = cadastro_clientes.cli_uf 
			LEFT JOIN end_municipios ON end_municipios.mun_id = cadastro_clientes.cli_municipio
			WHERE cli_url = :cli_url AND sis_url = :sis_url AND cli_status = :cli_status";
	$stmt = $PDO->prepare( $sql );
	$sistema = "proclegis";
	$stmt->bindParam( ':cli_url', $_SESSION['cliente']);
	$stmt->bindParam( ':sis_url', $sistema);
	$stmt->bindValue( ':cli_status', 	1 );
	$stmt->execute();
	$rows = $stmt->rowCount();	

	if($rows > 0)
	{
		$result = $stmt->fetch();	
		$sis_nome 	= $result['sis_nome'];	
		$sis_logo 	= $result['sis_logo'];	
		$sis_dominio = $result['sis_dominio'];	
		$cli_id 	= $result['cli_id'];	
		$cli_telefone 	= $result['cli_telefone'];
		$cli_whats 	= $result['cli_whats'];
		$cli_nome 	= $result['cli_nome'];
		$cli_foto 	= $result['cli_foto'];		
		$cli_foto 	= $result['cli_foto'];		
		$cli_cep 	= $result['cli_cep'];		
		$cli_endereco 	= $result['cli_endereco'];		
		$cli_numero 	= $result['cli_numero'];
		$cli_bairro 	= $result['cli_bairro'];		
		$mun_nome 	= $result['mun_nome'];		
		$uf_sigla 	= $result['uf_sigla'];		
		
	}
	else
	{	
		echo "Página não encontrada :(";
		unset($_SESSION['audipam']['webmaster']);
		unset($_SESSION['proclegis']);
		unset($_SESSION['usuario_name']);
		unset($_SESSION['usuario_id']);
		unset($_SESSION['usuario_login']);
		unset($_SESSION['setor_nome']);
		unset($_SESSION['setor_id']);
		unset($_SESSION['autor_id']);
		unset($_SESSION['cliente_id']);
		unset($_SESSION['cliente_url']);
		unset($_SESSION['sistema_url']);
		session_unset();
		session_destroy();
		session_write_close();
		exit;
	}
   $sql_consulta = "SELECT * FROM aux_configuracao_paginas WHERE clientes =:clientes ";	
            $stmt_consulta = $PDO_PROCLEGIS->prepare($sql_consulta);
            $stmt_consulta->bindParam(':clientes', $cli_id); 	
            $stmt_consulta->execute(); 
            $result= $stmt_consulta->fetch(); 
            $cor_fundo = $result['cor_fundo'];
            $cor_topo = $result['cor_topo'];            
            $cor_rodape = $result['cor_rodape'];
?>

<!-- META TAGS -->
<meta http-equiv="Content-Language" content="pt-br">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta 	name="author" content="Audipam">
<title><?php echo $cli_nome;?> | Processo Legislativo</title>

<!-- ESTILO E JQUERY -->
<link 	rel="shortcut icon" href="../core/imagens/favicon.png">
<link 	href="../core/mod_menu/css/reset.css" rel="stylesheet" > <!-- CSS reset -->
<link 	href="mod_includes_portal/css/style.css" rel="stylesheet" type="text/css" />

<script src="../core/mod_includes/js/jquery-2.1.4.js" type="text/javascript"></script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
<script src="../core/mod_includes/js/funcoes.js" type="text/javascript"></script>
<script src="mod_includes_portal/js/wow.min.js"></script>

<!-- TOOLBAR -->
<link 	href="../core/mod_includes/js/toolbar/jquery.toolbars.css" rel="stylesheet" />
<link 	href="../core/mod_includes/js/toolbar/bootstrap.icons.css" rel="stylesheet">
<script src="../core/mod_includes/js/toolbar/jquery.toolbar.js"></script>

<!-- ui -->
<link 	href="../core/mod_includes/js/janela/jquery-ui.css" rel="stylesheet" >
<script src="../core/mod_includes/js/janela/jquery-ui.js"></script>

<!-- ABAS -->
<link 	href="../core/mod_includes/js/abas/bootstrap.css" rel="stylesheet">
<script src="../core/mod_includes/js/abas/bootstrap.js"></script>
	
<!-- Material Design Bootstrap -->
<link href="../core/mod_includes/js/mdbootstrap/css/mdb.css" rel="stylesheet">

<!-- CHARTS -->
<script src="../core/mod_includes/js/graficos/zingchart.min.js"></script>
<script>zingchart.MODULESDIR="..//core/mod_includes/js/graficos/modules/";</script>
<script src= "https://cdn.zingchart.com/zingchart.min.js"></script>
<script> zingchart.MODULESDIR = "https://cdn.zingchart.com/modules/";
ZC.LICENSE = ["569d52cefae586f634c54f86dc99e6a9","ee6b7db5b51705a13dc2339db3edaf6d"];</script>

<div vw class="enabled">
    <div vw-access-button class="active"></div>
    <div vw-plugin-wrapper>
        <div class="vw-plugin-top-wrapper"></div>
    </div>
</div>
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>
    new window.VLibras.Widget('https://vlibras.gov.br/app');
</script>

<script>
    new WOW().init();
</script>
<script>
$(document).ready(function(){
    $('.bt_topo').css('display','none');
    $(window).scroll(function(){
        if ($(this).scrollTop() > 200) {
            $('.bt_topo').fadeIn();
        } else {
            $('.bt_topo').fadeOut();
        }
    });

    $('.bt_topo').click(function(){
        $('html, body').animate({scrollTop : 0},800);
        return false;
    });

});

function fonte(e){
	var elemento = $("body, a, p, title");
	var fonte = elemento.css('font-size');
	if (e == 'a') {
		elemento.css("font-size", parseInt(fonte) +2);	
	} else if(e == 'd'){
		elemento.css("font-size", parseInt(fonte) -2);

	} else if(e == 'n'){
		elemento.css("font-size",16);
	}	
}

function modContrast(dataContraste) {
  var setId;
  var cont = dataContraste; 
  if (cont == 1) {
    setId = 'contrastePreto';
  } else {
    setId = ''
  }
  document.querySelector("body").setAttribute("id", setId);
  	
    $.post('contraste.php',{contraste: cont},function(contraste){})
}

$( document ).ready(function() {
    var variavel = "<?php echo $_SESSION['contraste']; ?>";
    if(variavel == 1){
        modContrast(variavel)
    }
    else {
        modContrast(variavel)
    }

});

 </script>
    
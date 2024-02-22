<?php
$sql = "SELECT * FROM cadastro_usuarios 
		WHERE usu_id = :usu_id";
$stmt = $PDO->prepare( $sql );
$stmt->bindParam( ':usu_id', $_SESSION['usuario_id'] );
$stmt->execute();
$result = $stmt->fetch();
$foto = $result['usu_foto'];

if($foto == '')
{
	$foto = '../core/imagens/perfil.png';
}
?>

<script>
function verifyAlerta(){  
	var tempo = 5000;
	jQuery.post("../core/mod_includes/php/verifica_alerta.php",
	{
		usuario:<?php echo $_SESSION["usuario_id"];?>
	},
	function(valor) // Carrega o resultado acima para o campo
	{	
		if(valor == 0)
		{
			jQuery(".nova_alerta").html("&nbsp;");	
			jQuery(".nova_alerta").css({"visibility":"hidden"});	
		}
		else
		{
			jQuery(".nova_alerta").html(valor);	
			jQuery(".nova_alerta").css({"visibility":"visible"});	
		}
	});
	
	jQuery.post("../core/mod_includes/php/carrega_alerta.php",
	{
		usuario:<?php echo $_SESSION["usuario_id"];?>
	},
	function(valor) // Carrega o resultado acima para o campo
	{	
		if(valor == 0)
		{
			jQuery(".alts").html("&nbsp;");	
		}
		else
		{
			jQuery(".alts").html(valor);
		}
	});
	
	setTimeout(function () 
	{
		verifyAlerta();
	}
	,tempo);
}
verifyAlerta();

var wd = screen.availWidth;
var hd = screen.availHeight;
</script>
<header class="cd-main-header"  id="barra" style='z-index:8'>
    <a href="#0" class="cd-logo"> </a>
    <div class='suggestion2'>
    <div class="cd-search is-hidden">
        <form action="#0">
        	
            	<input type="search" placeholder=" Pesquisar..." id="search">
                <div class='suggestionsBox2' id='suggestions2' style='display: none;'>
                    <div class='suggestionList2' id='autoSuggestionsList2'>
                        &nbsp;
                    </div>
                </div>
             
        </form>
    </div> <!-- cd-search -->
	 </div>  
    <a href="#0" class="cd-nav-trigger"><span></span></a>
    <div class="meu_perfil" >
        <a href="meu_perfil">
            <img class="perfil" src="<?php echo $foto?>" alt="avatar">            
        </a><div>&nbsp;</div>
    </div>
	<div class="alerts">
        <a href='#0' class="alerta"><i class="far fa-bell"></i></a><div class='nova_alerta'>&nbsp;</div>
        <div class='alertas'>
            <img src='../core/imagens/seta_alertas.png' width='20' style='float:right; margin-top:-22px; margin-right:5px; '/>
            <p class='titulo'>Notificações</p>
            
            <div class='alts'>
            </div>
		</div><div>&nbsp;</div>
	</div>
	<div class="agenda">
		<a href="social_agenda/view">
			<i class="far fa-calendar-alt"></i>
		</a>
	</div>
    <nav class="cd-nav">
        <ul class="cd-top-nav">
            
        </ul>
    </nav>
</header> <!-- .cd-main-header -->
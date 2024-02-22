<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- Chat -->
<link type="text/css" rel="stylesheet" media="all" href="../mod_includes/js/chat/chat.css" />
<link type="text/css" rel="stylesheet" media="all" href="../mod_includes/js/chat/screen.css" />
<!-- Chat -->
<link href="../mod_menu/menu.css" rel="stylesheet" type="text/css" />
<script>
jQuery(document).ready(function()
{
	jQuery(".sub").hide();
 	jQuery("div.menus").click(function(){
		jQuery(this).toggleClass("active").next().slideToggle("slow");
		return false;
	});
});
</script>
</head>
<?php
function RetirarAcentos($frase) {
    $frase = str_replace(array("à","á","â","ã","ä","è","é","ê","ë","ì","í","î","ï","ò","ó","ô","õ","ö","ù","ú","û","ü","À","Á","Â","Ã","Ä","È","É","Ê","Ë","Ì","Í","Î","Ò","Ó","Ô","Õ","Ö","Ù","Ú","Û","Ü","ç","Ç","ñ","Ñ"),
                         array("a","a","a","a","a","e","e","e","e","i","i","i","i","o","o","o","o","o","u","u","u","u","A","A","A","A","A","E","E","E","E","I","I","I","O","O","O","O","O","U","U","U","U","c","C","n","N"), $frase);
 
    return $frase;                           
}
$sql = "SELECT emp_logo FROM cadastro_empresas
		INNER JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_empresa = cadastro_empresas.emp_id
		WHERE ctt_id = :id ";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':id',$_SESSION['contato_id']);
$stmt->execute();
$foto = $stmt->fetch(PDO::FETCH_OBJ)->emp_logo;
if($foto == '')
{
	$foto = '../imagens/perfil.png';
}
?>
<body>
<div id='janela' class='janela' style='display:none;'> </div>
<div id='janelaAcao' class='janelaAcao' style='display:none;'> </div>
<div class="containermenu bodytext">
    <div class="textomenu"> 
    	<div id="logo">
	        <img src="../imagens/logo_branco.png" border="0" />	
        </div>
    	<div id='usuario'>
        	<a href='meu_perfil.php?pagina=meu_perfil<?php echo $autenticacao;?>'>
        	<img src='../admin/imagem.php?arquivo=<?php echo $foto;?>&largura=80&altura=80&marca=mini' border='0' title='Meu Perfil' />
            </a>
            <div class='info'>
          		<span class='nome'><?php echo $n;?></span><br /><span class='setor'><?php echo $_SESSION['setor_nome'];?></span>
           	</div>
        </div>
    	<div class="menu"><a href="admin.php?<?php echo $autenticacao;?>" class="top_link" target="_parent"><img src="../imagens/icon-home.png" border="0" valign='top' /> &nbsp;&nbsp; Dashboard</a></div> 
        
        <?php
		$a = "solicitacoes";
		$b = explode("_",$pagina);
		?>
        <div class='menus' id='solicitacoes'><a href='#' target='_parent'><img src='../imagens/icon-solicitacoes.png' border='0' valign='top' /> &nbsp;&nbsp; Solicitações</a></div>
        <div class='sub'>
            <div class='block'>
                <a href='solicitacoes_registrar.php?pagina=solicitacoes_registrar<?php echo $autenticacao;?>' target='_parent'>&raquo; Registrar</a><br />
                <a href='solicitacoes_consultar.php?pagina=solicitacoes_consultar<?php echo $autenticacao;?>' target='_parent'>&raquo; Consultar</a>
                <br>
            </div>
        </div>  
       	<?php
		$pos = 	strpos(strtolower($a), $b[0]);
		$pos2 = strpos(strtolower($a), $b[1]);
		$pos3 = strpos(strtolower($a), $b[2]);
		//if($pos === false && $pos2 === false && $pos3 === false)
		if($pos === false)
		{
			
		}
		else
		{
			?>
			<script>
			jQuery(document).ready(function()
			{
				jQuery("div#solicitacoes").toggleClass("active").next().slideToggle("slow");
			});
			</script>
			<?php
		}
		?>
         
        <div class="menu">   
            	<a onclick="
                	abreMask(
                    'Deseja realmente sair do sistema?<br><br>'+
                    '<input value=\' Sim \' type=\'button\' onclick=javascript:window.location.href=\'logout.php?pagina=logout\';>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                    '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
                " class="top_link" target="_parent"><img src='../imagens/icon-sair.png' border='0' valign='top'> &nbsp;&nbsp; Sair</a>
   		</div> 
    </div>    
</div>



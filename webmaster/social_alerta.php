<?php
//include_once("url.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../core/mod_includes/php/connect_sistema.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php echo include_once("url.php");?>
    <title>NCW Brasil</title>
    <meta	name="viewport" content="width=device-width, initial-scale=1">
    <meta 	name="author" content="MogiComp">
    <meta 	http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link 	rel="shortcut icon" href="imagens/favicon.png">
    <link 	href="../core/mod_menu/css/reset.css" rel="stylesheet" > <!-- CSS reset -->
    <link 	href="../core/css/style.css" rel="stylesheet" type="text/css" />
    <script src="../core/mod_includes/js/jquery-2.1.4.js" type="text/javascript"></script>
    <script src="../core/mod_includes/js/funcoes.js" type="text/javascript"></script>
    <script type="text/javascript" src="../core/mod_includes/js/tooltip.js"></script>
    <!-- TOOLBAR -->
    <link 	href="../core/mod_includes/js/toolbar/jquery.toolbars.css" rel="stylesheet" />
    <link 	href="../core/mod_includes/js/toolbar/bootstrap.icons.css" rel="stylesheet">
    <script src="../core/mod_includes/js/toolbar/jquery.toolbar.js"></script>
    <!-- TOOLBAR -->
    <link 	href="../core/mod_includes/js/janela/jquery-ui.css" rel="stylesheet" >
    <script src="../core/mod_includes/js/janela/jquery-ui.js"></script>
    <!-- MENU -->
    <link 	href="../core/mod_menu/css/style.css" rel="stylesheet" > <!-- Resource style -->
    <script src="../core/mod_menu/js/modernizr.js"></script> <!-- Modernizr -->
    <script src="../core/mod_menu/js/jquery.menu-aim.js"></script>
    <script src="../core/mod_menu/js/main.js"></script> <!-- Resource jQuery -->    
    <!-- FIM MENU -->
    <!-- ABAS -->
    <link 	href="../core/mod_includes/js/abas/bootstrap.css" rel="stylesheet">
    <script src="../core/mod_includes/js/abas/bootstrap.js"></script>
    <!-- ABAS -->
    <script>

</script>
</head>
<body>
	<?php
	require_once('../core/mod_includes/php/funcoes-jquery.php');
	require_once('../core/mod_includes/php/verificalogin.php');
	//require_once('../core/mod_includes/php/verificapermissao.php');
	include("../core/mod_menu/barra.php");
	?>
	<main class="cd-main-content">
    	<!--MENU-->
		<?php include("../core/mod_menu/menu.php"); ?>
        
        <!--CONTEUDO CENTRO-->
            <div class="content-wrapper">
            <div class='mensagem'></div>
            <p class="titulo">Notificações</p>
            <br />
            <?php
            $sql = "SELECT * FROM social_alertas 
                    LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = social_alertas.ale_remetente
                    WHERE ale_destinatario = :ale_destinatario AND ale_arquivado <> :ale_arquivado
                    ORDER BY ale_data_cadastro DESC";
            $stmt = $PDO->prepare($sql);
            $stmt->bindParam(":ale_destinatario",$_SESSION['usuario_id']);
            $stmt->bindValue(":ale_arquivado",1);
            $stmt->execute();
            $rows = $stmt->rowCount();
            if($rows > 0)
            {
                while($result = $stmt->fetch())
                {
                    $link='';
                    $ale_id = $result['ale_id'];
                    $descricao = $result['ale_descricao'];
                    $data = implode("/",array_reverse(explode("-",substr($result['ale_data_cadastro'],0,10))));
                    if($data == date("d/m/Y"))
                    {
                        $data = "Hoje";
                        
                        $data_ini = strtotime($result['ale_data_cadastro']);
                        $data_fim = strtotime(date("Y-m-d H:i:s"));
                        
                        $diferenca = $data_fim - $data_ini; 
                        if($diferenca < 3600) 
                        { 
                            $hora = (int)floor( $diferenca / (60)); 
                            $data_final = "há $hora minuto(s)";
                        }
                        else
                        {
                            $hora = (int)floor( $diferenca / (60 * 60)); 
                            $data_final = "há $hora hora(s)";
                        }
                        
                    }
                    else
                    {
                        $hora = substr($result['ale_data_cadastro'],11,5);
                        $data_final = "$data às $hora";
                    }
                    $lida = $result['ale_lida'];
                    $link = $result['ale_link'];
                    if($result['usu_foto'] != '')
                    {
                        $foto = $result['usu_foto'];
                    }
                    else
                    {
                        $foto = "../core/imagens/perfil.png";
                    }
                    $nome = $result['usu_nome'];
                    echo "
                    <div class='alertaBox "; if($lida == 0){ echo "n_lida";} echo "'>
                        <a href='"; if($link != ''){ echo $link;}else{ echo "#";} echo "'>
                        <div class='foto'>
                        <div class='foto_perfil' style='width:60px; height:60px; background:url($foto) center center; background-size: cover; border-radius:50px;' border='0'></div>
                        </div>
                        <div class='infos' onclick='alertaMarcarLida(".$ale_id.",this);'>
                            <div class='naolida'>
                            ";if($lida == 0){ echo "<i class='fas fa-exclamation-triangle'></i> &nbsp; ";} echo "
                            </div>
                            <div class='nome'>$nome</div>
                            <div class='descricao'>$descricao</div>
                            <div class='data'>$data_final</div>
                        </div>
                        </a>
                        <div class='acao'>
                        "; if($lida == 0){ echo "<span class='arq' onclick='alertaMarcarLida(".$ale_id.",this);' title='Marcar como lido'><i class='far fa-dot-circle'></i></span>";} echo "
                            <span onclick='alertaArquivar(".$ale_id.",this);' title='Arquivar notificação'><i class='far fa-times-circle'></i></span>
                        </div>                        
                        
                    </div>
                    ";
                }
            }
            else
            {
                echo "Não há alertas.";
            }
            
            ?>
		</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
</body>
</html>
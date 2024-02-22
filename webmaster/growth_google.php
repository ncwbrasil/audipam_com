<?php
namespace Vision;
$visionKey = "AIzaSyC_cWDwXo-k080lywdG9I2A7tTgGk9v4sA";


//autoload dos namespaces e classes
use Vision\Vision;
use Vision\Image;
use Vision\Feature;
use Google\Cloud\Translate\TranslateClient;

require_once('../vendor/autoload.php');

$pagina_link = 'growth_google';
include_once("url.php");
include_once("../core/mod_includes/php/connect_sistema.php");
include_once("../core/mod_includes/php/funcoes.php");
sec_session_start(); 
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Audipam | Gerenciador de Sistemas</title>
    <meta	name="viewport" content="width=device-width, initial-scale=1">
    <meta 	name="author" content="MogiComp">
    <meta 	http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link 	rel="shortcut icon" href="../core/imagens/favicon.png">
    <link 	href="../core/mod_menu/css/reset.css" rel="stylesheet" > <!-- CSS reset -->
    <link 	href="../core/css/style.css" rel="stylesheet" type="text/css" />
    <script src="../core/mod_includes/js/jquery-2.1.4.js" type="text/javascript"></script>
    <script src="../core/mod_includes/js/funcoes.js" type="text/javascript"></script>
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
     <!-- Bootstrap core CSS -->
	<!-- Material Design Bootstrap -->
    <link href="../core/mod_includes/js/mdbootstrap/css/mdb.css" rel="stylesheet">
    <!-- TINY -->
    <script src="../core/mod_includes/js/tinymce/tinymce.min.js"></script>
    <script>tinymce.init({ 
        selector:'textarea',
        content_style: 'textarea { font-family:"PT Sans" }',
        plugins: "image code jbimages imagetools advlist link table textcolor media paste",
        toolbar: "undo redo format fontsizeselect bold italic forecolor  alignleft aligncenter alignright alignjustify bullist numlist  table link media image jbimages",
        imagetools_toolbar: "rotateleft rotateright | flipv fliph | editimage imageoptions",
        paste_data_images: true,
        media_live_embeds: true,
        relative_urls : false,                
        paste_as_text: true,
        entity_encoding: 'raw'
    });
    tinyMCE.triggerSave();
    </script>
    <!-- TINY -->
    <style>
    .textarea {width:82%;float:right; margin-right:10px; display:table;} 
    </style>
</head>
<body>
	<?php
	require_once('../core/mod_includes/php/funcoes-jquery.php');
	require_once('../core/mod_includes/php/verificalogin.php');
	require_once('../core/mod_includes/php/verificapermissao.php');
	include("../core/mod_menu/barra.php");
	?>
	<main class="cd-main-content">
        
    	<!--MENU-->
		<?php include("../core/mod_menu/menu.php"); ?>
            
        <!--CONTEUDO CENTRO-->
		<div class="content-wrapper">
        <div class='mensagem'></div>
		<?php
        $page = "Growth &raquo; <a href='growth_google/view'>Google</a>";
        if(isset($_GET['goo_id'])){$goo_id = $_GET['goo_id'];} 
		if($goo_id == ''){ $goo_id = $_POST['goo_id'];}
        $goo_tipo = $_POST['goo_tipo'];
        $goo_query = $_POST['goo_query'];        
        $goo_string = rBlankLines(trim(strip_tags(str_replace("&nbsp;","",str_replace("<br />","\r\n",$_POST['goo_string'])))));
        
        
    //     $goo_string = urldecode(rBlankLines(trim(strip_tags(str_replace("&nbsp;","",str_replace("<br />","\r\n",$_POST['goo_string']))))));
    //     echo $goo_string;
    //     $regex = '/a\shref=\"https:\/\/br.linkedin.com\/in\/([a-zA-ZÀ-ú0-9-_]+)/';
    //     preg_match_all($regex, $goo_string, $resposta);
        
    //     echo "<pre>";
    //     $posts = array_values( array_unique($resposta[1]));
    //     print_r($posts);

    //     //foreach($posts as $key => $val)
    //    // {
    //         $handle = curl_init();
            
    //         $url = "https://www.linkedin.com/in/roxanedantas/detail/contact-info/";
            
    //         // Set the url
    //         curl_setopt($handle, CURLOPT_URL, $url);
    //         // Set the result output to be a string.
    //         curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    //         curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
    //         curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    //         curl_setopt($handle, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
    //         $output = curl_exec($handle);
            
    //         curl_close($handle);
    //         echo $output;exit;
            
    //         //echo $output;
    //         $a = explode('<meta property="og:image" content="',$output);
    //         $image = explode('" />',$a[1]);
    //         //echo $image[0];exit;
    //         echo "<br><img src='".$image[0]."' width='200'><br>";
    //         # OCR LABELS DETECTION #
    //         //instanciando a classe de client do Vision, passando a chave da API e o tipo de funcionalidade, no caso o Text Detection
    //         $vision = new Vision(
    //             $visionKey, 
    //             [
    //                 // See a list of all features in the table below
    //                 // Feature, Limit
    //                 new \Vision\Feature(Feature::LABEL_DETECTION, 100),
    //             ]
    //         );

    //         //enviando a imagem do gato e realizando a request.
    //         $imagePath = $image[0];
    //         $response = $vision->request(
    //             new Image($imagePath)
    //         );

    //         //recebendo o texto
    //         $labels = $response->getLabelAnnotations();
    //         // echo "<pre>";
    //         // print_r($texts);
    //         foreach ($labels as $label) 
    //         {                                
    //             $lab_label  = ($label->getDescription(). '');
    //             $lab_score      = ($label->getScore(). ' ');
    //             echo   $lab_label. " - ". $lab_score ."<br>";
    //         }
            
    //     //}


//        $regex = '/https:\/\/www.instagram.com\/p\/([a-zA-Z0-9]+)\//';
//        preg_match_all($regex, $goo_string, $resposta);
       
//         echo "<pre>";
//         $posts = array_values( array_unique($resposta[0]));
//         //print_r($posts);exit;
//         foreach($posts as $key => $val)
//         {
//             $handle = curl_init();
            
//             $url = $val;
            
//             // Set the url
//             curl_setopt($handle, CURLOPT_URL, $url);
//             // Set the result output to be a string.
//             curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
//             curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
//             curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
//             curl_setopt($handle, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
//             $output = curl_exec($handle);
            
//             curl_close($handle);
            
//             //echo $output;
//             $a = explode('<meta property="og:image" content="',$output);
//             $image = explode('" />',$a[1]);
//             //echo $image[0];exit;
//             echo "<br><img src='".$image[0]."' width='200'><br>";
//             # OCR LABELS DETECTION #
//             //instanciando a classe de client do Vision, passando a chave da API e o tipo de funcionalidade, no caso o Text Detection
//             $vision = new Vision(
//                 $visionKey, 
//                 [
//                     // See a list of all features in the table below
//                     // Feature, Limit
//                     new \Vision\Feature(Feature::LABEL_DETECTION, 100),
//                 ]
//             );

//             //enviando a imagem do gato e realizando a request.
//             $imagePath = $image[0];
//             $response = $vision->request(
//                 new Image($imagePath)
//             );

//             //recebendo o texto
//             $labels = $response->getLabelAnnotations();
//             // echo "<pre>";
//             // print_r($texts);
//             foreach ($labels as $label) 
//             {                                
//                 $lab_label  = ($label->getDescription(). '');
//                 $lab_score      = ($label->getScore(). ' ');
//                 echo   $lab_label. " - ". $lab_score ."<br>";
//             }
            
//         }
// exit;
//         $post = trim($resposta[1]);	
				




        $dados = array_filter(array(
            'goo_tipo' 	    => $goo_tipo,
            'goo_string' 	=> $goo_string,
            'goo_query' 	=> $goo_query
        ));
        
        if($action == "adicionar")
        {
            $sql = "INSERT INTO growth_google SET ".bindFields($dados);
            $stmt = $PDO->prepare($sql);	
            if($stmt->execute($dados))
            {		
               
				?>
				<script>
					mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
				</script>
				<?php

            }
            else
            {
                ?>
				<script>
					mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
				</script>
				<?php 
            }	
        }
        
    
        
        if($action == 'excluir')
        {
            unset($_SESSION['action']);
            $sql = "DELETE FROM growth_google WHERE goo_id = :goo_id";
            $stmt = $PDO->prepare($sql);
            $stmt->bindParam(':goo_id',$goo_id);
            if($stmt->execute())
            {
                ?>
				<script>
					mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
				</script>
				<?php
            }
            else
            {
                ?>
				<script>
					mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
				</script>
				<?php
            }
        }
       
        
        
        $num_por_pagina = 100;
        if(!$pag){$primeiro_registro = 0; $pag = 1;}
        else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
        $fil_nome = $_REQUEST['fil_nome'];
        if($fil_nome == '')
        {
            $nome_query = " 1 = 1 ";
        }
        else
        {
            $fil_nome1 = "%".$fil_nome."%";
            $nome_query = " (goo_query LIKE :fil_nome1  ) ";
        }
        $fil_executado = $_REQUEST['fil_executado'];
        if($fil_executado == '')
        {
            //$executado_query = " goo_executado = 0 ";
            $executado_query = " 1 = 1 ";
            $fil_executado_n = "Executado?";
        }
        elseif($fil_executado == 'Todos')
        {
            $executado_query = " 1 = 1 ";
            $fil_executado_n = "Executado?";
        }
        else
        {
            $executado_query = " goo_executado = :fil_executado ";	
            if($fil_executado == "1")
            {
                $fil_executado_n = "Sim";
            }
            elseif($fil_executado == "0")
            {
                $fil_executado_n = "Não";
            }
            		
        }
        
        $sql = "SELECT * FROM growth_google 
                WHERE ".$nome_query." AND ".$executado_query."
                ORDER BY goo_id DESC
                LIMIT :primeiro_registro, :num_por_pagina ";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':fil_nome1', 	$fil_nome1);
        $stmt->bindParam(':fil_executado', 	$fil_executado);
        $stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
        $stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
        $stmt->execute();
        $rows = $stmt->rowCount();

        $sql = "SELECT * FROM growth_google 
                WHERE ".$nome_query." AND ".$executado_query."
                ORDER BY goo_id DESC
                ";
        $stmt_all = $PDO->prepare($sql);
        $stmt_all->bindParam(':fil_nome1', 	$fil_nome1);
        $stmt_all->bindParam(':fil_executado', 	$fil_executado);
        $stmt_all->execute();
        $rows_all = $stmt_all->rowCount();

        if($pagina == "view")
        {
            echo "
            <div class='titulo'> $page  </div>
            <div id='botoes'>
				<div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"".$pagina_link."/add\");'><i class='fas fa-plus'></i></div>
                <div class='filtrar'><i class='fas fa-filter'></i></div>
               
                <div class='filtro'>
                    <form name='form_growth_google' id='form_growth_google' enctype='multipart/form-data' method='post' action='growth_google/view'>
                        <input type='text' name='fil_nome' id='fil_nome' placeholder='Profile' value='$fil_nome'>                    
                        <select name='fil_executado' id='fil_executado'>
                            <option value='$fil_executado'>$fil_executado_n</option>
                            <option value='1'>Sim</option> 
                            <option value='0'>Não</option>                                                            
                            <option value='Todos'>Todos</option>
                        </select>
                        <input type='submit' value=' Filtrar '>
                    </form>            
                
                </div>
            </div>  
            $rows_all querys
            <p>       
            ";
            if ($rows > 0)
            {
                echo "
                <table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
                    <tr>
                        <td class='titulo_first' align='left'>Query</td>
                        <td class='titulo_first' align='left'>Tipo</td>
                        <td class='titulo_first' align='center'>Executado?</td>
                        <td class='titulo_last' align='right'>Gerenciar</td>
                    </tr>";
                    $c=0;
                     while($result = $stmt->fetch())
                    {
                        $goo_id 	= $result['goo_id'];
                        $goo_tipo	= $result['goo_tipo'];
                        $goo_query 	= $result['goo_query'];
                        $goo_executado 	= $result['goo_executado'];
                        if($goo_executado == 1)
                        {
                            $executado = "<i class='fas fa-check green'></i>";
                        }
                        else
                        {
                            $executado = "<i class='fas fa-times red'></i>";
                        }
                    
                        $id = $goo_id;
                        //include("../core/mod_includes/modal/popUp.php");
                        if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;}  
                        echo "<tr class='$c1'>
                                  <td>$goo_query</td>
                                  <td>$goo_tipo</td>
                                  <td align='center'>$executado</td>
                                  <td align=center>
										<div class='g_excluir' title='Excluir' onclick=\"
											abreMask(
												'Essa operação não poderá ser desfeita. Deseja realmente excluir este item? <br><br>'+
												'<input value=\' Sim \' type=\'button\' class=\'close_janela\' onclick=verificaPermissao(".$permissoes["excluir"].",\'".$pagina_link."/view/excluir/$goo_id\');>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
												'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
											\">	<i class='far fa-trash-alt'></i>
										</div>
										<div class='g_exibir' title='Link' onclick='MyPopUpWin(\"https://instagram.com/".$goo_query."\", 500, 500); marcarExecutado(\"$goo_id\",this);'><i class='fas fa-external-link-alt'></i></div>
                                  
                                  </td>
                              </tr>";
                    }
                    echo "</table>";
                    $variavel = "&fil_nome=$fil_nome";
                    $cnt = "SELECT COUNT(*) FROM growth_google WHERE ".$nome_query." AND  ".$executado_query." ";
                    $stmt = $PDO->prepare($cnt);
                    $stmt->bindParam(':fil_nome1', 	$fil_nome1);
                    $stmt->bindParam(':fil_executado', 	$fil_executado);
                    include("../core/mod_includes/php/paginacao.php");
            }
            else
            {
                echo "<br><br><br>Não há nenhum item cadastrado.";
            }
        }
        if($pagina == 'add')
        {
            echo "	
            <form name='form' id='form' enctype='multipart/form-data' method='post' action='growth_google/view/adicionar'>
                <div class='titulo'> $page &raquo; Adicionar  </div>
                <ul class='nav nav-tabs'>
                  <li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>
                </ul>
                <div class='tab-content'>
                    <div id='dados_gerais'  class='tab-pane fade in active'>
                        <p><label>Tipo:</label> 
                            <select name='goo_tipo' id='goo_tipo' class='obg'>
                                <option value=''>Tipo</option>
                                <option value='Email'>Email</option>
                                <option value='Instagram'>Instagram</option>
                                <option value='Instagram Seguidores'>Instagram Seguidores</option>
                                <option value='Linkedin'>Linkedin</option>
                                <option value='Facebook'>Facebook</option>
                            </select>
                        <p><label>Resultados por página:</label> <input name='goo_num' id='goo_num' placeholder='Resultados por página (1 - 100)' class='obg'>
                        <p><label>Start:</label> <input name='goo_start' id='goo_start' placeholder='Start' class='obg'>
                        <p><label>Query:</label> <input name='goo_query' id='goo_query' placeholder='Query' class='obg'>
                        <p><label>String:</label> <div class='textarea'><textarea name='goo_string' id='goo_string' placeholder='String' class='obg'></textarea></div>
                        <div class='result' style='display:table'>

                        </div>
						<br>							
                    </div>
				</div>
                <br>
                <center>
                <div id='erro' align='center'>&nbsp;</div>
                <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='growth_google/view'; value='Cancelar'/></center>
                </center>
            </form>
            ";
        }        
        
		?>
        </div> <!-- .content-wrapper -->
    </main> <!-- .cd-main-content -->
    

    <script>
        function MyPopUpWin(url, width, height) {
        var leftPosition, topPosition;
        //Allow for borders.
        leftPosition = (window.screen.width / 2) - ((width / 2) + 10);
        //Allow for title and status bars.
        topPosition = (window.screen.height / 2) - ((height / 2) + 50);
        //Open the window.
        window.open(url, "Window2",
        "status=no,height=" + height + ",width=" + width + ",resizable=yes,left="
        + leftPosition + ",top=" + topPosition + ",screenX=" + leftPosition + ",screenY="
        + topPosition + ",toolbar=no,menubar=no,scrollbars=no,location=no,directories=no");
    }
    </script>
    <!-- MODAL -->
</body>
</html>
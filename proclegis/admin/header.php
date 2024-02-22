<?php
	include_once('url.php');
	include_once("../../core/mod_includes/php/dadosGerais.php"); 
?>
<!-- META TAGS -->
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta 	name="author" content="Audipam">
<meta 	http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Processo Legislativo | <?php echo $cli_nome;?></title>

<!-- ESTILO E JQUERY -->
<link rel="shortcut icon" href="../../core/imagens/favicon.png">
<link 	href="../../core/mod_menu/css/reset.css" rel="stylesheet" > <!-- CSS reset -->
<link 	href="../mod_includes/css/style.css" rel="stylesheet" type="text/css" />
<script src="../../core/mod_includes/js/jquery-2.1.4.js" type="text/javascript"></script>
<script src="../../core/mod_includes/js/funcoes.js" type="text/javascript"></script>

<!-- TOOLBAR -->
<link 	href="../../core/mod_includes/js/toolbar/jquery.toolbars.css" rel="stylesheet" />
<link 	href="../../core/mod_includes/js/toolbar/bootstrap.icons.css" rel="stylesheet">
<script src="../../core/mod_includes/js/toolbar/jquery.toolbar.js"></script>

<!-- ui -->
<link 	href="../../core/mod_includes/js/janela/jquery-ui.css" rel="stylesheet" >
<script src="../../core/mod_includes/js/janela/jquery-ui.js"></script>

<!-- ABAS -->
<link 	href="../../core/mod_includes/js/abas/bootstrap.css" rel="stylesheet">
<script src="../../core/mod_includes/js/abas/bootstrap.js"></script>
	
<!-- Material Design Bootstrap -->
<link href="../../core/mod_includes/js/mdbootstrap/css/mdb.css" rel="stylesheet">

<!-- JS TREE -->
<link rel="stylesheet" href="../../core/mod_includes/js/jstree/dist/themes/default/style.min.css" />

<!-- CHARTS -->
<script src="../../core/mod_includes/js/graficos/zingchart.min.js"></script>
<script>zingchart.MODULESDIR="../../core/mod_includes/js/graficos/modules/";</script>
<script src= "https://cdn.zingchart.com/zingchart.min.js"></script>
<script> zingchart.MODULESDIR = "https://cdn.zingchart.com/modules/";
ZC.LICENSE = ["569d52cefae586f634c54f86dc99e6a9","ee6b7db5b51705a13dc2339db3edaf6d"];</script>

<!-- TINY MCE -->
<script src="https://cdn.tiny.cloud/1/mlku98statni1114hhmw3eeks82m4ky26l9ecm3mluxchknu/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<link href='../../core/mod_includes/js/froala/css/froala_editor.pkgd.min.css' rel='stylesheet' type='text/css' />

<script>
      tinymce.init({
		  plugins: [
			'advlist autolink link image lists charmap print preview hr anchor pagebreak',
			'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
			'table emoticons template paste help'
			],
			images_file_types: 'jpg,jpeg,png,svg,webp',
		toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons',
        selector: '#conteudo',
		images_upload_url: 'tinymce_upload.php',
		height: 300,
		automatic_uploads : false,
		keep_styles : true,
		paste_retain_style_properties : "all",
		paste_word_valid_elements: "b,strong,i,em,h1,h2,u,p,ol,ul,li,a[href],span,color,font-size,font-color,font-family,mark",
		// images_upload_handler : function(blobInfo, success, failure) {
		// 	var xhr, formData;

		// 	xhr = new XMLHttpRequest();
		// 	xhr.withCredentials = false;
		// 	xhr.open('POST', 'tinymce_upload.php');

		// 	xhr.onload = function() {
		// 		var json;

		// 		if (xhr.status != 200) {
		// 			failure('HTTP Error: ' + xhr.status);
		// 			return;
		// 		}

		// 		json = JSON.parse(xhr.responseText);

		// 		if (!json || typeof json.file_path != 'string') {
		// 			failure('Invalid JSON: ' + xhr.responseText);
		// 			return;
		// 		}

		// 		success(json.file_path);
		// 	};

		// 	formData = new FormData();
		// 	formData.append('file', blobInfo.blob(), blobInfo.filename());

		// 	xhr.send(formData);
		// },
      });
    </script>

<?php
	function log_operacao($id, $PDO_PROCLEGIS){		
		$log = explode("/", $_SERVER['REQUEST_URI']);
		$log2 = explode("/", $_SERVER['REQUEST_URI']);
		$log = end($log);
		$e1 = explode("?", $log); 
		$e2 =  explode("=", $e1[1]);
		$evento =  $e2[0];
		if ($evento == 'pag'){
			$log = $log2[6]; 
		}
		$numero = $evento % 2; 		
		if (is_int($numero)){
			$log = $log2[6];
		}
		$data_cadastro = date('Y-m-d H:i:s');
		$sql_log = "INSERT INTO log_operacoes (lop_id_usuario, lop_url_pagina, lop_acao, lop_id_registro, lop_data) VALUES (:lop_id_usuario, :lop_url_pagina, :lop_acao, :lop_id_registro, :lop_data)";
		$stmt_log = $PDO_PROCLEGIS->prepare($sql_log);
		$stmt_log->bindValue('lop_id_usuario', $_SESSION['usuario_id']); 
		$stmt_log->bindValue('lop_url_pagina',$_SERVER['REQUEST_URI']); 
		$stmt_log->bindValue('lop_data', $data_cadastro); 
		$stmt_log->bindValue('lop_acao',$log); 
		$stmt_log->bindValue('lop_id_registro',$id); 
		$stmt_log->execute();		
	}

	require_once('../mod_includes/php/funcoes-jquery.php');
	require_once('../mod_includes/php/verificalogin.php');
	if($page_link != "dashboard")
	{
		require_once('../mod_includes/php/verificapermissao.php');	
	}	


?>


    
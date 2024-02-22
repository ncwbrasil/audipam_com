<?php
session_start (); 
$pagina_link = 'agenda_gerenciar';
$age_id = $_GET['age_id'];
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Audipam - Sistema Administrativo Integrado</title>
<meta name="author" content="MogiComp">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="../imagens/favicon.ico">
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<!-- ABAS -->
<link rel="stylesheet" href="../mod_includes/js/abas/bootstrap.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="../mod_includes/js/tooltip.js"></script>
<script src="../mod_includes/js/abas/bootstrap.js"></script>
<!-- ABAS -->
<script src="../mod_includes/js/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="../mod_includes/js/funcoes.js"></script>
<!-- TOOLBAR -->
<link href="../mod_includes/js/toolbar/jquery.toolbars.css" rel="stylesheet" />
<link href="../mod_includes/js/toolbar/bootstrap.icons.css" rel="stylesheet">
<script src="../mod_includes/js/toolbar/jquery.toolbar.js"></script>
<!-- TOOLBAR -->
<link rel="stylesheet" href="../mod_includes/js/janela/jquery-ui.css">
<script src="../mod_includes/js/janela/jquery-ui.js"></script>
<link rel="stylesheet" href="../css/eventCalendar.css">
<link rel="stylesheet" href="../css/eventCalendar_theme_responsive.css">


</head>
<body>
<?php	
require_once("../mod_includes/php/ctracker.php");
include		('../mod_includes/php/connect.php');
include		('../mod_includes/php/funcoes-jquery.php');
require_once('../mod_includes/php/verificalogin.php');
require_once('../mod_includes/php/verificapermissao.php');
?>
<div class='lateral'>
	<?php include("../mod_menu/menu.php");?>
</div>
<div class='barra'> 
    <?php include("../mod_menu/barra.php");?>
</div>
<div class='centro'>
    <div class='box'>
	<?php
    $page = "Cadastros &raquo; <a href='agenda_gerenciar.php?pagina=agenda_gerenciar".$autenticacao."'>Agenda</a> &raquo; <a href='agenda_gerenciar_itens.php?pagina=agenda_gerenciar_itens&age_id=$age_id".$autenticacao."'>Itens</a>";
	$agi_id = $_GET['agi_id'];
	$agi_agenda = $age_id;
	//$agi_cli = $_POST['agi_cli'];
	//$agi_cliente = $_POST['agi_cliente'];
	//$agi_cliente_nome = $_POST['agi_cliente_nome'];
	$agi_data = implode("-",array_reverse(explode("/",$_POST['agi_data'])));
	$agi_data_final = implode("-",array_reverse(explode("/",$_POST['agi_data_final'])));
	$agi_horario_inicial = $_POST['agi_horario_inicial'];
	$agi_horario = $_POST['agi_horario'];
	$agi_forma_atendimento = $_POST['agi_forma_atendimento'];
	$agi_descricao = str_replace('"','',str_replace(array("\r\n", "\r", "\n"), "<br />", $_POST['agi_descricao']));
	//if(!isset($_POST['agi_status'])){$agi_status = 1;}else{$agi_status = $_POST['agi_status'];}
	

	$dados = array_filter(array(
		'agi_agenda' 			=> $agi_agenda,
		//'agi_cli' 				=> $agi_cli,
		//'agi_cliente' 			=> $agi_cliente,
		'agi_data' 				=> $agi_data,
		'agi_data_final' 		=> $agi_data_final,
		'agi_horario_inicial' 	=> $agi_horario_inicial,
		'agi_horario' 			=> $agi_horario,
		'agi_forma_atendimento' => $agi_forma_atendimento,
		'agi_descricao' 		=> $agi_descricao
	));
	if($action == "adicionar")
    {
        $sql = "INSERT INTO agenda_gerenciar_itens SET ".bindFields($dados);
		$stmt = $PDO->prepare($sql);	
        if($stmt->execute($dados))
        {		
			$agi_id = $PDO->lastInsertId();
			
			//UPLOAD ARQUIVOS
			$caminho = "../admin/agenda_anexos/$agi_id/";
			foreach($_FILES as $key => $files)
			{
				$files_test = array_filter($files['name']);
				if(!empty($files_test))
				{
					if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
					if(!empty($files["name"]["anexo"]))
					{
						$nomeArquivo 	= $files["name"]["anexo"];
						$nomeTemporario = $files["tmp_name"]["anexo"];
						$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
						$agi_anexo	= $caminho;
						$agi_anexo .= "anexo_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($agi_anexo));
						$sql = "UPDATE agenda_gerenciar_itens SET 
								agi_anexo 	 = :agi_anexo
								WHERE agi_id = :agi_id ";
						$stmt = $PDO->prepare($sql);
						$stmt->bindParam(':agi_anexo',$agi_anexo);
						$stmt->bindParam(':agi_id',$agi_id);
						if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
					}					
				}
			}
			//
			
			//TECNICOS - CAMPOS DINÂMICOS
			if(!empty($_POST['tecnico']) && is_array($_POST['tecnico']))
			{
				//LIMPA ARRAY
				foreach($_POST['tecnico'] as $item => $valor) 
				{
					$tecnico_filtrado[$item] = array_filter($valor);
				}
				//
				foreach($tecnico_filtrado as $item => $valor) 
				{
					if(!empty($valor))
					{
						$valor['agu_agenda_item'] = $agi_id;
						$sql = "INSERT INTO agenda_gerenciar_usuario SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//INSERE
							
							// ALERTA
							$sql = "INSERT INTO social_alertas (
									ale_usuario, 
									ale_descricao, 
									ale_lida, 
									ale_arquivado,
									ale_link) VALUES (
									:ale_usuario, 
									:ale_descricao, 
									:ale_lida,
									:ale_arquivado,
									:ale_link
									) ";
							$stmt_alerta = $PDO->prepare($sql);	
							$ale_descricao = "Você tem uma nova tarefa agendada para ".$_POST['agi_data']." às ".$agi_horario_inicial."";
							$ale_link = "social_agenda.php?pagina=agenda";
							$stmt_alerta->bindParam(':ale_usuario',$valor['agu_usuario']);
							$stmt_alerta->bindParam(':ale_descricao',$ale_descricao);
							$stmt_alerta->bindValue(':ale_lida',0);
							$stmt_alerta->bindValue(':ale_arquivado',0);
							$stmt_alerta->bindParam(':ale_link',$ale_link);
							if($stmt_alerta->execute())
							{		
							}
							else{ $erro=1; $err = $stmt_alerta->errorInfo();}
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
				}
			}
			
			//VEÍCULOS - CAMPOS DINÂMICOS			
			if(!empty($_POST['veiculo']) && is_array($_POST['veiculo']))
			{
				//LIMPA ARRAY
				foreach($_POST['veiculo'] as $item => $valor) 
				{
					$veiculo_filtrado[$item] = array_filter($valor);
				}
				//
				foreach($veiculo_filtrado as $item => $valor) 
				{		
					if(!empty($valor))
					{	
					
						$valor['agc_agenda_item'] = $agi_id;
						$sql = "INSERT INTO agenda_gerenciar_carro SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//INSERE
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
				}
			}
		}
		else
		{
			$erro=1;
		}
		if(!$erro)
		{
			echo "
            <SCRIPT language='JavaScript'>
                abreMask(
                '<img src=../imagens/ok.png> Cadastro efetuado com sucesso.<br><br>'+
                '<input value=\' Ok \' type=\'button\' class=\'close_janela\'>' );
            </SCRIPT>
                ";
		}
		else
        {
            echo "
            <SCRIPT language='JavaScript'>
                abreMask(
                '<img src=../imagens/x.png> Erro ao efetuar cadastro.<br><br>'+
                '<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>');
            </SCRIPT>
                "; 
        }	
    }
	
	if($action == "adicionar_rotineira")
    {
		function geraTimestamp($data) {
		$partes = explode('-', $data);
		return mktime(0, 0, 0, $partes[1], $partes[2], $partes[0]);
		}
		
		// Usa a função criada e pega o timestamp das duas datas:
		$time_inicial = geraTimestamp($agi_data);
		$time_final = geraTimestamp($agi_data_final);
		// Calcula a diferença de segundos entre as duas datas:
		$diferenca = $time_final - $time_inicial; // 19522800 segundos
		// Calcula a diferença de dias
		$dias = (int)floor( $diferenca / (60 * 60 * 24)); // 225 dias
		// Exibe uma mensagem de resultado:
		//echo "A diferença entre as datas ".$data_inicial." e ".$data_final." é de <strong>".$dias."</strong> dias";
		
		for($x = 0; $x <= $dias; $x++)
		{
			
			$data = date('Y-m-d', strtotime("+".$x." days",strtotime($agi_data))); 
			//echo $data."<br>";
			unset($dados['agi_data']);
			$dados['agi_data'] = $data;
			//print_r($dados);
		
		
		
        $sql = "INSERT INTO agenda_gerenciar_itens SET ".bindFields($dados);
		$stmt = $PDO->prepare($sql);	
        if($stmt->execute($dados))
        {		
			$agi_id = $PDO->lastInsertId();
			
			//UPLOAD ARQUIVOS
			$caminho = "../admin/agenda_anexos/$agi_id/";
			foreach($_FILES as $key => $files)
			{
				$files_test = array_filter($files['name']);
				if(!empty($files_test))
				{
					if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
					if(!empty($files["name"]["anexo"]))
					{
						$nomeArquivo 	= $files["name"]["anexo"];
						$nomeTemporario = $files["tmp_name"]["anexo"];
						$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
						$agi_anexo	= $caminho;
						$agi_anexo .= "anexo_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($agi_anexo));
						$sql = "UPDATE agenda_gerenciar_itens SET 
								agi_anexo 	 = :agi_anexo
								WHERE agi_id = :agi_id ";
						$stmt = $PDO->prepare($sql);
						$stmt->bindParam(':agi_anexo',$agi_anexo);
						$stmt->bindParam(':agi_id',$agi_id);
						if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
					}					
				}
			}
			//
			
			//TECNICOS - CAMPOS DINÂMICOS
			if(!empty($_POST['tecnico']) && is_array($_POST['tecnico']))
			{
				//LIMPA ARRAY
				foreach($_POST['tecnico'] as $item => $valor) 
				{
					$tecnico_filtrado[$item] = array_filter($valor);
				}
				//
				foreach($tecnico_filtrado as $item => $valor) 
				{
					if(!empty($valor))
					{
						$valor['agu_agenda_item'] = $agi_id;
						$sql = "INSERT INTO agenda_gerenciar_usuario SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//INSERE
							
							// ALERTA
							$sql = "INSERT INTO social_alertas (
									ale_usuario, 
									ale_descricao, 
									ale_lida, 
									ale_arquivado,
									ale_link) VALUES (
									:ale_usuario, 
									:ale_descricao, 
									:ale_lida,
									:ale_arquivado,
									:ale_link
									) ";
							$stmt_alerta = $PDO->prepare($sql);	
							$ale_descricao = "Você tem uma nova tarefa agendada para ".implode("/",array_reverse(explode("-",$dados['agi_data'])))." às ".$agi_horario_inicial."";
							$ale_link = "social_agenda.php?pagina=agenda";
							$stmt_alerta->bindParam(':ale_usuario',$valor['agu_usuario']);
							$stmt_alerta->bindParam(':ale_descricao',$ale_descricao);
							$stmt_alerta->bindValue(':ale_lida',0);
							$stmt_alerta->bindValue(':ale_arquivado',0);
							$stmt_alerta->bindParam(':ale_link',$ale_link);
							if($stmt_alerta->execute())
							{		
							}
							else{ $erro=1; $err = $stmt_alerta->errorInfo();}
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
				}
			}
			
			//VEÍCULOS - CAMPOS DINÂMICOS			
			if(!empty($_POST['veiculo']) && is_array($_POST['veiculo']))
			{
				//LIMPA ARRAY
				foreach($_POST['veiculo'] as $item => $valor) 
				{
					$veiculo_filtrado[$item] = array_filter($valor);
				}
				//
				foreach($veiculo_filtrado as $item => $valor) 
				{		
					if(!empty($valor))
					{	
					
						$valor['agc_agenda_item'] = $agi_id;
						$sql = "INSERT INTO agenda_gerenciar_carro SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//INSERE
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
				}
			}
		}
		else
		{
			$erro=1;
		}
	}
	
		if(!$erro)
		{
			echo "
            <SCRIPT language='JavaScript'>
                abreMask(
                '<img src=../imagens/ok.png> Cadastro efetuado com sucesso.<br><br>'+
                '<input value=\' Ok \' type=\'button\' class=\'close_janela\'>' );
            </SCRIPT>
                ";
		}
		else
        {
            echo "
            <SCRIPT language='JavaScript'>
                abreMask(
                '<img src=../imagens/x.png> Erro ao efetuar cadastro.<br><br>'+
                '<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>');
            </SCRIPT>
                "; 
        }	
    }
    
    if($action == 'editar')
    {
        $sql = "UPDATE agenda_gerenciar_itens SET ".bindFields($dados)." WHERE agi_id = :agi_id ";
		$stmt = $PDO->prepare($sql); 
		$dados['agi_id'] =  $agi_id;
		if($stmt->execute($dados))
        {
			//UPLOAD ARQUIVOS
			$caminho = "../admin/agenda_anexos/$agi_id/";
			foreach($_FILES as $key => $files)
			{
				$files_test = array_filter($files['name']);
				if(!empty($files_test))
				{
					if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
					if(!empty($files["name"]["anexo"]))
					{
						$nomeArquivo 	= $files["name"]["anexo"];
						$nomeTemporario = $files["tmp_name"]["anexo"];
						$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
						$agi_anexo	= $caminho;
						$agi_anexo .= "anexo_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($agi_anexo));
						$sql = "UPDATE agenda_gerenciar_itens SET 
								agi_anexo 	 = :agi_anexo
								WHERE agi_id = :agi_id ";
						$stmt = $PDO->prepare($sql);
						$stmt->bindParam(':agi_anexo',$agi_anexo);
						$stmt->bindParam(':agi_id',$agi_id);
						if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
					}					
				}
			}
			//
			
			## CAMPOS DINÂMICOS ##
			
			// TECNICOS - EXCLUI OS REMOVIDOS
			if(!empty($_POST['tecnico']) && is_array($_POST['tecnico']))
			{
				//LIMPA ARRAY
				foreach($_POST['tecnico'] as $item => $valor) 
				{
					$tecnico_filtrado[$item] = array_filter($valor);
				}
				//
				$a_excluir = array();
				foreach($tecnico_filtrado as $item) 
				{
					if(isset($item['agu_id']))
					{
						$a_excluir[] = $item['agu_id'];
					}
				}
				if(!empty($a_excluir))
				{
					$sql = "DELETE FROM agenda_gerenciar_usuario WHERE agu_agenda_item = :agi_id AND agu_id NOT IN (".implode(",",$a_excluir).") ";
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':agi_id', $agi_id);
					if($stmt->execute())
					{
						//echo "Excluido <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
				else
				{
					$sql = "DELETE FROM agenda_gerenciar_usuario WHERE agu_agenda_item = :agi_id ";
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':agi_id', $agi_id);
					if($stmt->execute())
					{
						//echo "Excluido todos <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
			}
			else
			{
				$sql = "DELETE FROM agenda_gerenciar_usuario WHERE agu_agenda_item = :agi_id ";
				$stmt = $PDO->prepare($sql); 
				$stmt->bindParam(':agi_id', $agi_id);
				if($stmt->execute())
				{
					//echo "Excluido todos <br>";
				}
				else{ $erro=1; $err = $stmt->errorInfo();}
			}
			// TECNICOS - ATUALIZA OU INSERE NOVOS
			if(!empty($_POST['tecnico']) && is_array($_POST['tecnico']))
			{
				//LIMPA ARRAY
				foreach($_POST['tecnico'] as $item => $valor) 
				{
					$tecnico_filtrado[$item] = array_filter($valor);
				}
				//
				foreach(array_filter($tecnico_filtrado) as $item => $valor) 
				{		
							
					if(isset($valor['agu_id']))
					{
						$valor2 = $valor;
						unset($valor2['agu_id']);
						
						$sql = "UPDATE agenda_gerenciar_usuario SET ".bindFields($valor2)." WHERE agu_id = :agu_id";
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//echo "Atualizado <br>";
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
					else
					{
						
						$valor['agu_agenda_item'] = $agi_id;
						$sql = "INSERT INTO agenda_gerenciar_usuario SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//echo "Inserido <br>";
						}
						else{ $erro=1; $err = $stmt->errorInfo();}						
					}
				}
			}
			
			// VEICULOS - EXCLUI OS REMOVIDOS
			if(!empty($_POST['veiculo']) && is_array($_POST['veiculo']))
			{
				//LIMPA ARRAY
				foreach($_POST['veiculo'] as $item => $valor) 
				{
					$veiculo_filtrado[$item] = array_filter($valor);
				}
				//
				
				$a_excluir = array();
				foreach($veiculo_filtrado as $item) 
				{
					if(isset($item['agc_id']))
					{
						$a_excluir[] = $item['agc_id'];
					}
				}
				if(!empty($a_excluir))
				{
					$sql = "DELETE FROM agenda_gerenciar_carro WHERE agc_agenda_item = :agi_id AND agc_id NOT IN (".implode(",",$a_excluir).") ";
					
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':agi_id', $agi_id);
					if($stmt->execute())
					{
						//echo "Excluido <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
				else
				{
					$sql = "DELETE FROM agenda_gerenciar_carro WHERE agc_agenda_item = :agi_id ";
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':agi_id', $agi_id);
					if($stmt->execute())
					{
						//echo "Excluido todos <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
			}
			else
			{
				$sql = "DELETE FROM agenda_gerenciar_carro WHERE agc_agenda_item = :agi_id ";
				$stmt = $PDO->prepare($sql); 
				$stmt->bindParam(':agi_id', $agi_id);
				if($stmt->execute())
				{
					//echo "Excluido todos <br>";
				}
				else{ $erro=1; $err = $stmt->errorInfo();}
			}
			
			// VEICULOS - ATUALIZA OU INSERE NOVOS
			if(!empty($_POST['veiculo']) && is_array($_POST['veiculo']))
			{
				//LIMPA ARRAY
				foreach($_POST['veiculo'] as $item => $valor) 
				{
					$veiculo_filtrado[$item] = array_filter($valor);
				}
				//
				foreach(array_filter($veiculo_filtrado) as $item => $valor) 
				{
					if(isset($valor['agc_id']))
					{
						$valor2 = $valor;
						unset($valor2['agc_id']);
						
						$sql = "UPDATE agenda_gerenciar_carro SET ".bindFields($valor2)." WHERE agc_id = :agc_id";
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//echo "Atualizado <br>";
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
					else
					{
						$valor['agc_agenda_item'] = $agi_id;
						$sql = "INSERT INTO agenda_gerenciar_carro SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//echo "Inserido <br>";
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
				}
			}
        }
        else{ $erro=1; $err = $stmt->errorInfo();}

        if(!$erro)
		{
			echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../imagens/ok.png> Dados alterados com sucesso.<br><br>'+
				'<input value=\' Ok \' type=\'button\' class=\'close_janela\'>' );
			</SCRIPT>
				";
		}
		else
		{
			echo "
			<SCRIPT language='JavaScript'>
				abreMask(
				'<img src=../imagens/x.png> Erro ao alterar dados.<br>Por favor verifique a exclusão de algum registro relacionado a outra tabela.<br><br>'+
				'<input value=\' Ok \' type=\'button\' class=\'close_janela\'>' );
			</SCRIPT>
			";
		}
    }
    
    if($action == 'excluir')
    {
       	$sql = "DELETE FROM agenda_gerenciar_itens WHERE agi_id = :agi_id";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':agi_id',$agi_id);
        if($stmt->execute())
        {
           /* echo "
            <SCRIPT language='JavaScript'>
                abreMask(
                '<img src=../imagens/ok.png> Exclusão realizada com sucesso<br><br>'+
                '<input value=\' OK \' type=\'button\' class=\'close_janela\'>' );
            </SCRIPT>
                ";*/
        }
        else
        {
            echo "
            <SCRIPT language='JavaScript'>
                abreMask(
                '<img src=../imagens/x.png> Erro ao realizar exclusão.<br><br>'+
                '<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back(); >');
            </SCRIPT>
            ";
        }
    }
	
    
    $num_por_pagina = 20;
    if(!$pag){$primeiro_registro = 0; $pag = 1;}
    else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
    $sql = "SELECT * FROM agenda_gerenciar_itens 
			LEFT JOIN ( agenda_gerenciar 
				LEFT JOIN cadastro_empresas AS emp_agenda ON emp_agenda.emp_id = agenda_gerenciar.age_empresa )
			ON agenda_gerenciar.age_id = agenda_gerenciar_itens.agi_agenda
			WHERE agi_agenda = :age_id
            ORDER BY agi_id DESC
			LIMIT :primeiro_registro, :num_por_pagina ";
  	$stmt = $PDO->prepare($sql);
	$stmt->bindParam(':age_id', 	$age_id);
	$stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
	$stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
	$stmt->execute();
    $rows = $stmt->rowCount();
    $result = $stmt->fetch();
    if($pagina == "agenda_gerenciar_itens")
    {
        echo "
		<div class='titulo'> $page  </div>
		<div id='botoes' style='width:500px;'>
			<input value='Nova Data' type='button' onclick=javascript:window.location.href='agenda_gerenciar_itens.php?pagina=agenda_gerenciar_itens_adicionar&age_id=$age_id".$autenticacao."'; />
			<input value='Tarefa Rotineira' type='button' onclick=javascript:window.location.href='agenda_gerenciar_itens.php?pagina=agenda_gerenciar_rotineira_adicionar&age_id=$age_id".$autenticacao."'; />
		</div>
		";
		if ($rows > 0)
		{
			?>
            <script>
				jQuery(document).ready(function() {
					jQuery("#eventCalendarShowDescription").eventCalendar({
						eventsjson: '../mod_includes/json/events.json_empresa.php?age_id=<?php echo $age_id.$autenticacao;?>',
						showDescription: true,
					});
				});
			</script>
			<?php
			echo "
			<br><p>
			<div class='corpo' id='agenda'>
			<br><br>
				<div class='empresa_agenda'> ".$result['emp_fantasia']."</div>
				<div id='eventCalendarShowDescription'></div>
				
			 </div>
			";
		}
		else
		{
			echo "<br><br><br><br>Não há nenhuma data cadastrada.";
		}
    }
    if($pagina == 'agenda_gerenciar_itens_adicionar')
    {
        echo "	
		<form name='form_agenda_gerenciar_itens' id='form_agenda_gerenciar_itens' enctype='multipart/form-data' method='post' action='agenda_gerenciar_itens.php?pagina=agenda_gerenciar_itens&action=adicionar&age_id=$age_id$autenticacao'>
            <div class='titulo'> $page &raquo; Adicionar  </div>
			<ul class='nav nav-tabs'>
			  <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
			  <li><a data-toggle='tab' href='#tecnicos'>Técnicos</a></li>
			  <li><a data-toggle='tab' href='#veiculos'>Veículos</a></li>			  
			</ul>
			
			<div class='tab-content'>
				<div id='dados_gerais' class='tab-pane fade in active'>
					<table align='center' cellspacing='0' width='100%' class='borda_aba'>
						<tr>
							<td align='left'>
								<br>
								<label>Data:</label> <input name='agi_data' id='agi_data' placeholder='Data'  onkeypress='return mascaraData(this,event);'>
								<p><label>Horário Inicial:</label> <input name='agi_horario_inicial' id='agi_horario_inicial' placeholder='Horário Inicial' onkeypress='return mascaraHorario(this,event);'>
								<p><label>Horário:</label> <input name='agi_horario' id='agi_horario' placeholder='Horário' >
								<p><label>Forma de Atendimento:</label> 
										<select name='agi_forma_atendimento' id='agi_forma_atendimento' >
										<option value=''>Forma de Atendimento</option>
										"; 
										$sql = "SELECT * FROM aux_formas_atendimento 
												ORDER BY fat_descricao ASC";
										$stmt = $PDO->prepare($sql);
										$stmt->execute();
										while($result = $stmt->fetch())
										{
											echo "<option value='".$result['fat_id']."'>".$result['fat_descricao']."</option>";
										}
										echo "
									</select>
								<p><label>Descrição:</label> <textarea name='agi_descricao' id='agi_descricao' placeholder='Descrição'></textarea>
								<p><label>Anexo:</label> <input type='file' name='agi_anexo[anexo]' id='agi_anexo'> 
									
							</td>
						</tr>
					</table>
			  	</div>
				<div id='tecnicos' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
								<div id='p_scents_tecnico'>
								<div class='bloco_tecnico'>
									<input type='hidden' name='tecnico[1][agu_id]' id='agu_id'>
									<br><label>Técnico:</label> 
										<select name='tecnico[1][agu_usuario]' id='agu_usuario' class='agu_usuario' >
										<option value=''>Técnico</option>
										"; 
										$sql = "SELECT * FROM admin_usuarios ORDER BY usu_nome ASC";
										$stmt = $PDO->prepare($sql);
										$stmt->execute();
										while($result = $stmt->fetch())
										{
											echo "<option value='".$result['usu_id']."'>".$result['usu_nome']."</option>";
										}
										echo "
									</select>
									<label>Responsável?</label>	
									   <select name='tecnico[1][agu_responsavel]' id='agu_responsavel' class='agu_responsavel'>
									   		<option value=''>Responsável?</option>
											<option value='Sim'>Sim</option>
											<option value='Não'>Não</option>
									   </select>
									<p>
									<img src='../imagens/icon-add.png' id='addTecnico' title='Adicionar +' class='botao_dinamico'>
									<br><br>
								</div>
								</div>
							</td>
						</tr>
					</table>
			  	</div>
			 	<div id='veiculos' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
								<div id='p_scents_veiculo'>
								<div class='bloco_veiculo'>
									<input type='hidden' name='veiculo[1][agc_id]' id='agc_id'>
									<br><label>Veículo:</label> 
										<select name='veiculo[1][agc_carro]' id='agc_carro'>
										<option value=''>Veículo</option>
										"; 
										$sql = "SELECT * FROM cadastro_carros ORDER BY car_descricao ASC";
										$stmt = $PDO->prepare($sql);
										$stmt->execute();
										while($result = $stmt->fetch())
										{
											echo "<option value='".$result['car_id']."'>".$result['car_descricao']."</option>";
										}
										echo "
									</select>
									<p>
									<img src='../imagens/icon-add.png' id='addVeiculo' title='Adicionar +' class='botao_dinamico'>
									<br>
								</div>
								</div>
							</td>
						</tr>
					</table>
			  	</div>				
			</div>   			
			<center>
			<div id='erro' align='center'>&nbsp;</div>
			<input type='button' id='bt_agenda_gerenciar_itens' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
			<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='agenda_gerenciar_itens.php?pagina=agenda_gerenciar_itens&age_id=$age_id".$autenticacao."'; value='Cancelar'/></center>
			</center>
        </form>
        ";
    }
    
	if($pagina == 'agenda_gerenciar_rotineira_adicionar')
    {
        echo "	
		<form name='form_agenda_gerenciar_itens' id='form_agenda_gerenciar_itens' enctype='multipart/form-data' method='post' action='agenda_gerenciar_itens.php?pagina=agenda_gerenciar_itens&action=adicionar_rotineira&age_id=$age_id$autenticacao'>
            <div class='titulo'> $page &raquo; Adicionar  </div>
			<ul class='nav nav-tabs'>
			  <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
			  <li><a data-toggle='tab' href='#tecnicos'>Técnicos</a></li>
			  <li><a data-toggle='tab' href='#veiculos'>Veículos</a></li>			  
			</ul>
			
			<div class='tab-content'>
				<div id='dados_gerais' class='tab-pane fade in active'>
					<table align='center' cellspacing='0' width='100%' class='borda_aba'>
						<tr>
							<td align='left'>
								<br>
								<label>Data inicial:</label> <input name='agi_data' id='agi_data' placeholder='Data inicial'  onkeypress='return mascaraData(this,event);'>
								<p><label>Data final:</label> <input name='agi_data_final' id='agi_data_final' placeholder='Data final'  onkeypress='return mascaraData(this,event);'>
								<p><label>Horário Inicial:</label> <input name='agi_horario_inicial' id='agi_horario_inicial' placeholder='Horário Inicial' onkeypress='return mascaraHorario(this,event);'>
								<p><label>Horário:</label> <input name='agi_horario' id='agi_horario' placeholder='Horário' >
								<p><label>Forma de Atendimento:</label> 
										<select name='agi_forma_atendimento' id='agi_forma_atendimento' >
										<option value=''>Forma de Atendimento</option>
										"; 
										$sql = "SELECT * FROM aux_formas_atendimento 
												ORDER BY fat_descricao ASC";
										$stmt = $PDO->prepare($sql);
										$stmt->execute();
										while($result = $stmt->fetch())
										{
											echo "<option value='".$result['fat_id']."'>".$result['fat_descricao']."</option>";
										}
										echo "
									</select>
								<p><label>Descrição:</label> <textarea name='agi_descricao' id='agi_descricao' placeholder='Descrição'></textarea>
								<p><label>Anexo:</label> <input type='file' name='agi_anexo[anexo]' id='agi_anexo'> 
									
							</td>
						</tr>
					</table>
			  	</div>
				<div id='tecnicos' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
								<div id='p_scents_tecnico'>
								<div class='bloco_tecnico'>
									<input type='hidden' name='tecnico[1][agu_id]' id='agu_id'>
									<br><label>Técnico:</label> 
										<select name='tecnico[1][agu_usuario]' id='agu_usuario' class='agu_usuario' >
										<option value=''>Técnico</option>
										"; 
										$sql = "SELECT * FROM admin_usuarios ORDER BY usu_nome ASC";
										$stmt = $PDO->prepare($sql);
										$stmt->execute();
										while($result = $stmt->fetch())
										{
											echo "<option value='".$result['usu_id']."'>".$result['usu_nome']."</option>";
										}
										echo "
									</select>
									<label>Responsável?</label>	
									   <select name='tecnico[1][agu_responsavel]' id='agu_responsavel' class='agu_responsavel'>
									   		<option value=''>Responsável?</option>
											<option value='Sim'>Sim</option>
											<option value='Não'>Não</option>
									   </select>
									<p>
									<img src='../imagens/icon-add.png' id='addTecnico' title='Adicionar +' class='botao_dinamico'>
									<br><br>
								</div>
								</div>
							</td>
						</tr>
					</table>
			  	</div>
			 	<div id='veiculos' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
								<div id='p_scents_veiculo'>
								<div class='bloco_veiculo'>
									<input type='hidden' name='veiculo[1][agc_id]' id='agc_id'>
									<br><label>Veículo:</label> 
										<select name='veiculo[1][agc_carro]' id='agc_carro'>
										<option value=''>Veículo</option>
										"; 
										$sql = "SELECT * FROM cadastro_carros ORDER BY car_descricao ASC";
										$stmt = $PDO->prepare($sql);
										$stmt->execute();
										while($result = $stmt->fetch())
										{
											echo "<option value='".$result['car_id']."'>".$result['car_descricao']."</option>";
										}
										echo "
									</select>
									<p>
									<img src='../imagens/icon-add.png' id='addVeiculo' title='Adicionar +' class='botao_dinamico'>
									<br>
								</div>
								</div>
							</td>
						</tr>
					</table>
			  	</div>				
			</div>   			
			<center>
			<div id='erro' align='center'>&nbsp;</div>
			<input type='button' id='bt_agenda_gerenciar_itens' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
			<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='agenda_gerenciar_itens.php?pagina=agenda_gerenciar_itens&age_id=$age_id".$autenticacao."'; value='Cancelar'/></center>
			</center>
        </form>
        ";
    }
	
    if($pagina == 'agenda_gerenciar_itens_editar')
    {
        $sql = "SELECT * FROM agenda_gerenciar_itens 
				LEFT JOIN aux_formas_atendimento ON aux_formas_atendimento.fat_id = agenda_gerenciar_itens.agi_forma_atendimento
				WHERE agi_id = :agi_id";
        $stmt = $PDO->prepare($sql);	
		$stmt->bindParam(':agi_id', $agi_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
        {
			$result = $stmt->fetch();
          	$agi_data 					= implode("/",array_reverse(explode("-",$result['agi_data'])));
			$agi_horario_inicial		= $result['agi_horario_inicial'];
			$agi_horario 				= $result['agi_horario'];
			$agi_forma_atendimento 		= $result['agi_forma_atendimento'];
			$fat_descricao 				= $result['fat_descricao'];
			$ser_descricao		 		= $result['ser_descricao'];
			$agi_descricao		 		= $result['agi_descricao'];
			$agi_anexo		 			= $result['agi_anexo'];
			$agi_status			 		= $result['agi_status'];
			echo "
            <form name='form_agenda_gerenciar_itens' id='form_agenda_gerenciar_itens' enctype='multipart/form-data' method='post' action='agenda_gerenciar_itens.php?pagina=agenda_gerenciar_itens&action=editar&age_id=$age_id&agi_id=$agi_id$autenticacao'>
                <div class='titulo'> $page &raquo; Editar </div>
				<ul class='nav nav-tabs'>
				  <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
				  <li><a data-toggle='tab' href='#tecnicos'>Técnicos</a></li>
				  <li><a data-toggle='tab' href='#veiculos'>Veículos</a></li>			  
				</ul>
				
				<div class='tab-content'>
					<div id='dados_gerais' class='tab-pane fade in active'>
						<table align='center' cellspacing='0' width='100%' class='borda_aba'>
							<tr>
								<td align='left'>
									<br>
									<label>Data:</label> <input name='agi_data' id='agi_data' value='$agi_data' placeholder='Data'  onkeypress='return mascaraData(this,event);'>
									<p><label>Horário Inicial:</label> <input name='agi_horario_inicial' id='agi_horario_inicial' value='$agi_horario_inicial' placeholder='Horário Inicial' onkeypress='return mascaraHorario(this,event);'>
									<p><label>Horário:</label> <input name='agi_horario' id='agi_horario' value='$agi_horario' placeholder='Horário' >
									<p><label>Forma de Atendimento:</label> 
											<select name='agi_forma_atendimento' id='agi_forma_atendimento' >
											<option value='$agi_forma_atendimento'>$fat_descricao</option>
											"; 
											$sql = "SELECT * FROM aux_formas_atendimento 
													ORDER BY fat_descricao ASC";
											$stmt = $PDO->prepare($sql);
											$stmt->execute();
											while($result = $stmt->fetch())
											{
												echo "<option value='".$result['fat_id']."'>".$result['fat_descricao']."</option>";
											}
											echo "
										</select>
									<p><label>Descrição:</label> <textarea name='agi_descricao' id='agi_descricao' placeholder='Descrição'>$agi_descricao</textarea>
									<p><label>Anexo:</label> ";if($agi_anexo != ''){ echo "<a href='$agi_anexo ' target='_blank'><img src='../imagens/icon-anexo.png' valign='middle' border='0'></a>";} echo " &nbsp; 
									<p><label>Alterar Anexo:</label> <input type='file' name='agi_anexo[anexo]' id='agi_anexo'> 
								</td>
							</tr>
						</table>
					</div>
					<div id='tecnicos' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
							<tr>
								<td align='left'>
									<div id='p_scents_tecnico'>
									";
									$sql = "SELECT * FROM agenda_gerenciar_usuario 
											LEFT JOIN admin_usuarios ON admin_usuarios.usu_id = agenda_gerenciar_usuario.agu_usuario	
											WHERE agu_agenda_item = :agu_agenda_item";
									$stmt = $PDO->prepare($sql);
									$stmt->bindParam(':agu_agenda_item', $agi_id);
									$stmt->execute();
									$rows = $stmt->rowCount();
									if($rows > 0)
									{
										$x=0;
										while($result = $stmt->fetch())
										{
											$x++;
											echo "
											<div class='bloco_tecnico'>
												<input type='hidden' name='tecnico[$x][agu_id]' id='agu_id' value='".$result['agu_id']."'>
												"; if($x > 1){ echo "<br><br><hr><p>";}else{ echo "<br>";} 
												echo "
												<label>Técnico:</label> 
													<select name='tecnico[$x][agu_usuario]' id='agu_usuario'  class='agu_usuario'>
													<option value='".$result['agu_usuario']."'>".$result['usu_nome']."</option>
													"; 
													$sql = "SELECT * FROM admin_usuarios ORDER BY usu_nome ASC";
													$stmt_gestor = $PDO->prepare($sql);
													$stmt_gestor->execute();
													while($result_gestor = $stmt_gestor->fetch())
													{
														echo "<option value='".$result_gestor['usu_id']."'>".$result_gestor['usu_nome']."</option>";
													}
													echo "
												</select>
												<label>Responsável?</label>	
												   <select name='tecnico[$x][agu_responsavel]' id='agu_responsavel' class='agu_responsavel'>
														<option value='".$result['agu_responsavel']."'>".$result['agu_responsavel']."</option>
														<option value='Sim'>Sim</option>
														<option value='Não'>Não</option>
												   </select>
												<p><img src='../imagens/icon-add.png' id='addTecnico' title='Adicionar +' class='botao_dinamico'> <img src='../imagens/icon-rmv.png' id='remTecnico' title='Remover' class='botao_dinamico'><br><br>
											</div>
											";
										}
									}
									else
									{
										echo "
										<div class='bloco_tecnico'>
											<input type='hidden' name='tecnico[1][agu_id]' id='agu_id'>
											<br><label>Técnico:</label> 
												<select name='tecnico[1][agu_usuario]' id='agu_usuario' class='agu_usuario' >
												<option value=''>Técnico</option>
												"; 
												$sql = "SELECT * FROM admin_usuarios ORDER BY usu_nome ASC";
												$stmt = $PDO->prepare($sql);
												$stmt->execute();
												while($result = $stmt->fetch())
												{
													echo "<option value='".$result['usu_id']."'>".$result['usu_nome']."</option>";
												}
												echo "
											</select>
											<label>Responsável?</label>	
											   <select name='tecnico[1][agu_responsavel]' id='agu_responsavel' class='agu_responsavel'>
													<option value=''>Responsável?</option>
													<option value='Sim'>Sim</option>
													<option value='Não'>Não</option>
											   </select>
											<p>
											<img src='../imagens/icon-add.png' id='addTecnico' title='Adicionar +' class='botao_dinamico'>
											<br><br>
										</div>
										";
									}
									echo "
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div id='veiculos' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
							<tr>
								<td align='left'>
									<div id='p_scents_veiculo'>
									";
									$sql = "SELECT * FROM agenda_gerenciar_carro
											LEFT JOIN cadastro_carros ON cadastro_carros.car_id = agenda_gerenciar_carro.agc_carro	
											WHERE agc_agenda_item = :agc_agenda_item";
									$stmt = $PDO->prepare($sql);
									$stmt->bindParam(':agc_agenda_item', $agi_id);
									$stmt->execute();
									$rows = $stmt->rowCount();
									if($rows > 0)
									{
										$x=0;
										while($result = $stmt->fetch())
										{
											$x++;
											echo "
											<div class='bloco_veiculo'>
												<input type='hidden' name='veiculo[$x][agc_id]' id='agc_id' value='".$result['agc_id']."'>
												"; if($x > 1){ echo "<br><br><hr><p>";}else{ echo "<br>";} 
												echo "
												<label>Tipo:</label> 
													<select name='veiculo[$x][agc_carro]' id='agc_carro' >
													<option value='".$result['agc_carro']."'>".$result['car_descricao']."</option>
													"; 
													$sql = "SELECT * FROM cadastro_carros ORDER BY car_descricao ASC";
													$stmt = $PDO->prepare($sql);
													$stmt->execute();
													while($result = $stmt->fetch())
													{
														echo "<option value='".$result['car_id']."'>".$result['car_descricao']."</option>";
													}
													echo "
												</select>
												<p>
												<img src='../imagens/icon-add.png' id='addVeiculo' title='Adicionar +' class='botao_dinamico'> <img src='../imagens/icon-rmv.png' id='remVeiculo' title='Remover' class='botao_dinamico'>
												<br>
											</div>
											";
										}
									}
									else
									{
										echo "
										<div class='bloco_veiculo'>
											<input type='hidden' name='veiculo[1][agc_id]' id='agc_id'>
											<br><label>Veículo:</label> 
												<select name='veiculo[1][agc_carro]' id='agc_carro'>
												<option value=''>Veículo</option>
												"; 
												$sql = "SELECT * FROM cadastro_carros ORDER BY car_descricao ASC";
												$stmt = $PDO->prepare($sql);
												$stmt->execute();
												while($result = $stmt->fetch())
												{
													echo "<option value='".$result['car_id']."'>".$result['car_descricao']."</option>";
												}
												echo "
											</select>
											<p>
											<img src='../imagens/icon-add.png' id='addVeiculo' title='Adicionar +' class='botao_dinamico'>
											<br>
										</div>
										";
									}
									echo "
									</div>
								</td>
							</tr>
						</table>
					</div>		
				</div>    
				<center>
				<div id='erro' align='center'>&nbsp;</div>
				<input type='button' id='bt_agenda_gerenciar_itens' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
				<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='agenda_gerenciar_itens.php?pagina=agenda_gerenciar_itens&age_id=$age_id".$autenticacao."'; value='Cancelar'/></center>
				</center>
			</form>
            ";
        }
    }	
	?>
    </div>
</div>
<?php
include('../mod_rodape/rodape.php');
?>
</body>
</html>
<?php
include("../mod_includes/js/jquery.eventCalendar.php");
?>

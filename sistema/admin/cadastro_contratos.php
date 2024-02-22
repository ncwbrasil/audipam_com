<?php
session_start (); 
$pagina_link = 'cadastro_contratos';
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
    $page = "Cadastros &raquo; <a href='cadastro_contratos.php?pagina=cadastro_contratos".$autenticacao."'>Contratos</a>";
	$con_id = $_GET['con_id'];
	$con_contratante = $_POST['con_contratante'];
	$con_contratado = $_POST['con_contratado'];
	$con_tipo_ajuste = $_POST['con_tipo_ajuste'];
	$con_numero_ajuste = $_POST['con_numero_ajuste'];
	$con_ano_ajuste = $_POST['con_ano_ajuste'];
	$con_servico = $_POST['con_servico'];
	$con_objeto = $_POST['con_objeto'];
	$con_resumo_objeto = $_POST['con_resumo_objeto'];
	$con_regime = $_POST['con_regime'];
	$con_modalidade = $_POST['con_modalidade'];
	$con_numero_modalidade = $_POST['con_numero_modalidade'];
	$con_ano_modalidade = $_POST['con_ano_modalidade'];
	$con_processo_adm = $_POST['con_processo_adm'];
	$con_numero_processo = $_POST['con_numero_processo'];
	$con_ano_processo = $_POST['con_ano_processo'];
	$con_data_assinatura = implode("-",array_reverse(explode("/",$_POST['con_data_assinatura'])));
	$con_inicio_vig = implode("-",array_reverse(explode("/",$_POST['con_inicio_vig'])));
	$con_final_vig = implode("-",array_reverse(explode("/",$_POST['con_final_vig'])));
	$con_total_meses = $_POST['con_total_meses'];
	$con_dia_emissao_nf = $_POST['con_dia_emissao_nf'];
	$con_periodicidade = $_POST['con_periodicidade'];
	$con_numero_parcelas = $_POST['con_numero_parcelas'];
	$con_previsao_prorrogacao = $_POST['con_previsao_prorrogacao'];
	$con_limite_legal = $_POST['con_limite_legal'];
	$con_data_limite_prorrog = implode("-",array_reverse(explode("/",$_POST['con_data_limite_prorrog'])));
	$con_valor_global_inicial = str_replace(",",".",str_replace(".","",$_POST['con_valor_global_inicial']));
	$con_valor_unitario_parcela = str_replace(",",".",str_replace(".","",$_POST['con_valor_unitario_parcela']));
	$con_valor_global_atual = str_replace(",",".",str_replace(".","",$_POST['con_valor_global_atual']));
	$con_valor_unitario_atual = str_replace(",",".",str_replace(".","",$_POST['con_valor_unitario_atual']));
	$con_implantacao = $_POST['con_implantacao'];
	$con_valor_implantacao = str_replace(",",".",str_replace(".","",$_POST['con_valor_implantacao']));
	$con_prazo_implantacao = $_POST['con_prazo_implantacao'];
	$con_data_implantacao = implode("-",array_reverse(explode("/",$_POST['con_data_implantacao'])));
	$con_garantia = $_POST['con_garantia'];
	$con_valor_garantia = str_replace(",",".",str_replace(".","",$_POST['con_valor_garantia']));
	$con_status = $_POST['con_status'];
	$con_previsao_reajuste = $_POST['con_previsao_reajuste'];
	$con_indice_reajuste = $_POST['con_indice_reajuste'];
	$con_forma_reajuste = $_POST['con_forma_reajuste'];
	$con_proximo_reajuste = implode("-",array_reverse(explode("/",$_POST['con_proximo_reajuste'])));
	$con_ultimo_reajuste = implode("-",array_reverse(explode("/",$_POST['con_ultimo_reajuste'])));
	$con_tipo_multa = $_POST['con_tipo_multa'];
	$con_valor_multa = str_replace(",",".",str_replace(".","",$_POST['con_valor_multa']));
	$con_memorial = $_POST['con_memorial'];
	$con_status = $_POST['con_status'];
	

	$dados = array_filter(array(
		'con_contratante' 			=> $con_contratante,
		'con_contratado' 			=> $con_contratado,
		'con_tipo_ajuste' 			=> $con_tipo_ajuste,
		'con_numero_ajuste' 		=> $con_numero_ajuste,
		'con_ano_ajuste' 			=> $con_ano_ajuste,
		'con_servico' 				=> $con_servico,
		'con_objeto' 				=> $con_objeto,
		'con_resumo_objeto' 		=> $con_resumo_objeto,
		'con_regime' 				=> $con_regime,
		'con_modalidade' 			=> $con_modalidade,
		'con_numero_modalidade' 	=> $con_numero_modalidade,
		'con_ano_modalidade' 		=> $con_ano_modalidade,
		'con_processo_adm' 			=> $con_processo_adm,
		'con_numero_processo' 		=> $con_numero_processo,
		'con_ano_processo' 			=> $con_ano_processo,
		'con_data_assinatura' 		=> $con_data_assinatura,
		'con_inicio_vig' 			=> $con_inicio_vig,
		'con_final_vig' 			=> $con_final_vig,
		'con_total_meses' 			=> $con_total_meses,
		'con_dia_emissao_nf' 		=> $con_dia_emissao_nf,
		'con_periodicidade' 		=> $con_periodicidade,
		'con_numero_parcelas' 		=> $con_numero_parcelas,
		'con_previsao_prorrogacao' 	=> $con_previsao_prorrogacao,
		'con_limite_legal' 			=> $con_limite_legal,
		'con_data_limite_prorrog' 	=> $con_data_limite_prorrog,
		'con_valor_global_inicial' 	=> $con_valor_global_inicial,
		'con_valor_unitario_parcela'=> $con_valor_unitario_parcela,
		'con_valor_global_atual' 	=> $con_valor_global_atual,
		'con_valor_unitario_atual' 	=> $con_valor_unitario_atual,
		'con_implantacao' 			=> $con_implantacao,
		'con_valor_implantacao' 	=> $con_valor_implantacao,
		'con_prazo_implantacao' 	=> $con_prazo_implantacao,
		'con_data_implantacao' 		=> $con_data_implantacao,
		'con_garantia' 				=> $con_garantia,
		'con_valor_garantia' 		=> $con_valor_garantia,
		'con_status' 				=> $con_status,
		'con_previsao_reajuste' 	=> $con_previsao_reajuste,
		'con_indice_reajuste' 		=> $con_indice_reajuste,
		'con_forma_reajuste' 		=> $con_forma_reajuste,
		'con_proximo_reajuste' 		=> $con_proximo_reajuste,
		'con_ultimo_reajuste' 		=> $con_ultimo_reajuste,
		'con_tipo_multa' 			=> $con_tipo_multa,
		'con_valor_multa' 			=> $con_valor_multa,
		'con_memorial'				=> $con_memorial
	),'strlen');
	if($action == "adicionar")
    {
        $sql = "INSERT INTO cadastro_contratos SET ".bindFields($dados);
		$stmt = $PDO->prepare($sql);	
        if($stmt->execute($dados))
        {		
		
			$con_id = $PDO->lastInsertId();
			
			//UPLOAD ARQUIVOS
			$caminho = "../admin/contrato_arquivos/$con_id/";
			foreach($_FILES as $key => $files)
			{
				$files_test = array_filter($files['name']);
				if(!empty($files_test))
				{
					if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
					if(!empty($files["name"]["edital"]))
					{
						$nomeArquivo 	= $files["name"]["edital"];
						$nomeTemporario = $files["tmp_name"]["edital"];
						$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
						$con_edital	= $caminho;
						$con_edital .= "edital_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($con_edital));
					}
					if(!empty($files["name"]["proposta"]))
					{
						$nomeArquivo = $files["name"]["proposta"];
						$nomeTemporario = $files["tmp_name"]["proposta"];
						$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
						$con_proposta = $caminho;
						$con_proposta .= "proposta_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($con_proposta));
					}
					if(!empty($files["name"]["contrato"]))
					{
						$nomeArquivo	= $files["name"]["contrato"];
						$nomeTemporario = $files["tmp_name"]["contrato"];
						$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
						$con_contrato = $caminho;
						$con_contrato .= "contrato_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($con_contrato));
					}
					if(!empty($files["name"]["outro"]))
					{
						$nomeArquivo 	= $files["name"]["outro"];
						$nomeTemporario = $files["tmp_name"]["outro"];
						$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
						$con_outro = $caminho;
						$con_outro .= "outro_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($con_outro));
					}
				}
			}
				
			$sql = "UPDATE cadastro_contratos SET 
					con_edital 	 = :con_edital,
					con_proposta = :con_proposta,
					con_contrato = :con_contrato,
					con_outro    = :con_outro
					WHERE con_id = :con_id ";
			$stmt = $PDO->prepare($sql);
			$stmt->bindParam(':con_edital',$con_edital);
			$stmt->bindParam(':con_proposta',$con_proposta);
			$stmt->bindParam(':con_contrato',$con_contrato);
			$stmt->bindParam(':con_outro',$con_outro);
			$stmt->bindParam(':con_id',$con_id);
			if($stmt->execute())
			{							 
			}
			else
			{
				$erro=1;
			}
			//
			
			//GESTOR - CAMPOS DINÂMICOS
			if(!empty($_POST['gestor']) && is_array($_POST['gestor']))
			{
				//LIMPA ARRAY
				foreach($_POST['gestor'] as $item => $valor) 
				{
					$gestor_filtrado[$item] = array_filter($valor);
				}
				//
				foreach($gestor_filtrado as $item => $valor) 
				{
					if(!empty($valor))
					{
						//INVERTE DATA
						if(isset($valor['ges_data_inicio']))
						{
							$data_nova = implode("-",array_reverse(explode("/",$valor['ges_data_inicio'])));
							unset($valor['ges_data_inicio']);
							$valor['ges_data_inicio'] = $data_nova;
						}
						if(isset($valor['ges_data_fim']))
						{
							$data_nova = implode("-",array_reverse(explode("/",$valor['ges_data_fim'])));
							unset($valor['ges_data_fim']);
							$valor['ges_data_fim'] = $data_nova;
						}
						//
						
						$valor['ges_contrato'] = $con_id;
						$sql = "INSERT INTO cadastro_contratos_gestor SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//INSERE
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
				}
			}
			
			//ITENS DO CONTRATO - CAMPOS DINÂMICOS			
			if(!empty($_POST['item']) && is_array($_POST['item']))
			{
				//LIMPA ARRAY
				foreach($_POST['item'] as $item => $valor) 
				{
					$item_filtrado[$item] = array_filter($valor);
				}
				//
				foreach($item_filtrado as $item => $valor) 
				{		
					if(!empty($valor))
					{	
					
						//INVERTE MOEDA
						if(isset($valor['ite_valor_unitario']))
						{
							$valor_novo = str_replace(",",".",str_replace(".","",$valor['ite_valor_unitario']));
							unset($valor['ite_valor_unitario']);
							$valor['ite_valor_unitario'] = $valor_novo;
						}
						if(isset($valor['ite_valor_total']))
						{
							$valor_novo = str_replace(",",".",str_replace(".","",$valor['ite_valor_total']));
							unset($valor['ite_valor_total']);
							$valor['ite_valor_total'] = $valor_novo;
						}
						//			
						$valor['ite_contrato'] = $con_id;
						$sql = "INSERT INTO cadastro_contratos_itens SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//INSERE
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
				}
			}
			
			//FORMA DE ATENDIMENTO - CAMPOS DINÂMICOS
			if(!empty($_POST['forma_atendimento']) && is_array($_POST['forma_atendimento']))
			{
				//LIMPA ARRAY
				foreach($_POST['forma_atendimento'] as $item => $valor) 
				{
					$forma_atendimento_filtrado[$item] = array_filter($valor);
				}
				//
				foreach($forma_atendimento_filtrado as $item => $valor) 
				{
					if(!empty($valor))
					{
						$valor['cfa_contrato'] = $con_id;
						$sql = "INSERT INTO cadastro_contratos_fa SET ".bindFields($valor);
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
    
    if($action == 'editar')
    {
        $sql = "UPDATE cadastro_contratos SET ".bindFields($dados)." WHERE con_id = :con_id ";
		$stmt = $PDO->prepare($sql); 
		$dados['con_id'] =  $con_id;
		if($stmt->execute($dados))
        {
			
			//UPLOAD ARQUIVOS
			$caminho = "../admin/contrato_arquivos/$con_id/";
			foreach($_FILES as $key => $files)
			{
				$files_test = array_filter($files['name']);
				if(!empty($files_test))
				{
					if(!file_exists($caminho)){mkdir($caminho, 0755, true);}
					if(!empty($files["name"]["edital"]))
					{
						$nomeArquivo 	= $files["name"]["edital"];
						$nomeTemporario = $files["tmp_name"]["edital"];
						$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
						$con_edital	= $caminho;
						$con_edital .= "edital_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($con_edital));
						$sql = "UPDATE cadastro_contratos SET 
								con_edital 	 = :con_edital
								WHERE con_id = :con_id ";
						$stmt = $PDO->prepare($sql);
						$stmt->bindParam(':con_edital',$con_edital);
						$stmt->bindParam(':con_id',$con_id);
						if($stmt->execute()){}else{$erro=1;}
					}
					if(!empty($files["name"]["proposta"]))
					{
						$nomeArquivo = $files["name"]["proposta"];
						$nomeTemporario = $files["tmp_name"]["proposta"];
						$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
						$con_proposta = $caminho;
						$con_proposta .= "proposta_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($con_proposta));
						$sql = "UPDATE cadastro_contratos SET 
								con_proposta = :con_proposta
								WHERE con_id = :con_id ";
						$stmt = $PDO->prepare($sql);
						$stmt->bindParam(':con_proposta',$con_proposta);
						$stmt->bindParam(':con_id',$con_id);
						if($stmt->execute()){}else{$erro=1;}
					}
					if(!empty($files["name"]["contrato"]))
					{
						$nomeArquivo	= $files["name"]["contrato"];
						$nomeTemporario = $files["tmp_name"]["contrato"];
						$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
						$con_contrato = $caminho;
						$con_contrato .= "contrato_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($con_contrato));
						$sql = "UPDATE cadastro_contratos SET 
								con_contrato = :con_contrato
								WHERE con_id = :con_id ";
						$stmt = $PDO->prepare($sql);
						$stmt->bindParam(':con_contrato',$con_contrato);
						$stmt->bindParam(':con_id',$con_id);
						if($stmt->execute()){}else{$erro=1;}
					}
					if(!empty($files["name"]["outro"]))
					{
						$nomeArquivo 	= $files["name"]["outro"];
						$nomeTemporario = $files["tmp_name"]["outro"];
						$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
						$con_outro = $caminho;
						$con_outro .= "outro_".md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;					
						move_uploaded_file($nomeTemporario, ($con_outro));
						$sql = "UPDATE cadastro_contratos SET 
								con_outro    = :con_outro
								WHERE con_id = :con_id ";
						$stmt = $PDO->prepare($sql);
						$stmt->bindParam(':con_outro',$con_outro);
						$stmt->bindParam(':con_id',$con_id);
						if($stmt->execute()){}else{$erro=1;}
					}
				}
			}
			//
			
			## CAMPOS DINÂMICOS ##
			
			// GESTOR - EXCLUI OS REMOVIDOS
			if(!empty($_POST['gestor']) && is_array($_POST['gestor']))
			{
				//LIMPA ARRAY
				foreach($_POST['gestor'] as $item => $valor) 
				{
					$gestor_filtrado[$item] = array_filter($valor);
				}
				//
				$a_excluir = array();
				foreach($gestor_filtrado as $item) 
				{
					if(isset($item['ges_id']))
					{
						$a_excluir[] = $item['ges_id'];
					}
				}
				if(!empty($a_excluir))
				{
					$sql = "DELETE FROM cadastro_contratos_gestor WHERE ges_contrato = :con_id AND ges_id NOT IN (".implode(",",$a_excluir).") ";
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':con_id', $con_id);
					if($stmt->execute())
					{
						//echo "Excluido <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
				else
				{
					$sql = "DELETE FROM cadastro_contratos_gestor WHERE ges_contrato = :con_id ";
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':con_id', $con_id);
					if($stmt->execute())
					{
						//echo "Excluido todos <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
			}
			else
			{
				$sql = "DELETE FROM cadastro_contratos_gestor WHERE ges_contrato = :con_id ";
				$stmt = $PDO->prepare($sql); 
				$stmt->bindParam(':con_id', $con_id);
				if($stmt->execute())
				{
					//echo "Excluido todos <br>";
				}
				else{ $erro=1; $err = $stmt->errorInfo();}
			}
			// GESTOR - ATUALIZA OU INSERE NOVOS
			if(!empty($_POST['gestor']) && is_array($_POST['gestor']))
			{
				//LIMPA ARRAY
				foreach($_POST['gestor'] as $item => $valor) 
				{
					$gestor_filtrado[$item] = array_filter($valor);
				}
				//
				foreach(array_filter($gestor_filtrado) as $item => $valor) 
				{		
							
					if(isset($valor['ges_id']))
					{
						//INVERTE DATA
						if(isset($valor['ges_data_inicio']))
						{
							$data_nova = implode("-",array_reverse(explode("/",$valor['ges_data_inicio'])));
							unset($valor['ges_data_inicio']);
							$valor['ges_data_inicio'] = $data_nova;
						}
						if(isset($valor['ges_data_fim']))
						{
							$data_nova = implode("-",array_reverse(explode("/",$valor['ges_data_fim'])));
							unset($valor['ges_data_fim']);
							$valor['ges_data_fim'] = $data_nova;
						}
						//
						
						$valor2 = $valor;
						unset($valor2['ges_id']);
						
						$sql = "UPDATE cadastro_contratos_gestor SET ".bindFields($valor2)." WHERE ges_id = :ges_id";
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//echo "Atualizado <br>";
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
					else
					{
						
						//INVERTE DATA
						if(isset($valor['ges_data_inicio']))
						{
							$data_nova = implode("-",array_reverse(explode("/",$valor['ges_data_inicio'])));
							unset($valor['ges_data_inicio']);
							$valor['ges_data_inicio'] = $data_nova;
						}
						if(isset($valor['ges_data_fim']))
						{
							$data_nova = implode("-",array_reverse(explode("/",$valor['ges_data_fim'])));
							unset($valor['ges_data_fim']);
							$valor['ges_data_fim'] = $data_nova;
						}
						//
						
						$valor['ges_contrato'] = $con_id;
						$sql = "INSERT INTO cadastro_contratos_gestor SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//echo "Inserido <br>";
						}
						else{ $erro=1; $err = $stmt->errorInfo();}						
					}
				}
			}
			
			// ITENS DO CONTRATO - EXCLUI OS REMOVIDOS
			if(!empty($_POST['item']) && is_array($_POST['item']))
			{
				//LIMPA ARRAY
				foreach($_POST['item'] as $item => $valor) 
				{
					$item_filtrado[$item] = array_filter($valor);
				}
				//
				
				$a_excluir = array();
				foreach($item_filtrado as $item) 
				{
					if(isset($item['ite_id']))
					{
						$a_excluir[] = $item['ite_id'];
					}
				}
				if(!empty($a_excluir))
				{
					$sql = "DELETE FROM cadastro_contratos_itens WHERE ite_contrato = :con_id AND ite_id NOT IN (".implode(",",$a_excluir).") ";
					
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':con_id', $con_id);
					if($stmt->execute())
					{
						//echo "Excluido <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
				else
				{
					$sql = "DELETE FROM cadastro_contratos_itens WHERE ite_contrato = :con_id ";
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':con_id', $con_id);
					if($stmt->execute())
					{
						//echo "Excluido todos <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
			}
			else
			{
				$sql = "DELETE FROM cadastro_contratos_itens WHERE ite_contrato = :con_id ";
				$stmt = $PDO->prepare($sql); 
				$stmt->bindParam(':con_id', $con_id);
				if($stmt->execute())
				{
					//echo "Excluido todos <br>";
				}
				else{ $erro=1; $err = $stmt->errorInfo();}
			}
			
			// ITENS DO CONTRATO - ATUALIZA OU INSERE NOVOS
			if(!empty($_POST['item']) && is_array($_POST['item']))
			{
				//LIMPA ARRAY
				foreach($_POST['item'] as $item => $valor) 
				{
					$item_filtrado[$item] = array_filter($valor);
				}
				//
				foreach(array_filter($item_filtrado) as $item => $valor) 
				{
					if(isset($valor['ite_id']))
					{
						//INVERTE MOEDA
						if(isset($valor['ite_valor_unitario']))
						{
							$valor_novo = str_replace(",",".",str_replace(".","",$valor['ite_valor_unitario']));
							unset($valor['ite_valor_unitario']);
							$valor['ite_valor_unitario'] = $valor_novo;
						}
						if(isset($valor['ite_valor_total']))
						{
							$valor_novo = str_replace(",",".",str_replace(".","",$valor['ite_valor_total']));
							unset($valor['ite_valor_total']);
							$valor['ite_valor_total'] = $valor_novo;
						}
						//
						
						$valor2 = $valor;
						unset($valor2['ite_id']);
						
						$sql = "UPDATE cadastro_contratos_itens SET ".bindFields($valor2)." WHERE ite_id = :ite_id";
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//echo "Atualizado <br>";
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
					else
					{
						//INVERTE MOEDA
						if(isset($valor['ite_valor_unitario']))
						{
							$valor_novo = str_replace(",",".",str_replace(".","",$valor['ite_valor_unitario']));
							unset($valor['ite_valor_unitario']);
							$valor['ite_valor_unitario'] = $valor_novo;
						}
						if(isset($valor['ite_valor_total']))
						{
							$valor_novo = str_replace(",",".",str_replace(".","",$valor['ite_valor_total']));
							unset($valor['ite_valor_total']);
							$valor['ite_valor_total'] = $valor_novo;
						}
						//
						
						$valor['ite_contrato'] = $con_id;
						$sql = "INSERT INTO cadastro_contratos_itens SET ".bindFields($valor);
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							//echo "Inserido <br>";
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
				}
			}
			
			// FORMA ATENDIMENTO - EXCLUI OS REMOVIDOS
			if(!empty($_POST['forma_atendimento']) && is_array($_POST['forma_atendimento']))
			{
				//LIMPA ARRAY
				foreach($_POST['forma_atendimento'] as $item => $valor) 
				{
					$forma_atendimento_filtrado[$item] = array_filter($valor);
				}
				//
				$a_excluir = array();
				foreach($forma_atendimento_filtrado as $item) 
				{
					if(isset($item['cfa_id']))
					{
						$a_excluir[] = $item['cfa_id'];
					}
				}
				if(!empty($a_excluir))
				{
					$sql = "DELETE FROM cadastro_contratos_fa WHERE cfa_contrato = :con_id AND cfa_id NOT IN (".implode(",",$a_excluir).") ";
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':con_id', $con_id);
					if($stmt->execute())
					{
						//echo "Excluido <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
				else
				{
					$sql = "DELETE FROM cadastro_contratos_fa WHERE cfa_contrato = :con_id ";
					$stmt = $PDO->prepare($sql); 
					$stmt->bindParam(':con_id', $con_id);
					if($stmt->execute())
					{
						//echo "Excluido todos <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
			}
			else
			{
				$sql = "DELETE FROM cadastro_contratos_fa WHERE cfa_contrato = :con_id ";
				$stmt = $PDO->prepare($sql); 
				$stmt->bindParam(':con_id', $con_id);
				if($stmt->execute())
				{
					//echo "Excluido todos <br>";
				}
				else{ $erro=1; $err = $stmt->errorInfo();}
			}
			// FORMA ATENDIMENTO - ATUALIZA OU INSERE NOVOS
			if(!empty($_POST['forma_atendimento']) && is_array($_POST['forma_atendimento']))
			{
				//LIMPA ARRAY
				foreach($_POST['forma_atendimento'] as $item => $valor) 
				{
					$forma_atendimento_filtrado[$item] = array_filter($valor);
				}
				//
				foreach(array_filter($forma_atendimento_filtrado) as $item => $valor) 
				{		
							
					if(isset($valor['cfa_id']))
					{
						$valor2 = $valor;
						unset($valor2['cfa_id']);
						$sql = "UPDATE cadastro_contratos_fa SET ".bindFields($valor2)." WHERE cfa_id = :cfa_id";
						$stmt = $PDO->prepare($sql);	
						if($stmt->execute($valor))
						{
							echo "Atualizado <br>";
						}
						else{ $erro=1; $err = $stmt->errorInfo();}
					}
					else
					{
						$valor['cfa_contrato'] = $con_id;
						$sql = "INSERT INTO cadastro_contratos_fa SET ".bindFields($valor);
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
       	$sql = "DELETE FROM cadastro_contratos WHERE con_id = :con_id";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':con_id',$con_id);
        if($stmt->execute())
        {
            echo "
            <SCRIPT language='JavaScript'>
                abreMask(
                '<img src=../imagens/ok.png> Exclusão realizada com sucesso<br><br>'+
                '<input value=\' OK \' type=\'button\' class=\'close_janela\'>' );
            </SCRIPT>
                ";
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
	$fil_nome = $_REQUEST['fil_nome'];
	if($fil_nome == '')
	{
		$nome_query = " 1 = 1 ";
	}
	else
	{
		$fil_nome1 = $fil_nome2 = "%".$fil_nome."%";
		$nome_query = " (a1.emp_nome_razao LIKE :fil_nome1 OR a1.emp_fantasia LIKE :fil_nome2 ) ";
	}
    $sql = "SELECT *, a1.emp_nome_razao, a1.emp_fantasia,a1.emp_logo, a2.emp_logo as logo_contratado FROM cadastro_contratos 
			LEFT JOIN cadastro_empresas a1 ON a1.emp_id = cadastro_contratos.con_contratante
			LEFT JOIN cadastro_empresas a2 ON a2.emp_id = cadastro_contratos.con_contratado
			LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
			LEFT JOIN aux_servicos ON aux_servicos.ser_id = cadastro_contratos.con_servico
			WHERE ".$nome_query."
			ORDER BY con_id DESC
            LIMIT :primeiro_registro, :num_por_pagina ";
  	$stmt = $PDO->prepare($sql);
	$stmt->bindParam(':fil_nome1', 	$fil_nome1);
	$stmt->bindParam(':fil_nome2', 	$fil_nome2);
	$stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
	$stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
	$stmt->execute();
    $rows = $stmt->rowCount();
    if($pagina == "cadastro_contratos")
    {
        echo "
		<div class='titulo'> $page  </div>
		<div id='botoes'><input value='Novo Contrato' type='button' onclick=javascript:window.location.href='cadastro_contratos.php?pagina=cadastro_contratos_adicionar".$autenticacao."'; /></div>
		<div class='filtro'>
			<form name='form_filtro' id='form_filtro' enctype='multipart/form-data' method='post' action='cadastro_contratos.php?pagina=cadastro_contratos".$autenticacao."'>
			<input name='fil_nome' id='fil_nome' value='$fil_nome' placeholder='Nome'>
			<input type='submit' value='Filtrar'> 
			</form>
		</div>
		";
		if ($rows > 0)
		{
			echo "
			<table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
				<tr>
					<td class='titulo_first'>Contratante</td>
					<td class='titulo_tabela'>Contratado</td>
					<td class='titulo_tabela'>Contrato</td>
					<td class='titulo_tabela'>Modalidade</td>
					<td class='titulo_tabela'>N°/Ano</td>
					<td class='titulo_tabela'>Encerramento</td>
					<td class='titulo_tabela'>Serviço</td>
					<td class='titulo_tabela' align='center'>Status</td>
					<td class='titulo_last' align='center'>Gerenciar</td>
				</tr>";
				$c=0;
				 while($result = $stmt->fetch())
				{
					$con_id 				= $result['con_id'];
					$emp_nome_razao 		= $result['emp_nome_razao'];
					$emp_fantasia 			= $result['emp_fantasia'];
					$con_status 			= $result['con_status'];
					$con_numero_processo 		= $result['con_numero_processo'];
					$con_ano_processo			= $result['con_ano_processo'];
					$con_numero_modalidade 	= $result['con_numero_modalidade'];
					$con_ano_modalidade 	= $result['con_ano_modalidade'];
					$mod_descricao	 		= $result['mod_descricao'];
					$con_modalidade 		= $result['con_modalidade'];
					$con_final_vig 			= implode("/",array_reverse(explode("-",$result['con_final_vig'])));
					$ser_descricao	 		= $result['ser_descricao'];
					$logo_contratado 			= $result['logo_contratado'];
					if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;}  
					echo "
					<script type='text/javascript'>
						jQuery(document).ready(function($) {
					
							// Define any icon actions before calling the toolbar
							$('.toolbar-icons a').on('click', function( event ) {
								$(this).click();
								
							});
							$('#normal-button-$con_id').toolbar({content: '#user-options-$con_id', position: 'top', hideOnClick: true});
							$('#normal-button-bottom').toolbar({content: '#user-options', position: 'bottom'});
							$('#normal-button-small').toolbar({content: '#user-options-small', position: 'top', hideOnClick: true});
							$('#button-left').toolbar({content: '#user-options', position: 'left'});
							$('#button-right').toolbar({content: '#user-options', position: 'right'});
							$('#link-toolbar').toolbar({content: '#user-options', position: 'top' });
						});
					</script>
					<div id='user-options-$con_id' class='toolbar-icons' style='display: none;'>
						<a title='Editar' href='cadastro_contratos.php?pagina=cadastro_contratos_editar&con_id=$con_id$autenticacao'><img border='0' src='../imagens/icon-editar.png' ></a>
						<a title='Excluir' onclick=\"
							abreMask(
								'Deseja realmente excluir este contrato?<br><br>'+
								'<input value=\' Sim \' type=\'button\' onclick=javascript:window.location.href=\'cadastro_contratos.php?pagina=cadastro_contratos&action=excluir&con_id=$con_id$autenticacao\';>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
								'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
							\">
							<img border='0' src='../imagens/icon-excluir.png' ></i>
						</a>
					</div>
					";
					echo "<tr class='$c1'>
								<td>$emp_fantasia<br><span class='detalhe'>$emp_nome_razao</span></td>
								<td><div style='background:url($logo_contratado); height: 50px; width:50px; background-size:100%; object-fit:cover;  border-radius:100px;'></div></td>
								<td>$con_numero_processo/$con_ano_processo</td>
								<td>$mod_descricao</td>
								<td>$con_numero_modalidade/$con_ano_modalidade</td>
								<td>$con_final_vig</td>
								<td>$ser_descricao</td>
								<td align='center'>$con_status</td>
							  <td align=center><div id='normal-button-$con_id' class='settings-button'><img src='../imagens/icon-cog-small.png' /></div></td>
						  </tr>";
				}
				echo "</table>";
				$variavel = "&pagina=cadastro_contratos&fil_nome=$fil_nome".$autenticacao."";
				$cnt = "SELECT COUNT(*) FROM cadastro_contratos 
						LEFT JOIN cadastro_empresas a1 ON a1.emp_id = cadastro_contratos.con_contratante
						LEFT JOIN cadastro_empresas a2 ON a2.emp_id = cadastro_contratos.con_contratado
						LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
						LEFT JOIN aux_servicos ON aux_servicos.ser_id = cadastro_contratos.con_servico
						WHERE ".$nome_query." ";
				$stmt = $PDO->prepare($cnt);
				$stmt->bindParam(':fil_nome1', 	$fil_nome1);
				$stmt->bindParam(':fil_nome2', 	$fil_nome2);
				include("../mod_includes/php/paginacao.php");
		}
		else
		{
			echo "<br><br><br>Não há nenhum contrato cadastrado.";
		}
    }
    if($pagina == 'cadastro_contratos_adicionar')
    {
        echo "	
		<form name='form_cadastro_contratos' id='form_cadastro_contratos' enctype='multipart/form-data' method='post' action='cadastro_contratos.php?pagina=cadastro_contratos&action=adicionar&id=$id$autenticacao'>
            <div class='titulo'> $page &raquo; Adicionar  </div>
			<ul class='nav nav-tabs'>
			  <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
			  <li><a data-toggle='tab' href='#gestor'>Gestor do Contrato</a></li>
			  <li><a data-toggle='tab' href='#itens'>Itens do Contrato</a></li>
			  <li><a data-toggle='tab' href='#formas_atendimento'>Formas de Atendimento</a></li>
			  <li><a data-toggle='tab' href='#faturamento'>Faturamento</a></li>
			  <li><a data-toggle='tab' href='#aditivos'>Aditivos</a></li>
			  <li><a data-toggle='tab' href='#representacao'>Representação</a></li>
			  <li><a data-toggle='tab' href='#encargos'>Encargos</a></li>
			  <li><a data-toggle='tab' href='#parametrizacao'>Parametrização</a></li>
			  <li><a data-toggle='tab' href='#bloco_notas'>Bloco de Notas</a></li>
			  <li><a data-toggle='tab' href='#memorial'>Memorial / T.R.</a></li>
			</ul>
			
			<div class='tab-content'>
				<div id='dados_gerais' class='tab-pane fade in active'>
					<table align='center' cellspacing='0' width='100%' class='borda_aba'>
						<tr>
							<td align='left'>
								<br><label>Contratante:</label> 
									<div class='suggestion' style='width:80%;'>
										<input name='con_contratante' id='con_contratante' placeholder='ID' autocomplete='off'  type='hidden' />
										<input style='width:100%;' name='con_contratante_nome' id='con_contratante_nome' type='text' placeholder='Contratante: Digite o nome ou CNPJ/CPF' autocomplete='off' />
										<div class='suggestionsBox' id='suggestions' style='display: none;'>
											<div class='suggestionList' id='autoSuggestionsList'>
												&nbsp;
											</div>
										</div>
									</div>
									<br><br>
								<p><label>Contratado:</label> 
									<div class='suggestion2' style='width:80%;'>
										<input name='con_contratado' id='con_contratado' placeholder='ID' autocomplete='off'  type='hidden' />
										<input style='width:100%;'  name='con_contratado_nome' id='con_contratado_nome' type='text' placeholder='Contratado: Digite o nome ou CNPJ/CPF' autocomplete='off' />
										<div class='suggestionsBox' id='suggestions2' style='display: none;'>
											<div class='suggestionList2' id='autoSuggestionsList2'>
												&nbsp;
											</div>
										</div>
									</div>
									<br><br>
								<p><label>Tipo de Ajuste:</label> <select name='con_tipo_ajuste' id='con_tipo_ajuste'>
									<option value=''>Tipo de Ajuste</option>
									<option value='Contrato'>Contrato</option>
									<option value='Ata Registro'>Ata Registro</option>
									<option value='AF Dispensa'>AF Dispensa</option>
								</select>
								<p><label>Número Ajuste:</label> <input name='con_numero_ajuste' id='con_numero_ajuste' placeholder='Número Ajuste'  maxlength='5' onkeypress='return SomenteNumero(event);'>
								<p><label>Ano Ajuste:</label> <input name='con_ano_ajuste' id='con_ano_ajuste' placeholder='Ano Ajuste'  maxlength='4' onkeypress='return SomenteNumero(event);'>
								<p><label>Serviço:</label> <select name='con_servico' id='con_servico'>
									<option value=''>Serviço</option>
									"; 
									$sql = " SELECT * FROM aux_servicos WHERE ser_status = :ser_status ORDER BY ser_descricao ";
									$stmt = $PDO->prepare($sql);
									$stmt->bindValue(':ser_status',1);
									$stmt->execute();
									while($result = $stmt->fetch())
									{
										echo "<option value='".$result['ser_id']."'>".$result['ser_descricao']."</option>";
									}
									echo "
								</select>
								<p><label>Objeto:</label> <textarea name='con_objeto' id='con_objeto' placeholder='Objeto'></textarea>
								<p><label>Resumo do Objeto:</label> <input name='con_resumo_objeto' id='con_resumo_objeto' placeholder='Resumo do Objeto'>
								<p><label>Regime de Execução:</label> <select name='con_regime' id='con_regime'>
									<option value=''>Regime de Execução</option>
									<option value='Empreitada por preço global'>Empreitada por preço global</option>
									<option value='Empreitada por preço unitário'>Empreitada por preço unitário</option>
									<option value='Tarefa'>Tarefa</option>
									<option value='Empreitada integral'>Empreitada integral</option>
								</select>
								<p><label>Modalidade:</label> <select name='con_modalidade' id='con_modalidade'>
									<option value=''>Modalidade</option>";
									$sql = " SELECT * FROM aux_modalidades ORDER BY mod_descricao";
									$stmt = $PDO->prepare($sql);
									$stmt->execute();
									while($result = $stmt->fetch())
									{
										echo "<option value='".$result['mod_id']."'>".$result['mod_descricao']."</option>";
									}
									echo "
								</select>
								<p><label>Número Modalidade:</label> <input name='con_numero_modalidade' id='con_numero_modalidade' placeholder='Número Modalidade'  maxlength='5' onkeypress='return SomenteNumero(event);'>
								<p><label>Ano Modalidade:</label> <input name='con_ano_modalidade' id='con_ano_modalidade' placeholder='Ano Modalidade'  maxlength='4' onkeypress='return SomenteNumero(event);'>
								<p><label>Processo Administrativo:</label> <input name='con_processo_adm' id='con_processo_adm' placeholder='Processo Administrativo'>
								<p><label>Número Processo:</label> <input name='con_numero_processo' id='con_numero_processo' placeholder='Número Processo'  maxlength='5' onkeypress='return SomenteNumero(event);'>
								<p><label>Ano Processo:</label> <input name='con_ano_processo' id='con_ano_processo' placeholder='Ano Processo'  maxlength='4' onkeypress='return SomenteNumero(event);'>
								<p><label>Data Assinatura:</label> <input name='con_data_assinatura' id='con_data_assinatura' placeholder='Data Assinatura' onkeypress='return mascaraData(this,event);'>
								<p><label>Ínicio Vigência:</label> <input name='con_inicio_vig' id='con_inicio_vig' placeholder='Inicío Vigência' onkeypress='return mascaraData(this,event);'>
								<p><label>Fim Vigência:</label> <input name='con_final_vig' id='con_final_vig' placeholder='Fim Vigência' onkeypress='return mascaraData(this,event);'>
								<p><label>Total Meses:</label> <input name='con_total_meses' id='con_total_meses' placeholder='Total Meses' onkeypress='return SomenteNumero(event);'>
								<p><label>Dia Emissão NF:</label> <select name='con_dia_emissao_nf' id='con_dia_emissao_nf'>
									<option value=''>Dia Emissão NF</option>
									<option value='01'>01</option>
									<option value='02'>02</option>
									<option value='03'>03</option>
									<option value='04'>04</option>
									<option value='05'>05</option>
									<option value='06'>06</option>
									<option value='07'>07</option>
									<option value='08'>08</option>
									<option value='09'>09</option>
									<option value='10'>10</option>
									<option value='11'>11</option>
									<option value='12'>12</option>
									<option value='13'>13</option>
									<option value='14'>14</option>
									<option value='15'>15</option>
									<option value='16'>16</option>
									<option value='17'>17</option>
									<option value='18'>18</option>
									<option value='19'>19</option>
									<option value='20'>20</option>
									<option value='21'>21</option>
									<option value='22'>22</option>
									<option value='23'>23</option>
									<option value='24'>24</option>
									<option value='25'>25</option>
									<option value='26'>26</option>
									<option value='27'>27</option>
									<option value='28'>28</option>
									<option value='29'>29</option>
									<option value='30'>30</option>
									<option value='31'>31</option>
								</select>
								<p><label>Peridiocidade:</label> <select name='con_periodicidade' id='con_periodicidade'>
									<option value=''>Peridiocidade</option>
									<option value='Mensal'>Mensal</option>
									<option value='Anual'>Anual</option>
									<option value='Trimestral'>Trimestral</option>
									<option value='Diária'>Diária</option>
									<option value='Quadrimestral'>Quadrimestral</option>
									<option value='Semestral'>Semestral</option>
									<option value='Prazo pré determinado'>Prazo pré determinado</option>
								</select>						
								<p><label>Nº Parcelas:</label> <input name='con_numero_parcelas' id='con_numero_parcelas' placeholder='Nº Parcelas' onkeypress='return SomenteNumero(event);'>
								<p><label>Previsão Prorrogação:</label> <input name='con_previsao_prorrogacao' id='con_previsao_prorrogacao' placeholder='Previsão de Prorrogação' maxlength='1'>
								<p><label>Limite Legal:</label> <select name='con_limite_legal' id='con_limite_legal'>
									<option value=''>Limite Legal</option>
									<option value='01'>01</option>
									<option value='02'>02</option>
									<option value='03'>03</option>
									<option value='04'>04</option>
									<option value='05'>05</option>
									<option value='06'>06</option>
									<option value='07'>07</option>
									<option value='08'>08</option>
									<option value='09'>09</option>
									<option value='10'>10</option>
									<option value='11'>11</option>
									<option value='12'>12</option>
									<option value='13'>13</option>
									<option value='14'>14</option>
									<option value='15'>15</option>
									<option value='16'>16</option>
									<option value='17'>17</option>
									<option value='18'>18</option>
									<option value='19'>19</option>
									<option value='20'>20</option>
									<option value='21'>21</option>
									<option value='22'>22</option>
									<option value='23'>23</option>
									<option value='24'>24</option>
									<option value='25'>25</option>
									<option value='26'>26</option>
									<option value='27'>27</option>
									<option value='28'>28</option>
									<option value='29'>29</option>
									<option value='30'>30</option>
									<option value='31'>31</option>
									<option value='32'>32</option>
									<option value='33'>33</option>
									<option value='34'>34</option>
									<option value='35'>35</option>
									<option value='36'>36</option>
									<option value='37'>37</option>
									<option value='38'>38</option>
									<option value='39'>39</option>
									<option value='40'>40</option>
									<option value='41'>41</option>
									<option value='42'>42</option>
									<option value='43'>43</option>
									<option value='44'>44</option>
									<option value='45'>45</option>
									<option value='46'>46</option>
									<option value='47'>47</option>
									<option value='48'>48</option>
									<option value='49'>49</option>
									<option value='50'>50</option>
									<option value='51'>51</option>
									<option value='52'>52</option>
									<option value='53'>53</option>
									<option value='54'>54</option>
									<option value='55'>55</option>
									<option value='56'>56</option>
									<option value='57'>57</option>
									<option value='58'>58</option>
									<option value='59'>59</option>
									<option value='60'>60</option>
								</select>
								<p><label>Data Limite Prorrogação:</label> <input name='con_data_limite_prorrog' id='con_data_limite_prorrog' placeholder='Data Limite Prorrog' onkeypress='return mascaraData(this,event);'>
								<p><label>Valor Global Inicial:</label> <input name='con_valor_global_inicial' id='con_valor_global_inicial' placeholder='Valor Global Inicial' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
								<p><label>Valor Unitário (parcela):</label> <input name='con_valor_unitario_parcela' id='con_valor_unitario_parcela' placeholder='Valor Unitário (parcela)' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
								<p><label>Valor Global Atual:</label> <input name='con_valor_global_atual' id='con_valor_global_atual' placeholder='Valor Global Atual' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
								<p><label>Valor Unitário Atual:</label> <input name='con_valor_unitario_atual' id='con_valor_unitario_atual' placeholder='Valor Unitário Atual' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
								<p><label>Implantação:</label> <input name='con_implantacao' id='con_implantacao' placeholder='Implantação' maxlength='1'>
								<p><label>Valor Implantação:</label> <input name='con_valor_implantacao' id='con_valor_implantacao' placeholder='Valor Implantação' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
								<p><label>Prazo Implantação:</label> <select name='con_prazo_implantacao' id='con_prazo_implantacao'>
									<option value=''>Prazo Implantação</option>
									<option value='01'>01</option>
									<option value='02'>02</option>
									<option value='03'>03</option>
									<option value='04'>04</option>
									<option value='05'>05</option>
									<option value='06'>06</option>
									<option value='07'>07</option>
									<option value='08'>08</option>
									<option value='09'>09</option>
									<option value='10'>10</option>
									<option value='11'>11</option>
									<option value='12'>12</option>
									<option value='13'>13</option>
									<option value='14'>14</option>
									<option value='15'>15</option>
									<option value='16'>16</option>
									<option value='17'>17</option>
									<option value='18'>18</option>
									<option value='19'>19</option>
									<option value='20'>20</option>
									<option value='21'>21</option>
									<option value='22'>22</option>
									<option value='23'>23</option>
									<option value='24'>24</option>
									<option value='25'>25</option>
									<option value='26'>26</option>
									<option value='27'>27</option>
									<option value='28'>28</option>
									<option value='29'>29</option>
									<option value='30'>30</option>
									<option value='31'>31</option>
									<option value='32'>32</option>
									<option value='33'>33</option>
									<option value='34'>34</option>
									<option value='35'>35</option>
									<option value='36'>36</option>
									<option value='37'>37</option>
									<option value='38'>38</option>
									<option value='39'>39</option>
									<option value='40'>40</option>
									<option value='41'>41</option>
									<option value='42'>42</option>
									<option value='43'>43</option>
									<option value='44'>44</option>
									<option value='45'>45</option>
									<option value='46'>46</option>
									<option value='47'>47</option>
									<option value='48'>48</option>
									<option value='49'>49</option>
									<option value='50'>50</option>
									<option value='51'>51</option>
									<option value='52'>52</option>
									<option value='53'>53</option>
									<option value='54'>54</option>
									<option value='55'>55</option>
									<option value='56'>56</option>
									<option value='57'>57</option>
									<option value='58'>58</option>
									<option value='59'>59</option>
									<option value='60'>60</option>
								</select>
								<p><label>Data Implantação:</label> <input name='con_data_implantacao' id='con_data_implantacao' placeholder='Data Implantação' onkeypress='return mascaraData(this,event);'>
								<p><label>Garantia:</label> <input name='con_garantia' id='con_garantia' placeholder='Garantia' maxlength='1'>
								<p><label>Valor Garantia:</label> <input name='con_valor_garantia' id='con_valor_garantia' placeholder='Valor Garantia' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
								<p><label>Status:</label> <select name='con_status' id='con_status'>
									<option value=''>Status</option>
									<option value='Vigente'>Vigente</option>
									<option value='Cancelado'>Cancelado</option>
									<option value='Finalizado'>Finalizado</option>
									<option value='Suspenso'>Suspenso</option>
								</select>
								<p><label>Previsão Reajuste:</label> <input name='con_previsao_reajuste' id='con_previsao_reajuste' placeholder='Previsão Reajuste' maxlength='1'>
								<p><label>Índice Reajuste:</label> <select name='con_indice_reajuste' id='con_indice_reajuste'>
									<option value=''>Índice Reajuste</option>
									<option value='IGP-M'>IGP-M</option>
									<option value='IPCA/IBGE'>IPCA/IBGE</option>
									<option value='INPC/IBGE'>INPC/IBGE</option>
									<option value='IGP-M/FGV'>IGP-M/FGV</option>
								</select>
								<p><label>Forma Reajuste:</label> <select name='con_forma_reajuste' id='con_forma_reajuste'>
									<option value=''>Forma Reajuste</option>
									<option value='Anual'>Anual</option>
									<option value='Semestral'>Semestral</option>
									<option value='Mensal'>Mensal</option>
									<option value='Quinzenal'>Quinzenal</option>
								</select>
								<p><label>Próximo Reajuste:</label> <input name='con_proximo_reajuste' id='con_proximo_reajuste' placeholder='Próximo Reajuste' onkeypress='return mascaraData(this,event);'>
								<p><label>Último Reajuste:</label> <input name='con_ultimo_reajuste' id='con_ultimo_reajuste' placeholder='Último Reajuste' onkeypress='return mascaraData(this,event);'>
								<p><label>Tipo Multa:</label> <select name='con_tipo_multa' id='con_tipo_multa'>
									<option value=''>Tipo Multa</option>
									<option value='Valor (R$)'>Valor (R$)</option>
									<option value='Percentual'>Percentual</option>
								</select>
								<p><label>Valor Multa:</label> <input name='con_valor_multa' id='con_valor_multa' placeholder='Valor Multa' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
								<p><label>Edital:</label> <input type='file' name='con_arquivo[edital]' id='con_edital'> 
								<p><label>Proposta:</label> <input type='file' name='con_arquivo[proposta]' id='con_proposta'> 
								<p><label>Contrato:</label> <input type='file' name='con_arquivo[contrato]' id='con_contrato'> 
								<p><label>Outro:</label> <input type='file' name='con_arquivo[outro]' id='con_outro'> 
							</td>
						</tr>
					</table>
			  	</div>
				<div id='gestor' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
								<div id='p_scents_gestor'>
								<div class='bloco_gestor'>
									<input type='hidden' name='gestor[1][ges_id]' id='ges_id'>
									<br><label>Gestor:</label> 
										<select name='gestor[1][ges_contato]' id='ges_contato' class='ges_contato' >
										<option value=''>Gestor</option>
										"; 
										$sql = "SELECT * FROM cadastro_empresas 
												LEFT JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_empresa = cadastro_empresas.emp_id
												LEFT JOIN cadastro_contratos ON cadastro_contratos.con_contratante = cadastro_empresas.emp_id
												WHERE con_id = :con_id
												ORDER BY ctt_nome ASC";
										$stmt = $PDO->prepare($sql);
										$stmt->bindParam(':con_id',$con_id);
										$stmt->execute();
										while($result = $stmt->fetch())
										{
											echo "<option value='".$result['ctt_id']."'>".$result['ctt_nome']."</option>";
										}
										echo "
									</select>
									<p><label>Data Início:</label>	<input name='gestor[1][ges_data_inicio]' id='ges_data_inicio' placeholder='Data Início' onkeypress='return mascaraData(this,event);'></p>
									<p><label>Data Fim:</label>		<input name='gestor[1][ges_data_fim]' id='ges_data_fim' placeholder='Data Fim' onkeypress='return mascaraData(this,event);'></p>
									<img src='../imagens/icon-add.png' id='addGestor' title='Adicionar +' class='botao_dinamico'>
								</div>
								</div>
							</td>
						</tr>
					</table>
			  	</div>
			 	<div id='itens' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
						<tr>
							<td align='left'>
								<div id='p_scents_item'>
								<div class='bloco_item'>
									<input type='hidden' name='item[1][ite_id]' id='ite_id'>
									<br><label>Tipo:</label> 
										<select name='item[1][ite_tipo]' id='ite_tipo' >
										<option value=''>Tipo</option>
										<option value='Material Permanente'>Material Permanente</option>
										<option value='Material de Consumo'>Material de Consumo</option>
										<option value='Obras e Serviços de Engenharia'>Obras e Serviços de Engenharia</option>
										<option value='Serviços Terceiros - PJ'>Serviços Terceiros - PJ</option>
									</select>
									<p><label>Descrição:</label>	<input name='item[1][ite_descricao]' id='ite_descricao' placeholder='Descrição'></p>
									<p><label>Quantidade:</label>	<input name='item[1][ite_quantidade]' id='ite_quantidade' placeholder='Quantidade'  onkeypress='return SomenteNumero(event);'></p>
									   <label>Unidade:</label> 
										<select name='item[1][ite_unidade]' id='ite_unidade' >
										<option value=''>Unidade</option>
										<option value='Bloco'>Bloco</option>	
										<option value='Centena'>Centena</option>
										<option value='Dúzia'>Dúzia</option>
										<option value='Folhas'>Folhas</option>
										<option value='Hora/Trabalho'>Hora/Trabalho</option>	
										<option value='Kilo'>Kilo</option>
										<option value='Litro'>Litro</option>		
										<option value='Páginas'>Páginas</option>
										<option value='Unidades'>Unidades</option>
									</select>
									<p><label>Valor Unitário:</label>	<input name='item[1][ite_valor_unitario]' id='ite_valor_unitario' placeholder='Valor Unitário'  onkeypress='return MascaraMoeda(this,\".\",\",\",event);'></p>
									<p><label>Valor Total:</label>		<input name='item[1][ite_valor_total]' id='ite_valor_total' placeholder='Valor Total'  onkeypress='return MascaraMoeda(this,\".\",\",\",event);'></p>
									<p><label>Marca:</label>			<input name='item[1][ite_marca]' id='ite_marca' placeholder='Marca'></p>
									<img src='../imagens/icon-add.png' id='addItem' title='Adicionar +' class='botao_dinamico'>
								</div>
								</div>
							</td>
						</tr>
					</table>
			  	</div>
				<div id='formas_atendimento' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%' class='borda_aba'>
						<tr>
							<td align='left'>
							<div id='p_scents_forma_atendimento'>
								<div class='bloco_forma_atendimento'>
									<input type='hidden' name='forma_atendimento[1][cfa_id]' id='cfa_id'>
									<br><label>Forma de Atendimento:</label> 
										<select name='forma_atendimento[1][cfa_forma_atendimento]' id='cfa_forma_atendimento' class='cfa_forma_atendimento' >
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
									<p>
									<img src='../imagens/icon-add.png' id='addFormaAtendimento' title='Adicionar +' class='botao_dinamico'>
									
								</div>
								</div>
							</td>
						</tr>
					</table>
				</div> 
				
				<div id='faturamento' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%' class='borda_aba'>
						<tr>
							<td align='left'>
							&nbsp;
							</td>
						</tr>
					</table>
				</div>
				<div id='aditivos' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%' class='borda_aba'>
						<tr>
							<td align='left'>
							&nbsp;
							</td>
						</tr>
					</table>
				</div>
				<div id='representacao' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%' class='borda_aba'>
						<tr>
							<td align='left'>
							&nbsp;
							</td>
						</tr>
					</table>
				</div>
				<div id='encargos' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%' class='borda_aba'>
						<tr>
							<td align='left'>
							&nbsp;
							</td>
						</tr>
					</table>
				</div>
				<div id='parametrizacao' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%' class='borda_aba'>
						<tr>
							<td align='left'>
							&nbsp;
							</td>
						</tr>
					</table>
				</div>
				<div id='bloco_notas' class='tab-pane fade'>
					<table align='center' cellspacing='0' width='100%' class='borda_aba'>
						<tr>
							<td align='left'>
							&nbsp;
							</td>
						</tr>
					</table>
				</div>
				<div id='memorial' class='tab-pane fade'>
				<p><label>Descrição:</label> <textarea name='con_memorial' id='con_memorial' style='height:150px;'  placeholder='Objeto'>$con_memorial</textarea>
				</div>
			</div>   
			
			<center>
			<div id='erro' align='center'>&nbsp;</div>
			<input type='button' id='bt_cadastro_contratos_sair' value='Salvar e Sair' />&nbsp;&nbsp;&nbsp;&nbsp; 
			<input type='button' id='bt_cadastro_contratos' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
			<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_contratos.php?pagina=cadastro_contratos".$autenticacao."'; value='Cancelar'/></center>
			</center>
        </form>
        ";
    }
    
    if($pagina == 'cadastro_contratos_editar')
    {
        $sql = "SELECT *,contratante.emp_nome_razao as contratante, contratado.emp_nome_razao as contratado
			    FROM cadastro_contratos 
				LEFT JOIN cadastro_empresas as contratante ON contratante.emp_id = cadastro_contratos.con_contratante
				LEFT JOIN cadastro_empresas as contratado ON contratado.emp_id = cadastro_contratos.con_contratado
				LEFT JOIN aux_modalidades ON aux_modalidades.mod_id = cadastro_contratos.con_modalidade
				LEFT JOIN aux_servicos ON aux_servicos.ser_id = cadastro_contratos.con_servico
				WHERE con_id = :con_id";
        $stmt = $PDO->prepare($sql);	
		$stmt->bindParam(':con_id', $con_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
        {
			$result = $stmt->fetch();
          	$con_contratante 			= $result['con_contratante'];
			$contratante 				= $result['contratante'];
			$con_contratado 			= $result['con_contratado'];
			$contratado 				= $result['contratado'];
			$con_tipo_ajuste 			= $result['con_tipo_ajuste'];
			$con_numero_ajuste 			= $result['con_numero_ajuste'];
			$con_ano_ajuste 			= $result['con_ano_ajuste'];
			$con_servico 				= $result['con_servico'];
			$ser_descricao 				= $result['ser_descricao'];
			$con_objeto 				= $result['con_objeto'];
			$con_resumo_objeto 			= $result['con_resumo_objeto'];
			$con_regime 				= $result['con_regime'];
			$con_modalidade 			= $result['con_modalidade'];
			$mod_descricao 				= $result['mod_descricao'];
			$con_numero_modalidade 		= $result['con_numero_modalidade'];
			$con_ano_modalidade 		= $result['con_ano_modalidade'];
			$con_processo_adm 			= $result['con_processo_adm'];
			$con_numero_processo 		= $result['con_numero_processo'];
			$con_ano_processo 			= $result['con_ano_processo'];
			$con_data_assinatura 		= implode("/",array_reverse(explode("-",$result['con_data_assinatura'])));
			$con_inicio_vig 			= implode("/",array_reverse(explode("-",$result['con_inicio_vig'])));
			$con_final_vig 				= implode("/",array_reverse(explode("-",$result['con_final_vig'])));
			$con_total_meses 			= $result['con_total_meses'];
			$con_dia_emissao_nf 		= $result['con_dia_emissao_nf'];
			$con_periodicidade 			= $result['con_periodicidade'];
			$con_numero_parcelas 		= $result['con_numero_parcelas'];
			$con_previsao_prorrogacao	= $result['con_previsao_prorrogacao'];
			$con_limite_legal 			= $result['con_limite_legal'];
			$con_data_limite_prorrog 	= implode("/",array_reverse(explode("-",$result['con_data_limite_prorrog'])));
			if(!empty($result['con_valor_global_inicial']))
			$con_valor_global_inicial 	= number_format($result['con_valor_global_inicial'],2,",",".");
			if(!empty($result['con_valor_unitario_parcela']))
			$con_valor_unitario_parcela	= number_format($result['con_valor_unitario_parcela'],2,",",".");
			if(!empty($result['con_valor_global_atual']))
			$con_valor_global_atual 	= number_format($result['con_valor_global_atual'],2,",",".");
			if(!empty($result['con_valor_unitario_atual']))
			$con_valor_unitario_atual 	= number_format($result['con_valor_unitario_atual'],2,",",".");
			$con_implantacao 			= $result['con_implantacao'];
			if(!empty($result['con_valor_implantacao']))
			$con_valor_implantacao 		= number_format($result['con_valor_implantacao'],2,",",".");
			$con_prazo_implantacao 		= $result['con_prazo_implantacao'];
			$con_data_implantacao 		= implode("/",array_reverse(explode("-",$result['con_data_implantacao'])));
			$con_garantia 				= $result['con_garantia'];
			if(!empty($result['con_valor_garantia']))
			$con_valor_garantia 		= number_format($result['con_valor_garantia'],2,",",".");
			$con_status 				= $result['con_status'];
			$con_previsao_reajuste 		= $result['con_previsao_reajuste'];
			$con_indice_reajuste 		= $result['con_indice_reajuste'];
			$con_forma_reajuste 		= $result['con_forma_reajuste'];
			$con_proximo_reajuste 		= implode("/",array_reverse(explode("-",$result['con_proximo_reajuste'])));
			$con_ultimo_reajuste 		= implode("/",array_reverse(explode("-",$result['con_ultimo_reajuste'])));
			$con_tipo_multa 			= $result['con_tipo_multa'];
			if(!empty($result['con_valor_multa']))
			$con_valor_multa 			= number_format($result['con_valor_multa'],2,",",".");
			$con_edital 				= $result['con_edital'];
			$con_proposta 				= $result['con_proposta'];
			$con_contrato 				= $result['con_contrato'];
			$con_outro 					= $result['con_outro'];
			$con_memorial 				= $result['con_memorial'];
			$con_status					= $result['con_status'];
			echo "
            <form name='form_cadastro_contratos' id='form_cadastro_contratos' enctype='multipart/form-data' method='post' action='cadastro_contratos.php?pagina=cadastro_contratos&action=editar&con_id=$con_id$autenticacao'>
                <div class='titulo'> $page &raquo; Editar </div>
				<ul class='nav nav-tabs'>
				  <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>
				  <li><a data-toggle='tab' href='#gestor'>Gestor do Contrato</a></li>
				  <li><a data-toggle='tab' href='#itens'>Itens do Contrato</a></li>
				  <li><a data-toggle='tab' href='#formas_atendimento'>Formas de Atendimento</a></li>
				  <li><a data-toggle='tab' href='#faturamento'>Faturamento</a></li>
				  <li><a data-toggle='tab' href='#aditivos'>Aditivos</a></li>
				  <li><a data-toggle='tab' href='#representacao'>Representação</a></li>
				  <li><a data-toggle='tab' href='#encargos'>Encargos</a></li>
				  <li><a data-toggle='tab' href='#parametrizacao'>Parametrização</a></li>
				  <li><a data-toggle='tab' href='#bloco_notas'>Bloco de Notas</a></li>
				  <li><a data-toggle='tab' href='#memorial'>Memorial / T.R.</a></li>
				</ul>
				
				<div class='tab-content'>
					<div id='dados_gerais' class='tab-pane fade in active'>
						<table align='center' cellspacing='0' width='100%' class='borda_aba'>
							<tr>
								<td align='left'>
									<br><label>Contratante:</label> 
										<div class='suggestion' style='width:80%;'>
											<input name='con_contratante' id='con_contratante' value='$con_contratante'  placeholder='ID' autocomplete='off'  type='hidden' />
											<input style='width:100%;' name='con_contratante_nome' value='$contratante'  id='con_contratante_nome' type='text' placeholder='Contratante: Digite o nome ou CNPJ/CPF' autocomplete='off' />
											<div class='suggestionsBox' id='suggestions' style='display: none;'>
												<div class='suggestionList' id='autoSuggestionsList'>
													&nbsp;
												</div>
											</div>
										</div>
										<br><br>
									<p><label>Contratado:</label> 
										<div class='suggestion2' style='width:80%;'>
											<input name='con_contratado' id='con_contratado' value='$con_contratado'  placeholder='ID' autocomplete='off'  type='hidden' />
											<input style='width:100%;'  name='con_contratado_nome' value='$contratado' id='con_contratado_nome' type='text' placeholder='Contratado: Digite o nome ou CNPJ/CPF' autocomplete='off' />
											<div class='suggestionsBox' id='suggestions2' style='display: none;'>
												<div class='suggestionList2' id='autoSuggestionsList2'>
													&nbsp;
												</div>
											</div>
										</div>
										<br><br>
									<p><label>Tipo de Ajuste:</label> <select name='con_tipo_ajuste' id='con_tipo_ajuste'>
										<option value='$con_tipo_ajuste'>$con_tipo_ajuste</option>
										<option value='Contrato'>Contrato</option>
										<option value='Ata Registro'>Ata Registro</option>
										<option value='AF Dispensa'>AF Dispensa</option>
									</select>
									<p><label>Número Ajuste:</label> <input name='con_numero_ajuste' id='con_numero_ajuste' value='$con_numero_ajuste' placeholder='Número Ajuste'  maxlength='5' onkeypress='return SomenteNumero(event);'>
									<p><label>Ano Ajuste:</label> <input name='con_ano_ajuste' id='con_ano_ajuste' value='$con_ano_ajuste' placeholder='Ano Ajuste'  maxlength='4' onkeypress='return SomenteNumero(event);'>
									<p><label>Serviço:</label> <select name='con_servico' id='con_servico'>
										<option value='$con_servico'>$ser_descricao</option>
										"; 
										$sql = " SELECT * FROM aux_servicos WHERE ser_status = :ser_status ORDER BY ser_descricao ";
										$stmt = $PDO->prepare($sql);
										$stmt->bindValue(':ser_status',1);
										$stmt->execute();
										while($result = $stmt->fetch())
										{
											echo "<option value='".$result['ser_id']."'>".$result['ser_descricao']."</option>";
										}
										echo "
									</select>
									<p><label>Objeto:</label> <textarea name='con_objeto' id='con_objeto' placeholder='Objeto'>$con_objeto</textarea>
									<p><label>Resumo do Objeto:</label> <input name='con_resumo_objeto' id='con_resumo_objeto' value='$con_resumo_objeto' placeholder='Resumo do Objeto'>
									<p><label>Regime de Execução:</label> <select name='con_regime' id='con_regime'>
										<option value='$con_regime'>$con_regime</option>
										<option value='Empreitada por preço global'>Empreitada por preço global</option>
										<option value='Empreitada por preço unitário'>Empreitada por preço unitário</option>
										<option value='Tarefa'>Tarefa</option>
										<option value='Empreitada integral'>Empreitada integral</option>
									</select>
									<p><label>Modalidade:</label> <select name='con_modalidade' id='con_modalidade'>
										<option value='$con_modalidade'>$mod_descricao</option>";
										$sql = " SELECT * FROM aux_modalidades ORDER BY mod_descricao";
										$stmt = $PDO->prepare($sql);
										$stmt->execute();
										while($result = $stmt->fetch())
										{
											echo "<option value='".$result['mod_id']."'>".$result['mod_descricao']."</option>";
										}
										echo "
									</select>
									<p><label>Número Modalidade:</label> <input name='con_numero_modalidade' id='con_numero_modalidade' value='$con_numero_modalidade' placeholder='Número Modalidade'  maxlength='5' onkeypress='return SomenteNumero(event);'>
									<p><label>Ano Modalidade:</label> <input name='con_ano_modalidade' id='con_ano_modalidade' value='$con_ano_modalidade' placeholder='Ano Modalidade'  maxlength='4' onkeypress='return SomenteNumero(event);'>
									<p><label>Processo Administrativo:</label> <input name='con_processo_adm' id='con_processo_adm' value='$con_processo_adm' placeholder='Processo Administrativo'>
									<p><label>Número Processo:</label> <input name='con_numero_processo' id='con_numero_processo' value='$con_numero_processo' placeholder='Número Processo'  maxlength='5' onkeypress='return SomenteNumero(event);'>
									<p><label>Ano Processo:</label> <input name='con_ano_processo' id='con_ano_processo' value='$con_ano_processo' placeholder='Ano Processo'  maxlength='4' onkeypress='return SomenteNumero(event);'>
									<p><label>Data Assinatura:</label> <input name='con_data_assinatura' id='con_data_assinatura' value='$con_data_assinatura' placeholder='Data Assinatura' onkeypress='return mascaraData(this,event);'>
									<p><label>Ínicio Vigência:</label> <input name='con_inicio_vig' id='con_inicio_vig' value='$con_inicio_vig' placeholder='Inicío Vigência' onkeypress='return mascaraData(this,event);'>
									<p><label>Fim Vigência:</label> <input name='con_final_vig' id='con_final_vig' value='$con_final_vig' placeholder='Fim Vigência' onkeypress='return mascaraData(this,event);'>
									<p><label>Total Meses:</label> <input name='con_total_meses' id='con_total_meses' value='$con_total_meses' placeholder='Total Meses' onkeypress='return SomenteNumero(event);'>
									<p><label>Dia Emissão NF:</label> <select name='con_dia_emissao_nf' id='con_dia_emissao_nf'>
										<option value='$con_dia_emissao_nf'>$con_dia_emissao_nf</option>
										<option value='01'>01</option>
										<option value='02'>02</option>
										<option value='03'>03</option>
										<option value='04'>04</option>
										<option value='05'>05</option>
										<option value='06'>06</option>
										<option value='07'>07</option>
										<option value='08'>08</option>
										<option value='09'>09</option>
										<option value='10'>10</option>
										<option value='11'>11</option>
										<option value='12'>12</option>
										<option value='13'>13</option>
										<option value='14'>14</option>
										<option value='15'>15</option>
										<option value='16'>16</option>
										<option value='17'>17</option>
										<option value='18'>18</option>
										<option value='19'>19</option>
										<option value='20'>20</option>
										<option value='21'>21</option>
										<option value='22'>22</option>
										<option value='23'>23</option>
										<option value='24'>24</option>
										<option value='25'>25</option>
										<option value='26'>26</option>
										<option value='27'>27</option>
										<option value='28'>28</option>
										<option value='29'>29</option>
										<option value='30'>30</option>
										<option value='31'>31</option>
									</select>
									<p><label>Peridiocidade:</label> <select name='con_periodicidade' id='con_periodicidade'>
										<option value='$con_periodicidade'>$con_periodicidade</option>
										<option value='Mensal'>Mensal</option>
										<option value='Anual'>Anual</option>
										<option value='Trimestral'>Trimestral</option>
										<option value='Diária'>Diária</option>
										<option value='Quadrimestral'>Quadrimestral</option>
										<option value='Semestral'>Semestral</option>
										<option value='Prazo pré determinado'>Prazo pré determinado</option>
									</select>						
									<p><label>Nº Parcelas:</label> <input name='con_numero_parcelas' id='con_numero_parcelas' value='$con_numero_parcelas' placeholder='Nº Parcelas' onkeypress='return SomenteNumero(event);'>
									<p><label>Previsão Prorrogação:</label> <input name='con_previsao_prorrogacao' id='con_previsao_prorrogacao' value='$con_previsao_prorrogacao' placeholder='Previsão de Prorrogação' maxlength='1'>
									<p><label>Limite Legal:</label> <select name='con_limite_legal' id='con_limite_legal'>
										<option value='$con_limite_legal'>$con_limite_legal</option>
										<option value='01'>01</option>
										<option value='02'>02</option>
										<option value='03'>03</option>
										<option value='04'>04</option>
										<option value='05'>05</option>
										<option value='06'>06</option>
										<option value='07'>07</option>
										<option value='08'>08</option>
										<option value='09'>09</option>
										<option value='10'>10</option>
										<option value='11'>11</option>
										<option value='12'>12</option>
										<option value='13'>13</option>
										<option value='14'>14</option>
										<option value='15'>15</option>
										<option value='16'>16</option>
										<option value='17'>17</option>
										<option value='18'>18</option>
										<option value='19'>19</option>
										<option value='20'>20</option>
										<option value='21'>21</option>
										<option value='22'>22</option>
										<option value='23'>23</option>
										<option value='24'>24</option>
										<option value='25'>25</option>
										<option value='26'>26</option>
										<option value='27'>27</option>
										<option value='28'>28</option>
										<option value='29'>29</option>
										<option value='30'>30</option>
										<option value='31'>31</option>
										<option value='32'>32</option>
										<option value='33'>33</option>
										<option value='34'>34</option>
										<option value='35'>35</option>
										<option value='36'>36</option>
										<option value='37'>37</option>
										<option value='38'>38</option>
										<option value='39'>39</option>
										<option value='40'>40</option>
										<option value='41'>41</option>
										<option value='42'>42</option>
										<option value='43'>43</option>
										<option value='44'>44</option>
										<option value='45'>45</option>
										<option value='46'>46</option>
										<option value='47'>47</option>
										<option value='48'>48</option>
										<option value='49'>49</option>
										<option value='50'>50</option>
										<option value='51'>51</option>
										<option value='52'>52</option>
										<option value='53'>53</option>
										<option value='54'>54</option>
										<option value='55'>55</option>
										<option value='56'>56</option>
										<option value='57'>57</option>
										<option value='58'>58</option>
										<option value='59'>59</option>
										<option value='60'>60</option>
									</select>
									<p><label>Data Limite Prorrogação:</label> <input name='con_data_limite_prorrog' id='con_data_limite_prorrog' value='$con_data_limite_prorrog' placeholder='Data Limite Prorrog' onkeypress='return mascaraData(this,event);'>
									<p><label>Valor Global Inicial:</label> <input name='con_valor_global_inicial' id='con_valor_global_inicial' value='$con_valor_global_inicial' placeholder='Valor Global Inicial' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
									<p><label>Valor Unitário (parcela):</label> <input name='con_valor_unitario_parcela' id='con_valor_unitario_parcela' value='$con_valor_unitario' placeholder='Valor Unitário (parcela)' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
									<p><label>Valor Global Atual:</label> <input name='con_valor_global_atual' id='con_valor_global_atual' value='$con_valor_global_atual' placeholder='Valor Global Atual' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
									<p><label>Valor Unitário Atual:</label> <input name='con_valor_unitario_atual' id='con_valor_unitario_atual' value='$con_valor_unitario_atual' placeholder='Valor Unitário Atual' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
									<p><label>Implantação:</label> <input name='con_implantacao' id='con_implantacao' value='$con_implantacao' placeholder='Implantação' maxlength='1'>
									<p><label>Valor Implantação:</label> <input name='con_valor_implantacao' id='con_valor_implantacao' value='$con_valor_implantacao' placeholder='Valor Implantação' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
									<p><label>Prazo Implantação:</label> <select name='con_prazo_implantacao' id='con_prazo_implantacao'>
										<option value='$con_prazo_implantacao'>$con_prazo_implantacao</option>
										<option value='01'>01</option>
										<option value='02'>02</option>
										<option value='03'>03</option>
										<option value='04'>04</option>
										<option value='05'>05</option>
										<option value='06'>06</option>
										<option value='07'>07</option>
										<option value='08'>08</option>
										<option value='09'>09</option>
										<option value='10'>10</option>
										<option value='11'>11</option>
										<option value='12'>12</option>
										<option value='13'>13</option>
										<option value='14'>14</option>
										<option value='15'>15</option>
										<option value='16'>16</option>
										<option value='17'>17</option>
										<option value='18'>18</option>
										<option value='19'>19</option>
										<option value='20'>20</option>
										<option value='21'>21</option>
										<option value='22'>22</option>
										<option value='23'>23</option>
										<option value='24'>24</option>
										<option value='25'>25</option>
										<option value='26'>26</option>
										<option value='27'>27</option>
										<option value='28'>28</option>
										<option value='29'>29</option>
										<option value='30'>30</option>
										<option value='31'>31</option>
										<option value='32'>32</option>
										<option value='33'>33</option>
										<option value='34'>34</option>
										<option value='35'>35</option>
										<option value='36'>36</option>
										<option value='37'>37</option>
										<option value='38'>38</option>
										<option value='39'>39</option>
										<option value='40'>40</option>
										<option value='41'>41</option>
										<option value='42'>42</option>
										<option value='43'>43</option>
										<option value='44'>44</option>
										<option value='45'>45</option>
										<option value='46'>46</option>
										<option value='47'>47</option>
										<option value='48'>48</option>
										<option value='49'>49</option>
										<option value='50'>50</option>
										<option value='51'>51</option>
										<option value='52'>52</option>
										<option value='53'>53</option>
										<option value='54'>54</option>
										<option value='55'>55</option>
										<option value='56'>56</option>
										<option value='57'>57</option>
										<option value='58'>58</option>
										<option value='59'>59</option>
										<option value='60'>60</option>
									</select>
									<p><label>Data Implantação:</label> <input name='con_data_implantacao' id='con_data_implantacao' value='$con_data_implantacao' placeholder='Data Implantação' onkeypress='return mascaraData(this,event);'>
									<p><label>Garantia:</label> <input name='con_garantia' id='con_garantia' value='$con_garantia' placeholder='Garantia' maxlength='1'>
									<p><label>Valor Garantia:</label> <input name='con_valor_garantia' id='con_valor_garantia' value='$con_valor_garantia' placeholder='Valor Garantia' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
									<p><label>Status:</label> <select name='con_status' id='con_status'>
										<option value='$con_status'>$con_status</option>
										<option value='Vigente'>Vigente</option>
										<option value='Cancelado'>Cancelado</option>
										<option value='Finalizado'>Finalizado</option>
										<option value='Suspenso'>Suspenso</option>
									</select>
									<p><label>Previsão Reajuste:</label> <input name='con_previsao_reajuste' id='con_previsao_reajuste' value='$con_previsao_reajuste' placeholder='Previsão Reajuste' maxlength='1'>
									<p><label>Índice Reajuste:</label> <select name='con_indice_reajuste' id='con_indice_reajuste'>
										<option value='$con_indice_reajuste'>$con_indice_reajuste</option>
										<option value='IGP-M'>IGP-M</option>
										<option value='IPCA/IBGE'>IPCA/IBGE</option>
										<option value='INPC/IBGE'>INPC/IBGE</option>
										<option value='IGP-M/FGV'>IGP-M/FGV</option>
									</select>
									<p><label>Forma Reajuste:</label> <select name='con_forma_reajuste' id='con_forma_reajuste'>
										<option value='$con_forma_reajuste'>$con_forma_reajuste</option>
										<option value='Anual'>Anual</option>
										<option value='Semestral'>Semestral</option>
										<option value='Mensal'>Mensal</option>
										<option value='Quinzenal'>Quinzenal</option>
									</select>
									<p><label>Próximo Reajuste:</label> <input name='con_proximo_reajuste' id='con_proximo_reajuste' value='$con_proximo_reajuste' placeholder='Próximo Reajuste' onkeypress='return mascaraData(this,event);'>
									<p><label>Último Reajuste:</label> <input name='con_ultimo_reajuste' id='con_ultimo_reajuste' value='$con_ultimo_reajuste' placeholder='Último Reajuste' onkeypress='return mascaraData(this,event);'>
									<p><label>Tipo Multa:</label> <select name='con_tipo_multa' id='con_tipo_multa'>
										<option value='$con_tipo_multa'>$con_tipo_multa</option>
										<option value='Valor (R$)'>Valor (R$)</option>
										<option value='Percentual'>Percentual</option>
									</select>
									<p><label>Valor Multa:</label> <input name='con_valor_multa' id='con_valor_multa' value='$con_valor_multa' placeholder='Valor Multa' onkeypress='return MascaraMoeda(this,\".\",\",\",event);'>
									<p><label>Edital:</label> ";if($con_edital != ''){ echo "<a href='$con_edital' target='_blank'><img src='../imagens/icon-pdf.png' valign='middle' border='0'></a>";} echo " &nbsp; 
									<p><label>Alterar Edital:</label> <input type='file' name='con_arquivo[edital]' id='con_edital'> 
									<p><label>Proposta:</label> ";if($con_proposta != ''){ echo "<a href='$con_proposta' target='_blank'><img src='../imagens/icon-pdf.png' valign='middle' border='0'></a>";} echo "  &nbsp;
									<p><label>Alterar Proposta:</label> <input type='file' name='con_arquivo[proposta]' id='con_proposta'> 
									<p><label>Contrato:</label> ";if($con_contrato != ''){ echo "<a href='$con_contrato' target='_blank'><img src='../imagens/icon-pdf.png' valign='middle' border='0'></a>";} echo "  &nbsp;
									<p><label>Alterar Contrato:</label> <input type='file' name='con_arquivo[contrato]' id='con_contrato'>  
									<p><label>Outro:</label> ";if($con_outro != ''){ echo "<a href='$con_outro' target='_blank'><img src='../imagens/icon-pdf.png' valign='middle' border='0'></a>";} echo "   &nbsp; 
									<p><label>Alterar Outro:</label> <input type='file' name='con_arquivo[outro]' id='con_outro'> 									
								</td>
							</tr>
						</table>
					</div>
					<div id='gestor' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
							<tr>
								<td align='left'>
									<div id='p_scents_gestor'>
									";
									$sql = "SELECT * FROM cadastro_contratos_gestor 
											LEFT JOIN cadastro_contratos ON cadastro_contratos.con_id = cadastro_contratos_gestor.ges_contrato	
											LEFT JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_id = cadastro_contratos_gestor.ges_contato	
											WHERE ges_contrato = :ges_contrato";
									$stmt = $PDO->prepare($sql);
									$stmt->bindParam(':ges_contrato', $con_id);
									$stmt->execute();
									$rows = $stmt->rowCount();
									if($rows > 0)
									{
										$x=0;
										while($result = $stmt->fetch())
										{
											$x++;
											echo "
											<div class='bloco_gestor'>
												<input type='hidden' name='gestor[$x][ges_id]' id='ges_id' value='".$result['ges_id']."'>
												"; if($x > 1){ echo "<br><br><hr><p>";}else{ echo "<br>";} 
												echo "
												<br><label>Gestor:</label> 
													<select name='gestor[$x][ges_contato]' id='ges_contato' class='ges_contato' >
													<option value='".$result['ges_contato']."'>".$result['ctt_nome']."</option>
													"; 
													$sql = "SELECT * FROM cadastro_empresas 
															LEFT JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_empresa = cadastro_empresas.emp_id
															LEFT JOIN cadastro_contratos ON cadastro_contratos.con_contratante = cadastro_empresas.emp_id
															WHERE con_id = :con_id
															ORDER BY ctt_nome ASC";
													$stmt_gestor = $PDO->prepare($sql);
													$stmt_gestor->bindParam(':con_id',$con_id);
													$stmt_gestor->execute();
													while($result_gestor = $stmt_gestor->fetch())
													{
														echo "<option value='".$result_gestor['ctt_id']."'>".$result_gestor['ctt_nome']."</option>";
													}
													echo "
												</select>
												<p><label>Data Início:</label>	<input name='gestor[$x][ges_data_inicio]' id='ges_data_inicio' value='".implode("/",array_reverse(explode("-",$result['ges_data_inicio'])))."' placeholder='Data Início' onkeypress='return mascaraData(this,event);'></p>
												<p><label>Data Fim:</label>		<input name='gestor[$x][ges_data_fim]' id='ges_data_fim' value='".implode("/",array_reverse(explode("-",$result['ges_data_fim'])))."' placeholder='Data Fim' onkeypress='return mascaraData(this,event);'></p>
												<img src='../imagens/icon-add.png' id='addGestor' title='Adicionar +' class='botao_dinamico'> <img src='../imagens/icon-rmv.png' id='remGestor' title='Remover' class='botao_dinamico'>
											</div>
											";
										}
									}
									else
									{
										echo "
										<div class='bloco_gestor'>
											<input type='hidden' name='gestor[1][ges_id]' id='ges_id'>
											<br><label>Gestor:</label> 
												<select name='gestor[1][ges_contato]' id='ges_contato' class='ges_contato' >
												<option value=''>Gestor</option>
												"; 
												$sql = "SELECT * FROM cadastro_empresas 
														LEFT JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_empresa = cadastro_empresas.emp_id
														LEFT JOIN cadastro_contratos ON cadastro_contratos.con_contratante = cadastro_empresas.emp_id
														WHERE con_id = :con_id
														ORDER BY ctt_nome ASC";
												$stmt = $PDO->prepare($sql);
												$stmt->bindParam(':con_id',$con_id);
												$stmt->execute();
												while($result = $stmt->fetch())
												{
													echo "<option value='".$result['ctt_id']."'>".$result['ctt_nome']."</option>";
												}
												echo "
											</select>
											<p><label>Data Início:</label>	<input name='gestor[1][ges_data_inicio]' id='ges_data_inicio' placeholder='Data Início' onkeypress='return mascaraData(this,event);'></p>
											<p><label>Data Fim:</label>		<input name='gestor[1][ges_data_fim]' id='ges_data_fim' placeholder='Data Fim' onkeypress='return mascaraData(this,event);'></p>
											<img src='../imagens/icon-add.png' id='addGestor' title='Adicionar +' class='botao_dinamico'>
										</div>
										";
									}
									echo "
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div id='itens' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%'  class='borda_aba'>
							<tr>
								<td align='left'>
									<div id='p_scents_item'>
									";
									$sql = "SELECT * FROM cadastro_contratos_itens 
											LEFT JOIN cadastro_contratos ON cadastro_contratos.con_id = cadastro_contratos_itens.ite_contrato	
											WHERE ite_contrato = :ite_contrato";
									$stmt = $PDO->prepare($sql);
									$stmt->bindParam(':ite_contrato', $con_id);
									$stmt->execute();
									$rows = $stmt->rowCount();
									if($rows > 0)
									{
										$x=0;
										while($result = $stmt->fetch())
										{
											$x++;
											echo "
											<div class='bloco_item'>
												<input type='hidden' name='item[$x][ite_id]' id='ite_id' value='".$result['ite_id']."'>
												"; if($x > 1){ echo "<br><br><hr><p>";}else{ echo "<br>";} 
												echo "
												<br><label>Tipo:</label> 
													<select name='item[$x][ite_tipo]' id='ite_tipo' >
													<option value='".$result['ite_tipo']."'>".$result['ite_tipo']."</option>
													<option value='Material Permanente'>Material Permanente</option>
													<option value='Material de Consumo'>Material de Consumo</option>
													<option value='Obras e Serviços de Engenharia'>Obras e Serviços de Engenharia</option>
													<option value='Serviços Terceiros - PJ'>Serviços Terceiros - PJ</option>
												</select>
												<p><label>Descrição:</label>	<input name='item[$x][ite_descricao]' id='ite_descricao' value='".$result['ite_descricao']."' placeholder='Descrição'></p>
												<p><label>Quantidade:</label>	<input name='item[$x][ite_quantidade]' id='ite_quantidade' value='".$result['ite_quantidade']."' placeholder='Quantidade'  onkeypress='return SomenteNumero(event);'></p>
												   <label>Unidade:</label> 
													<select name='item[$x][ite_unidade]' id='ite_unidade' >
													<option value='".$result['ite_unidade']."'>".$result['ite_unidade']."</option>
													<option value='Bloco'>Bloco</option>	
													<option value='Centena'>Centena</option>
													<option value='Dúzia'>Dúzia</option>
													<option value='Folhas'>Folhas</option>
													<option value='Hora/Trabalho'>Hora/Trabalho</option>	
													<option value='Kilo'>Kilo</option>
													<option value='Litro'>Litro</option>		
													<option value='Páginas'>Páginas</option>
													<option value='Unidades'>Unidades</option>
												</select>
												<p><label>Valor Unitário:</label>	<input name='item[$x][ite_valor_unitario]' id='ite_valor_unitario' value='".number_format($result['ite_valor_unitario'],2,',','.')."' placeholder='Valor Unitário'  onkeypress='return MascaraMoeda(this,\".\",\",\",event);'></p>
												<p><label>Valor Total:</label>		<input name='item[$x][ite_valor_total]' id='ite_valor_total' value='".number_format($result['ite_valor_total'],2,',','.')."' placeholder='Valor Total'  onkeypress='return MascaraMoeda(this,\".\",\",\",event);'></p>
												<p><label>Marca:</label>			<input name='item[$x][ite_marca]' id='ite_marca' value='".$result['ite_marca']."' placeholder='Marca'></p>
												<img src='../imagens/icon-add.png' id='addItem' title='Adicionar +' class='botao_dinamico'> <img src='../imagens/icon-rmv.png' id='remItem' title='Remover' class='botao_dinamico'>
											</div>
											";
										}
									}
									else
									{
										echo "
										<div class='bloco_item'>
											<input type='hidden' name='item[1][ite_id]' id='ite_id'>
											<br><label>Tipo:</label> 
												<select name='item[1][ite_tipo]' id='ite_tipo' >
												<option value=''>Tipo</option>
												<option value='Material Permanente'>Material Permanente</option>
												<option value='Material de Consumo'>Material de Consumo</option>
												<option value='Obras e Serviços de Engenharia'>Obras e Serviços de Engenharia</option>
												<option value='Serviços Terceiros - PJ'>Serviços Terceiros - PJ</option>
											</select>
											<p><label>Descrição:</label>	<input name='item[1][ite_descricao]' id='ite_descricao' placeholder='Descrição'></p>
											<p><label>Quantidade:</label>	<input name='item[1][ite_quantidade]' id='ite_quantidade' placeholder='Quantidade'  onkeypress='return SomenteNumero(event);'></p>
											   <label>Unidade:</label> 
												<select name='item[1][ite_unidade]' id='ite_unidade' >
												<option value=''>Unidade</option>
												<option value='Bloco'>Bloco</option>	
												<option value='Centena'>Centena</option>
												<option value='Dúzia'>Dúzia</option>
												<option value='Folhas'>Folhas</option>
												<option value='Hora/Trabalho'>Hora/Trabalho</option>	
												<option value='Kilo'>Kilo</option>
												<option value='Litro'>Litro</option>		
												<option value='Páginas'>Páginas</option>
												<option value='Unidades'>Unidades</option>
											</select>
											<p><label>Valor Unitário:</label>	<input name='item[1][ite_valor_unitario]' id='ite_valor_unitario' placeholder='Valor Unitário'  onkeypress='return MascaraMoeda(this,\".\",\",\",event);'></p>
											<p><label>Valor Total:</label>		<input name='item[1][ite_valor_total]' id='ite_valor_total' placeholder='Valor Total'  onkeypress='return MascaraMoeda(this,\".\",\",\",event);'></p>
											<p><label>Marca:</label>			<input name='item[1][ite_marca]' id='ite_marca' placeholder='Marca'></p>
											<img src='../imagens/icon-add.png' id='addItem' title='Adicionar +' class='botao_dinamico'>
										</div>
										";
									}
									echo "
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div id='formas_atendimento' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%' class='borda_aba'>
							<tr>
								<td align='left'>
								<div id='p_scents_forma_atendimento'>
									";
									$sql = "SELECT * FROM cadastro_contratos_fa 
											LEFT JOIN cadastro_contratos ON cadastro_contratos.con_id = cadastro_contratos_fa.cfa_contrato
											LEFT JOIN aux_formas_atendimento ON aux_formas_atendimento.fat_id = cadastro_contratos_fa.cfa_forma_atendimento
											WHERE cfa_contrato = :cfa_contrato";
									$stmt = $PDO->prepare($sql);
									$stmt->bindParam(':cfa_contrato', $con_id);
									$stmt->execute();
									$rows = $stmt->rowCount();
									if($rows > 0)
									{
										$x=0;
										while($result = $stmt->fetch())
										{
											$x++;
											echo "
											<div class='bloco_forma_atendimento'>
												<input type='hidden' name='forma_atendimento[$x][cfa_id]' id='cfa_id' value='".$result['cfa_id']."'>
												"; if($x > 1){ echo "<br><br><hr><p>";}else{ echo "<br>";} 
												echo "
												<br><label>Forma de Atendimento:</label> 
													<select name='forma_atendimento[$x][cfa_forma_atendimento]' id='cfa_forma_atendimento' class='cfa_forma_atendimento' >
													<option value='".$result['cfa_forma_atendimento']."'>".$result['fat_descricao']."</option>
													"; 
													$sql = "SELECT * FROM aux_formas_atendimento 
															ORDER BY fat_descricao ASC";
													$stmt_fa = $PDO->prepare($sql);
													$stmt_fa->execute();
													while($result_fa = $stmt_fa->fetch())
													{
														echo "<option value='".$result_fa['fat_id']."'>".$result_fa['fat_descricao']."</option>";
													}
													echo "
												</select>
												<img src='../imagens/icon-add.png' id='addFormaAtendimento' title='Adicionar +' class='botao_dinamico'> <img src='../imagens/icon-rmv.png' id='remFormaAtendimento' title='Remover' class='botao_dinamico'>
											</div>
											";
										}
									}
									else
									{
										echo "
										<div class='bloco_forma_atendimento'>
											<input type='hidden' name='forma_atendimento[1][cfa_id]' id='cfa_id'>
											<br><label>Forma de Atendimento:</label> 
												<select name='forma_atendimento[1][cfa_forma_atendimento]' id='cfa_forma_atendimento' class='cfa_forma_atendimento' >
												<option value=''>Forma de Atendimento</option>
												"; 
												$sql = "SELECT * FROM aux_formas_atendimento 
														ORDER BY fat_descricao ASC";
												$stmt = $PDO->prepare($sql);
												$stmt->bindParam(':con_id',$con_id);
												$stmt->execute();
												while($result = $stmt->fetch())
												{
													echo "<option value='".$result['fat_id']."'>".$result['fat_descricao']."</option>";
												}
												echo "
											</select>
											<img src='../imagens/icon-add.png' id='addFormaAtendimento' title='Adicionar +' class='botao_dinamico'>
										</div>
										";
									}
									echo "
									</div>
								</td>
							</tr>
						</table>
					</div> 
					<div id='faturamento' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%' class='borda_aba'>
							<tr>
								<td align='left'>
								&nbsp;
								</td>
							</tr>
						</table>
					</div>
					<div id='aditivos' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%' class='borda_aba'>
							<tr>
								<td align='left'>
								&nbsp;
								</td>
							</tr>
						</table>
					</div>
					<div id='representacao' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%' class='borda_aba'>
							<tr>
								<td align='left'>
								&nbsp;
								</td>
							</tr>
						</table>
					</div>
					<div id='encargos' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%' class='borda_aba'>
							<tr>
								<td align='left'>
								&nbsp;
								</td>
							</tr>
						</table>
					</div>
					<div id='parametrizacao' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%' class='borda_aba'>
							<tr>
								<td align='left'>
								&nbsp;
								</td>
							</tr>
						</table>
					</div>
					<div id='bloco_notas' class='tab-pane fade'>
						<table align='center' cellspacing='0' width='100%' class='borda_aba'>
							<tr>
								<td align='left'>
								&nbsp;
								</td>
							</tr>
						</table>
					</div>
					<div id='memorial' class='tab-pane fade'>
						<p><label>Descrição:</label> <textarea name='con_memorial' id='con_memorial' style='height:150px;' placeholder='Objeto'>$con_memorial</textarea>
					</div>
				</div>    
				<center>
				<div id='erro' align='center'>&nbsp;</div>
				<input type='button' id='bt_cadastro_contratos_sair' value='Salvar e Sair' />&nbsp;&nbsp;&nbsp;&nbsp; 
				<input type='button' id='bt_cadastro_contratos_a' onclick='alteraActionContrato(".$con_id.")'value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
				<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_contratos.php?pagina=cadastro_contratos".$autenticacao."'; value='Cancelar'/></center>
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
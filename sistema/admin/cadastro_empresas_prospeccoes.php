<?php
session_start (); 
$pagina_link = 'cadastro_empresas';
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
$emp_id = $_GET['emp_id'];
$pro_id = $_GET['pro_id'];
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
	
    $page = "Cadastros &raquo; <a href='cadastro_empresas.php?pagina=cadastro_empresas".$autenticacao."'>Empresas</a> &raquo; <a href='cadastro_empresas.php?pagina=cadastro_empresas_editar&emp_id=$emp_id".$autenticacao."'>Editar</a>";
	
	
	$emp_tipo = $_POST['emp_tipo'];
	$emp_cpf_cnpj = $_POST['emp_cnpj'].$_POST['emp_cpf'];
	$emp_nome_razao = $_POST['emp_nome_razao'];
	$emp_fantasia = $_POST['emp_fantasia'];
	$emp_sigla = $_POST['emp_sigla'];
	$emp_data_fundacao = implode("-",array_reverse(explode("/",$_POST['emp_data_fundacao'])));if($emp_data_fundacao == ''){ $emp_data_fundacao = null;}
	$emp_categ_adm = $_POST['emp_categ_adm'];if($emp_categ_adm == ''){ $emp_categ_adm = null;}
	$emp_esfera = $_POST['emp_esfera'];if($emp_esfera == ''){ $emp_esfera = null;}
	$emp_poder = $_POST['emp_poder'];if($emp_poder == ''){ $emp_poder = null;}
	$emp_orgao = $_POST['emp_orgao'];if($emp_orgao == ''){ $emp_orgao = null;}
	$emp_cep = $_POST['emp_cep'];
	$emp_uf = $_POST['emp_uf'];
	$emp_municipio = $_POST['emp_municipio'];
	$emp_bairro = $_POST['emp_bairro'];
	$emp_endereco = $_POST['emp_endereco'];
	$emp_numero = $_POST['emp_numero'];
	$emp_comp = $_POST['emp_comp'];
	$emp_telefone = $_POST['emp_telefone'];
	$emp_fax = $_POST['emp_fax'];
	$emp_email = $_POST['emp_email'];
	$emp_site = $_POST['emp_site'];
	$emp_status = $_POST['emp_status'];
	$dados = array_filter(array(
		'emp_tipo' 			=> $emp_tipo,
		'emp_cpf_cnpj' 		=> $emp_cpf_cnpj,
		'emp_nome_razao' 	=> $emp_nome_razao,
		'emp_fantasia' 		=> $emp_fantasia,
		'emp_sigla' 		=> $emp_sigla,
		'emp_data_fundacao' => $emp_data_fundacao,
		'emp_categ_adm' 	=> $emp_categ_adm,
		'emp_esfera' 		=> $emp_esfera,
		'emp_poder' 		=> $emp_poder,
		'emp_orgao' 		=> $emp_orgao,
		'emp_cep' 			=> $emp_cep,
		'emp_uf' 			=> $emp_uf,
		'emp_municipio'	 	=> $emp_municipio,
		'emp_bairro' 		=> $emp_bairro,
		'emp_endereco' 		=> $emp_endereco,
		'emp_numero' 		=> $emp_numero,
		'emp_comp' 			=> $emp_comp,
		'emp_telefone' 		=> $emp_telefone,
		'emp_fax' 			=> $emp_fax,
		'emp_email' 		=> $emp_email,
		'emp_site' 			=> $emp_site,
		'emp_status'		=> $emp_status
	),'strlen');
	
	if($action == 'editar')
    {
		
		
		## CAMPOS DINÂMICOS ##
		
		// CONTATO - EXCLUI OS REMOVIDOS
		if(!empty($_POST['prospeccoes']) && is_array($_POST['prospeccoes']))
		{
			//LIMPA ARRAY
			foreach($_POST['prospeccoes'] as $item => $valor) 
			{
				$prospeccoes_filtrado[$item] = array_filter($valor);
			}
			//
			
			$a_excluir = array();
			foreach($prospeccoes_filtrado as $item) 
			{
				if(isset($item['epr_id']))
				{
					$a_excluir[] = $item['epr_id'];
				}
			}
			if(!empty($a_excluir))
			{
				$sql = "DELETE FROM cadastro_empresas_prospeccoes WHERE epr_empresa = :emp_id AND epr_id NOT IN (".implode(",",$a_excluir).") ";
				
				$stmt = $PDO->prepare($sql); 
				$stmt->bindParam(':emp_id', $emp_id);
				if($stmt->execute())
				{
					//echo "Excluido <br>";
				}
				else{ $erro=1; $err = $stmt->errorInfo();}
			}
			else
			{
				$sql = "DELETE FROM cadastro_empresas_prospeccoes WHERE epr_empresa = :emp_id ";
				$stmt = $PDO->prepare($sql); 
				$stmt->bindParam(':emp_id', $emp_id);
				if($stmt->execute())
				{
					//echo "Excluido todos <br>";
				}
				else{ $erro=1; $err = $stmt->errorInfo();}
			}
		}
		else
		{
			$sql = "DELETE FROM cadastro_empresas_prospeccoes WHERE epr_empresa = :emp_id ";
			$stmt = $PDO->prepare($sql); 
			$stmt->bindParam(':emp_id', $emp_id);
			if($stmt->execute())
			{
				//echo "Excluido todos <br>";
			}
			else{ $erro=1; $err = $stmt->errorInfo();}
		}
		
		// CONTATO - ATUALIZA OU INSERE NOVOS
		if(!empty($_POST['prospeccoes']) && is_array($_POST['prospeccoes']))
		{
			//LIMPA ARRAY
			foreach($_POST['prospeccoes'] as $item => $valor) 
			{
				$prospeccoes_filtrado[$item] = array_filter($valor);
			}
			//
			foreach(array_filter($prospeccoes_filtrado) as $item => $valor) 
			{
				if(isset($valor['epr_id']))
				{
					//INVERTE DATA
					if(isset($valor['epr_data']))
					{
						$data_nova = implode("-",array_reverse(explode("/",$valor['epr_data'])));
						unset($valor['epr_data']);
						$valor['epr_data'] = $data_nova;
					}
					//
					
					$valor2 = $valor;
					unset($valor2['epr_id']);
					
					$sql = "UPDATE cadastro_empresas_prospeccoes SET ".bindFields($valor2)." WHERE epr_id = :epr_id";
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
					if(isset($valor['epr_data']))
					{
						$data_nova = implode("-",array_reverse(explode("/",$valor['epr_data'])));
						unset($valor['epr_data']);
						$valor['epr_data'] = $data_nova;
					}
					//
					
					$valor['epr_empresa'] = $emp_id;
					$sql = "INSERT INTO cadastro_empresas_prospeccoes SET ".bindFields($valor);
					$stmt = $PDO->prepare($sql);	
					if($stmt->execute($valor))
					{
						//echo "Inserido <br>";
					}
					else{ $erro=1; $err = $stmt->errorInfo();}
				}
			}
		}
		
		
		
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
		unset($_SESSION['action']);
       	$sql = "DELETE FROM cadastro_empresas WHERE emp_id = :emp_id";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':emp_id',$emp_id);
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
                '<img src=../imagens/x.png> Erro ao realizar exclusão.<br>Por favor verifique a exclusão de algum registro relacionado a outra tabela.<br><br>'+
                '<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back(); >');
            </SCRIPT>
            ";
        }
    }
	
    if($pagina == "cadastro_empresas_prospeccoes")
    {
		$sql = "SELECT * FROM cadastro_prospeccoes
				WHERE pro_id = :pro_id";
		$stmt = $PDO->prepare($sql);	
		$stmt->bindParam(':pro_id', $pro_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
		{			
			while($result = $stmt->fetch())
			{
				$pro_nome = $result['pro_nome'];
			}
		}
		echo "
		<form name='form_cadastro_empresas_prospeccoes' id='form_cadastro_empresas_prospeccoes' enctype='multipart/form-data' method='post' action='cadastro_empresas_prospeccoes.php?pagina=cadastro_empresas_prospeccoes&action=editar&emp_id=$emp_id&pro_id=$pro_id$autenticacao'>
			<div class='titulo'> $page &raquo; Prospecções </div>
			<img class='hand' title='Imprimir Relatório' style='float:right; margin:0 5px;' src='../imagens/icon-pdf.png' onclick=javascript:window.open('relatorio_prospeccoes_imprimir.php?emp_id=$emp_id$autenticacao');>
				
			<ul class='nav nav-tabs'>
				<li class='active'><a data-toggle='tab' 	href='#dados_gerais'>Dados Gerais</a></li>				  
			</ul>
			<div class='tab-content'>
					
				<div id='dados_gerais' class='tab-pane fade in active'>
						
					<table align='center' cellspacing='0' width='100%' class='borda_aba'>
						<tr>
							<td align='left'>
								<div id='p_scents_prospeccoes'>
									";
									$sql = "SELECT *, emp2.emp_nome_razao as empresa_prospectada FROM cadastro_empresas_prospeccoes 
											LEFT JOIN cadastro_empresas ON cadastro_empresas.emp_id = cadastro_empresas_prospeccoes.epr_empresa
											LEFT JOIN cadastro_empresas emp2 ON emp2.emp_id = cadastro_empresas_prospeccoes.epr_empresa_prospectada
											LEFT JOIN cadastro_prospeccoes ON cadastro_prospeccoes.pro_id = cadastro_empresas_prospeccoes.epr_icone
											LEFT JOIN cadastro_empresas_contatos ON cadastro_empresas_contatos.ctt_id = cadastro_empresas_prospeccoes.epr_contato
											WHERE epr_empresa = :epr_empresa AND epr_icone = :epr_icone";
									$stmt = $PDO->prepare($sql);	
									$stmt->bindParam(':epr_empresa', $emp_id);
									$stmt->bindParam(':epr_icone', $pro_id);
									$stmt->execute();
									$rows = $stmt->rowCount();
									if($rows > 0)
									{
										$x=0;
										while($result = $stmt->fetch())
										{
											$x++;
											$epr_contato 		= $result['epr_contato'];
											$epr_empresa_prospectada 		= $result['epr_empresa_prospectada'];
											$empresa_prospectada 		= $result['empresa_prospectada'];
											$ctt_nome 			= $result['ctt_nome'];
											$epr_data 			= implode("/",array_reverse(explode("-",$result['epr_data'])));
											$epr_breve_historico= $result['epr_breve_historico'];		
											echo "
											<div class='bloco_prospeccoes'>
												<input type='hidden' name='prospeccoes[$x][epr_id]' id='epr_id' value='".$result['epr_id']."'>
												<input type='hidden' name='prospeccoes[$x][epr_usuario]' id='epr_usuario' value='".$result['epr_usuario']."'>
												"; if($x > 1){ echo "<br><br><hr><p>";}else{ echo "<br>";}  echo "
												<p><label>Prospecção:</label> <input  style='border:none;' value='$pro_nome' placeholder='Prospecção'  readonly/>
												<p><label>Responsável:</label> <input style='border:none;' id='usu_nome' value='".$_SESSION['n']."' placeholder='Responsável'  readonly/>
												<p><label>Empresa:</label> 
												<select class='epr_empresa_prospectada' name='prospeccoes[$x][epr_empresa_prospectada]' >
													<option value='$epr_empresa_prospectada' >$empresa_prospectada</option>
													"; 
													$sql = "SELECT * FROM agenda_gerenciar
															INNER JOIN cadastro_empresas ON cadastro_empresas.emp_id =  agenda_gerenciar.age_empresa
															ORDER BY emp_fantasia";
													$stmt_cat = $PDO->prepare($sql);
													$stmt_cat->execute();
													while($result_cat = $stmt_cat->fetch())
													{
														echo "<option value='".$result_cat['emp_id']."'>".$result_cat['emp_fantasia']."</option>";
													}
													echo "
												</select>
												<p><label>Contato:</label> <select name='prospeccoes[$x][epr_contato]' id='epr_contato'>
																			<option value='$epr_contato'>$ctt_nome</option>
																			"; 
																			$sql = "SELECT * FROM cadastro_empresas_contatos 
																					WHERE ctt_empresa = :ctt_empresa
																					ORDER BY ctt_nome ";
																			$stmt_contato = $PDO->prepare($sql);
																			$stmt_contato->bindParam(":ctt_empresa", $emp_id);
																			$stmt_contato->execute();
																			while($result_contato = $stmt_contato->fetch())
																			{
																				echo "<option value='".$result_contato['ctt_id']."'>".$result_contato['ctt_nome']."</option>";
																			}
																			echo "
																		</select>
												<p><label>Data:</label> <input name='prospeccoes[$x][epr_data]' id='epr_data' value='$epr_data' class='datepicker' placeholder='Data' />
												<p><label>Breve Histórico:</label> <input name='prospeccoes[$x][epr_breve_historico]' id='epr_breve_historico' value='$epr_breve_historico' placeholder='Breve Histórico' />
												<p><img src='../imagens/icon-add.png' id='addProspeccao' title='Adicionar +' class='botao_dinamico'> <img src='../imagens/icon-rmv.png' id='remProspeccao' title='Remover' class='botao_dinamico'>
											</div>
											";											
										}
									}
									else
									{
										echo "
										<div class='bloco_prospeccoes'>
											<br><input type='hidden' name='prospeccoes[1][epr_empresa]' id='epr_empresa' value='$emp_id'>
											<input type='hidden' name='prospeccoes[1][epr_icone]' id='epr_icone' value='$pro_id'>
											<input type='hidden' name='prospeccoes[1][epr_usuario]' id='epr_usuario' value='".$_SESSION['usuario_id']."'>
											<p><label>Prospecção:</label> <input  style='border:none;' id='pro_nome' value='$pro_nome' placeholder='Prospecção'  readonly/>
											<p><label>Responsável:</label> <input  style='border:none;' id='usu_nome' value='".$_SESSION['n']."' placeholder='Responsável'  readonly/>
											<p><label>Empresa:</label> 
											<select name='prospeccoes[1][epr_empresa_prospectada]' class='epr_empresa_prospectada' >
													<option value='' >Empresa</option>
													"; 
													$sql = "SELECT * FROM agenda_gerenciar
															INNER JOIN cadastro_empresas ON cadastro_empresas.emp_id =  agenda_gerenciar.age_empresa
															ORDER BY emp_fantasia";
													$stmt_cat = $PDO->prepare($sql);
													$stmt_cat->execute();
													while($result_cat = $stmt_cat->fetch())
													{
														echo "<option value='".$result_cat['emp_id']."'>".$result_cat['emp_fantasia']."</option>";
													}
													echo "
												</select>
											<p><label>Contato:</label> <select name='prospeccoes[1][epr_contato]' id='epr_contato'>
																	<option value='$epr_contato'>Contato</option>
																	"; 
																	$sql = "SELECT * FROM cadastro_empresas_contatos 
																			WHERE ctt_empresa = :ctt_empresa
																			ORDER BY ctt_nome ";
																	$stmt_contato = $PDO->prepare($sql);
																	$stmt_contato->bindParam(":ctt_empresa", $emp_id);
																	$stmt_contato->execute();
																	while($result_contato = $stmt_contato->fetch())
																	{
																		echo "<option value='".$result_contato['ctt_id']."'>".$result_contato['ctt_nome']."</option>";
																	}
																	echo "
																</select>
											<p><label>Data:</label> <input name='prospeccoes[1][epr_data]' id='epr_data' value='$epr_data' class='datepicker' placeholder='Data' />
											<p><label>Breve Histórico:</label> <input name='prospeccoes[1][epr_breve_historico]' id='epr_breve_historico' value='$epr_breve_historico' placeholder='Breve Histórico' />
											<p><img src='../imagens/icon-add.png' id='addProspeccao' title='Adicionar +' class='botao_dinamico'>
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
			<!--<input type='button' id='bt_cadastro_empresas_prospeccoes_sair' value='Salvar e Sair' />&nbsp;&nbsp;&nbsp;&nbsp; -->
			<input type='button' id='bt_cadastro_empresas_prospeccoes_a' onclick='alteraActionProspeccoes(".$emp_id.",".$pro_id.")' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
			<input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_empresas.php?pagina=cadastro_empresas_editar&emp_id=$emp_id$autenticacao'; value='Cancelar'/></center>
			</center>
		</form>
		";
    }    
	?>
    </div>
</div>
<?php
include('../mod_rodape/rodape.php');
?>
</body>
</html>
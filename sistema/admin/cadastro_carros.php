<?php
session_start (); 
$pagina_link = 'cadastro_carros';
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Audipam - Sistema Administrativo Integrado</title>
<meta name="author" content="MogiComp">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="../imagens/favicon.ico">
<link href="../css/style.css" rel="stylesheet" type="text/css" />
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
    $page = "Cadastros &raquo; <a href='cadastro_carros.php?pagina=cadastro_carros".$autenticacao."'>Carros</a>";
	$car_id = $_GET['car_id'];
	$car_marca = $_POST['car_marca'];
	$car_descricao = $_POST['car_descricao'];
	$car_ano = $_POST['car_ano'];
	$car_cor = $_POST['car_cor'];
	$car_placa = $_POST['car_placa'];
	$car_chassi = $_POST['car_chassi'];
	$car_renavam = $_POST['car_renavam'];
	$car_data_compra = implode("-",array_reverse(explode("/",$_POST['car_data_compra'])));if($car_data_compra == ''){$car_data_compra = null;} 
	$car_status = $_POST['car_status'];
	$dados = array(
		'car_marca' 		=> $car_marca,
		'car_descricao' 	=> $car_descricao,
		'car_ano' 			=> $car_ano,
		'car_cor' 			=> $car_cor,
		'car_placa'			=> $car_placa,
		'car_chassi' 		=> $car_chassi,
		'car_renavam' 		=> $car_renavam,
		'car_data_compra' 	=> $car_data_compra,
		'car_status' 		=> $car_status
	);
	if($action == "adicionar")
    {
        $sql = "INSERT INTO cadastro_carros SET ".bindFields($dados);
		$stmt = $PDO->prepare($sql);	
        if($stmt->execute($dados))
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
        $sql = "UPDATE cadastro_carros SET ".bindFields($dados)." WHERE car_id = :car_id ";
		$stmt = $PDO->prepare($sql); 
		$dados['car_id'] =  $car_id;
		if($stmt->execute($dados))
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
                '<img src=../imagens/x.png> Erro ao alterar dados.<br><br>'+
                '<input value=\' Ok \' type=\'button\' onclick=javascript:window.history.back();>');
            </SCRIPT>
            ";
        }
    }
    
    if($action == 'excluir')
    {
       	$sql = "DELETE FROM cadastro_carros WHERE car_id = :car_id";
		$stmt = $PDO->prepare($sql);
		$stmt->bindParam(':car_id',$car_id);
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
    if($action == 'ativar')
    {
        $sql = "UPDATE cadastro_carros SET car_status = :car_status WHERE car_id = :car_id ";
        $stmt = $PDO->prepare($sql);
		$stmt->bindValue(':car_status',1);
        $stmt->bindParam(':car_id',$car_id);
        $stmt->execute();
    }
    if($action == 'desativar')
    {
        $sql = "UPDATE cadastro_carros SET car_status = :car_status WHERE car_id = :car_id ";
        $stmt = $PDO->prepare($sql);
		$stmt->bindValue(':car_status',0);
        $stmt->bindParam(':car_id',$car_id);
        $stmt->execute();
    }
    $num_por_pagina = 10;
    if(!$pag){$primeiro_registro = 0; $pag = 1;}
    else{$primeiro_registro = ($pag - 1) * $num_por_pagina;}
    $sql = "SELECT * FROM cadastro_carros 
            ORDER BY car_id ASC
            LIMIT :primeiro_registro, :num_por_pagina ";
  	$stmt = $PDO->prepare($sql);
	$stmt->bindParam(':primeiro_registro', 	$primeiro_registro);
	$stmt->bindParam(':num_por_pagina', 	$num_por_pagina);
	$stmt->execute();
    $rows = $stmt->rowCount();
    if($pagina == "cadastro_carros")
    {
        echo "
		<div class='titulo'> $page  </div>
		<div id='botoes'><input value='Novo Carro' type='button' onclick=javascript:window.location.href='cadastro_carros.php?pagina=cadastro_carros_adicionar".$autenticacao."'; /></div>
		";
		if ($rows > 0)
		{
			echo "
			<table align='center' width='100%' border='0' cellspacing='0' cellpadding='10' class='bordatabela'>
				<tr>
					<td class='titulo_first'>Marca</td>
					<td class='titulo_tabela'>Descrição</td>
					<td class='titulo_tabela'>Ano/Modelo</td>
					<td class='titulo_tabela'>Placa</td>
					<td class='titulo_tabela'>Data Compra</td>
					<td class='titulo_tabela' align='center'>Status</td>
					<td class='titulo_last' align='center'>Gerenciar</td>
				</tr>";
				$c=0;
				 while($result = $stmt->fetch())
				{
					$car_id 			= $result['car_id'];
					$car_marca 			= $result['car_marca'];
					$car_descricao 		= $result['car_descricao'];
					$car_ano 			= $result['car_ano'];
					$car_placa 			= $result['car_placa'];
					$car_data_compra 	= implode("/",array_reverse(explode("-",$result['car_data_compra'])));
					$car_status 		= $result['car_status'];
					
					if ($c == 0){$c1 = "linhaimpar";$c=1;}else{$c1 = "linhapar";$c=0;}  
					echo "
					<script type='text/javascript'>
						jQuery(document).ready(function($) {
					
							// Define any icon actions before calling the toolbar
							$('.toolbar-icons a').on('click', function( event ) {
								$(this).click();
								
							});
							$('#normal-button-$car_id').toolbar({content: '#user-options-$car_id', position: 'top', hideOnClick: true});
							$('#normal-button-bottom').toolbar({content: '#user-options', position: 'bottom'});
							$('#normal-button-small').toolbar({content: '#user-options-small', position: 'top', hideOnClick: true});
							$('#button-left').toolbar({content: '#user-options', position: 'left'});
							$('#button-right').toolbar({content: '#user-options', position: 'right'});
							$('#link-toolbar').toolbar({content: '#user-options', position: 'top' });
						});
					</script>
					<div id='user-options-$car_id' class='toolbar-icons' style='display: none;'>
						";
						if($car_status == 1)
						{
							echo "<a title='Desativar' href='cadastro_carros.php?pagina=cadastro_carros&action=desativar&car_id=$car_id$autenticacao'><img border='0' src='../imagens/icon-ativa-desativa.png'></a>";
						}
						else
						{
							echo "<a title='Ativar' href='cadastro_carros.php?pagina=cadastro_carros&action=ativar&car_id=$car_id$autenticacao'><img border='0' src='../imagens/icon-ativa-desativa.png'></a>";
						}
						echo "
						<a title='Editar' href='cadastro_carros.php?pagina=cadastro_carros_editar&car_id=$car_id$autenticacao'><img border='0' src='../imagens/icon-editar.png'></a>
						<a title='Excluir' onclick=\"
							abreMask(
								'Deseja realmente excluir o carro <b>$car_marca - $car_descricao</b>?<br><br>'+
								'<input value=\' Sim \' type=\'button\' onclick=javascript:window.location.href=\'cadastro_carros.php?pagina=cadastro_carros&action=excluir&car_id=$car_id$autenticacao\';>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
								'<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
							\">
							<img border='0' src='../imagens/icon-excluir.png'></i>
						</a>
					</div>
					";
					echo "<tr class='$c1'>
							  <td>$car_marca</td>
							  <td>$car_descricao</td>
							  <td>$car_ano</td>
							  <td>$car_placa</td>
							  <td>$car_data_compra</td>
							  <td align=center>";
							  if($car_status == 1)
							  {
								echo "<img border='0' src='../imagens/icon-ativo.png' width='15' height='15'>";
							  }
							  else
							  {
								echo "<img border='0' src='../imagens/icon-inativo.png' width='15' height='15'>";
							  }
							  echo "
							  </td>
							  <td align=center><div id='normal-button-$car_id' class='settings-button'><img src='../imagens/icon-cog-small.png' /></div></td>
						  </tr>";
				}
				echo "</table>";
				$variavel = "&pagina=cadastro_carros".$autenticacao."";
				$cnt = "SELECT COUNT(*) FROM cadastro_carros ";
				$stmt = $PDO->prepare($cnt);
				include("../mod_includes/php/paginacao.php");
		}
		else
		{
			echo "<br><br><br>Não há nenhum carro cadastrado.";
		}
    }
    if($pagina == 'cadastro_carros_adicionar')
    {
        echo "	
        <form name='form_cadastro_carros' id='form_cadastro_carros' enctype='multipart/form-data' method='post' action='cadastro_carros.php?pagina=cadastro_carros&action=adicionar&id=$id$autenticacao'>
            <div class='titulo'> $page &raquo; Adicionar  </div>
            <table align='center' cellspacing='0' width='100%'>
                <tr>
                    <td align='left'>
						<div class='quadro'>
							<div class='formtitulo'>Dados Gerais</div>
							<label>Marca:</label> <select name='car_marca' id='car_marca'>
								<option value=''>Marca</option>
								<option value='Audi'>Audi</option>
								<option value='BMW'>BMW</option>
								<option value='Chevrolet'>Chevrolet</option>
								<option value='Citroen'>Citroen</option>
								<option value='Fiat'>Fiat</option>
								<option value='Ford'>Ford</option>
								<option value='Jac'>Jac</option>
								<option value='Honda'>Honda</option>
								<option value='Hyundai'>Hyundai</option>
								<option value='Mercedes-Benz'>Mercedes-Benz</option>
								<option value='Mitsubishi'>Mitsubishi</option>
								<option value='Nissan'>Nissan</option>
								<option value='Peugeot'>Peugeot</option>
								<option value='Renault'>Renault</option>
								<option value='Toyota'>Toyota</option>
								<option value='Volkswagen'>Volkswagen</option>
							</select>
							<p><label>Descrição:</label> <input name='car_descricao' id='car_descricao' placeholder='Descrição'>
							<p><label>Ano/Modelo:</label> <input name='car_ano' id='car_ano' placeholder='Ano/Modelo'>
							<p><label>Cor:</label> <input name='car_cor' id='car_cor' placeholder='Cor'>
							<p><label>Placa:</label> <input name='car_placa' id='car_placa' placeholder='Placa' onkeypress='return mascaraPlaca(this,event);'>
							<p><label>Chassi:</label> <input name='car_chassi' id='car_chassi' placeholder='Chassi'>
							<p><label>Renavam:</label> <input name='car_renavam' id='car_renavam' placeholder='Renavam'>
							<p><label>Data Compra:</label> <input name='car_data_compra' id='car_data_compra' placeholder='Data Compra' onkeypress='return mascaraData(this,event);'>
							<p><label>Status:</label> <input type='radio' name='car_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        	<input type='radio' name='car_status' value='0'> Inativo<br>
                        <br><br>
						</div>
                        <center>
						<div id='erro' align='center'>&nbsp;</div>
                        <input type='button' id='bt_cadastro_carros' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                        <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_carros.php?pagina=cadastro_carros".$autenticacao."'; value='Cancelar'/></center>
                        </center>
                    </td>
                </tr>
            </table>
        </form>
        ";
    }
    
    if($pagina == 'cadastro_carros_editar')
    {
        $sql = "SELECT * FROM cadastro_carros 
				WHERE car_id = :car_id";
        $stmt = $PDO->prepare($sql);	
		$stmt->bindParam(':car_id', $car_id);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows > 0)
        {
			$result = $stmt->fetch();
            $car_marca 			= $result['car_marca'];
			$car_descricao 		= $result['car_descricao'];
			$car_ano 			= $result['car_ano'];
			$car_cor 			= $result['car_cor'];
			$car_placa 			= $result['car_placa'];
			$car_chassi 		= $result['car_chassi'];
			$car_renavam 		= $result['car_renavam'];
			$car_data_compra 	= implode("/",array_reverse(explode("-",$result['car_data_compra'])));
			$car_status 		= $result['car_status'];
			echo "
            <form name='form_cadastro_carros' id='form_cadastro_carros' enctype='multipart/form-data' method='post' action='cadastro_carros.php?pagina=cadastro_carros&action=editar&car_id=$car_id$autenticacao'>
                <div class='titulo'> $page &raquo; Editar</div>
				<table align='center' cellspacing='0' width='100%'>
					<tr>
						<td align='left'>
                			<div class='quadro'>
								<div class='formtitulo'>Dados Gerais</div>
								<label>Marca:</label> 
								<select name='car_marca' id='car_marca'>
									<option value='$car_marca'>$car_marca</option>
									<option value='Audi'>Audi</option>
									<option value='BMW'>BMW</option>
									<option value='Chevrolet'>Chevrolet</option>
									<option value='Citroen'>Citroen</option>
									<option value='Fiat'>Fiat</option>
									<option value='Ford'>Ford</option>
									<option value='Jac'>Jac</option>
									<option value='Honda'>Honda</option>
									<option value='Hyundai'>Hyundai</option>
									<option value='Mercedes-Benz'>Mercedes-Benz</option>
									<option value='Mitsubishi'>Mitsubishi</option>
									<option value='Nissan'>Nissan</option>
									<option value='Peugeot'>Peugeot</option>
									<option value='Renault'>Renault</option>
									<option value='Toyota'>Toyota</option>
									<option value='Volkswagen'>Volkswagen</option>
								</select>
								<p><label>Descrição:</label> <input name='car_descricao' id='car_descricao' value='$car_descricao' placeholder='Descrição'>
								<p><label>Ano/Modelo:</label> <input name='car_ano' id='car_ano' value='$car_ano' placeholder='Ano/Modelo'>
								<p><label>Cor:</label> <input name='car_cor' id='car_cor' value='$car_cor' placeholder='Cor'>
								<p><label>Placa:</label> <input name='car_placa' id='car_placa' value='$car_placa' placeholder='Placa' onkeypress='return mascaraPlaca(this,event);'>
								<p><label>Chassi:</label> <input name='car_chassi' id='car_chassi' value='$car_chassi' placeholder='Chassi'>
								<p><label>Renavam:</label> <input name='car_renavam' id='car_renavam' value='$car_renavam' placeholder='Renavam'>
								<p><label>Data Compra:</label> <input name='car_data_compra' id='car_data_compra' value='$car_data_compra' placeholder='Data Compra' onkeypress='return mascaraData(this,event);'>
								<p><label>Status:</label>";
								if($car_status == 1)
								{
									echo "<input type='radio' name='car_status' value='1' checked> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										  <input type='radio' name='car_status' value='0'> Inativo
										 ";
								}
								else
								{
									echo "<input type='radio' name='car_status' value='1'> Ativo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										  <input type='radio' name='car_status' value='0' checked> Inativo
										 ";
								}
								echo "
							<br><br>
							</div>
							<br><br>
                            <center>
							<div id='erro' align='center'>&nbsp;</div>
                            <input type='button' id='bt_cadastro_carros' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='cadastro_carros.php?pagina=cadastro_carros$autenticacao'; value='Cancelar'/></center>
                            </center>
						</td>
					</tr>
				</table>
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
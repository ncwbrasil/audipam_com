<?php
$pagina = $_GET['pagina'];
if($pagina == 'c')
{

    foreach($_FILES['youse']['tmp_name'] as $key => $tmp_name )
	{
		$nomeArquivo = $_FILES["youse"]["name"][$key];
		$tamanhoArquivo = $_FILES["youse"]["size"][$key];
		$nomeTemporario = $_FILES["youse"]["tmp_name"][$key];
		$caminho = "temp/";
		if(!empty($nomeArquivo))
		{
			if(!file_exists($caminho))
			{
				mkdir($caminho, 0755, true); 
			}
			$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
			$arquivo = $caminho;
			$arquivo .= md5(mt_rand(1,10000).$nomeArquivo).'.'.$extensao;
		}
		if(!empty($nomeArquivo))
		{
			move_uploaded_file($nomeTemporario, ($arquivo));
		}
		$file=fopen($arquivo, 'r'); 
		$carros		=0;
		while(!feof($file))
		{
			$linha = str_replace('"','',trim(fgets($file, 4096)));
			

			$dado = explode(";", $linha);
			
			$pos = strpos($dado[2],',');
			if ($pos === false) 
			{
				echo $linha."<br>";
			}
			else
			{
				$tipo = explode(",",$dado[2]);
				$num = explode(",",$dado[3]);
				$ano = explode(",",$dado[4]);

				$multiplo = 0;

				foreach(array_filter($num) as $key => $val)
				{
					echo $dado[0].';'.$dado[1].';'.$tipo[$key].';'.$num[$key].';'.$ano[$key].'';
					echo "<br>";
					$multiplo++;
				}
				//exit;

				if($multiplo > 0)
				{

				}
				else
				{
					echo $linha."<br>";
				}
				
								
			}
			
			
		}
		fclose($file);
		unlink($arquivo);
	}
   
}
?>

<form name='form_youse' id='form_youse' enctype='multipart/form-data' method='post' action='converter_revog.php?pagina=c'>
    <div class='imports_seguradora'>Youse: </div> <input type='file' name='youse[]' id='youse' multiple /><div id='youse_erro'> </div>
	<input type='submit' value='enviar'>
</form>
<?php




?>
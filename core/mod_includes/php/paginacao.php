<?php
echo "<br>";

$limite = 1;	
$stmt->execute();			
list($total_linhas) = $stmt->fetch();
//list($total_linhas) = mysql_fetch_array(mysql_query($cnt,$conexao));
$total = $total_linhas/$num_por_pagina;

$prox = $pag + 1;
$ant = $pag - 1;
$ultima_pag = ceil($total / $limite);
$penultima = $ultima_pag - 1;  
$adjacentes = 3;

if(strpos($_SERVER['REQUEST_URI'], 'view') !== false)
{
	$posicao = strpos($_SERVER['REQUEST_URI'], 'view');
}
elseif($posicao = strpos($_SERVER['REQUEST_URI'], '?') !== false)
{
	$posicao = strpos($_SERVER['REQUEST_URI'], '?')-4;
}
elseif($posicao == "")
{
	$posicao=9999;
}


$url= substr($_SERVER['REQUEST_URI'],0, $posicao+4); 
//echo $url;
if ($pag>1)
{
  $paginacao = ' <a href="'.$url.'?pag='.$ant.''.$variavel.'"><font color=#000000><img src="../core/imagens/icon-anterior.png" width="16" border="0" ></font></a> ';
}
  
if ($ultima_pag <= 10)
{
  for ($i=1; $i< $ultima_pag+1; $i++)
  {
	if ($i == $pag)
	{
	  $paginacao .= ' <a href="'.$url.'?pag='.$i.''.$variavel.'"> ['.$i.'] </a> ';        
	} else
	{
	  $paginacao .= ' <a href="'.$url.'?pag='.$i.''.$variavel.'"><font color=#000000> '.$i.' </font></a> ';  
	}
  }
}
if ($ultima_pag > 10)
{
  if ($pag < 1 + (2 * $adjacentes))
  {
	for ($i=1; $i< 2 + (2 * $adjacentes); $i++)
	{
	  if ($i == $pag)
	  {
		$paginacao .= ' <a  href="'.$url.'?pag='.$i.''.$variavel.'">['.$i.']</a> ';        
	  }
	  else 
	  {
		$paginacao .= ' <a href="'.$url.'?pag='.$i.''.$variavel.'"><font color=#000000>'.$i.'</font></a> ';  
	  }
	}
	$paginacao .= ' ... ';
	$paginacao .= ' <a href="'.$url.'?pag='.$penultima.'"><font color=#000000>'.$penultima.'</font></a> ';
	$paginacao .= ' <a href="'.$url.'?pag='.$ultima_pag.'"><font color=#000000>'.$ultima_pag.'</font></a> ';
  }
  
  elseif($pag > (2 * $adjacentes) && $pag < $ultima_pag - 3)
  {
	$paginacao .= ' <a href="'.$url.'?pag=1"><font color=#000000>1</font></a> ';        
	$paginacao .= ' <a href="'.$url.'?pag=2"><font color=#000000>2</font></a> ... ';  
	for ($i = $pag-$adjacentes; $i<= $pag + $adjacentes; $i++)
	{
	  if ($i == $pag)
	  {
		$paginacao .= ' <a href="'.$url.'?pag='.$i.''.$variavel.'">['.$i.']</a> ';        
	  }
	  else
	  {
		$paginacao .= ' <a href="'.$url.'?pag='.$i.''.$variavel.'"><font color=#000000>'.$i.'</font></a> ';  
	  }
	}
	$paginacao .= ' ...';
	$paginacao .= ' <a href="'.$url.'?pag='.$penultima.'"><font color=#000000>'.$penultima.'</font></a> ';
	$paginacao .= ' <a href="'.$url.'?pag='.$ultima_pag.'"><font color=#000000>'.$ultima_pag.'</font></a> ';
  }
  else 
  {
	$paginacao .= ' <a href="'.$url.'?pag=1"><font color=#000000>1</font></a> ';        
	$paginacao .= ' <a href="'.$url.'?pag=1"><font color=#000000>2</font></a> ... ';  
	for ($i = $ultima_pag - (1 + (2 * $adjacentes)); $i <= $ultima_pag; $i++)
	{
	  if ($i == $pag)
	  {
		$paginacao .= ' <a href="'.$url.'?pag='.$i.''.$variavel.'">['.$i.']</a> ';        
	  } else {
		$paginacao .= ' <a href="'.$url.'?pag='.$i.''.$variavel.'"><font color=#000000>'.$i.'</font></a> ';  
	  }
	}
  }
}
if ($prox <= $ultima_pag && $ultima_pag > 2)
{
  $paginacao .= ' <a href="'.$url.'?pag='.$prox.''.$variavel.'"><font color=#000000><img src="../core/imagens/icon-proxima.png" width="16" border="0"></font></a> ';
}

echo "<center>$paginacao</center>";
						
?>
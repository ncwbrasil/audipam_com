<?php
$patch = "../uploads/images/";
$diretorio = dir($patch);

$response = new StdClass;
$files = array();
$c = 0;
while($arquivo = $diretorio -> read()){

      $files[$c]['url'] = "https://audipam.com.br/proclegis/uploads/images/".$arquivo;    
      $files[$c]['thumb'] = "https://audipam.com.br/proclegis/uploads/images/".$arquivo;      
      $c++;       
}

echo stripslashes(json_encode($files));

  ?>
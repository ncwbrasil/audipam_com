<?php
include_once("../core/mod_includes/php/connect_sistema.php");
include_once("../core/mod_includes/php/funcoes.php");
require_once '../core/mod_includes/php/lib/WideImage.php';

$sag_id = $_GET['sag_id'];

//$ds          = DIRECTORY_SEPARATOR;  //1
 
$storeFolder = "../core/uploads/ensaios/".$sag_id ."";
 
if (!empty($_FILES)) 
{
    if(!file_exists($storeFolder)){mkdir($storeFolder, 0755, true);}

    $tempFile = $_FILES['file']['tmp_name'];          //3             
    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    //$targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;  //4
    $targetPath = $storeFolder."/";  //4
    $targetFile =  $targetPath.date("YmdHis")."_". limpaStringAll(str_replace($ext,"",$_FILES['file']['name'])).'.'.$ext;  //5
 
    move_uploaded_file($tempFile,$targetFile); //6

    $imnfo = getimagesize($targetFile);
    $img_w = $imnfo[0];	  // largura
    $img_h = $imnfo[1];	  // altura
    if($img_w > 600 || $img_h > 600)
    {
        $image = WideImage::load($targetFile);
        $image = $image->resize(600, 600);
        $image->saveToFile($targetFile);
    }
    $sql = "INSERT INTO social_agenda_fotos SET 
            fot_ensaio 	 = :fot_ensaio,
            fot_foto 	 = :fot_foto
            
             ";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':fot_ensaio',$sag_id);
    $stmt->bindParam(':fot_foto',$targetFile);
    if($stmt->execute()){}else{$erro=1; $err = $stmt->errorInfo();}
}
?>     
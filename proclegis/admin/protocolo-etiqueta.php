<?php
$pagina_link = 'protocolo_gerais';
include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");
require '../../vendor/autoload.php'; 

?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include("header.php");?> 
    <script>
        function printDiv(selector) {
            $('.right').hide();
            //$('body').css({display:'none'});
            var content = $(selector).clone();
            //$('body').before(content);
            window.print();
            $('.right').show();
            //$(selector).first().remove();
            //$('body').css({display:''});
        }
    </script>
</head>
        
<body style='background: #FFF;'>
    <?php
    # PEGA NOME SISTEMA E CLIENTE #
    $sql = "SELECT * FROM cadastro_clientes
            INNER JOIN cadastro_sistemas ON cadastro_sistemas.sis_id = cadastro_clientes.cli_sistema 
            WHERE cli_id = :cli_id";
    $stmt = $PDO->prepare( $sql );
    $stmt->bindParam( ':cli_id', $_SESSION['cliente_id']);       
    $stmt->bindValue( ':cli_status', 	1 );
    $stmt->execute();
    $rows = $stmt->rowCount();	

    if($rows > 0)
    {
        $result = $stmt->fetch();	
        $sis_logo 	= $result['sis_logo'];	
        $cli_nome 	= $result['cli_nome'];
        $cli_foto 	= $result['cli_foto'];		
      
    }
    
        
    if(isset($_GET['id'])){$id = $_GET['id'];}
    
  
    $sql = "SELECT *
            FROM protocolo_gerais 
            WHERE protocolo_gerais.id = :id 			
    ";
    $stmt = $PDO_PROCLEGIS->prepare($sql);    
    $stmt->bindParam(':id', 	$id);
        
    $stmt->execute();
    $rows = $stmt->rowCount();
    if($rows > 0)
    {
        $result = $stmt->fetch();
        ?>
        <input type='button' class='right' style='margin-right:10px' value=' Imprimir ' onclick='printDiv(".imprimir");'>
    
        <div  id='prot-etiqueta'>
            <div class='topo imprimir' >
                <div class='borda'>
                <?php 
                
                echo "<p class='bold'>".$cli_nome."</p>";
                $generator = new Picqer\Barcode\BarcodeGeneratorJPG();
                //file_put_contents('../uploads/protocolo_barcodes/1.jpg', str_pad($result['numero'],6,"0",STR_PAD_LEFT)."/".$result['ano']);
                file_put_contents('../uploads/protocolo_barcodes/'.$id.'.jpg', $generator->getBarcode(str_pad($result['numero'],6,"0",STR_PAD_LEFT)."/".$result['ano'], $generator::TYPE_CODE_128,2,60));
                //echo "<center>".$generator->getBarcode(str_pad($result['numero'],6,"0",STR_PAD_LEFT)."/".$result['ano'], $generator::TYPE_CODE_128,2,50)."</center><p>";
                echo "<center><img style='width:200px' src='../uploads/protocolo_barcodes/".$id.".jpg'></center><p>";
                
                echo "Protocolo : ".str_pad($result['numero'],6,"0",STR_PAD_LEFT)."/".$result['ano']."<p>";
                echo "Data: ".reverteData(substr($result['data_cadastro'],0,10))." às ".substr($result['data_cadastro'],11,5)."<p>";
                echo "Natureza: ".$result['natureza']."<p>";
                echo "Interessado: ".$result['interessado'];

                ?> 
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    <script>
   
    </script>
</body>
</html>
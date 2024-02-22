<?php
$pagina_link = 'cadastro_proposicoes';
include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include("header.php");?> 
</head>
<body id='recibo' style='background: #FFF;'>

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
  
  
    $sql = "SELECT *, cadastro_proposicoes.id as id
                , cadastro_proposicoes.ementa as ementa
                , aux_proposicoes_tipos.descricao as tipo 
            FROM cadastro_proposicoes 
            LEFT JOIN aux_proposicoes_tipos ON aux_proposicoes_tipos.id = cadastro_proposicoes.tipo
            LEFT JOIN aux_autoria_autores ON aux_autoria_autores.id = cadastro_proposicoes.autor
            LEFT JOIN cadastro_proposicoes_status as h1 ON h1.proposicao = cadastro_proposicoes.id
            WHERE cadastro_proposicoes.id = :id AND h1.id = (SELECT MAX(h2.id) FROM cadastro_proposicoes_status h2 where h2.proposicao = h1.proposicao) AND status = :status OR status = :status2 ";
    $stmt = $PDO_PROCLEGIS->prepare($sql);    
    $stmt->bindParam(':id', 	$id);
    $stmt->bindValue(':status', 'Enviado');
    $stmt->bindValue(':status2', 'Recebido');
    $stmt->execute();
    $rows = $stmt->rowCount();
    if($rows > 0)
    {
        $result = $stmt->fetch();
        ?>
        <div class='topo'>
            <div class='logo'>
                <img src='../<?php echo $cli_foto;?>'  alt="Logo" /> 
            </div> 
            <div class='nome'>
                <p class='titulo' style='margin:0 0 5px 0; text-transform:uppercase; border:0; font-size:30px;'><?php echo $cli_nome;?></p>
                
                Sistema Processo Legislativo
            </div>   
            <div class='title'>
                RECIBO DE ENVIO DE PROPOSIÇÃO
            </div>
            <div class='corpo'>
                <table cellpadding='6'>
                    <tr>
                        <td width='25%' align='right' class='bold'>Código do documento:</td>
                        <td width='25%' align='left'><?php echo md5($id);?></td>
                        <td width='25%' align='right'  class='bold'>Tipo Proposição:</td>
                        <td width='25%' align='left'><?php echo $result['tipo'];?></td>
                    </tr>
                    <tr>
                        <td width='25%' align='right' class='bold'>Autor:</td>
                        <td width='25%' align='left'><?php echo $result['nome'];?></td>
                        <td width='25%' align='right'  class='bold'>Data Envio:</td>
                        <td width='25%' align='left'><?php echo reverteData(substr($result['data_cadastro'],0,10))." ".substr($result['data_cadastro'],11,5) ;?></td>
                    </tr>
                    <tr>
                        <td width='25%' align='right' class='bold'>Descrição:</td>
                        <td width='25%' align='left' colspan='3'><?php echo $result['ementa'];?></td>                        
                    </tr>
                </table>
                
            </div>
            <p><br>
            <center>Declaro que o conteúdo do texto impresso em anexo é idêntico ao conteúdo enviado eletronicamente por meio do sistema para esta proposição.</center>
            <p><br><br><br>
            <p style='border-bottom:1px solid #666; width:50%; align:center; margin:0 auto;'> </p><br>
            <?php echo $result['nome'];?>
        </div>
        <?php
    }
    ?>
</body>
</html>
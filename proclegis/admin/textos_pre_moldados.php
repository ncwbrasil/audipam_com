<?php 
$pagina_link = 'textos_pre_moldados';
include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include('header.php'); ?>
</head>
<body>	
	<main class="cd-main-content">
    	<!--MENU-->
		<?php include("../mod_menu/menu.php"); ?>
        
        <!--CONTEUDO CENTRO-->
		<div class="content-wrapper">
            <div class='mensagem'></div>
            <?php
            $page = "<a href='textos_pre_moldados/view'>Textos pré-moldados</a>";
            $id = $_GET['tv_id'];

            $txt_descricao = $_POST['txt_descricao'];    
            $txt_titulo = $_POST['txt_titulo'];    
            
            $dados = array(
                'txt_titulo' 		=> $txt_titulo,
                'txt_descricao' 		=> $txt_descricao
                );
        
            if($action == "adicionar")
            {                       
            
                $sql = "INSERT INTO textos_pre_moldados SET ".bindFields($dados);
                $stmt = $PDO_PROCLEGIS->prepare($sql);	
                if($stmt->execute($dados))
                {		
                    $id = $PDO_PROCLEGIS->lastInsertId();                               				
                
                    if($erro != 1)
                    {
                        log_operacao($id, $PDO_PROCLEGIS);        
                        ?>
                        <script>
                            mensagem("Ok","<i class='fas fa-check-circle'></i> Operação realizada com sucesso!");
                        </script>
                        <?php
                    }
                    else
                    {
                        ?>
                        <script>
                            mensagem("X","<i class='fa fa-exclamation-circle'></i> Falha ao comunicar com o equipamento. Error: <?php echo $return['msg'];?>");
                        </script>
                        <?php 
                    }
                
                }
                else
                {
                    ?>
                    <script>
                        mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao realizar operação.");
                    </script>
                    <?php 
                }	
            }

            if($pagina == "view")
            {

                echo"<div class='titulo'> $page  </div>  ";
                $sql = "SELECT * FROM textos_pre_moldados ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);    
                $stmt->execute();
                $rows = $stmt->rowCount();
                    echo "
                    <div id='botoes'>
                        <div class='g_adicionar' title='Adicionar' onclick='verificaPermissao(".$permissoes["add"].",\"textos_pre_moldados/add\");'><i class='fas fa-plus'></i></div>   
                    </div>
                    ";

                    if ($rows > 0)
                    {
                            while($result = $stmt->fetch())
                            {

                                echo "
                                <a href='textos_pre_moldados/visualizar/".$result['txt_id']."'>
                                    <div class='modulos'>
                                        ".$result['txt_titulo']."
                                    </div>
                                </a>
                                ";    

                            }
                    }
            }  

            if($pagina == 'visualizar')
            {      		
                $sql = "SELECT * FROM textos_pre_moldados 
                        WHERE txt_id = :txt_id ";
                $stmt = $PDO_PROCLEGIS->prepare($sql);            
                $stmt->bindParam(':txt_id', $id);
                $stmt->execute();
                $rows = $stmt->rowCount();
                
                if($rows > 0)
                {
                    $result = $stmt->fetch();
                    
                    echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='textos_pre_moldados/view/exportar/$id' autocomplete='off'>
                        <div class='titulo'> $page &raquo; Exportar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                                                 
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Título:</label> <input name='txt_titulo' id='descricao' value='".$result['txt_titulo']."' readonly placeholder='Título'  class='obg'>                                
                                <p><label>Descrição:</label> <textarea name='txt_decricao' id='descricao' class='obg'>".$result['txt_descricao']."</textarea>                                
                            </div>                                                                        				
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Exportar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='textos_pre_moldados/view'; value='Cancelar'/></center>
                            </center>
                        </div>
                    </form>
                    ";
                }
            }
            if($pagina == 'add')
            {
                echo "
                    <form name='form' id='form' enctype='multipart/form-data' method='post' action='textos_pre_moldados/view/adicionar' autocomplete='off'>
                        <div class='titulo'> $page &raquo; Exportar </div>
                        <ul class='nav nav-tabs'>
                            <li class='active'><a data-toggle='tab' href='#dados_gerais'>Dados Gerais</a></li>                                                 
                        </ul>
                        <div class='tab-content'>
                            <div id='dados_gerais' class='tab-pane fade in active'>
                                <p><label>Título:</label> <input name='txt_titulo' id='descricao' placeholder='Título'  class='obg'>                                
                                <p><label>Descrição:</label> <textarea name='txt_descricao' id='descricao' class='obg'></textarea>                                
                            </div>                                                                        				
                            <center>
                            <div id='erro' align='center'>&nbsp;</div>
                            <input type='submit' value='Salvar' />&nbsp;&nbsp;&nbsp;&nbsp; 
                            <input type='button' id='botao_cancelar' onclick=javascript:window.location.href='textos_pre_moldados/view'; value='Cancelar'/></center>
                            </center>
                        </div>
                    </form>
                    ";
            }	 
            
            if($action == 'exportar'){
                
               //Incluir a biblioteca PhpWord usando o Composer
                include '../../vendor/autoload.php';

                //Instanciar o PhpWord
                $phpWord = new \phpOffice\PhpWord\PhpWord();

                $section = $phpWord->addSection();

                $fontePersonalizada = 'fonteTahoma';
                $phpWord->addFontStyle(
                    $fontePersonalizada,
                    array('name' => 'Tahoma', 'size' => 10, 'color' => '1B2232', 'bold' => true)
                );

                $section->addText(
                    'Curabitur iaculis lacinia nulla, ac blandit quam. Morbi accumsan enim sed elit tristique tempor. Sed quis facilisis eros, vel vulputate tellus.', $fontePersonalizada
                );

                $objWriter = \phpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                $objWriter->save('meuprimeiroarquivo.docx');
            }
            ?>
       	</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
</body>
</html>

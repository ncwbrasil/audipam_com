<?php
$pagina_link = 'docadm_documentos';
include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include("header.php");?> 
    <style>
        * {border :1px solid #CCC}
</style>
</head>
<body>
	<main class="cd-main-content" >    
    	<!--MENU-->
		<?php include("../mod_menu/menu.php"); ?>
        
        <!--CONTEUDO CENTRO-->
		<div class="content-wrapper">
            <div class='mensagem'></div>
            <?php                     
            $page = "Cadastro &raquo; <a href='docadm_documentos/view'>Documentos administrativos</a>";
            if(isset($_GET['id'])){$id = $_GET['id'];}
            

            // PARA MONTAR BARRA DE NAVEGAÇÃO            
            $sql = "SELECT aux_administrativo_tipo_documento.nome as tipo_nome,
                            aux_administrativo_tipo_documento.id as tipo_id,
                            docadm_documentos.numero as numero,
                            docadm_documentos.ano as ano
                    FROM docadm_documentos 
                    LEFT JOIN aux_administrativo_tipo_documento ON aux_administrativo_tipo_documento.id = docadm_documentos.tipo  
                    WHERE docadm_documentos.id = :id";
            $stmt_nome = $PDO_PROCLEGIS->prepare($sql);    
            $stmt_nome->bindParam(':id', 	$id); 
            $stmt_nome->execute();
            $rows_nome = $stmt_nome->rowCount();
            $result_nome = $stmt_nome->fetch();
            $tipo_id = $result_nome['tipo_id'];
            $tipo_nome = $result_nome['tipo_nome'];
            $numero = $result_nome['numero'];
            $ano = $result_nome['ano'];
                                                       
            echo "
            <div class='titulo'> $page  &raquo; <a href='docadm_documentos/view/?fil_tipo=$tipo_id'> $tipo_nome </a> &raquo; <a href='docadm_documentos/exib/".$id."#tramitacao'>".$numero."/".$ano." </a>  &raquo; Pasta Virtual</div>
            ";
            
            ?>
            <div style='padding:10px; float:left; width:25%; background: #323639; height:100%;'>
                <div id="jstree" style='padding:10px; background: #FFF; height:100%;' >
                    <?php
                    $sql = "SELECT * FROM docadm_documentos 
                            WHERE docadm_documentos.id = :id                         
                            ";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);    
                    $stmt->bindParam(':id', 	$id);                
                    $stmt->execute();
                    $rows = $stmt->rowCount();
                    if($rows > 0)
                    {
                        $result = $stmt->fetch();

                        $sql = "SELECT *, docadm_documentos_tramitacao.id as id_tramitacao
                                        , aux_administrativo_status_tramitacao.nome as nome_status                                                   
                                        , cadastro_usuarios.usu_nome as nome_responsavel 
                                FROM docadm_documentos_tramitacao 
                                LEFT JOIN aux_materias_unidade_tramitacao t1 ON t1.id = docadm_documentos_tramitacao.unidade_origem
                                LEFT JOIN aux_materias_unidade_tramitacao t2 ON t2.id = docadm_documentos_tramitacao.unidade_destino                                        
                                LEFT JOIN aux_administrativo_status_tramitacao ON aux_administrativo_status_tramitacao.id = docadm_documentos_tramitacao.status_tramitacao  
                                LEFT JOIN cadastro_usuarios ON cadastro_usuarios.usu_id = docadm_documentos_tramitacao.responsavel
                                WHERE documento = :documento AND docadm_documentos_tramitacao.anexo IS NOT NULL
                                ORDER BY docadm_documentos_tramitacao.data_tramitacao ASC
                                ";
                        $stmt_tra = $PDO_PROCLEGIS->prepare($sql);    
                        $stmt_tra->bindParam(':fil_ementa1', 	$fil_ementa1);
                        $stmt_tra->bindParam(':documento', 	$id);                                    
                        $stmt_tra->execute();
                        $rows_tra = $stmt_tra->rowCount();
                        if($rows_tra > 0)
                        {
                            echo "<ul>";
                            while($result_tra = $stmt_tra->fetch())
                            {
                                echo "                            
                                <li>".$tipo_nome."
                                <ul>
                                    <li data-jstree='{\"icon\":\"far fa-file-pdf\"}'>
                                    <a href='".$result_tra['anexo']."' target='_blank'>".$result_tra['nome_anexo']."</a>
                                    </li>
                                </ul>
                                </li>                            
                                ";
                            }
                            echo "</ul>";
                        }
                    }
                    ?>
                </div> 
            </div>
            <div  style='float:right; width:75%; height:100%; border: 10px solid #323639; padding:0; background: #FFF;'>  
                <object name="myiframe" id="myiframe" data="" type="application/pdf" width="100%" height="650"> 
                    <p>Parece que você não tem um leitor PDF instalado em seu navegador.<p>
                    <a id="link" href="">Clique aqui</a> para fazer download do documento</p>  
                </object>     
            </div>         
        </div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
 
    <!-- 4 include the jQuery library -->
    <script src="../../core/mod_includes/js/jstree/dist/libs/jquery.js"></script>
    <!-- 5 include the minified jstree source -->
    <script src="../../core/mod_includes/js/jstree/dist/jstree.min.js"></script>
    <script>
    $(function () {
        // 6 create an instance when the DOM is ready
        $('#jstree').jstree({
            "core" : {
                "themes" : {
                    "variant" : "large"
                }
            },
            "checkbox" : {
                "keep_selected_style" : false
            },
            "plugins" : ["themes","html_data","ui"],
        });
        
        // 7 bind to events triggered on the tree
        $('#jstree').on("changed.jstree", function (e, data) {
        console.log(data.selected);
        });
        // 8 interact with the tree - either way is OK
        $('button').on('click', function () {
        $('#jstree').jstree(true).select_node('child_node_1');
        $('#jstree').jstree('select_node', 'child_node_1');
        $.jstree.reference('#jstree').select_node('child_node_1');
        });
        
        $("#jstree li").on("click", "a", 
            function() {
                if($(this).attr("href") != "#")
                {
                    $('#myiframe').attr('data', $(this).attr("href"));
                    $('#link').attr('href', $(this).attr("href"));
                }
              
            }
        );
    });
    </script>
</body>
</html>
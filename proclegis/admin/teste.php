<?php
$pagina_link = 'aux_materias';
include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include("header.php");?> 
</head>
<body>
	<main class="cd-main-content">    
    	<!--MENU-->
		<?php include("../mod_menu/menu.php"); ?>
        
        <!--CONTEUDO CENTRO-->
		<div class="content-wrapper">
            <?php 
            $page = "Auxiliares &raquo; <a href='aux_materias/view'>Matérias Legislativas</a> &raquo; <a href='aux_materias_orgaos/view'>Órgãos</a>";
            
            echo "
            <div class='titulo'> $page  </div>
            ";

            ?>
            <div id="jstree">
            <!-- in this example the tree is populated from inline HTML -->
            <ul>
            <li>Root node 1
                <ul>
                    <li id="child_node_1">Child node 1
                        <ul>
                            <li data-jstree='{"icon":"far fa-file-pdf"}'>
                            <a href='aaa/aaa.php' target='_blank'>aaaa</a>
                            </li>
                        </ul>
                    </li>
                    <li>Child node 2</li>
                    
                    
                    
                </ul>
            </li>
            <li>Root node 2</li>
            </ul>
            </div>
        <button>demo button</button>

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
                    $('#myiframe').attr('src', "http://www.google.com");
                }
            );
        });
        </script>
    	
        
        <iframe name="myiframe" id="myiframe" src="a.pdf">


        </div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
</body>
</html>
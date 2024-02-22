<link rel="stylesheet" type="text/css" href="../mod_menu/css/demo.css" />
<link rel="stylesheet" type="text/css" href="../mod_menu/css/component.css" />
<script src="../../mod_menu/js/modernizr.custom.js"></script>

<ul id="gn-menu" class="gn-menu-main">
    <li class="gn-trigger">
        <i class="fas fa-align-justify"></i>
        <nav class="gn-menu-wrapper">
            <div class="gn-scroller">
                <ul class="gn-menu">
                    
                    <li>
                        <a href="dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <?php
                   
                        if(isset($_SESSION['webmaster']['audipam']))
                        {
                            $query_setor = " 1 = 1 ";
                        }
                        else
                        {
                            $query_setor = " sep_setor = :sep_setor ";        
                        }
                        
                        $sql = "SELECT * FROM admin_modulos
                                LEFT JOIN ( admin_setores_permissoes 
                                    LEFT JOIN ( admin_setores 
                                        LEFT JOIN cadastro_usuarios 
                                        ON cadastro_usuarios.usu_setor = admin_setores.set_id )
                                    ON admin_setores.set_id = admin_setores_permissoes.sep_setor )
                                ON admin_setores_permissoes.sep_modulo = admin_modulos.mod_id
                                WHERE ".$query_setor."
                                GROUP BY mod_id  
                                ORDER BY mod_ordem ASC
                                ";
                        $stmt = $PDO_PROCLEGIS->prepare($sql);        
                        $stmt->bindParam(':sep_setor', $_SESSION['setor_id'] );        
                        $stmt->execute();
                        $rows = $stmt->rowCount();
                        if($rows > 0)
                        {   
                            $i = 0; 
                            while($result = $stmt->fetch())
                            { 
                                $i++; 
                                $a = strtolower(str_replace("/","",RetirarAcentos($result['mod_nome'])));
                                $b = explode("_",$pagina_link);
                                echo "	
                                    <li>
                                        <a onclick='abreSubmenu($i)'><b class='item-menu' >".$result['mod_img'].' '.$result['mod_nome']."</b></a>
                                        <ul class='gn-submenu' id='$i'>";
                                            $sql_sub = "SELECT * FROM admin_submodulos
                                                        LEFT JOIN admin_modulos ON admin_modulos.mod_id = admin_submodulos.sub_modulo 
                                                        LEFT JOIN (admin_setores_permissoes 
                                                            LEFT JOIN ( admin_setores 
                                                                LEFT JOIN cadastro_usuarios 
                                                                ON cadastro_usuarios.usu_setor = admin_setores.set_id )
                                                            ON admin_setores.set_id = admin_setores_permissoes.sep_setor )
                                                        ON admin_setores_permissoes.sep_submodulo = admin_submodulos.sub_id
                                            
                                                        WHERE  ".$query_setor." AND mod_id = :mod_id  
                                                        GROUP BY sub_id  
                                                        ORDER BY sub_ordem, sub_id ASC
                                                    ";
                                            $stmt_sub = $PDO_PROCLEGIS->prepare($sql_sub);
                                            $stmt_sub->bindParam(':sep_setor', $_SESSION['setor_id'] );						
                                            $stmt_sub->bindParam(':mod_id', $result['mod_id'] );
                                            $stmt_sub->execute();
                                            $rows_sub = $stmt_sub->rowCount();
                                            if($rows_sub > 0)
                                            {
                                                while($result_sub = $stmt_sub->fetch())
                                                {
                                                    if($result_sub['sub_link'] == "cadastro_materias")
                                                    {
                                                        echo "
                                                        <li class='sub'><a href='".$result_sub['sub_link']."/group'>&raquo; ".$result_sub['sub_nome']."</a></li>
                                                        ";
                                                    }
                                                    else
                                                    {
                                                        echo "
                                                        <li class='sub'><a href='".$result_sub['sub_link']."/view'>&raquo; ".$result_sub['sub_nome']."</a></li>
                                                        ";
                                                    }
                                                    
                                                }
                                            }
                                            echo "
                                        </ul>
                                    </li>    
                                ";
                            }
                        }
                    ?>
                </ul>
            </div><!-- /gn-scroller -->
        </nav>
    </li>

    <li><?php echo $page?></li>

    <li class="gn-search-item">
    <input type="search" placeholder=" Pesquisar..." id="search" class="gn-search" autocomplete="off">
        <div class='suggestionsBox2' id='suggestions2' style='display: none;'>
            <div class='suggestionList2' id='autoSuggestionsList2'>
                &nbsp;
            </div>
        </div>
    </li> 

    

    <li  class="botao_topo" style='float: right;'>
        <a  onclick="
        abreMask(
        'Deseja realmente sair do sistema?<br><br>'+
        '<input value=\' Sim \' type=\'button\' onclick=javascript:window.location.href=\'logout\';>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
        '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
        "class="top_link" target="_parent"><i class="fas fa-power-off"></i> Sair</a>
    </li>

    <li  class="botao_topo" style='float: right;'>
        <a href="https://audipam.adm.br/sistema/cliente/" target="_blank"> <i class="fas fa-phone-alt"></i> SUGESTÕES </a>
    </li>

</ul>

<script src="../mod_menu/js/classie.js"></script>
<script src="../mod_menu/js/gnmenu.js"></script>
<script>
    new gnMenu( document.getElementById( 'gn-menu' ) );

    $( window ).on( "load", function() {
        var largura = $(window).width();

        if(largura > 800){
            $('nav.gn-menu-wrapper').addClass('gn-open-part' );
        }
        else{
            $('nav.gn-menu-wrapper').removeClass('gn-open-part' );
        }
     });

     function abreSubmenu(el){
        $( "#"+el ).toggle( "slow");
    }
</script>



<?php   
    //LOGO
    $sql = "SELECT * FROM cadastro_usuarios 
            WHERE usu_id = :usu_id";
    $stmt = $PDO->prepare( $sql );
    $stmt->bindParam( ':usu_id', $_SESSION['usuario_id'] );
    $stmt->execute();
    $result = $stmt->fetch();
    $foto = $result['usu_foto'];

    if($foto == '')
    {
        $foto = '../core/imagens/perfil.png';
    }

   
?>
<nav class="cd-side-nav" style='z-index:99999999999; overflow:auto;'>
    <ul>
        <!--<li class="cd-label">Main</li>-->
        <br>
        <div style="margin-top:-70px; z-index:99999999999;"><center><img src='../core/imagens/logo_branco.png' alt='Logo' style='max-width:200px;'></center></div>

        <li>
            <a href="dashboard"><span class='icon'><i class="fas fa-tachometer-alt"></i></span> &nbsp;&nbsp; Dashboard</a>
        </li>
        
        <?php

        

		
        $query_setor = " sep_setor = :sep_setor ";        
		
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
        $stmt = $PDO->prepare($sql);        
        $stmt->bindParam(':sep_setor', $_SESSION['setor_id'] );        
        $stmt->execute();
        $rows = $stmt->rowCount();
        if($rows > 0)
        {
            while($result = $stmt->fetch())
            {
                $a = strtolower(str_replace("/","",RetirarAcentos($result['mod_nome'])));
                $b = explode("_",$pagina_link);
                echo "	
                        
                        <li class='has-children' id='".$a."'>
                            <a href='#0' class='sub'><span class='icon'>".$result['mod_img']."</span> &nbsp;&nbsp; ".$result['mod_nome']."</a>
                            <ul>";
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
                        $stmt_sub = $PDO->prepare($sql_sub);
                        $stmt_sub->bindParam(':sep_setor', $_SESSION['setor_id'] );						
                        $stmt_sub->bindParam(':mod_id', $result['mod_id'] );
                        $stmt_sub->execute();
                        $rows_sub = $stmt_sub->rowCount();
                        if($rows_sub > 0)
                        {
                            while($result_sub = $stmt_sub->fetch())
                            {
                                echo "
                                <li><a href='".$result_sub['sub_link']."/view'>&raquo; ".$result_sub['sub_nome']."</a></li>
                                ";
                            }
                        }
                        echo "
                        </ul>
                        </li>    
                ";
                
                $pos = strpos(strtolower($a), $b[0]);
                $pos2 = strpos(strtolower($a), $b[1]);
                $pos3 = strpos(strtolower($a), $b[2]);
                //if($pos === false && $pos2 === false && $pos3 === false)
                if($pos === false)
                {
                    
                }
                else
                {
                    ?>
                    <script>
                    
                        jQuery("li#<?php echo $a;?> a.sub").next("ul").slideToggle("slow");
                
                    </script>
                    <?php
                }
            }
        }
         ?>
        <li class="has-children">
            <a href="#" onclick="
            abreMask(
            'Deseja realmente sair do sistema?<br><br>'+
            '<input value=\' Sim \' type=\'button\' onclick=javascript:window.location.href=\'logout\';>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
            '<input value=\' Não \' type=\'button\' class=\'close_janela\'>');
        " class="top_link" target="_parent"><span class='icon'><i class="fas fa-power-off"></i></span> &nbsp;&nbsp; Sair</a>
        </li>


</nav>
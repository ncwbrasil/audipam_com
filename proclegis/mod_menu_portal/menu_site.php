<link rel="stylesheet" type="text/css" href="mod_includes_portal/css/menu_resp.css">
<nav>
    <label for="drop" class="toggle barras" style="border:none;"><i class="fas fa-bars"></i></label>
    <input type="checkbox" id="drop" />
    
    <ul class="menu_resp">
        <?php 
            if($_SESSION['cliente']!='cmmc'){
        ?>
        <li>
            <label for='drop-1' class='toggle submenu'><a>Vereadores <i class='fas fa-caret-down'></i> </a></label> <!--  ITEM QUE APARECE NO MENU RESPONSÍVO -->
            <a href='#'>Vereadores<i class='fas fa-caret-down' style='padding-left:5px' ></i></a><!--  ITEM QUE APARECE NO MONITOR -->
            <input type='checkbox' id='drop-1'/> 
            <ul>
                <li><a href='vereadores-exercicio'>Em exercício</a></li>
                <li><a href='vereadores-legislaturas'>Legislaturas</a></li>
            </ul>
        </li>
        <li><a href='mesa-diretora'>Mesa Diretora</a></li>
        <li><a href='comissoes'>Comissões</a></li>
        <li><a href='sessoes'>Sessões</a></li>
        <?php }?>
        <li><a href='materias'>Matérias Legislativas</a></li>
        <li><a href='normas'>Leis e Normas</a></li>
        <!-- <li><a href='relatorios'>Relatórios</a></li> -->
    </ul>
</nav>


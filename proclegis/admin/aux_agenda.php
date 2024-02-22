<?php 
$pagina_link = 'aux_agenda';
include_once("../../core/mod_includes/php/funcoes.php");
sec_session_start(); 
include_once("../../core/mod_includes/php/connect.php");
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <?php 
        include('header.php');
        $mensagem = false;

        if ($action == "adicionar") {

            $sql = "DELETE FROM aux_agenda_data_inativa";
            $stmt = $PDO_PROCLEGIS->prepare($sql);
            $stmt->execute();

            if(isset($_POST['din_dia'])) {

                for($i = 0; $i < count($_POST['din_dia']); $i++) {
                    
                    // troca o mês de escrito para número
                    ($_POST['din_mes'][$i] == 'Janeiro' ? $din_mes = 1 : 
                    ($_POST['din_mes'][$i] == 'Fevereiro' ? $din_mes = 2 : 
                    ($_POST['din_mes'][$i] == 'Março' ? $din_mes = 3 : 
                    ($_POST['din_mes'][$i] == 'Abril' ? $din_mes = 4 :
                    ($_POST['din_mes'][$i] == 'Maio' ? $din_mes = 5 :
                    ($_POST['din_mes'][$i] == 'Junho' ? $din_mes = 6 :
                    ($_POST['din_mes'][$i] == 'Julho' ? $din_mes = 7 :
                    ($_POST['din_mes'][$i] == 'Agosto' ? $din_mes = 8 :
                    ($_POST['din_mes'][$i] == 'Setembro' ? $din_mes = 9 :
                    ($_POST['din_mes'][$i] == 'Outubro' ? $din_mes = 10 :
                    ($_POST['din_mes'][$i] == 'Novembro' ? $din_mes = 11 : 12)))))))))));

                    // monta a data em formato padrão
                    $din_data_completa = $_POST['din_ano'][$i].'-'.$din_mes.'-'.$_POST['din_dia'][$i];
                    $din_descricao = $_POST['din_descricao'][$i];
                    $din_mes_escrito = $_POST['din_mes'][$i];
                    $din_ano = $_POST['din_ano'][$i];
                    $din_dia = $_POST['din_dia'][$i];                                                                                                                                                          

                    // INSERT NOVAS DATAS
                    $sql = "INSERT INTO aux_agenda_data_inativa (din_data_completa, din_descricao, din_dia, din_mes, din_ano) VALUES (:din_data_completa, :din_descricao, :din_dia, :din_mes, :din_ano)";
                    $stmt = $PDO_PROCLEGIS->prepare($sql);
                    $stmt->bindValue(':din_data_completa', $din_data_completa);
                    $stmt->bindValue(':din_descricao', $din_descricao);
                    $stmt->bindValue(':din_dia', $din_dia);
                    $stmt->bindValue(':din_mes', $din_mes_escrito);
                    $stmt->bindValue(':din_ano', $din_ano);
                    $stmt->execute();

                    $mensagem = true;

                }
            }

        }

        $sql_datas = "SELECT * FROM aux_agenda_data_inativa";
        $stmt_datas = $PDO_PROCLEGIS->prepare($sql_datas);    
        $stmt_datas->execute();
        $rows_datas = $stmt_datas->rowCount();
    
    ?>
    
<!-- arquivos do plugin calendario -->
<link rel="stylesheet" href="../mod_includes/js/janela/jquery-ui.css">
<script src="../mod_includes/js/janela/jquery-ui.js"></script>
<link type="text/css" rel="stylesheet" media="all" href="../mod_includes/js/chat/chat.css" />
<link type="text/css" rel="stylesheet" media="all" href="../mod_includes/js/chat/screen.css" />
<link rel="stylesheet" href="../mod_includes/css/eventCalendar.css">
<link rel="stylesheet" href="../mod_includes/css/eventCalendar_theme_responsive.css">
<!-- fim calendario -->

<style>
    .datas{
        max-height: 300px;
        overflow: auto;
        margin-top: 30px;
        margin-bottom: 30px
    }
    .dias input{
        width: 120px !important;
    }
</style>
</head>
<body>	
	<main class="cd-main-content">
    	<!--MENU-->
		<?php include("../mod_menu/menu.php"); ?>
        
        <!--CONTEUDO CENTRO-->
		<div class="content-wrapper">
            <div class='mensagem'></div>
            <?php if($mensagem) : ?>
                <script>
                        mensagem("Ok","<i class='fas fa-check-circle'></i> Dados atulizados com sucesso!");
                </script>
            <?php endif ?>
            
            <?php if($pagina == "view") :?>
                <div class="corpo" id="agenda">
                    <div class='titulo'>Selecione os dias para inativar  </div>
                    <script>
                        jQuery(document).ready(function() {
                            jQuery("#eventCalendarShowDescription").eventCalendar({
                               
                                showDescription: true,
                            });
                        });
                    </script>
                    <div id="eventCalendarShowDescription"></div>
                    
                    <br><br>

                    <h1>Dias inativos</h1>
                    <form method="POST" action="aux_agenda/view/adicionar" style="background: #dddddd; padding-left: 15px">

                        <div class="datas">
                            <div id='tabela_dias'></div>
                            <?php
                                if($rows_datas > 0){
                                    while($result_datas = $stmt_datas->fetch()){
                                       
                                        if($result_datas['din_data_completa'] == date('Y-m-d')){
                                            echo'<p class="dias">Dia: <input type="text" name="din_dia[]" value="'.$result_datas['din_dia'].'" readonly><input type="text" name="din_mes[]" value="'.$result_datas['din_mes'].'" readonly><input type="text" name="din_ano[]" value="'.$result_datas['din_ano'].'" readonly> <input type="text" value="'.$result_datas['din_descricao'].'" readonly name="din_descricao[]" style="width: 50% !important" ><input type="hidden" name="din_id[]" value="'.$result_datas['din_id'].'" > <span class="g_excluir" style="float: none" title="Retirar" id="remContato"><i class="far fa-trash-alt"></i></span>&nbsp;Hoje</p>';
                                        }else{
                                            echo'<p class="dias">Dia: <input type="text" name="din_dia[]" value="'.$result_datas['din_dia'].'" readonly><input type="text" name="din_mes[]" value="'.$result_datas['din_mes'].'" readonly><input type="text" name="din_ano[]" value="'.$result_datas['din_ano'].'" readonly> <input type="text" value="'.$result_datas['din_descricao'].'" readonly name="din_descricao[]" style="width: 50% !important" ><input type="hidden" name="din_id[]" value="'.$result_datas['din_id'].'" > <span class="g_excluir" style="float: none" title="Retirar" id="remContato"><i class="far fa-trash-alt"></i></span></p>';
                                        }
                                    }
                                }
                            ?>
                        </div>
                        <p id="btn_salvar" style='text-align: center'></p>
                    </form>
                </div>
            <?php endif ?>

       	</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
</body>
</html>


<script>
		 $(document).on('click', '.eventsCalendar-day', function() {
					
            // get valores 
            var dia = document.getElementsByClassName("eventsCalendar-day current")[0].textContent;
            var mesAno = document.getElementsByClassName("eventsCalendar-currentTitle")[0].textContent;
            var mesAnoArray = mesAno.split(" ", 2);
            var mes = mesAnoArray[0];
            var ano = mesAnoArray[1];

            for(cont = 1; cont <= 31; cont++){
                if(cont == dia){
                    $("#dayList_"+cont+" a").css("background-color", "#cccc", "!important");
                }
            }

            $('#btn_salvar').html("<input type='submit' value='Salvar' />");

            // adiciona a data selecionada
            $('#tabela_dias').append('<p class="dias">Dia: <input type="text" name="din_dia[]" value="'+dia+'" readonly><input type="text" name="din_mes[]" value="'+mes+'" readonly><input type="text" name="din_ano[]" value="'+ano+'" readonly> <input type="text" placeholder="Descrição" name="din_descricao[]" style="width: 50% !important" > <span class="g_excluir" style="float: none" title="Retirar" id="remContato"><i class="far fa-trash-alt"></i></span>&nbsp;<i class="fa fa-check" aria-hidden="true"></i></p>');
                  
        });

        // remover a data 
        $(document).on('click','#remContato', function() { 
				$(this).parents('.dias').remove();
                $('#btn_salvar').html("<input type='submit' value='Salvar' />");
	    });

</script>

<?php
    include("../mod_includes/js/jquery.eventCalendar.php");
?>

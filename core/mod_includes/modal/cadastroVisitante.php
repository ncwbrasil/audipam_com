<div class="modal fade " id="basicExampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Cadastro de Visitante</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
        <?php
        echo "	
            <form name='form' id='form' enctype='multipart/form-data' method='post' action=''>							
                        <p><label>Nome*:</label> <input name='vis_nome' id='vis_nome' value='".$_GET['nome']."' placeholder='Nome' class='obg'>
                        <p><label>CPF*:</label> <input name='vis_cpf' id='vis_cpf' placeholder='C.P.F' value='".$_GET['cpf']."' onkeypress='return mascaraCPF(this);' class='obg' maxlength='14'>
                        ";
                        if($_GET['local'] == "" && $_GET['morador'] == "")
                        {
                            echo "
                        <p><label>Bloco*:</label>
                            <select name='bloco' id='bloco' class='obg'>
                                <option  value='' selected>Bloco</option>
                                ";
                                $sql = " SELECT * FROM cadastro_torres WHERE tor_cliente = :tor_cliente ORDER BY tor_guarita";
                                $stmt_cargo = $PDO->prepare($sql);
                                
                                $stmt_cargo->bindParam(':tor_cliente', 	$_SESSION['cliente_id']);
                                $stmt_cargo->execute();
                                while($result_cargo = $stmt_cargo->fetch())
                                {
                                    echo "<option value='".$result_cargo['tor_id']."'>".$result_cargo['tor_nome']." (".$result_cargo['tor_guarita'].")</option>";
                                }
                                echo "
                            </select>
                        <p><label>Conjunto/Empresa*:</label>
                            <select name='ap' id='ap' class='obg'>
                                <option  value='' selected>Conjunto/Empresa</option>
                        </select>
                        <p><label>Serial Cartão/Chaveiro:</label> <input name='cat_serial' id='cat_serial' placeholder='Serial Cartão/Chaveiro' autocomplete='off'/>
                        ";
                        }
                        elseif($_GET['morador'] != "")
                        {
                            echo "<input type='hidden' name='empresa' id='morador' value='".$_GET['morador']."' readonly>";
                            echo "<input type='hidden' name='vac_id' id='vac_id' value='".$_GET['vac_id']."' readonly>";
                            echo "<input type='hidden' name='vis_id' id='vis_id' value='".$_GET['vis_id']."' readonly>";
                        }
                        else
                        {
                            echo "<p><label>Local*:</label> <input name='vis_local' id='vis_local' value='".$_GET['local']."' placeholder='Local' class='obg' readonly>
                                <input type='hidden' name='con_id' id='con_id' value='".$_GET['con_id']."' readonly>
                            ";
                        }
                        echo "
                        <center><input type='button' value='Tirar Foto' onClick='bater_foto()' id='snap' > </center>
                        <br>
                        <center>
                            <div class='webcam'>
                                <div class='vid' style='display:inline; width:50%'>
                                    <div id='minha_camera'></div>
                                </div>
                                <div class='can' style='float:right; width:50%'>
                                    <div id='results'></div>
                                </div>
                            </div> 
                        </center>                       
            </form>
            ";
            ?>
        </div>
        <div class="modal-footer">            
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            <button type="button" class="btn btn-primary" id="salvaVisitante" data-dismiss="modal">Salvar</button>            
        </div>
        </div>
    </div>
    </div>
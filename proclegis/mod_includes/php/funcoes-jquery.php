<script language="javascript">	 
	// var tempo = new Number();
    // // Tempo em segundos
    // tempo = t;
 

	function startCountdown1(tempo, sessao, action)
	{		
		if(action == "start")
		{
			keepGoing1 = true;
		}
		else if(action == "stop")
		{
			keepGoing1 = false;
		}
		if(keepGoing1) 
		{
			// Se o tempo não for zerado
			if((tempo - 1) >= 0){
				// Pega a parte inteira dos minutos
				var min = parseInt(tempo/60);
				// Calcula os segundos restantes
				var seg = tempo%60;

				// Formata o número menor que dez, ex: 08, 07, ...
				if(min < 10){
					min = "0"+min;
					min = min.substr(0, 2);
				}
				if(seg <=9){
					seg = "0"+seg;
				}
			
				// Cria a variável para formatar no estilo hora/cronômetro
				horaImprimivel = '00:' + min + ':' + seg;
				//JQuery pra setar o valor
				
				$("#cron_discurso").val(horaImprimivel);   
				jQuery.post("../mod_includes/php/cronometro.php",
				{
					tipo:"discurso",
					time:horaImprimivel,
					sessao:sessao			
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{				
					
					
				});         
			
				// diminui o tempo
				tempo--;
				
				// Define que a função será executada novamente em 1000ms = 1 segundo
				setTimeout('startCountdown1('+tempo+','+sessao+')',1000);	

			// Quando o contador chegar a zero faz esta ação
			} 
			else 
			{
				//window.open('../controllers/logout.php', '_self');
			}
		}
		else
		{
			// Pega a parte inteira dos minutos
			var min = parseInt(tempo/60);
			// Calcula os segundos restantes
			var seg = tempo%60;

			// Formata o número menor que dez, ex: 08, 07, ...
			if(min < 10){
				min = "0"+min;
				min = min.substr(0, 2);
			}
			if(seg <=9){
				seg = "0"+seg;
			}
			
			// Cria a variável para formatar no estilo hora/cronômetro
			horaImprimivel = '00:' + min + ':' + seg;
			//JQuery pra setar o valor
			
			   
			jQuery.post("../mod_includes/php/zera_cronometro.php",
			{
				tipo:"discurso",
				time:horaImprimivel,
				sessao:sessao			
			},
			function(valor) // Carrega o resultado acima para o campo catadm
			{				
				$("#cron_discurso").val(valor);				
			}); 
		}
    }

	function startCountdown2(tempo, sessao, action)
	{		
		if(action == "start")
		{
			keepGoing2 = true;
		}
		else if(action == "stop")
		{
			keepGoing2 = false;
		}
		if(keepGoing2) 
		{
			// Se o tempo não for zerado
			if((tempo - 1) >= 0){
				// Pega a parte inteira dos minutos
				var min = parseInt(tempo/60);
				// Calcula os segundos restantes
				var seg = tempo%60;

				// Formata o número menor que dez, ex: 08, 07, ...
				if(min < 10){
					min = "0"+min;
					min = min.substr(0, 2);
				}
				if(seg <=9){
					seg = "0"+seg;
				}
			
				// Cria a variável para formatar no estilo hora/cronômetro
				horaImprimivel = '00:' + min + ':' + seg;
				//JQuery pra setar o valor
				
				$("#cron_aparte").val(horaImprimivel);   
				jQuery.post("../mod_includes/php/cronometro.php",
				{
					tipo:"aparte",
					time:horaImprimivel,
					sessao:sessao			
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{				
					
					
				});         
			
				// diminui o tempo
				tempo--;
				
				// Define que a função será executada novamente em 1000ms = 1 segundo
				setTimeout('startCountdown2('+tempo+','+sessao+')',1000);	

			// Quando o contador chegar a zero faz esta ação
			} 
			else 
			{
				//window.open('../controllers/logout.php', '_self');
			}
		}
		else
		{
			// Pega a parte inteira dos minutos
			var min = parseInt(tempo/60);
			// Calcula os segundos restantes
			var seg = tempo%60;

			// Formata o número menor que dez, ex: 08, 07, ...
			if(min < 10){
				min = "0"+min;
				min = min.substr(0, 2);
			}
			if(seg <=9){
				seg = "0"+seg;
			}
			
			// Cria a variável para formatar no estilo hora/cronômetro
			horaImprimivel = '00:' + min + ':' + seg;
			//JQuery pra setar o valor
			
			   
			jQuery.post("../mod_includes/php/zera_cronometro.php",
			{
				tipo:"aparte",
				time:horaImprimivel,
				sessao:sessao			
			},
			function(valor) // Carrega o resultado acima para o campo catadm
			{				
				$("#cron_aparte").val(valor);				
			}); 
		}
    }

	function startCountdown3(tempo, sessao, action)
	{		
		if(action == "start")
		{
			keepGoing3 = true;
		}
		else if(action == "stop")
		{
			keepGoing3 = false;
		}
		if(keepGoing3) 
		{
			// Se o tempo não for zerado
			if((tempo - 1) >= 0){
				// Pega a parte inteira dos minutos
				var min = parseInt(tempo/60);
				// Calcula os segundos restantes
				var seg = tempo%60;

				// Formata o número menor que dez, ex: 08, 07, ...
				if(min < 10){
					min = "0"+min;
					min = min.substr(0, 2);
				}
				if(seg <=9){
					seg = "0"+seg;
				}
			
				// Cria a variável para formatar no estilo hora/cronômetro
				horaImprimivel = '00:' + min + ':' + seg;
				//JQuery pra setar o valor
				
				$("#cron_ordem").val(horaImprimivel);   
				jQuery.post("../mod_includes/php/cronometro.php",
				{
					tipo:"ordem",
					time:horaImprimivel,
					sessao:sessao			
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{				
					
					
				});         
			
				// diminui o tempo
				tempo--;
				
				// Define que a função será executada novamente em 1000ms = 1 segundo
				setTimeout('startCountdown3('+tempo+','+sessao+')',1000);	

			// Quando o contador chegar a zero faz esta ação
			} 
			else 
			{
				//window.open('../controllers/logout.php', '_self');
			}
		}
		else
		{
			// Pega a parte inteira dos minutos
			var min = parseInt(tempo/60);
			// Calcula os segundos restantes
			var seg = tempo%60;

			// Formata o número menor que dez, ex: 08, 07, ...
			if(min < 10){
				min = "0"+min;
				min = min.substr(0, 2);
			}
			if(seg <=9){
				seg = "0"+seg;
			}
			
			// Cria a variável para formatar no estilo hora/cronômetro
			horaImprimivel = '00:' + min + ':' + seg;
			//JQuery pra setar o valor
			
			   
			jQuery.post("../mod_includes/php/zera_cronometro.php",
			{
				tipo:"ordem",
				time:horaImprimivel,
				sessao:sessao			
			},
			function(valor) // Carrega o resultado acima para o campo catadm
			{				
				$("#cron_ordem").val(valor);				
			}); 
		}
    }

	function startCountdown4(tempo, sessao, action)
	{		
		if(action == "start")
		{
			keepGoing4 = true;
		}
		else if(action == "stop")
		{
			keepGoing4 = false;
		}
		if(keepGoing4) 
		{
			// Se o tempo não for zerado
			if((tempo - 1) >= 0){
				// Pega a parte inteira dos minutos
				var min = parseInt(tempo/60);
				// Calcula os segundos restantes
				var seg = tempo%60;

				// Formata o número menor que dez, ex: 08, 07, ...
				if(min < 10){
					min = "0"+min;
					min = min.substr(0, 2);
				}
				if(seg <=9){
					seg = "0"+seg;
				}
			
				// Cria a variável para formatar no estilo hora/cronômetro
				horaImprimivel = '00:' + min + ':' + seg;
				//JQuery pra setar o valor
				
				$("#cron_consideracoes").val(horaImprimivel);   
				jQuery.post("../mod_includes/php/cronometro.php",
				{
					tipo:"consideracoes",
					time:horaImprimivel,
					sessao:sessao			
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{				
					
					
				});         
			
				// diminui o tempo
				tempo--;
				
				// Define que a função será executada novamente em 1000ms = 1 segundo
				setTimeout('startCountdown4('+tempo+','+sessao+')',1000);	

			// Quando o contador chegar a zero faz esta ação
			} 
			else 
			{
				//window.open('../controllers/logout.php', '_self');
			}
		}
		else
		{
			// Pega a parte inteira dos minutos
			var min = parseInt(tempo/60);
			// Calcula os segundos restantes
			var seg = tempo%60;

			// Formata o número menor que dez, ex: 08, 07, ...
			if(min < 10){
				min = "0"+min;
				min = min.substr(0, 2);
			}
			if(seg <=9){
				seg = "0"+seg;
			}
			
			// Cria a variável para formatar no estilo hora/cronômetro
			horaImprimivel = '00:' + min + ':' + seg;
			//JQuery pra setar o valor
			
			   
			jQuery.post("../mod_includes/php/zera_cronometro.php",
			{
				tipo:"consideracoes",
				time:horaImprimivel,
				sessao:sessao			
			},
			function(valor) // Carrega o resultado acima para o campo catadm
			{				
				$("#cron_consideracoes").val(valor);				
			}); 
		}
    }
	
	




	// MARCAR TODAS / DESMARCAR TODAS
		function marcardesmarcar()
		{
		if($('.todos').prop("checked"))
		{
			$('.marcar').each(
				function(){
				if($(this).prop("disabled"))
				{
				}
				else
				{
					$(this).prop("checked", true);
				}
				}
			);
		}
		else
		{
			$('.marcar').each(
				function(){
				$(this).prop("checked", false);               
				}
			);
		}
		}


	// VERIFICAÇÃO FORMULÁRIO 
		jQuery(document).on('submit',"#form",function()
		{
			
			var isValid = true;
			var isCPF = true;
			jQuery(".obg").each(function() 
			{
				var element = $(this);
				if(element.attr("id") == "vis_cpf")
				{
					if(!validaCPF(element.val()))
					{
						isCPF = false;
						jQuery(this).css({"border":"1px solid #F00"});					
					}
				}
				else if (element.val() == "") 
				{ 
					isValid = false; 
					element.css({"border" : "1px solid #F90F00"});
				}
				else
				{
					element.css({"border" : "1px solid #DDD"});
				}

			}); // each Function

			if(isValid == false)
			{ 
				jQuery('#erro').html("Por favor verifique os campos obrigatórios em vermelho"); 
				return false;
			}
			else if(isCPF == false)
			{ 
				jQuery('#erro').html("CPF inválido. Por favor verifique e tente novamente."); 
				return false;
			} 
			else { 
				abreMask(
				'<p><br>Por favor, aguarde enquanto as informação são salvas. <br><br>'+
				'<img src=\'../../core/imagens/carregando.gif\' ><p><br>');
				jQuery('#janela').children('div.close_janela').remove();
			 }   

		});	
	

	// MENSSAGEM DE RETORNO
		function mensagem(condicao,msg)
		{
			if(condicao == "Ok"){
				$("div.mensagem").removeClass("x");
				$("div.mensagem").addClass("ok");
			} 
			else if(condicao == "X"){ 
				$("div.mensagem").removeClass("ok");
				$("div.mensagem").addClass("x");
			} 
			jQuery('div.mensagem').html(msg+"<i class='far fa-times-circle right'></i>");
			jQuery('div.mensagem').slideDown(400);
			
		}

	// ABRE MASK
		function abreMask (msg)
		{
			jQuery('body').append('<div id="mask"></div>');
			jQuery('#mask').fadeIn(300);
			jQuery('#janela').html(msg);
			jQuery("#janela").fadeIn(300);
			jQuery('#janela').css({"display":""});
			jQuery('#janela').css({"height":"auto"});
			jQuery('#janela').prepend('<div class="close_janela hand" style="float:right"><i class="fas fa-window-close"></i></div>');
			//jQuery('body').css({'overflow':'hidden'});
			
			var popMargTopJanela = (jQuery("#janela").height() + 24) / 2; 
			var popMargLeftJanela = (jQuery("#janela").width() + 5) / 2; 
			
			jQuery("#janela").css({ 
				'margin-top' : -popMargTopJanela,
				'margin-left' : -popMargLeftJanela
			});
		}

	

	


	// CHECK ALL BOXS
		jQuery(document).on('click',' .check_all',function() 
		{ 
			if(jQuery(this).is(":checked"))
			{
				jQuery(this).parent().next("div.blocos").find("div.sub_blocos").find("input").prop("checked", true);
			}
			else
			{
				jQuery(this).parent().next("div.blocos").find("div.sub_blocos").find("input").prop("checked", false);
			}	
		});

	// DIV LOGOUT 
		jQuery(document).on('click','.close_janela, .ui-dialog-titlebar-close',function() { 
			jQuery('#mask , .janela, .janelaAcao').fadeOut(100 , function() {
				jQuery('.janela, .janelaAcao').fadeOut(100 , function() {
				jQuery('#mask').remove();  
				jQuery('body').css({'overflow':'visible'});
				});
			}); 
			return false;
		});

		jQuery(document).on('click','input.close_janela_foto, .ui-dialog-titlebar-close',function() { 
			jQuery('#mask').fadeOut(100 , function() {
				jQuery('#mask').remove();  
				jQuery('body').css({'overflow':'visible'});
				jQuery('#foto_perfil').dialog();
				jQuery('#foto_perfil').dialog('close'); 
			}); 
			return false;
		});
				
		

	// VERIFICA PERMISSAO	
		function verificaPermissao(permissao,pagina)
		{	

			jQuery.post("../../core/mod_includes/php/verifica_permissao.php",
			{
				a:permissao
			},
			function(valor) // Carrega o resultado acima para o campo
			{
				if(valor.indexOf("x") > -1)
				{
					mensagem("X","<i class='fa fa-exclamation-circle'></i> Você não tem permissão para realizar essa operação.");
					jQuery("i.far, i.fa").click(function()
					{
						jQuery('div.mensagem').slideUp(500);				
					});			
				}
				else
				{			
					jQuery('.janela').hide();
			
					
					window.location.href=pagina;			
				}
			});
		}


		function verificaPermissaoSubmit(permissao,pagina)
		{	
			jQuery.post("../../core/mod_includes/php/verifica_permissao.php",
			{
				a:permissao
			},
			function(valor) // Carrega o resultado acima para o campo
			{
				if(valor.indexOf("x") > -1)
				{
					mensagem("X","<i class='fa fa-exclamation-circle'></i> Você não tem permissão para realizar essa operação.");
					jQuery("i.far, i.fa").click(function()
					{
						jQuery('div.mensagem').slideUp(500);				
					});			
				}
				else
				{			
					jQuery('.janela').hide();
			
					jQuery("#form_visitantes_acesso").submit();			
				}
			});
		}

	// MODAIS
		jQuery(document).on('click',' #cadastrarMandatos, #cadastrarFiliacoes, #cadastrarDependentes , #cadastrarComposicao, #cadastrarReunioes, #cadastrarAnexada, #cadastrarModal',function() 	
		{		
			var isValid = true;
			
			var modal_id = jQuery(this).closest("div.modal").attr("id");
			
			// VERIFICA CAMPOS OBRIGATORIOS
			jQuery("#"+modal_id+" .obg").each(function() 
			{
				var element = $(this);
				if (element.val() == "") 
				{ 			
					isValid = false; 
					element.css({"border" : "1px solid #F90F00"});
				}
				else
				{
					element.css({"border" : "1px solid #DDD"});
				}

			}); // each Function

			if(isValid == false)
			{ 				
			}	
			else 
			{  						
				jQuery("#"+modal_id).find("div").find("div").find("div").find("form").submit();
			}  
		});	
		
		function cancelarLeituraExp(campo,id, id_exp_materias)
		{
			
			var modal_id = jQuery(campo).closest("div.modal").attr("id");

			jQuery("#"+modal_id).find("form").attr("action","cadastro_sessoes_plenarias_exp_materias/"+id+"/view/excluir_leitura/"+id_exp_materias);
			jQuery("#"+modal_id).find("div").find("div").find("div").find("form").submit();
			//$("#frm").attr("action","excluir.php");
		}

		function cancelarVotacaoExp(campo,id, id_exp_materias)
		{
			
			var modal_id = jQuery(campo).closest("div.modal").attr("id");

			jQuery("#"+modal_id).find("form").attr("action","cadastro_sessoes_plenarias_exp_materias/"+id+"/view/excluir_votacao/"+id_exp_materias);
			jQuery("#"+modal_id).find("div").find("div").find("div").find("form").submit();
			//$("#frm").attr("action","excluir.php");
		}

		function cancelarLeituraOd(campo,id, id_od_materias)
		{
			
			var modal_id = jQuery(campo).closest("div.modal").attr("id");

			jQuery("#"+modal_id).find("form").attr("action","cadastro_sessoes_plenarias_od_materias/"+id+"/view/excluir_leitura/"+id_od_materias);
			jQuery("#"+modal_id).find("div").find("div").find("div").find("form").submit();
			//$("#frm").attr("action","excluir.php");
		}

		function cancelarVotacaoOd(campo,id, id_od_materias)
		{
			
			var modal_id = jQuery(campo).closest("div.modal").attr("id");

			jQuery("#"+modal_id).find("form").attr("action","cadastro_sessoes_plenarias_od_materias/"+id+"/view/excluir_votacao/"+id_od_materias);
			jQuery("#"+modal_id).find("div").find("div").find("div").find("form").submit();
			//$("#frm").attr("action","excluir.php");
		}


		jQuery(document).on('change', 'input[name="sim"], input[name="nao"], input[name="abstencao"] ',function()
		{
			var sim = jQuery(this).parent().parent().find('input[name="sim"]').val(); if(sim == ""){sim = 0;}
			var nao = jQuery(this).parent().parent().find('input[name="nao"]').val(); if(nao == ""){nao = 0;}
			var abstencao = jQuery(this).parent().parent().find('input[name="abstencao"]').val(); if(abstencao == ""){abstencao = 0;}

			
			var total = parseFloat(sim) + parseFloat(nao) + parseFloat(abstencao);
			jQuery(this).parent().parent().find('input[name="total_votos"]').val(total);
		});
		
		
		jQuery(document).on('change', 'select[name="voto[]"] ',function()
		{			
			var sim = 0;
			var nao = 0;
			var abs = 0;

			jQuery(this).parent().parent().find("select[name='voto[]']").each( function() 
			{ 
				if(jQuery(this).val() == "Sim")
				{
					sim++;
				}
				else if(jQuery(this).val() == "Não")
				{
					nao++;
				}
				else if(jQuery(this).val() == "Abstenção")
				{
					abs++;
				}
				
			});			

			jQuery(this).parent().parent().find('input[name="total_votos_sim"]').val(sim);
			jQuery(this).parent().parent().find('input[name="total_votos_nao"]').val(nao);
			jQuery(this).parent().parent().find('input[name="total_votos_abstencao"]').val(abs);
		});
		
	
	// AUX AUTORES - 
		jQuery(document).on('click',' .autor',function() 	
		{	
			jQuery(this).parents("#autores").next().find("input#nome").val(jQuery(this).val());
		});

	// FILTRO
		//$(document).on('click',"div.filtro").hide();
		jQuery(document).on('click',".filtrar",function()
		{
			jQuery(this).next('div.filtro').slideToggle('fast');
		});	

	

	jQuery(document).ready(function()
	{					
		
		jQuery("form").attr('autocomplete', 'off');
		
		// ABRIR ABA AO RECARREGAR PAGINA
			var hash = window.location.hash;
			if(hash)
			{	
				jQuery("li").removeClass("active");
				jQuery("div.tab-pane").removeClass("active");
				jQuery(hash).addClass("active");
				jQuery(hash+"-tab").parent().addClass("active");				
			}
			
			
		
							
			jQuery('a, img').tooltip(
			{
				show: {effect:"fadeIn", delay:0},
				position: {
					my: "left top+13", 
					at: "left bottom"
				}
			});

		/*----------- CARREGA CAMPOS DINAMICAMENTE --------------*/
		
		/// UNIDADES DE TRAMITACAO - LIMPA OUTROS SELECTS ///		
			jQuery("select[name=orgao]").change(function()
			{				
				jQuery(".comissao_unidade").val('');
				jQuery(".parlamentar_unidade").val('');
			});
			jQuery("select[name=comissao]").change(function()
			{			
				jQuery(".orgao_unidade").val('');
				jQuery(".parlamentar_unidade").val('');
			});
			jQuery("select[name=parlamentar]").change(function()
			{		
				jQuery(".orgao_unidade").val('');
				jQuery(".comissao_unidade").val('');
			});
		
		/// SESSAO PLENARIA - RETIRADA DE PAUTA - LIMPA OUTROS SELECTS ///		
			jQuery("select[name=materia_ordem_dia]").change(function()
			{								
				jQuery("select[name=materia_expediente]").val('');				
			});
			jQuery("select[name=materia_expediente]").change(function()
			{							
				jQuery("select[name=materia_ordem_dia]").val('');			
			});
	

		/// MESA DIRETORA - PROCURA SESSÃO ///		
			jQuery("select[name=legislatura]").change(function()
			{			
				jQuery("select[name=sessao]").html('<option value="">Carregando...</option>');
				jQuery.post("../mod_includes/php/procura_sessao.php",
				{
					legislatura:jQuery(this).val()					
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{				
					jQuery("select[name=sessao]").html(valor);
				});
			});

		/// AUTORES - PROCURA AUTOR POR TIPO ///		
			jQuery("select[name=tipo_autor]").change(function()
			{
				//jQuery("select[name=municipio]").html('<option value="">Carregando...</option>');
				jQuery.post("../mod_includes/php/procura_autores.php",
				{
					tipo_autor:jQuery(this).val()					
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{
					jQuery("#autores").html(valor);
				});
			});

		/// MATERIA LEGISLATIVA - PROCURA AUTOR POR TIPO ///		
			jQuery(".tp_autor").change(function()
			{
				//jQuery("select[name=municipio]").html('<option value="">Carregando...</option>');
				jQuery.post("../mod_includes/php/procura_autor.php",
				{
					tipo_autor:jQuery(this).val()					
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{
					jQuery("select[name=autor]").html(valor);
				});
			});
		/// MATERIA LEGISLATIVA - ANEXADA ///		
			jQuery("select[name=tipo_materia]").change(function()
			{				
				jQuery("select[name=materia_anexada]").html('<option value="">Carregando...</option>');
				jQuery("select[name=materia]").html('<option value="">Carregando...</option>');
				jQuery.post("../mod_includes/php/procura_materia.php",
				{
					tipo_materia:jQuery(this).val()					
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{					
					jQuery("select[name=materia_anexada]").html(valor);
					jQuery("select[name=materia]").html(valor);
				});
			});

		/// MATERIA LEGISLATIVA - RELATORIA ///		
			jQuery(".relatoria_periodo").change(function()
			{
				var comissao = jQuery(this).parent("p").prev("p").find("#comissao").val();
				jQuery("select[name=parlamentar]").html('<option value="">Carregando...</option>');
				jQuery.post("../mod_includes/php/procura_parlamentar.php",
				{
					comissao:comissao,
					periodo:jQuery(this).val()					
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{
					jQuery("select[name=parlamentar]").html(valor);
				});
			});
		
		/// NORMA JURÍDICA - REVOGAÇÃO ///		
			jQuery("select[name=tipo_norma]").change(function()
			{
				
				jQuery("select[name=norma_revogada]").html('<option value="">Carregando...</option>');
				jQuery("select[name=norma_juridica]").html('<option value="">Carregando...</option>');
				jQuery.post("../mod_includes/php/procura_norma.php",
				{
					tipo_norma:jQuery(this).val()					
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{
					jQuery("select[name=norma_revogada]").html(valor);	
					jQuery("select[name=norma_juridica]").html(valor);					
				});
			});
		
		/// NORMA JURÍDICA - REVOGAÇÃO ///		
			jQuery("select[name=norma_revogada], select[name=norma_juridica]").change(function()
			{
				
				jQuery("select[name=ementa_norma]").val('Carregando...');
				jQuery.post("../mod_includes/php/procura_ementa_norma.php",
				{
					norma:jQuery(this).val()					
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{
					jQuery("textarea[name=ementa_norma]").val(valor);					
				});

			});

		/// DOCUMENTO ADMINISTRATIVO - ANEXADO ///		
			jQuery("select[name=tipo_documento]").change(function()
			{
				
				jQuery("select[name=documento_anexado]").html('<option value="">Carregando...</option>');
				jQuery.post("../mod_includes/php/procura_documento.php",
				{
					tipo_documento:jQuery(this).val()					
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{
					jQuery("select[name=documento_anexado]").html(valor);
					jQuery("select[name=documento]").html(valor);
				});
			});
		
		/// DOCUMENTO ADMINISTRATIVO - ANEXADO ///		
			jQuery("select.tipo_documento").change(function()
			{
				
				jQuery.post("../mod_includes/php/procura_numero_documento.php",
				{
					tipo_documento:jQuery(this).val()					
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{
					jQuery("input[name=numero]").val(valor);
					jQuery("input[name=ano]").val("<?php echo date('Y');?>");
				});
			});

		/// ENDEREÇO CLIENTE ///
			jQuery("#cep").blur(function()
			{
				/* CARREGA UF */
				jQuery("select[name=uf]").html('<option value="">Carregando...</option>');
				jQuery.post("../mod_includes/php/procura_cep.php",
				{
					cep:jQuery(this).val(),
					up:"uf"
				},
				function(valor) // Carrega o resultado acima para o campo
				{	
				
					jQuery("select[name=uf]").html(valor);
				});
				
				/* CARREGA MUNICIPIO */
				jQuery("select[name=municipio]").html('<option value="">Carregando...</option>');
				jQuery.post("../mod_includes/php/procura_cep.php",
				{
					cep:jQuery(this).val(),
					up:"municipio"
				},
				function(valor) // Carrega o resultado acima para o campo
				{	
					jQuery("select[name=municipio]").html(valor);
				});
				
				/* CARREGA BAIRRO */
				jQuery("input[name=bairro]").val('Carregando...');
				jQuery.post("../mod_includes/php/procura_cep.php",
				{
					cep:jQuery(this).val(),
					up:"bairro"
				},
				function(valor) // Carrega o resultado acima para o campo
				{	
					jQuery("input[name=bairro]").val(valor);
				});
				
				/* CARREGA RUA */
				jQuery("input[name=endereco]").val('Carregando...');
				jQuery.post("../mod_includes/php/procura_cep.php",
				{
					cep:jQuery(this).val(),
					up:"endereco"
				},
				function(valor) // Carrega o resultado acima para o campo
				{	
					jQuery("input[name=endereco]").val(valor);
				});
			});
			jQuery("select[name=uf]").change(function()
			{
				jQuery("select[name=municipio]").html('<option value="">Carregando...</option>');
				jQuery.post("../mod_includes/php/procura_uf.php",
				{
					uf:jQuery(this).val()
					
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{
					jQuery("select[name=municipio]").html(valor);
				});
			});
					
			jQuery("input[name=ser_cliente]").keyup(function()
			{
					
				jQuery.post("../mod_includes/php/procura_cliente.php",
				{
					busca:jQuery(this).val()
					
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{
					if(jQuery("#ser_cliente").val() != "")
					{
						jQuery('#suggestions').show();
						jQuery("#autoSuggestionsList").html(valor);
					}
					else
					{
						
						jQuery("#autoSuggestionsList").html("");
						jQuery('#suggestions').hide();
						
					}
				});
			});

		/// BARRA DE PESQUISA ///
			jQuery("#search").keyup(function()
			{
				if(jQuery(this).val().length > 3)
				{
					var sea = jQuery(this).val();
					jQuery.post("../mod_includes/php/procura_search.php",
					{
						busca:jQuery(this).val()
						
					},
					function(valor) // Carrega o resultado acima para o campo catadm
					{						
						if(sea != '')
						{
							jQuery('#suggestions2').slideDown();
							jQuery("#autoSuggestionsList2").html(valor);
						}
						else
						{
							jQuery('#suggestions2').hide();
							jQuery("#autoSuggestionsList2").html("");
						}
					});
				}
				else
				{
					jQuery('#suggestions2').hide();
					
				}
			});												
		
		/*----------- FIM CARREGA CAMPOS DINAMICAMENTE --------------*/	
		
		// CALENDÁRIOinput
		jQuery("input[name*='data'], #fil_ext_de, #fil_ext_ate").datepicker({
				dateFormat: 'dd/mm/yy',
				dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
				dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
				dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
				monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
				nextText: 'Próximo',
				prevText: 'Anterior'
			});
			
	}); // FIM jQuery(document).ready



	/* --------- FUNCOES GERAIS  ------------ */

	function link_mask(url)
	{
		document.location.href=url;
	}
		
	function sleep(milliseconds)
	{
		setTimeout(function(){
		var start = new Date().getTime();
		while ((new Date().getTime() - start) < milliseconds){
		// Do nothing
		}
		},0);
	}
		
	function blink(selector)
	{
		jQuery(selector).fadeOut('slow', function() {
			jQuery(this).fadeIn('slow', function() {
				blink(this);
			});
		});
	}
	blink('.piscar');
		
	function validaCPF(cpf)
	{
		cpf = cpf.replace(".", "");
		cpf = cpf.replace(".", "");
		cpf = cpf.replace("-", "");

		var numeros, digitos, soma, i, resultado, digitos_iguais;
		digitos_iguais = 1;
		if (cpf.length < 11)
				return false;
		for (i = 0; i < cpf.length - 1; i++)
				if (cpf.charAt(i) != cpf.charAt(i + 1))
					{
					digitos_iguais = 0;
					break;
					}
		if (!digitos_iguais)
				{
				numeros = cpf.substring(0,9);
				digitos = cpf.substring(9);
				soma = 0;
				for (i = 10; i > 1; i--)
					soma += numeros.charAt(10 - i) * i;
				resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
				if (resultado != digitos.charAt(0))
					return false;
				numeros = cpf.substring(0,10);
				soma = 0;
				for (i = 11; i > 1; i--)
					soma += numeros.charAt(11 - i) * i;
				resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
				if (resultado != digitos.charAt(1))
					return false;
				return true;
				}
		else
				return false;
	}

	function validaCNPJ(cnpj)
	{
		//cpf = cpf.replace(".", "");
		//cpf = cpf.replace(".", "");
		//cpf = cpf.replace("-", "");

		cnpj = cnpj.replace(/[^\d]+/g,'');
	
		if(cnpj == '') return false;
		
		if (cnpj.length != 14)
			return false;
	
		// Elimina CNPJs invalidos conhecidos
		if (cnpj == "00000000000000" || 
			cnpj == "11111111111111" || 
			cnpj == "22222222222222" || 
			cnpj == "33333333333333" || 
			cnpj == "44444444444444" || 
			cnpj == "55555555555555" || 
			cnpj == "66666666666666" || 
			cnpj == "77777777777777" || 
			cnpj == "88888888888888" || 
			cnpj == "99999999999999")
			return false;
			
		// Valida DVs
		tamanho = cnpj.length - 2
		numeros = cnpj.substring(0,tamanho);
		digitos = cnpj.substring(tamanho);
		soma = 0;
		pos = tamanho - 7;
		for (i = tamanho; i >= 1; i--) {
		soma += numeros.charAt(tamanho - i) * pos--;
		if (pos < 2)
				pos = 9;
		}
		resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
		if (resultado != digitos.charAt(0))
			return false;
			
		tamanho = tamanho + 1;
		numeros = cnpj.substring(0,tamanho);
		soma = 0;
		pos = tamanho - 7;
		for (i = tamanho; i >= 1; i--) {
		soma += numeros.charAt(tamanho - i) * pos--;
		if (pos < 2)
				pos = 9;
		}
		resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
		if (resultado != digitos.charAt(1))
			return false;
			
		return true;
	}

	function validaRG(numero)
	{
		numero = numero.replace(".", "");
		numero = numero.replace(".", "");
		numero = numero.replace("-", "");
		/*
		##  Igor Carvalho de Escobar
		##    www.webtutoriais.com
		##   Java Script Developer
		*/
		var numero = numero.split("");
		tamanho = numero.length;
		vetor = new Array(tamanho);
	
		if(tamanho>=1)
		{
		vetor[0] = parseInt(numero[0]) * 2; 
		}
		if(tamanho>=2){
		vetor[1] = parseInt(numero[1]) * 3; 
		}
		if(tamanho>=3){
		vetor[2] = parseInt(numero[2]) * 4; 
		}
		if(tamanho>=4){
		vetor[3] = parseInt(numero[3]) * 5; 
		}
		if(tamanho>=5){
		vetor[4] = parseInt(numero[4]) * 6; 
		}
		if(tamanho>=6){
		vetor[5] = parseInt(numero[5]) * 7; 
		}
		if(tamanho>=7){
		vetor[6] = parseInt(numero[6]) * 8; 
		}
		if(tamanho>=8){
		vetor[7] = parseInt(numero[7]) * 9; 
		}
		if(tamanho>=9){
			if(numero[8] == 'x')
			{
				vetor[8] = 10*100;
			}
			else
			{
				vetor[8] = parseInt(numero[8]) * 100;
			}
		}
		
		total = 0;
		
		if(tamanho>=1){
		total += vetor[0];
		}
		if(tamanho>=2){
		total += vetor[1]; 
		}
		if(tamanho>=3){
		total += vetor[2]; 
		}
		if(tamanho>=4){
		total += vetor[3]; 
		}
		if(tamanho>=5){
		total += vetor[4]; 
		}
		if(tamanho>=6){
		total += vetor[5]; 
		}
		if(tamanho>=7){
		total += vetor[6];
		}
		if(tamanho>=8){
		total += vetor[7]; 
		}
		if(tamanho>=9){
		total += vetor[8]; 
		}
		
		alert(total);
		resto = total % 11;
		if(resto!=0){
		return false;
		}
		else{
		return true;
		}
	}

	function number_format( number, decimals, dec_point, thousands_sep ) {
		// %        nota 1: Para 1000.55 retorna com precisão 1 no FF/Opera é 1,000.5, mas no IE é 1,000.6
		// *     exemplo 1: number_format(1234.56);
		// *     retorno 1: '1,235'
		// *     exemplo 2: number_format(1234.56, 2, ',', ' ');
		// *     retorno 2: '1 234,56'
		// *     exemplo 3: number_format(1234.5678, 2, '.', '');
		// *     retorno 3: '1234.57'
		// *     exemplo 4: number_format(67, 2, ',', '.');
		// *     retorno 4: '67,00'
		// *     exemplo 5: number_format(1000);
		// *     retorno 5: '1,000'
		// *     exemplo 6: number_format(67.311, 2);
		// *     retorno 6: '67.31'
	
		var n = number, prec = decimals;
		n = !isFinite(+n) ? 0 : +n;
		prec = !isFinite(+prec) ? 0 : Math.abs(prec);
		var sep = (typeof thousands_sep == "undefined") ? ',' : thousands_sep;
		var dec = (typeof dec_point == "undefined") ? '.' : dec_point;
	
		var s = (prec > 0) ? n.toFixed(prec) : Math.round(n).toFixed(prec); //fix for IE parseFloat(0.55).toFixed(0) = 0;
	
		var abs = Math.abs(n).toFixed(prec);
		var _, i;
	
		if (abs >= 1000) {
			_ = abs.split(/\D/);
			i = _[0].length % 3 || 3;
	
			_[0] = s.slice(0,i + (n < 0)) +
				_[0].slice(i).replace(/(\d{3})/g, sep+'$1');
	
			s = _.join(dec);
		} else {
			s = s.replace('.', dec);
		}
	
		return s;
	}

	function replaceAll(string, token, newtoken) 
	{
		while (string.indexOf(token) != -1) {
			string = string.replace(token, newtoken);
		}
		return string;
	}

	//PUSH NOTIFICATION
	(function() {
	(function($) {
		var notify_methods;
		notify_methods = {
		create_notification: function(options) {
			return new Notification(options.title, options);
		},
		close_notification: function(notification, options) {
			return setTimeout(notification.close.bind(notification), options.closeTime);
		},
		set_default_icon: function(icon_url) {
			return default_options.icon = icon_url;
		},
		isSupported: function() {
			if (("Notification" in window) && (Notification.permission !== "denied")) {
			return true;
			} else {
			return false;
			}
		},
		permission_request: function() {
			if (Notification.permission === "default") {
			return Notification.requestPermission();
			}
		}
		};
		return $.extend({
		notify: function(body, arguments_options) {
			var notification, options;
			if (arguments.length < 1) {
			throw "Notification: few arguments";
			}
			if (typeof body !== 'string') {
			throw "Notification: body must 'String'";
			}

			var default_options = {
			'title': "Nova alerta!",
			'body': "Body",
			'closeTime': 3000000,
			'icon' : ""
			};
			default_options.body = body;
			options = $.extend(default_options, arguments_options);
			if (notify_methods.isSupported()) {
			notify_methods.permission_request();
			notification = notify_methods.create_notification(options);
			notify_methods.close_notification(notification, options);
			return {
				click: function(callback) {
				notification.addEventListener('click', function() {
					return callback();
				});
				return this;
				},
				show: function(callback) {
				notification.addEventListener('show', function() {
					return callback();
				});
				return this;
				},
				close: function(callback) {
				notification.addEventListener('close', function() {
					return callback();
				});
				return this;
				},
				error: function(callback) {
				notification.addEventListener('error', function() {
					return callback();
				});
				return this;
				}
			};
			}
		}
		});
	})(jQuery);

	}).call(this);
	//

</script>

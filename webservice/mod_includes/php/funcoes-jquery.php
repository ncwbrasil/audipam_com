<script language="javascript">
/*----------- VERIFICAÇÃO FORMULÁRIO --------------*/
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
		else {  }   

	});	
/*----------- FIM VERIFICAÇÃO FORMULÁRIO --------------*/

//#region 
	// CARREGA CLIENTE NO CAMPO
		function carregaBuscaCliente(valor, id)
		{
			jQuery("#ser_cliente").val('');
			jQuery("#ser_cliente").val(valor);
			jQuery("#ser_cliente_id").val(id);
			jQuery('#suggestions').hide();
			jQuery("#autoSuggestionsList").html("");
			
		}
//#endregion

	// AGENDA - EXCLUIR ITEM
		function excluir(id)
		{
			abreMask("Deseja realmente excluir este item?<br><br>"+
					"<input value=' Sim ' type='button' onclick=javascript:window.location.href='social_agenda/view/excluir/"+id+"';>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+
					"<input value=' Não ' type='button' class='close_janela'>");
		}

	// MARCAR TODAS / DESMARCAR TODAS
		function marcardesmarcar(){
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

	// MENSSAGEM DE RETORNO
		function mensagem(condicao,msg)
		{
			if(condicao == "Ok") 		{ jQuery("div.mensagem").addClass("ok");} 
			else if(condicao == "X") 	{ jQuery("div.mensagem").addClass("x");} 
			jQuery('div.mensagem').slideDown(500);
			jQuery('div.mensagem').html(msg+"<i class='far fa-times-circle right'></i>");
		}

	// ABRE MASK
		function abreMask (msg)
		{
			jQuery('body').append('<div id="mask"></div>');
			jQuery('#mask').fadeIn(300);
			jQuery('#janela').html(msg);
			jQuery("#janela").fadeIn(300);
			jQuery('#janela').css({"display":""});
			jQuery('#janela').css({"height":"90px"});
			//jQuery('body').css({'overflow':'hidden'});
			
			var popMargTopJanela = (jQuery("#janela").height() + 24) / 2; 
			var popMargLeftJanela = (jQuery("#janela").width() + 24) / 2; 
			
			jQuery("#janela").css({ 
				'margin-top' : -popMargTopJanela,
				'margin-left' : -popMargLeftJanela
			});
		}

	// ALERTA - ARQUIVAR
		function alertaArquivar(id,campo)
		{
			jQuery(campo).closest('.alertaBox').css({"position":"fixed","margin-top":"-4000px","z-index":"1","-webkit-transition-duration": "2s","-moz-transition-duration":"2s","transition-duration":"2s"})
			jQuery.post("../core/mod_includes/php/alerta_arquivar.php",
			{
				ale_id:id
			},
			function(valor) // Carrega o resultado acima para o campo
			{
				if(valor == "Ok")
				{	
				}		
			});
		}

	// ALERTA - MARCAR COMO LIDA
		function alertaMarcarLida(id,campo)
		{
			jQuery(campo).closest('.alertaBox').removeClass("n_lida");
			jQuery(campo).parent(".acao").siblings().find(".naolida").css({"display":"none"});
			jQuery.post("../core/mod_includes/php/alerta_marcar_lida.php",
			{
				ale_id:id
			},
			function(valor) // Carrega o resultado acima para o campo
			{
				
				if(valor == "Ok")
				{
					
				}	
					
			});
			
			
		}


	/* CHECK ALL BOXS */
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
		jQuery(document).on('click','input.close_janela, .ui-dialog-titlebar-close',function() { 
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
	// FIM DIV LOGOUT 			
		
	// VERIFICA PERMISSAO	
		function verificaPermissao(permissao,pagina)
		{	
			jQuery.post("../core/mod_includes/php/verifica_permissao.php",
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
			jQuery.post("../core/mod_includes/php/verifica_permissao.php",
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

	// DAR BAIXA EM FATURA
		jQuery(document).on('click',' #darBaixa',function() 	
		{		
			var isValid = true;

			//var modal = jQuery("#darBaixa"+jQuery("#fat_id").val());
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
				jQuery.post("../core/mod_includes/php/darBaixa.php",
				{
					fat_id:jQuery("#"+modal_id+" #fat_id").val(),
					fat_valor_pago:jQuery("#"+modal_id+" #fat_valor_pago").val(),
					fat_data_pagamento:jQuery( "#"+modal_id+" input[name=fat_data_pagamento]").val(),
					ftr_observacao:jQuery("#"+modal_id+" #ftr_observacao").val()		
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{		
					if(valor.indexOf("true") > -1)			
					{		
						// FECHA MODAL	
						$("#"+modal_id).removeClass("in");
						$("#"+modal_id).removeClass("show");
						$(".modal-backdrop").remove();
						$("#"+modal_id).hide();

						// RETORNA VALOR DINAMICAMENTE PARA TABLE
						$('#valor_pagamento_'+jQuery("#"+modal_id+" #fat_id").val()).html(jQuery("#"+modal_id+" #fat_valor_pago").val());
						$('#data_pagamento_'+jQuery("#"+modal_id+" #fat_id").val()).html(jQuery("#"+modal_id+" input[name=fat_data_pagamento]").val());
						$('#status_'+jQuery("#"+modal_id+" #fat_id").val()).html("<span class='verde'>Pago</span>");
						$("[data-target='#darBaixa"+jQuery("#"+modal_id+" #fat_id").val()+"']").hide();		
						$('#status_'+jQuery("#"+modal_id+" #fat_id").val()).next("td").find("div.g_exibir").hide();
						
						mensagem("Ok","<i class='fas fa-check-circle'></i> Cadastro realizado com sucesso!");				
						
					}
					else if(valor.indexOf("false") > -1)
					{
						mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao efetuar cadastro. Tente novamente");					
					}
				});

			}  
		});

	// LIBERAR FATURA
		function liberarFatura(id)
		{				
			jQuery.post("../core/mod_includes/php/liberarFatura.php",
			{
				fat_id:id	
			},
			function(valor) // Carrega o resultado acima para o campo catadm
			{								
				if(valor.indexOf("true") > -1)		
				{			
					mensagem("Ok","<i class='fas fa-check-circle'></i> Fatura liberada com sucesso!");
					
				}
				else if(valor.indexOf("false") > -1)
				{
					mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao liberar fatura.");			
				}
			});
		}


	// DAR BAIXA EM DESPESA
		jQuery(document).on('click',' #darBaixaDespesa',function() 	
		{			
			
			var isValid = true;
			//var modal = jQuery("#darBaixaDespesa"+jQuery("#des_id").val());	
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
				jQuery.post("../core/mod_includes/php/darBaixaDespesa.php",
				{
					des_id:jQuery("#"+modal_id+" #des_id").val(),
					des_valor_pago:jQuery("#"+modal_id+" #des_valor_pago").val(),
					des_data_pagamento:jQuery("#"+modal_id+" input[name=des_data_pagamento]").val()			
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{	
					if(valor.indexOf("true") > -1)
					//if(valor == "true")
					{		
						// FECHA MODAL	
						$("#"+modal_id).removeClass("in");
						$("#"+modal_id).removeClass("show");
						$(".modal-backdrop").remove();
						$("#"+modal_id).hide();

						// RETORNA VALOR DINAMICAMENTE PARA TABLE
						$('#valor_pagamento_'+jQuery("#"+modal_id+" #des_id").val()).html(jQuery("#"+modal_id+" #des_valor_pago").val());
						$('#data_pagamento_'+jQuery("#"+modal_id+" #des_id").val()).html(jQuery("#"+modal_id+" input[name=des_data_pagamento]").val());				
						$("[data-target='#darBaixaDespesa"+jQuery("#"+modal_id+" #des_id").val()+"']").hide();
						
						mensagem("Ok","<i class='fas fa-check-circle'></i> Cadastro realizado com sucesso!");				
						
					}
					else if(valor.indexOf("false") > -1)
					{
						mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao efetuar cadastro. Tente novamente");					
					}
				});

			}  
		});



		$( ".toogle-title" ).click(function() 
		{
			$(this).find("i").toggleClass("down");
			$(this).next().slideToggle( "slow", function()
			{
				// Animation complete.
			});
		});

		blink("div.piscar");
		jQuery(document).on('click',"i.far, i.fa",function()
		{
			jQuery('div.mensagem').slideUp(500);
			
		});
		jQuery("input[name*='data'], #fil_ext_de, #fil_ext_ate, #usu_dt_nasc").mask('99/99/9999');

	// LIBERAR FATURA
		jQuery(document).on('click',' i.excluirFoto',function() 	
		{
			var id = jQuery(this).attr("id");
							
			var foto = jQuery(this);
			
			jQuery.post("../core/mod_includes/php/excluirFoto.php",
			{
				fot_id:id	
			},
			function(valor) // Carrega o resultado acima para o campo catadm
			{								
				if(valor.indexOf("true") > -1)		
				{							
					foto.parent().fadeOut("fast");
					
				}
				else if(valor.indexOf("false") > -1)
				{
					mensagem("X","<i class='fa fa-exclamation-circle'></i> Erro ao liberar fatura.");			
				}
			});
		});

	// BOX ALERTA
		$(document).on('click',"a.alerta",function(e)
		{
			jQuery(this).next().next().slideToggle(300);
			e.stopPropagation();
		});

		$(document).on('click',"div.alertas",function(e)
		{
			e.stopPropagation();
		});
		$(document).on('click',function(){
			jQuery("div.alertas").slideUp(300);
		});
	// FIM BOX ALERTA
		jQuery("div.conteudo").hide();
		jQuery('div.status .subtitle').on('click',function()
		{
			jQuery(this).parent().find('div.conteudo').slideToggle('slow');
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
		
		// CADASTRO SERVICO 
			
			jQuery("select[name=ser_periodicidade]").on("change",function()
			{								
				if(jQuery(this).val().indexOf("Mensal") > -1)
				{
					
					jQuery("#ser_parcelas").attr("disabled",true);				jQuery("#ser_parcelas").val("");		jQuery("#ser_parcelas").css({"border" : "1px solid #EEE"});	
					
					if(jQuery("#ser_iniciado").val() == "Sim")
					{
						jQuery("#ser_data_fim").removeAttr("disabled");			jQuery("#ser_data_fim").val("");		jQuery("#ser_data_fim").css({"border" : "1px solid #DDD"});				
						jQuery("#ser_dia_vencimento").removeAttr("disabled");	jQuery("#ser_dia_vencimento").val("");	jQuery("#ser_dia_vencimento").css({"border" : "1px solid #DDD"});				
					}		
				}
				else
				{			
					jQuery("#ser_parcelas").removeAttr("disabled");			jQuery("#ser_parcelas").val(""); 		jQuery("#ser_parcelas").css({"border" : "1px solid #DDD"});	
					jQuery("#ser_data_fim").attr("disabled", true);			jQuery("#ser_data_fim").val("");		jQuery("#ser_data_fim").css({"border" : "1px solid #EEE"});				
					jQuery("#ser_dia_vencimento").attr("disabled", true);	jQuery("#ser_dia_vencimento").val("");	jQuery("#ser_dia_vencimento").css({"border" : "1px solid #EEE"});							
				}
			});


			jQuery("#ser_iniciado").change(function()
			{
				if(jQuery("#ser_iniciado").val() == "Sim")
				{
					jQuery("#ser_data_inicio").removeAttr("disabled");			jQuery("#ser_data_inicio").css({"border" : "1px solid #DDD"});						

					if(jQuery("#ser_periodicidade").val().indexOf("Mensal") > -1)
					{
						jQuery("#ser_data_fim").removeAttr("disabled");			jQuery("#ser_data_fim").val("");			jQuery("#ser_data_fim").css({"border" : "1px solid #DDD"});	
						jQuery("#ser_dia_vencimento").removeAttr("disabled");	jQuery("#ser_dia_vencimento").val("");		jQuery("#ser_dia_vencimento").css({"border" : "1px solid #DDD"});
						jQuery("#ftr_status").removeAttr("disabled");			jQuery("#ftr_status").val("");				jQuery("#ftr_status").css({"border" : "1px solid #DDDD"});				
					}
				}
				else
				{
					jQuery("#ser_data_inicio").attr("disabled", true);			jQuery("#ser_data_inicio").val("");			jQuery("#ser_data_inicio").css({"border" : "1px solid #EEE"});	
					jQuery("#ser_data_fim").attr("disabled", true);				jQuery("#ser_data_fim").val("");			jQuery("#ser_data_fim").css({"border" : "1px solid #EEE"});	
					jQuery("#ser_dia_vencimento").attr("disabled", true);		jQuery("#ser_dia_vencimento").val("");		jQuery("#ser_dia_vencimento").css({"border" : "1px solid #EEE"});
					jQuery("#ftr_status").attr("disabled", true);				jQuery("#ftr_status").val("");				jQuery("#ftr_status").css({"border" : "1px solid #EEE"});

														
						
						
					
						
				}
					
			});

			jQuery("#ser_cliente").click(function()
			{
				jQuery("#ser_cliente").val("");
				jQuery("#ser_cliente_id").val("");
			});

			jQuery("#ser_valor, #ser_parcelas").on("change blur keyup",function()
			{
				var valor_total = replaceAll(jQuery("#ser_valor").val(),".","").replace(",",".");
				var parcelas = jQuery("#ser_parcelas").val();
				if(parcelas == 0){parcelas = 1;}
				var valor_parcela = valor_total/parcelas;
				
				// RETURNS PARA OS CAMPOS
				
				if(jQuery("#ser_periodicidade").val().indexOf("Avulso") > -1)
				{
					jQuery("#ser_valor_parcela").val("R$ "+number_format(valor_parcela.toFixed(2),2,",","."));		
				}
				else
				{
					jQuery("#ser_valor_parcela").val("");	
				}
				
				
			});

		// ABRIR ABA AO RECARREGAR PAGINA
			var hash = window.location.hash;
			if(hash)
			{	
				jQuery("li").removeClass("active");
				jQuery("div.tab-pane").removeClass("active");
				jQuery(hash).addClass("active");
				jQuery(hash+"-tab").parent().addClass("active");				
			}
			
			
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
			
			jQuery('a, img').tooltip(
			{
				show: {effect:"fadeIn", delay:0},
				position: {
					my: "left top+13", 
					at: "left bottom"
				}
			});

		/*----------- CARREGA CAMPOS DINAMICAMENTE --------------*/

		/// ENDEREÇO CLIENTE ///
			jQuery("#cli_cep").blur(function()
			{
				/* CARREGA UF */
				jQuery("select[name=cli_uf]").html('<option value="">Carregando...</option>');
				jQuery.post("../core/mod_includes/php/procura_cep.php",
				{
					cep:jQuery(this).val(),
					up:"uf"
				},
				function(valor) // Carrega o resultado acima para o campo
				{	
				
					jQuery("select[name=cli_uf]").html(valor);
				});
				
				/* CARREGA MUNICIPIO */
				jQuery("select[name=cli_municipio]").html('<option value="">Carregando...</option>');
				jQuery.post("../core/mod_includes/php/procura_cep.php",
				{
					cep:jQuery(this).val(),
					up:"municipio"
				},
				function(valor) // Carrega o resultado acima para o campo
				{	
					jQuery("select[name=cli_municipio]").html(valor);
				});
				
				/* CARREGA BAIRRO */
				jQuery("input[name=cli_bairro]").val('Carregando...');
				jQuery.post("../core/mod_includes/php/procura_cep.php",
				{
					cep:jQuery(this).val(),
					up:"bairro"
				},
				function(valor) // Carrega o resultado acima para o campo
				{	
					jQuery("input[name=cli_bairro]").val(valor);
				});
				
				/* CARREGA RUA */
				jQuery("input[name=cli_endereco]").val('Carregando...');
				jQuery.post("../core/mod_includes/php/procura_cep.php",
				{
					cep:jQuery(this).val(),
					up:"endereco"
				},
				function(valor) // Carrega o resultado acima para o campo
				{	
					jQuery("input[name=cli_endereco]").val(valor);
				});
			});
			jQuery("select[name=cli_uf]").change(function()
			{
				jQuery("select[name=cli_municipio]").html('<option value="">Carregando...</option>');
				jQuery.post("../core/mod_includes/php/procura_uf.php",
				{
					uf:jQuery(this).val()
					
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{
					jQuery("select[name=cli_municipio]").html(valor);
				});
			});
					
			jQuery("input[name=ser_cliente]").keyup(function()
			{
					
				jQuery.post("../core/mod_includes/php/procura_cliente.php",
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
					jQuery.post("../core/mod_includes/php/procura_search.php",
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

		/// CARREGA SUBCATEGORIA ///
			jQuery("select[name=rou_categoria]").change(function()
			{
				jQuery("select[name=rou_subcategoria]").html('<option value="">Carregando...</option>');
				jQuery.post("../core/mod_includes/php/procura_categoria.php",
				{
					cat_id:jQuery(this).val()
					
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{
					jQuery("select[name=rou_subcategoria]").html(valor);
				});
			});	

		/// CARREGA SUBCATEGORIA SKU ///
			jQuery("select[name=sku_categoria]").change(function()
			{
				jQuery("select[name=sku_subcategoria]").html('<option value="">Carregando...</option>');
				jQuery.post("../core/mod_includes/php/procura_categoria.php",
				{
					cat_id:jQuery(this).val()
					
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{
					jQuery("select[name=sku_subcategoria]").html(valor);
				});
			});		
			

		/// MONTA GRADE DE VARIACOES ///
			jQuery("input[name^='tamanho'], input[name^='cor']").change(function()
			{
				var inputs_tamanhos = jQuery('input[name^=tamanho]:checked:enabled');
				var tamanho = [];
				jQuery.each(inputs_tamanhos, function(index, el) {
					// push the value in the vdo array
					tamanho.push($(el).val());
				});

				var inputs_cores = jQuery('input[name^=cor]:checked:enabled');
				var cor = [];
				jQuery.each(inputs_cores, function(index, el) {
					// push the value in the vdo array
					cor.push($(el).val());
				});


				jQuery.post("../core/mod_includes/php/montaGrade.php",
				{
					tamanho:tamanho,
					cor:cor
					
				},
				function(valor) // Carrega o resultado acima para o campo catadm
				{
					jQuery("div.grade_variacoes").html(valor);
				});
			});			
			
		/// CLIENTES -> FAMILIARES ///
			$(function() 
			{
				var scntDiv_familiares = $('#p_scents_familiares');
				//var i = $('#p_scents div.bloco').size() + 1;
				var x = jQuery('div.bloco_familiares').size() + 1;
					
				jQuery(document).on('click','#add_familiares',function() 
				{
					var total=0;
					jQuery('<div class="bloco_familiares">'+
						'<input type="hidden" name="familiares['+x+'][fam_id]" id="fam_id">'+
						'<br><br>'+
						'<p><label>Nome:</label>			<input name="familiares['+x+'][fam_nome]" id="fam_nome" placeholder="Nome">'+
						'<p><label>Data Nascimento:</label>			<input name="familiares['+x+'][fam_data_nasc]" placeholder="Data Nascimento" onkeypress="return mascaraData(this,event);">'+
						'<p><label>E-mail:</label>			<input name="familiares['+x+'][fam_email]" id="fam_email" placeholder="E-mail">'+				
						'<p><label>Telefone:</label>		<input name="familiares['+x+'][fam_telefone]" id="fam_telefone" placeholder="Telefone" onkeypress="mascaraTELEFONE(this); return SomenteNumeroCEL(this,event);">'+
						'<i class="fas fa-plus botao_dinamico_add" id="add_familiares" title="Adicionar"></i> &nbsp; <i class="far fa-trash-alt botao_dinamico_rmv" id="rem_familiares" title="Remover" ></i><hr style="width:100%; border:none; height:1px; background:#DDD;"></div>').appendTo(scntDiv_familiares);
					//i++;
					x++;
					//CALENDÁRIOinput
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
					return false;
				});
			
				jQuery(document).on('click','#rem_familiares', function() 
				{ 			
					var total=0;
					if( x >= 1 )
					{
						jQuery(this).parents('div.bloco_familiares').remove();
						//i--;
						//x--;
						
					}
					return false;
				});
			});

		/*----------- FIM CARREGA CAMPOS DINAMICAMENTE --------------*/	
		
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

	function replaceAll(string, token, newtoken) {
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

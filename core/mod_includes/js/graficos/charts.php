<script>

// GRAFICO LINHA - RECEITA/DESPESA
zingchart.THEME="classic";
var chartReceitaDespesa = {
    "background-color":"none",
    "graphset":[      
        {
            "type":"line",            
            "background-color":"#FFF",
            "border-color":"#CCC",
            "border-width":"1px",
            "width":"98%",
            "height":"100%",
            "x":"0",
            "y":"0",
            "margin-bottom":"25px",
            "title":{
                "margin-top":"7px",
                "margin-left":"12px",
                "text":"RECEITA / DESPESA ",
                "background-color":"none",
                "shadow":0,
                "text-align":"center",
                "font-family":"Quicksand",
                "font-size":"15px",
                "font-color":"#707d94"
            },
            "plot":{
                "aspect": 'spline',
                "animation":{
                    "delay":500,
                    "effect":"ANIMATION_SLIDE_LEFT"
                }
            },
            "plotarea":{
                "margin":"50px 25px 70px 46px",
            },
            "scale-y":{
                //"values":"0:50000:5000",
                "line-color":"none",
                "guide":{
                    "line-style":"solid",
                    "line-color":"#d2dae2",
                    "line-width":"1px",
                    "alpha":0.5
                },
                "tick":{
                    "visible":false
                },
                "item":{
                    "font-color":"#8391a5",
                    "font-family":"Quicksand",
                    "font-size":"11px",
                    "padding-right":"5px"
                }
            },
            "scale-x":{
                "line-color":"#d2dae2",
                "line-width":"2px",
                "values":<?php echo $leg_g4;?>,
                "tick":{
                    "line-color":"#d2dae2",
                    "line-width":"1px"
                },
                "guide":{
                    "visible":false
                },
                "item":{
                    "font-color":"#8391a5",
                    "font-family":"Quicksand",
                    "font-size":"11px",
                    "padding-top":"5px"
                }
            },
            "legend":{
                "layout":"x4",
                "background-color":"none",
                "shadow":0,
                "margin":"auto auto 15 auto",
                "border-width":0,
                "item":{
                    "font-color":"#707d94",
                    "font-family":"Quicksand",
                    "padding":"0px",
                    "margin":"0px",
                    "font-size":"11px"
                },
                "marker":{
                    "show-line":"true",
                    "type":"match",
                    "font-family":"Quicksand",
                    "font-size":"11px",
                    "size":4,
                    "line-width":2,
                    "padding":"3px"
                }
            },
            "crosshair-x":{
                "lineWidth":1,
                "line-color":"#707d94",
                "plotLabel":{
                    "shadow":false,
                    "font-color":"#000",
                    "font-family":"Quicksand",
                    "font-size":"11px",
                    "padding":"5px 10px",
                    "border-radius":"5px",
                    "alpha":1
                },
                "scale-label":{
                    "font-color":"#ffffff",
                    "background-color":"#707d94",
                    "font-family":"Quicksand",
                    "font-size":"11px",
                    "padding":"5px 10px",
                    "border-radius":"5px"
                }
            },
            "tooltip":{
                "visible":false
            },
            "series":[
                {
                    "values":[<?php echo $receita;?>],
                    "text":"Receitas",
                    "line-color":"#1DBA9B",
					"background-color": "#1DBA9B",
					"alpha-area": "0.1",
                    "line-width":"2px",
                    "shadow":0,
					"thousands-separator":".",
					"decimals-separator":",",
					"decimals":2,
                    "marker":{
                        "background-color":"#fff",
                        "size":3,
                        "border-width":1,
                        "border-color":"#1DBA9B",
                        "shadow":0
                    },
                    "palette":0
                },
                {
                    "values":[<?php echo $despesa;?>],
                    "text":"Despesas",
                    "line-width":"2px",
                    "line-color":"#DB5454",
                    "background-color":"#DB5454",
					"alpha-area": "0.1",
                    "shadow":0,
					"thousands-separator":".",
					"decimals-separator":",",
					"decimals":2,
                    "marker":{
                        "background-color":"#fff",
                        "size":3,
                        "border-width":1,
                        "border-color":"#DB5454",
                        "shadow":0
                    },
                    "palette":1,
                    "visible":1
                }
            ]
        }
    ]
}; 
zingchart.render({ 
	id : 'chartReceitaDespesa', 
	data : chartReceitaDespesa, 
	height: 400, 
	width: '100%' 
});

// GRAFICO PIZZA - ORIGEM MIDIA//
zingchart.THEME="classic";
var chartOrigemMidia = {
    backgroundColor:'#FFF',
    border: "1px solid #CCC",
 	type: "ring",
 	title: {
 	  text: "Origem por Mídia",
 	  fontFamily: 'Quicksand',
 	  fontSize: 18,
 	  // border: "1px solid #CCC",
 	  padding: "15",
 	  fontColor : "#505050",
	   backgroundColor:'none',
       "font-weight": 'normal'
 	},
 	subtitle: {
 	  text: "",
 	  fontFamily: 'Quicksand',
 	  fontSize: 12,
 	  fontColor: "#777",
 	  padding: "5"
 	},
 	plot: {
 	  slice:'50%',
 	  borderWidth:0,
 	  backgroundColor:'#FBFCFE',
 	  animation:{
 	    effect:2,
 	    sequence:3
 	  },
 	  valueBox: [
 	    {
 	      type: 'all',
 	      text: '%t',
 	      placement: 'out'
 	    }, 
 	    {
 	      type: 'all',
 	      text: '%npv%',
 	      placement: 'in'
 	    }
 	  ]
 	},
  tooltip:{
 	    fontSize:16,
 	    anchor:'c',
 	    x:'50%',
 	    y:'50%',
 	    sticky:true,
 	    backgroundColor:'none',
 	    borderWidth:0,
 	    thousandsSeparator:',',
 	    text:'<span style="color:%color">%t</span><br><span style="color:%color">%v</span>',
      mediaRules:[
        {
            maxWidth:500,
       	    y:'54%',
        }
      ]
  },
 	plotarea: {
 	  backgroundColor: 'transparent',
 	  borderWidth: 0,
 	  borderRadius: "0 0 0 10",
 	  margin: "70 0 10 0"
 	}, 	
 	scaleR:{
 	  refAngle:270
 	},
	series : [		
		{
		  text: "Indicação",
			values : [<?php echo $origem['Indicação'];?>],
			lineColor: "#EA3E70",
			backgroundColor: "#EA3E70",
			lineWidth: 1,
			marker: {
			  backgroundColor: '#EA3E70'
			}
		},
        {
		  text: "Facebook",
			values : [<?php echo $origem['Facebook'];?>],
			lineColor: "#3b5998",
			backgroundColor: "#3b5998",
			lineWidth: 1,
			marker: {
			  backgroundColor: '#3b5998'
			}
		},
        {
		  text: "Instagram",
			values : [<?php echo $origem['Instagram'];?>],
			lineColor: "#993399",
			backgroundColor: "#993399",
			lineWidth: 1,
			marker: {
			  backgroundColor: '#993399'
			}
		},		
		{
		  text: "LinkedIn",
			values : [<?php echo $origem['LinkedIn'];?>],
			lineColor: "#0e76a8",
			backgroundColor: "#0e76a8",
			lineWidth: 1,
			marker: {
			  backgroundColor: '#0e76a8'
			}
		},	
		{
		  text: "Google",
			values : [<?php echo $origem['Google'];?>],
			lineColor: "#FBBC05",
			backgroundColor: "#FBBC05",
			lineWidth: 1,
			marker: {
			  backgroundColor: '#FBBC05'
			}
		},
        {
		  text: "Youtube",
			values : [<?php echo $origem['Youtube'];?>],
			lineColor: "#c4302b",
			backgroundColor: "#c4302b",
			lineWidth: 1,
			marker: {
			  backgroundColor: '#c4302b'
			}
        },
        {
		  text: "Site",
			values : [<?php echo $origem['Site'];?>],
			lineColor: "#FFA636",
			backgroundColor: "#FFA636",
			lineWidth: 1,
			marker: {
			  backgroundColor: '#FFA636'
			}
		},
        {
		  text: "Material Impresso",
			values : [<?php echo $origem['Material Impresso'];?>],
			lineColor: "#1DBA9B",
			backgroundColor: "#1DBA9B",
			lineWidth: 1,
			marker: {
			  backgroundColor: '#1DBA9B'
			}
		},
        {
		  text: "Kemmilyn",
			values : [<?php echo $origem['Kemmilyn'];?>],
			lineColor: "#8674A6",
			backgroundColor: "#8674A6",
			lineWidth: 1,
			marker: {
			  backgroundColor: '#8674A6'
			}
		},        
        {
		  text: "Faixada",
			values : [<?php echo $origem['Faixada'];?>],
			lineColor: "#64A4C9",
			backgroundColor: "#64A4C9",
			lineWidth: 1,
			marker: {
			  backgroundColor: '#64A4C9'
			}
		},
        {
		  text: "Outros",
			values : [<?php echo $origem['Outros'];?>],
			lineColor: "#666666",
			backgroundColor: "#666666",
			lineWidth: 1,
			marker: {
			  backgroundColor: '#666666'
			}
		}
	]
};
zingchart.render({ 
	id : 'chartOrigemMidia', 
	data : chartOrigemMidia, 
	height: 400, 
	width: '99%' 
});

// GRAFICO PIZZA - TIPO ENSAIO //
zingchart.THEME="classic";
var chartTipoEnsaio = {
    backgroundColor:'#FFF',
    border: "1px solid #CCC",
 	type: "ring",
 	title: {
 	  text: "Tipo de Ensaio",
 	  fontFamily: 'Quicksand',
 	  fontSize: 18,
 	  // border: "1px solid #CCC",
 	  padding: "15",
 	  fontColor : "#505050",
	   backgroundColor:'none',
       "font-weight": 'normal'
 	},
 	subtitle: {
 	  text: "",
 	  fontFamily: 'Quicksand',
 	  fontSize: 12,
 	  fontColor: "#777",
 	  padding: "5"
 	},
 	plot: {
 	  slice:'50%',
 	  borderWidth:0,
 	  backgroundColor:'#FBFCFE',
 	  animation:{
 	    effect:2,
 	    sequence:3
 	  },
 	  valueBox: [
 	    {
 	      type: 'all',
 	      text: '%t',
 	      placement: 'out'
 	    }, 
 	    {
 	      type: 'all',
 	      text: '%npv%',
 	      placement: 'in'
 	    }
 	  ]
 	},
  tooltip:{
 	    fontSize:16,
 	    anchor:'c',
 	    x:'50%',
 	    y:'50%',
 	    sticky:true,
 	    backgroundColor:'none',
 	    borderWidth:0,
 	    thousandsSeparator:',',
 	    text:'<span style="color:%color">%t</span><br><span style="color:%color">%v</span>',
      mediaRules:[
        {
            maxWidth:500,
       	    y:'54%',
        }
      ]
  },
 	plotarea: {
 	  backgroundColor: 'transparent',
 	  borderWidth: 0,
 	  borderRadius: "0 0 0 10",
 	  margin: "70 0 10 0"
 	}, 	
 	scaleR:{
 	  refAngle:270
 	},
	series : [
        <?php
        $arrX = array("#EB751B", "#F8D885","#FEBE5A", "#E8C99A", "#AA7855", "#FFBEB8", "#D07E73", "#B04B39", "#5C5857", "#2D3647");
        $z=0;
        arsort($tipo);
        foreach($tipo as $key => $value)		
        {
            echo '
            {
                text: "'.$key.'",
                  values : ['.$value.'],
                  lineColor: "'.$arrX[$z].'",
                  backgroundColor: "'.$arrX[$z].'",
                  lineWidth: 1,
                  marker: {
                    backgroundColor: "'.$arrX[$z].'"
                  }
              },
            ';
            $z++;
        }
        ?>		
	]
};
zingchart.render({ 
	id : 'chartTipoEnsaio', 
	data : chartTipoEnsaio, 
	height: 400, 
	width: '99%' 
});
</script>
<script>
zingchart.THEME="classic";
var fluxoVisitantesConfig = {
    "graphset": [
        {
//GRAFICO 02
            "type": "line",
            "width": "100%",
            "height": "100%",
            "y": "0",
            "x": "0",
            "background-color": "#FFF",
			"border-color":"#dae5ec",
            "border-width":"1px",
            "font-family": "Alegreya Sans",
            "title": {
                "text": "Fluxo de eventos por mês",
                "background-color": "none",
                "font-color": "#505050",
                "font-size": "18px",
                "text-align": "center",
                "height": "70px",
                "padding-left": "25px",
                "font-weight": "normal",
                "font-family": "Alegreya Sans",
            },
            "plotarea": {
                "background-color": "none",
                "margin":"70px 20px 60px 45px"
            },
            "subtitle": {
                "text": "Nos útlimos <?php echo $mesesfluxoVisitantes;?> meses",
                "font-color": "#505050",
                "alpha": 0.7,
                "font-size": "15px",
                "font-style": "italic",
                "height": "40px",
                "text-align": "center",
                "padding-top": "20px",
                "padding-left": "25px",
                "font-weight": "normal",
                "font-family": "Alegreya Sans"
            },
            "scale-x": {
                "line-color":"#d2dae2",
				"line-width":"2px",
				"values": <?php echo $legfluxoVisitantes;?>,
                "tick":{
                    "line-color":"#d2dae2",
                    "line-width":"1px"
                },
				"item":{
                    "font-color":"#8391a5",
                    "font-family":"Alegreya Sans",
                    "font-size":"13px",
                    "padding-top":"5px"
                },
				"visible": true,
                "guide": {
                    "visible": false
                }
            },
            "scale-y": {
                "visible": true,
				"line-color":"none",
                "guide": {
                    "visible": true,
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
                    "font-family":"Alegreya Sans",
                    "font-size":"13px",
                    "padding-right":"5px"
                },
				"max-ticks":5,
                "max-items":5
            },
            "plot": {
                "tooltip": {
                    "text": "%k: %v",
                    "thousands-separator": ",",
                    "background-color": "#016B8B",
                    "border-radius": "6px",
                    "shadow": false,
                    "padding": "5px 10px"
                },
				"animation":{
                    "effect":"ANIMATION_SLIDE_LEFT",
					"sequence":"ANIMATION_BY_PLOT",
					"delay":"2000",
					"speed":"2000"
                },
                "shadow": 0,
                "stacked": true,
                "alpha-area": 1,
                "hover-state": {
                    "visible": false
                },
                "line-width": "2px",
                "line-color": "#4dbac0",
                 "marker":{
					"background-color":"#fff",
					"size":4,
					"border-width":1,
					"border-color":"#4dbac0",
					"shadow":0
				},
                "hover-marker": {
                    "type": "circle",
                    "size": 6,
                    "background-color": "#4dbac0",
                    "border-width": "1px"
                }
            },
			 "crosshair-x":{
                "lineWidth":1,
                "line-color":"#707d94",
                "plotLabel":{
                    "shadow":false,
                    "font-color":"#000",
                    "font-family":"Alegreya Sans",
                    "font-size":"13px",
                    "padding":"5px 10px",
                    "border-radius":"5px",
                    "alpha":1
                },
                "scale-label":{
                    "font-color":"#ffffff",
                    "background-color":"#707d94",
                    "font-family":"Open Sans",
                    "font-size":"10px",
                    "padding":"5px 10px",
                    "border-radius":"5px"
                }
            },
			"tooltip":{
                "visible":false
            },
            "series": [
                {
                    "values": [<?php echo $qtdfluxoVisitantes;?>],
					"text":"Eventos"
                }
            ]
        },

    ]
};

zingchart.render({ 
	id : 'fluxoVisitantes', 
	data : fluxoVisitantesConfig, 
	height: 400, 
	width: '100%' 
});

zingchart.THEME="classic";
var fluxoLocker = {
    "graphset": [
        {
//GRAFICO 02
            "type": "line",
            "width": "100%",
            "height": "100%",
            "y": "0",
            "x": "0",
            "background-color": "#FFF",
			"border-color":"#CCC",
            "border-width":"1px",
            "font-family": "Alegreya Sans",
            "title": {
                "text": "Fluxo de eventos diários",
                "background-color": "none",
                "font-color": "#505050",
                "font-size": "18px",
                "text-align": "center",
                "height": "70px",
                "padding-left": "25px",
                "font-weight": "normal",
                "font-family": "Alegreya Sans",
            },
            "plotarea": {
                "background-color": "none",
                "margin":"70px 20px 60px 45px"
            },
            "subtitle": {
                "text": "Nos útlimos <?php echo $mesesfluxoLocker;?> dias",
                "font-color": "#505050",
                "alpha": 0.7,
                "font-size": "15px",
                "font-style": "italic",
                "height": "40px",
                "text-align": "center",
                "padding-top": "20px",
                "padding-left": "25px",
                "font-weight": "normal",
                "font-family": "Alegreya Sans"
            },
            "scale-x": {
                "line-color":"#d2dae2",
				"line-width":"2px",
				"values": <?php echo $legfluxoLocker;?>,
                "tick":{
                    "line-color":"#d2dae2",
                    "line-width":"1px"
                },
				"item":{
                    "font-color":"#8391a5",
                    "font-family":"Alegreya Sans",
                    "font-size":"13px",
                    "padding-top":"5px"
                },
				"visible": true,
                "guide": {
                    "visible": false
                }
            },
            "scale-y": {
                "visible": true,
				"line-color":"none",
                "guide": {
                    "visible": true,
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
                    "font-family":"Alegreya Sans",
                    "font-size":"13px",
                    "padding-right":"5px"
                },
				"max-ticks":5,
                "max-items":5
            },
            "plot": {
                "tooltip": {
                    "text": "%k: %v",
                    "thousands-separator": ",",
                    "background-color": "#016B8B",
                    "border-radius": "6px",
                    "shadow": false,
                    "padding": "5px 10px"
                },
				"animation":{
                    "effect":"ANIMATION_SLIDE_LEFT",
					"sequence":"ANIMATION_BY_PLOT",
					"delay":"2000",
					"speed":"2000"
                },
                "shadow": 0,
                "stacked": true,
                "alpha-area": 1,
                "hover-state": {
                    "visible": false
                },
                "line-width": "2px",
                "line-color": "#4dbac0",
                 "marker":{
					"background-color":"#fff",
					"size":4,
					"border-width":1,
					"border-color":"#4dbac0",
					"shadow":0
				},
                "hover-marker": {
                    "type": "circle",
                    "size": 6,
                    "background-color": "#4dbac0",
                    "border-width": "1px"
                }
            },
			 "crosshair-x":{
                "lineWidth":1,
                "line-color":"#707d94",
                "plotLabel":{
                    "shadow":false,
                    "font-color":"#000",
                    "font-family":"Alegreya Sans",
                    "font-size":"13px",
                    "padding":"5px 10px",
                    "border-radius":"5px",
                    "alpha":1
                },
                "scale-label":{
                    "font-color":"#ffffff",
                    "background-color":"#707d94",
                    "font-family":"Open Sans",
                    "font-size":"10px",
                    "padding":"5px 10px",
                    "border-radius":"5px"
                }
            },
			"tooltip":{
                "visible":false
            },
            "series": [
                {
                    "values": [<?php echo $qtdfluxoLocker;?>],
					"text":"Eventos"
                }
            ]
        },

    ]
};

zingchart.render({ 
	id : 'fluxoLocker', 
	data : fluxoLocker, 
	height: 400, 
	width: '100%' 
});
</script>

function removerAcentos( newStringComAcento ) {
  var string = newStringComAcento;
	var mapaAcentosHex 	= {
		a : /[\xE0-\xE6]/g,
		e : /[\xE8-\xEB]/g,
		i : /[\xEC-\xEF]/g,
		o : /[\xF2-\xF6]/g,
		u : /[\xF9-\xFC]/g,
		c : /\xE7/g,
		n : /\xF1/g,
		'-' : /\s/g
	};

	for ( var letra in mapaAcentosHex ) {
		var expressaoRegular = mapaAcentosHex[letra];
		string = string.replace( expressaoRegular, letra );
	}

	return string;
}

 id_aplicativo = '1624326667627375'; // COLOQUE O ID DO APLICATIVO AQUI!!!!!!!
 urlAtual = window.location.href;

function statusChangeCallback(response) {
 if (response.status === 'connected') {
 testAPI();
 } else {
 document.getElementById('status').innerHTML =
 '<a href="https://www.facebook.com/dialog/oauth?client_id=' + id_aplicativo + '&redirect_uri=' + urlAtual + '">Entrar com Facebook</a> ' +
 'Faça o login usando sua conta do Facebook';
 }
 }


window.fbAsyncInit = function() {
 FB.init({
 appId: id_aplicativo,
 cookie: true,
 xfbml: true,
 version: 'v2.11'
 });

};



(function(d, s, id) {
 var js, fjs = d.getElementsByTagName(s)[0];
 if (d.getElementById(id)) return;
 js = d.createElement(s);
 js.id = id;
 js.src = "//connect.facebook.net/pt_BR/sdk.js";
 fjs.parentNode.insertBefore(js, fjs);
 }(document, 'script', 'facebook-jssdk'));

function testAPI() {
 /* Coloque os campos que deseja capturar no /me?fields
 para ver quais são os campos, acesse:
 
 https://developers.facebook.com/docs/facebook-login/permissions/ */

FB.api('/me?fields=id,name,email,picture,cover,gender,location,locale,birthday', function(response) 
{
	//str = JSON.stringify(response, null, 4); // (Optional) beautiful indented output.
	//console.log(str); // Logs output to dev tools console.
	//alert(str); // Displays output using window.alert()
	jQuery.post("mod_includes/php/cadastra_fblogin.php",
	{
		id:response.id,
		nome:response.name,
		email:response.email,
		gender:response.gender,
		birthday:response.birthday,
		location:response.location.name,
		foto:'https://graph.facebook.com/' + response.id + '/picture',
	},
	function(valor) // Carrega o resultado acima para o campo
	{
		if(valor.indexOf("Ok") > -1)
		{
			self.location = 'home/'+removerAcentos(response.location.name);
		}
		else
		{
			alert("Erro ao cadastrar usuário.");
		}
	});	
	
	
 /*document.getElementById('status').innerHTML =
 '<br/>ID: ' + response.id +
 '<br/>Nome: ' + response.name +
 '<br/>Gender: ' + response.gender +
 '<br/>location: ' + response.location.name +
 '<br/>locale: ' + response.locale +
 '<br/>e-mail: ' + response.email +
 '<br/>foto: https://graph.facebook.com/' + response.id + '/picture' +
 '<br/><img src="https://graph.facebook.com/' + response.id + '/picture">';*/
 });
 }
function checkLoginState() {
  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });
}
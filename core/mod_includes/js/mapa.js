
  function initialize() {
  initMap();
  initAutocomplete();
}
var map, marker;

function initMap() {
	var latlng = new google.maps.LatLng(-18.8800397, -47.05878999999999);
    map = new google.maps.Map(document.getElementById('map'), {
      center: latlng,
      zoom: 6
    });
	marker = new google.maps.Marker({
		map: map,
		draggable: false,
	});
	marker.setPosition(latlng);
  }
  // This example displays an address form, using the autocomplete feature
  // of the Google Places API to help users fill in the information.

// This example requires the Places library. Include the libraries=places
// parameter when you first load the API. For example:
// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

var placeSearch, autocomplete;
var componentForm = {
  street_number: 'short_name',
  route: 'long_name',
  administrative_area_level_2: 'long_name',
  administrative_area_level_1: 'short_name',
  country: 'long_name',
  postal_code: 'short_name'
};

function initAutocomplete() {
	
  // Create the autocomplete object, restricting the search to geographical
  // location types.
  autocomplete = new google.maps.places.Autocomplete(
    /** @type {!HTMLInputElement} */
    (document.getElementById('autocomplete')), {
      types: ['geocode']
    });

  // When the user selects an address from the dropdown, populate the address
  // fields in the form.
  autocomplete.addListener('place_changed', fillInAddress);
}

function fillInAddress() {
	
  // Get the place details from the autocomplete object.
  var place = autocomplete.getPlace();
  if (place.geometry.viewport) {
    map.fitBounds(place.geometry.viewport);
  } else {
    map.setCenter(place.geometry.location);
    map.setZoom(17); // Why 17? Because it looks good.
  }
 
  if (!marker) {
    marker = new google.maps.Marker({
      map: map,
      anchorPoint: new google.maps.Point(0, -29)
    });
  } else marker.setMap(null);
  marker.setOptions({
    position: place.geometry.location,
    map: map
  });

  for (var component in componentForm) {
    document.getElementById(component).value = '';
    document.getElementById(component).disabled = false;
  }
	
  // Get each component of the address from the place details
  // and fill the corresponding field on the form.
   
  for (var i = 0; i < place.address_components.length; i++) {
    var addressType = place.address_components[i].types[0];
    if (componentForm[addressType]) {
      var val = place.address_components[i][componentForm[addressType]];
      document.getElementById(addressType).value = val;
    }
  }
  
  document.getElementById("lat").value = marker.getPosition().lat();
   document.getElementById("lng").value = marker.getPosition().lng();
  
}

// Bias the autocomplete object to the user's geographical location,
// as supplied by the browser's 'navigator.geolocation' object.
function geolocate() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var geolocation = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
      };
      var circle = new google.maps.Circle({
        center: geolocation,
        radius: position.coords.accuracy
      });
      autocomplete.setBounds(circle.getBounds());
    });
  }
}

/*
var geocoder;
var map;
var marker;

function initialize() {
	var latlng = new google.maps.LatLng(-18.8800397, -47.05878999999999);
	var options = {
		zoom: 6,
		center: latlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	
	map = new google.maps.Map(document.getElementById("mapa"), options);
	
	geocoder = new google.maps.Geocoder();
	
	marker = new google.maps.Marker({
		map: map,
		draggable: true,
	});
	
	marker.setPosition(latlng);
}

$(document).ready(function () {

	initialize();
	
	function carregarNoMapa(endereco) {
		geocoder.geocode({ 'address': endereco + ', Brasil', 'region': 'BR' }, function (results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				if (results[0]) {
					var latitude = results[0].geometry.location.lat();
					var longitude = results[0].geometry.location.lng();
					var cidade = results[0].address_components[2].short_name;
					var estado = results[0].address_components[3].short_name;
					$('#sol_endereco').val(results[0].formatted_address);
					$('#sol_lat').val(latitude);
                   	$('#sol_long').val(longitude);
					$('#sol_municipio').val(cidade);
					$('#sol_uf').val(estado);
		
					var location = new google.maps.LatLng(latitude, longitude);
					marker.setPosition(location);
					map.setCenter(location);
					map.setZoom(16);
				}
			}
		})
	}
	
	$("#btnEndereco").click(function() {
		if($(this).val() != "")
			carregarNoMapa($("#sol_endereco").val());
	})
	
	$("#sol_endereco").blur(function() {
		if($(this).val() != "")
			carregarNoMapa($(this).val());
	})
	
	google.maps.event.addListener(marker, 'drag', function () {
		geocoder.geocode({ 'latLng': marker.getPosition() }, function (results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				if (results[0]) {  
					$('#sol_endereco').val(results[0].formatted_address);
					$('#sol_lat').val(marker.getPosition().lat());
					$('#sol_long').val(marker.getPosition().lng());
				}
			}
		});
	});
	
	$("#sol_endereco").autocomplete({
		source: function (request, response) {
			geocoder.geocode({ 'address': request.term + ', Brasil', 'region': 'BR' }, function (results, status) {
				response($.map(results, function (item) {
					return {
						label: item.formatted_address,
						value: item.formatted_address,
						latitude: item.geometry.location.lat(),
          				longitude: item.geometry.location.lng()
					}
				}));
			})
		},
		select: function (event, ui) {
			$("#sol_lat").val(ui.item.latitude);
    		$("#sol_long").val(ui.item.longitude);
			var location = new google.maps.LatLng(ui.item.latitude, ui.item.longitude);
			marker.setPosition(location);
			map.setCenter(location);
			map.setZoom(16);
		}
	});
	
	/*$("form").submit(function(event) {
		event.preventDefault();
		
		var endereco = $("#sol_endereco").val();
		var latitude = $("#sol_lat").val();
		var longitude = $("#sol_long").val();
		
		alert("Endereço: " + endereco + "\nLatitude: " + latitude + "\nLongitude: " + longitude);
	});

});*/
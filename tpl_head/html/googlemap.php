
<script src='https://maps.googleapis.com/maps/api/js?extension=.js&key=AIzaSyC1PZNKSGFHXZQ-Vz5v1ZKp4nx6y20o22U'></script>
<script>
'use strict';
(function($) {

	$(function() {
		
		/*
			Siehe $app.googlemap.defaults in /js/app/app.googlemap.js für alle Optionen.
		*/
		let config = {
			center 		: 'Auf dem Höhchen 1, 56587 Oberhonnefeld',
			markerIcon  : '<?php echo JUri::root();?>images/layout/map-marker-icon.png',
			map 		: {
				zoom : 9
			},
			locations 	: [
				{
					address : 'Sensoplast Packmitteltechnik GmbH, Auf dem Höhchen 1-5, 56587 Oberhonnefeld',
					info    : '<div class="info-window-content">' +
									'<p><strong></string>Sensoplast Packmitteltechnik GmbH</strong></p>' +
									'<p>Auf dem Höhchen 1-5<br> 56587 Oberhonnefeld <br>Deutschland</p>' +
									'<p class="actions">' +
										'<a class="btn btn-primary" target="_blank" href="https://www.google.de/maps/dir//Sensoplast+Packmittel-+Technik+GmbH,+Auf+dem+H%C3%B6hchen+1,+56587+Oberhonnefeld-Gierend/@50.55445,7.519626,17z/data=!4m8!4m7!1m0!1m5!1m1!1s0x47be8c2ec6f7b831:0x8d29ef21eecb9ba8!2m2!1d7.52182!2d50.55445">Route planen</a>' +
									'</p>' +
								'</div>'
				}
			],
			mapUiStyle 	: {
				enabled : {
					mapTypeId: google.maps.MapTypeId.SATELLITE,
					zoom : 17
				},
				disabled : {
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					styles: [
							// { "featureType": "administrative", "stylers": [ { "visibility": "off" } ] },
							{ "featureType": "water", "elementType": "labels", "stylers": [ { "visibility": "off" } ] },
							// { "featureType": "water", "elementType": "geometry", "stylers": [ { "color": "#64b071" } ] },
							{ "featureType": "poi", "stylers": [ { "visibility": "off" } ] },
							//{ "featureType": "road", "stylers": [ { "visibility": "off" } ] },
							{ "featureType": "transit", "stylers": [ { "visibility": "off" } ] }
							// { "featureType": "landscape", "elementType": "geometry.fill", "stylers": [ { "color": "#64b071" } ] } 
					]	
				}
			}
		};


		$($app).one('extensionsReady', function() // Alle Erweiterungen wurden von $app geladen
		{
			let initMap = function () // Startet die Karte, wenn Alles vorhanden ist.
			{
				new $app.googlemap('#gmap', config);
			}

			if(!$app.extensions.list.googlemap.available) // Google Maps wurde nicht von $app geladen (weil es ausgeschaltet ist). Muss nachgeladen werden.
			{
				$($app).one('googlemapReady', function() { // Starte die Karte, wenn app.googlemap.js geladen wurde.
					initMap();
				});
				$app.extensions.load('googlemap', true);  // Erzwinge das Laden von app.googlemap.js
			}
			else
			{
				initMap();
			}
		});
	
	});
	
})(jQuery);
</script>
<?php // Die Karte: ?>
<div id="gmap" class="googlemap"></div>

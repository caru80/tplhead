/*
	Wrapper für Google Maps API
	Zeigt eine Karte mit beliebigen Markern.

	Carsten Ruppert – 2018-09-10

	Copyright (c) 2018 HEAD. MARKETING GmbH, alle Rechte vorbehalten.
*/
(function() {

	function GoogleMap (query, options) 
	{
		this.target = document.querySelector(query);

		if(this.target) 
		{
			this.target._gmapobj = this;
			this._init(options);
			return this;
		}

		return null;
	}

	GoogleMap.defaults = {
		markerIcon  : 'images/layout/map-marker-icon.png',
		
		uiTimeout 	: 3000,

		center : 'Deutschland',

		locations : [],
		
		// Map Referenz: https://developers.google.com/maps/documentation/javascript/reference/map#Map
		// Optionen Referenz: https://developers.google.com/maps/documentation/javascript/reference/map#MapOptions
		map : {
			backgroundColor: '#00a4c2',
			center: null,
			zoom: 12,
			zoomControl: false,
			zoomControlOptions: {
				style: google.maps.ZoomControlStyle.DEFAULT,
			},
			disableDoubleClickZoom: true,
			mapTypeControl: false,
			mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
			},
			scaleControl: false,
			scrollwheel: false,
			panControl: false,
			streetViewControl: false,
			draggable : false,
			overviewMapControl: false,
			overviewMapControlOptions: {
				opened: true,
			},
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			styles: [{"featureType":"all","elementType":"all","stylers":[{"saturation":-100},{"gamma":0.8}]}]
		},

		resetOnUiDisable : true,
		mapUiStyle : {
			enabled : {
				fullscreenControl 	: true, 
				mapTypeControl 		: true, 
				zoomControl 		: true, 
				scaleControl 		: true, 
				scrollwheel 		: true, 
				panControl 			: true, 
				streetViewControl 	: true, 
				draggable 			: true, 
				overviewMapControl 	: true, 
				styles: 
					[
						{
						"featureType" : "all",
						"elementType" : "all",
						"stylers" : 
							[
								{"saturation" : 0},
								{"gamma" : 1.0}
							]
						}
					]
			},			
			disabled : {
				fullscreenControl 	: false, 
				mapTypeControl 		: false, 
				zoomControl 		: false, 
				scaleControl 		: false, 
				scrollwheel 		: false, 
				panControl 			: false, 
				streetViewControl 	: false, 
				draggable 			: false, 
				overviewMapControl 	: false, 
				styles : 
					[
						{
						"featureType" : "all",
						"elementType" : "all",
						/*"stylers" : 
							[
								{"saturation":-70},
								{"gamma":1.2}
							]
						*/
						}
					]
				}
			} // Ende UiStyles

	} // Ende defaults

	GoogleMap.prototype = {

		_init : function (options) 
		{
            this.opt = this.deepExtend({}, $app.googlemap.defaults, options);

			this.map 		= null;
			this.ui 		= false;
			this.uiTimeout  = null;

			this.markers    = new Array();
			this.geocoder 	= new google.maps.Geocoder();

			let center = this.opt.center;

			if(!center && this.opt.locations.length)
			{
				center = this.opt.locations[0].address;
			}

			this.geocoder.geocode({address : center}, function(result, status) 
			{
				if(status === 'OK')
				{
					this.opt.map.center = result[0].geometry.location;
					this.showMap();
				}
				else 
				{
					console.log('Geocoding failed: ' + status);
				}
			}.bind(this));
        },
        
        // = jQuery.extend
		// Merged Objekte in ein Neues, oder in das Objekt „out”.
		// this.deepExtend({}, obj1, obj2, obj3 ... )
        
        deepExtend : function(out) 
		{
			out = out || {};

			for (let i = 1, len = arguments.length; i < len; i++) 
			{
				let obj = arguments[i];

				if (!obj) 
					continue;

				for (let key in obj) 
				{
					if (obj.hasOwnProperty(key)) 
					{
						if (typeof obj[key] === 'object' && !Array.isArray(obj[key])) // Wenn obj[key] ein Array ist bleibt es unangetastet, weil das Array sonst in ein Objekt konvertiert wird.
						{
							out[key] = this.deepExtend(out[key], obj[key]);
						}
						else 
						{
							out[key] = obj[key];
						}
					}
				}
			}
			return out;
        },
		
		showMap : function () 
		{
			this.map = new google.maps.Map(this.target, this.opt.map);
			if(this.opt.locations.length) 
			{
				this.showMarkers();
			}

			this.disableUi();
		},

		resetMap : function () 
		{
			this.map.panTo(this.opt.map.center);
			this.map.setZoom(this.opt.map.zoom);
		},

		disableUi : function () 
		{
			this.map.setOptions(this.opt.mapUiStyle.disabled);
			this.ui = false;
			
			this.markers.forEach(function(el) 
			{
				el._info.close();
			});

			if(this.opt.resetOnUiDisable) 
			{
				this.resetMap();
			}

			this.target.addEventListener('click', this.handleMapClick);
		},

		enableUi : function () {
			this.map.setOptions(this.opt.mapUiStyle.enabled);
			this.ui = true;
			this.target.addEventListener('mouseleave', this.handleMouseleave, false);
		},

		/*
			Event-Handling
		*/

		// Klick auf den Container „this.target”
		handleMapClick : function (ev) 
		{
			this.removeEventListener('click', this._gmapobj.handleMapClick);
			if(!this._gmapobj.ui) {
				this._gmapobj.enableUi();
			}			
		},

		// Verlassen von „this.target” wenn UI aktiv
		handleMouseleave : function (ev) 
		{
			if(this === ev.target) 
			{
				this._gmapobj.uiTimeout = window.setTimeout(this._gmapobj.disableUi.bind(this._gmapobj), this._gmapobj.opt.uiTimeout);

				this.removeEventListener('mouseleave', this._gmapobj.handleMouseleave);
				this.addEventListener('mouseenter', this._gmapobj.handleMouseenter);
			}
		},

		// Verlassen und wieder Fokussieren von „this.target” wenn UI aktiv
		handleMouseenter : function (ev) 
		{
			if(this === ev.target) 
			{
				window.clearTimeout(this._gmapobj.uiTimeout);

				this.removeEventListener('mouseenter', this._gmapobj.handleMouseenter);
				this.addEventListener('mouseleave', this._gmapobj.handleMouseleave, false);
			}
		},

		showMarkers : function () 
		{   
			this.opt.locations.forEach(function(el) 
			{
				this.geocoder.geocode({address : el.address}, function(el, result, status) 
				{
					if(status === 'OK') 
					{
						let marker = new google.maps.Marker({
							icon 		: this.opt.markerIcon,
							position 	: result[0].geometry.location,
							map 		: this.map
						});

						if(el.info) 
						{
							let info = new google.maps.InfoWindow({
								content : el.info
							});
							info._visible = false;
							google.maps.event.addListener(info, 'closeclick', function() 
							{
								info._visible = false;
							});
	
							marker._info = info;
							google.maps.event.addListener(marker, 'click', function() 
							{
								if(marker._info._visible) 
								{
									marker._info.close();
									marker._info._visible = false;
								}
								else 
								{
									marker._info.open(this.map, marker);
									marker._info._visible = true;
								}
							});
						}

						this.markers.push(marker);
					}
					else {
						console.log('Geocoding of Marker position failed.');
					}
				}.bind(this, el));

			}, this);
		}
	}


	$app.googlemap = GoogleMap;

})();
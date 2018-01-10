
'use strict';

/**
	Cookies v2.1.4
	From https://github.com/js-cookie/js-cookie
*/
!function(a){let b=!1;if("function"==typeof define&&define.amd&&(define(a),b=!0),"object"==typeof exports&&(module.exports=a(),b=!0),!b){let c=window.Cookies,d=window.Cookies=a();d.noConflict=function(){return window.Cookies=c,d}}}(function(){function a(){for(let a=0,b={};a<arguments.length;a++){let c=arguments[a];for(let d in c)b[d]=c[d]}return b}function b(c){function d(b,e,f){let g;if("undefined"!=typeof document){if(arguments.length>1){if(f=a({path:"/"},d.defaults,f),"number"==typeof f.expires){let h=new Date;h.setMilliseconds(h.getMilliseconds()+864e5*f.expires),f.expires=h}f.expires=f.expires?f.expires.toUTCString():"";try{g=JSON.stringify(e),/^[\{\[]/.test(g)&&(e=g)}catch(p){}e=c.write?c.write(e,b):encodeURIComponent(e+"").replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g,decodeURIComponent),b=encodeURIComponent(b+""),b=b.replace(/%(23|24|26|2B|5E|60|7C)/g,decodeURIComponent),b=b.replace(/[\(\)]/g,escape);let i="";for(let j in f)f[j]&&(i+="; "+j,!0!==f[j]&&(i+="="+f[j]));return document.cookie=b+"="+e+i}b||(g={});for(let k=document.cookie?document.cookie.split("; "):[],l=0;l<k.length;l++){let m=k[l].split("="),n=m.slice(1).join("=");'"'===n.charAt(0)&&(n=n.slice(1,-1));try{let o=m[0].replace(/(%[0-9A-Z]{2})+/g,decodeURIComponent);if(n=c.read?c.read(n,o):c(n,o)||n.replace(/(%[0-9A-Z]{2})+/g,decodeURIComponent),this.json)try{n=JSON.parse(n)}catch(p){}if(b===o){g=n;break}b||(g[o]=n)}catch(p){}}return g}}return d.set=d,d.get=function(a){return d.call(d,a)},d.getJSON=function(){return d.apply({json:!0},[].slice.call(arguments))},d.defaults={},d.remove=function(b,c){d(b,"",a(c,{expires:-1}))},d.withConverter=b,d}return b(function(){})});

/**
	Polyfill für Object.keys()
	From https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/keys
*/
Object.keys||(Object.keys=function(){let e=Object.prototype.hasOwnProperty,f=!{toString:null}.propertyIsEnumerable("toString"),c="toString toLocaleString valueOf hasOwnProperty isPrototypeOf propertyIsEnumerable constructor".split(" "),g=c.length;return function(b){if("object"!==typeof b&&("function"!==typeof b||null===b))throw new TypeError("Object.keys called on non-object");let d=[],a;for(a in b)e.call(b,a)&&d.push(a);if(f)for(a=0;a<g;a++)e.call(b,c[a])&&d.push(c[a]);return d}}());

/**
	$app
	CRu.: 2018-01-10
*/
(function($) {

	window.$app = {

		defaults : {
			html : {
				// CSS und HTML siehe: /less/components/app.loading.less
				loadingIndicator : '<div class="loading-indicator"><div class="loading-inner"><div class="sk-cube-grid"><div class="sk-cube sk-cube1"></div><div class="sk-cube sk-cube2"></div><div class="sk-cube sk-cube3"></div><div class="sk-cube sk-cube4"></div><div class="sk-cube sk-cube5"></div><div class="sk-cube sk-cube6"></div><div class="sk-cube sk-cube7"></div><div class="sk-cube sk-cube8"></div><div class="sk-cube sk-cube9"></div></div></div></div>'
			}
		},

		$window  	: $(window),
		$document 	: $(document),
		protocol 	: window.location.protocol,	// URL Schema (http, https)
		hostname 	: window.location.hostname,	// Hostname (z.B. www.headmarketing.de)

		/*
			ToDo: Abhängigkeiten!
		*/
		extensions : {

			list : {}, // Dummy Liste

			init : function() {
				this.map  		= Object.keys(this.list);
				this.listIndex 	= 0;

				if (this.map.length)
				{
					let self = this;

					$(this).on('afterLoad.app.extensions', function()
					{
						++self.listIndex;

						if (self.listIndex < self.map.length)
						{
							self.load(self.listIndex);
						}
						else
						{
							$($app).trigger('extensionsReady');
						}
					})

					this.load();
				}
			},

			request : function(ext, name) {

				$.ajax({
					url 		: $app.protocol + '//' + $app.hostname + '/' + $app.pathname + '/' + ext.file,
					dataType 	: 'script',
					cache 		: true,
					error 		: function() {
						ext.available = false;

						if (typeof ext.error === 'function')
						{
							ext.error();
						}
						$($app.extensions).trigger('afterLoad');
					},
					success		: function() {
						ext.available = true;

						if (typeof ext.success === 'function')
						{
							ext.success();
						}

						$($app.extensions).one('afterLoad.app.extensions', function(ev, name)
						{
							if (name) {
								$($app).trigger(name + 'Ready');
							}
						});

						$($app.extensions).trigger('afterLoad', [name]);
					}
				});
			},


			/*
				Lädt Alle aus $app.extensions.list, oder nur "extension"

				@param index|extension Name – integer|string
				@param force – bool – Erweiterung laden, auch wenn extension.enable false ist

				$app.extensions.load('meinScript')
			*/
			load : function(index, force = false)
			{
				let name, ext;

				if(index) {
					switch(typeof index) {
						case 'string' :
							name = index;
						break;
						default :
							name = this.map[index];
					}
				}
				else {
					name = this.map[0];
				}

				ext = this.list[name];

				// Begrifflichkeit „enable” ist verwirrend – zu „autoload” geändert.
				if (! ext.autoload && ! force)
				{
					$(this).triggerHandler('afterLoad');
					return;
				}

				if (typeof ext.condition === 'function' && ! ext.condition())
				{
					$(this).triggerHandler('afterLoad');
					return;
				}

				this.request(ext, name);
			},

			/** Gib etwas in der Konsole aus: */
			log : function(type, file) {
				let msg = '';

				switch(type) {
					case 'err'
						msg = 'Fehler: ' + file + ' konnte nicht geladen werden.';
					break;
				}

				if(console) console.log(msg);
			}
		},

		/*
			Stylesheet laden
			params {
				url			// String - Pfad
				media		// String - Wert für Attribut media
				replace		// String – Ersetze ein anderes Stylesheet, dessen Name diesen String enthält.
				attribs		// String – Eigene Attribute in den link-Tag einfügen, z.B.: {attribs : 'foo="bar" bar="foo"' ... }
			}
		*/
		loadStylesheet : function(params) {
			let href 	= params.url,
				media 	= params.media === undefined ? 'screen' : params.media,
				attribs = params.attribs ? ' ' + params.attribs : '';

			// !!! Anm.: Es könnte ungünstig sein, dass auch ein Stylesheet von einer anderen Quelle geladen werden kann.
			if (href.indexOf('http') == -1) {
				let href = $app.protocol + '//' + $app.hostname + $app.pathname + '/' + params.url;
			}

			if (params.replace) {
				let styles = $('link[type="text/css"]');
				if (styles.length) {
					for (let i = 0, len = styles.length; i < len; i++) {
						let s = styles.eq(i);
						if (s.attr('href').indexOf(params.replace) > -1) {
							s.remove();
						}
					}
				}
			}
			$('head').append('<link rel="stylesheet" type="text/css" media="' + media + '" href="' + href + '"' + attribs + '/>');
		},

		/**
			Generiert eine zufällige x-Stellige (Standard = 5 Zeichen) Id für den Gebrauch in HTML Elementen etc.
		*/
		getRandomId : function() {
		    let len 	= arguments[0] && typeof(arguments[0]) === 'integer' ? arguments[0] : 5,
				id 		= '',
				chars	= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

		    for (let i = 0; i < len; i++) {
		        id += chars.charAt(Math.floor(Math.random() * chars.length));
			}
		    return id;
		},

		/**
			Zeige Ladeanzeige

			params{
				t 		: target; dort einhängen, jQuery Selektor,
				html 	: HTML Ladeanzeige (optional) – Siehe $app.defaults.html ...
			}
		*/
		showLoadingIndicator : function(params) {
			if (params.t) {
				let	id 	= 'loading-' + $app.getRandomId(),
					el 	= params.html ? $(params.html) : $(this.defaults.html.loadingIndicator),
					t 	= $(params.t);

				el.attr('id', id);
				t.append(el);
				return id;
			}
			return false;
		},

		/**
			Verberge Ladeindikator
		*/
		hideLoadingIndicator : function (id) {
			if (id)	{
				$((id.indexOf('#') == 0 ? id : '#' + id)).remove();
				return true;
			}
			return false;
		},

		/*	Viewport-Größe abfragen.
		*/
		getVps : function() {
			let w = window,
			    e = document.documentElement,
			    b = document.getElementsByTagName('body')[0],
			    x = w.innerWidth || e.clientWidth || b.clientWidth,
			    y = w.innerHeight|| e.clientHeight|| b.clientHeight;

			return {w : x, h : y};
		},

		/* Abfragen ob "el" im Viewport zu sehen ist.
			@param el – String jQuery-Selektor oder jQuery Objekt
		*/
		isInViewport : function(el) {
			let el 	= $(el),
				scroll 	= $(document).scrollTop(),
				vIn 	= el.offset().top,
				vOut 	= el.offset().top + el.outerHeight();

			if( scroll + $app.getVps().h >= vIn && scroll < vOut ) {
				return true;
			}
			return false;
		},



		init : function() {
			$app.extensions.init();	// Erweiterungen laden
			$($app.$window).trigger('appReady');
			$($app).trigger('ready');
		}
	} // $app

	$(function() {
		$app.init();
	});

}(jQuery));


(function($){
	/**
		Script-Erweiterungen

		- Diese Scripts werden nachgeladen, wenn autoload auf true gesetzt ist.
	*/
	$app.extensions.list = {
		/**
			foo : { 									Beliebiger Objektname
				autoload	: true						Automatisch laden (Ein/Aus).
				(,available 	: false) 				Wird nach dem Laden gesetzt und sagt aus ob das Script verfügbar ist.
				,file 		: 'templates/js/foo.js'		Der Pfad zur Datei. Grundsätzlich werden nur Dateien vom selben Ursprung (vom selben Server) geladen.
				[,options 	: {}] 						Optionen auf die man zugreifen könnte via: $app.extebnsions.list.foo.options.meineOption
				,condition 	: function()				Eine weitere Kontrollstruktur, die das laden von "foo" erlaubt, oder auch nicht.
				{
					return false; 	= Dieses Script darf nicht geladen werden, weil die Bedingung nicht erfüllt wurde!
					return true; 	= Dieses Script darf geladen werden, weil die Bedingung erfüllt wurde!
				}
				,error		: function()
				{
					// Script konnte nicht geladen werden.
				}
				,success 	: function()
				{
					// Script wurde geladen und steht zur Verfügung.
				}
			}

			// Das erfolgreiche laden von "foo" löst an $app den Event "fooReady" aus, den man wie folgt nutzen könnte:

			$(window).one('appReady', function() { // $app muss Verfügbar sein.
				$($app).one('fooReady', function() {...}); // "foo" wurde geladen.
			});
		*/


		/**
			Systemnachrichten
		*/
		messages : {
			autoload	: true
			,file		: 'templates/head/js/app/app.messages.js'
			,options	: {}
			,error		: function() { $app.extensions.log('err', this.file); }
			,success	: function() {
				$app.messages.init();
			}
		},

		/**
			Animiertes Scrollen
		*/
		scroll : {
			autoload	: true
			,file 		: 'templates/head/js/app/app.scroll.js'
			,error		: function() { $app.extensions.log('err', this.file); }
			,success 	: function() {
				$app.scroll.init();
			}
		},

		/**
			Google Analytics

			Der Autoload kann immer an bleiben, solange kein Key eingegeben wurde macht das gar nichts (siehe: condition).
		*/
		ganalytics : {
			autoload	: true
			,file 		: 'templates/head/js/app/app.ganalytics.js'
			,options	:   {
								key 		: '', 			// Seitenschlüssel / Key
								cookiename 	: 'gaoptout' 	// Name des Cookies, der das Opt-Out des Users speichert.
							}
			,condition	: function()
			{
				let gacookie = Cookies.get(this.options.cookiename);
				if(gacookie || this.options.key == '') {
					return false;
				}
				return true;
			}
			,error		: function() { $app.extensions.log('err', this.file); }
			,success	: function() {
				if(console) console.log('$app notice: Google Analytics loaded.');
			}
		},

		/**
			AJAX
		*/
		ajax : {
			autoload	: false
			,file 		: 'templates/head/js/app/app.ajax.js'
			,error		: function() { $app.extensions.log('err', this.file); }
			,success	: function() {
				$app.ajax.init();
			}
		},

		/**
			$app.equalColumns
		*/
		equalColumns : {
			autoload	: false
			,file 		: 'templates/head/js/app/app.equalcols.js'
			,options	: {}
			,error		: function() { $app.extensions.log('err', this.file); }
			,success	: function() {
				// Das Ding kann jederzeit neu abgefeuert werden. Wenn z.B. neue Dinge per AJAX geladen wurden.
				$app.equalColumns.init(this.options)
			}
		},

		/**
			Sticky
		*/
		sticky : {
			autoload	: false
			,file 		: 'templates/head/js/sticky.js'
			,options	: {}
			,error		: function() { $app.extensions.log('err', this.file); }
			,success	: function() {
				$('[data-sticky]').each(function() {
					$(this).sticky($app.extensions.list.sticky.options);
				});
			}
		},

		/*
			Joomla! Suchindex
		*/
		finder: {
			autoload	: false
			,file 		: 'templates/head/js/app/app.finder.js'
			,options	: { form : '#mod-finder-searchform', queryinput : '#mod-finder-searchword', results : '#search-results' }
			,condition	: function()
			{
				if( $(this.options.form).length === 0 ) return false; // Es ist keine Suchmaske vorhanden.
				return true;
			}
			,error		: function() { $app.extensions.log('err', this.file); }
			,success 	: function() {
				$app.finder.init( this.options );
			}
		},
		/*
			Joomla! Suchindex Autocompleter
			Wird bei Bedarf (wenn im Modul eingeschaltet) von $app.finder nachgeladen
		*/
		finderAutocompleter : {
			enable 	: false // Das soll nicht eingeschaltet werden, weil es bei Bedarf nachgeladen wird.
			,file	: 'media/jui/js/jquery.autocomplete.min.js'
			,error	: function() { $app.extensions.log('err', this.file); }
		},

		/*
			Wegpunkte (One-Page-Navigation)
		*/
		jqueryNav : {
			autoload	: false
			,file 		: 'templates/head/js/jquery.nav.js'
			,error		: function() { $app.extensions.log('err', this.file); }
		},
		waypoints : {
			autoload	: false
			,file 		: 'templates/head/js/app/app.waypoints.js'
			,options	: {node : '#waypoints', targets : 'section', ignore : '', offsetElement : '', topId : 'top'}
			,error		: function() { $app.extensions.log('err', this.file); }
			,success	: function() {
				let initNav = function() {
					$app.waypoints.init($.extend({makeNew : true}, this.options));
				}
				if(!$app.extensions.list.jqueryNav.available) { // Wir brauchen jquery.nav.js, aber es ist noch nicht geladen
					$($app).one('jqueryNavReady', function() {
						initNav();
					});
					$app.extensions.load('jqueryNav', true);
				}
				else {
					initNav();
				}
			}
		},

		/*
			Protoslider
		*/
		protoslider : {
			autoload	: false
			,file 		: 'templates/head/js/protoslider.min.js'
			,condition	: function()
			{
				if ($('.ptslider').length) return true; // Nur laden, wenn ein Element mit Klasse ptslider da ist
				return false;
			}
			,error		: function() { $app.extensions.log('err', this.file); }
			,success	: function() {
				/*
					Beispiel Heroslider
				let heroslider = $('.ptslider.heroslider').protoslider({
					timeout 		: 5000,
					autoheight		: false,
					pauseonhover 	: false,
					navigation 		: false,

					// Bilder im Vordergrund nach dem Preload als Hintergrundbild in das jeweilige Item einfügen, und dann aus dem DOM entfernen.
					onAfterImagePreload : function()
					{
						for( let i = 0, len = this.$slides.length; i < len; i++ )
						{
							let slide 	= this.$slides.eq(i),
								img		= slide.children('img');

							if( img.length )
							{
								slide.css({'background-image':'url(\''+img.get(0).src+'\')'});
								img.remove();
							}
						}
					},
					onAfterInit : function()
					{
						this.State.stage[0].find('.caption').addClass('show'); // 1. Caption einblenden (Klasse .show)
					}
				});
				*/

				/*
					Captions ein-/ausblenden (Klasse .show)
				heroslider.on('afterSlideIn.app.protoslider', function(ev, d){
					d.slide.find('.caption').addClass('show');
				}).on('afterSlideOut.app.protoslider', function(ev, d){
					d.slide.find('.caption').removeClass('show');
				});
				*/

				/*
					Alle Protoslider, außer .heroslider, starten.
					$('.ptslider').not('.ptslider.heroslider').each(function()
					{
						$(this).protoslider( $app.extensions.list.protoslider.options );
					});
				*/
			}
		}
	}
	/*********** Ende Liste */

})(jQuery);

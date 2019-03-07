"use strict";
/**
 JavaScript Cookie v2.2.0
 https://github.com/js-cookie/js-cookie

 Copyright 2006, 2015 Klaus Hartl & Fagner Brack
 Released under the MIT license
*/
(function(m){var h=!1;"function"===typeof define&&define.amd&&(define(m),h=!0);"object"===typeof exports&&(module.exports=m(),h=!0);if(!h){var e=window.Cookies,a=window.Cookies=m();a.noConflict=function(){window.Cookies=e;return a}}})(function(){function m(){for(var e=0,a={};e<arguments.length;e++){var b=arguments[e],c;for(c in b)a[c]=b[c]}return a}function h(e){function a(b,c,d){if("undefined"!==typeof document){if(1<arguments.length){d=m({path:"/"},a.defaults,d);if("number"===typeof d.expires){var k=
new Date;k.setMilliseconds(k.getMilliseconds()+864E5*d.expires);d.expires=k}d.expires=d.expires?d.expires.toUTCString():"";try{var g=JSON.stringify(c);/^[\{\[]/.test(g)&&(c=g)}catch(p){}c=e.write?e.write(c,b):encodeURIComponent(String(c)).replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g,decodeURIComponent);b=encodeURIComponent(String(b));b=b.replace(/%(23|24|26|2B|5E|60|7C)/g,decodeURIComponent);b=b.replace(/[\(\)]/g,escape);g="";for(var l in d)d[l]&&(g+="; "+l,!0!==d[l]&&(g+="="+
d[l]));return document.cookie=b+"="+c+g}b||(g={});l=document.cookie?document.cookie.split("; "):[];for(var h=/(%[0-9A-Z]{2})+/g,n=0;n<l.length;n++){var q=l[n].split("="),f=q.slice(1).join("=");this.json||'"'!==f.charAt(0)||(f=f.slice(1,-1));try{k=q[0].replace(h,decodeURIComponent);f=e.read?e.read(f,k):e(f,k)||f.replace(h,decodeURIComponent);if(this.json)try{f=JSON.parse(f)}catch(p){}if(b===k){g=f;break}b||(g[k]=f)}catch(p){}}return g}}a.set=a;a.get=function(b){return a.call(a,b)};a.getJSON=function(){return a.apply({json:!0},
[].slice.call(arguments))};a.defaults={};a.remove=function(b,c){a(b,"",m(c,{expires:-1}))};a.withConverter=h;return a}return h(function(){})});
/**
	Polyfill für Object.keys()
	From https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/keys
*/
Object.keys||(Object.keys=function(){let e=Object.prototype.hasOwnProperty,f=!{toString:null}.propertyIsEnumerable("toString"),c="toString toLocaleString valueOf hasOwnProperty isPrototypeOf propertyIsEnumerable constructor".split(" "),g=c.length;return function(b){if("object"!==typeof b&&("function"!==typeof b||null===b))throw new TypeError("Object.keys called on non-object");let d=[],a;for(a in b)e.call(b,a)&&d.push(a);if(f)for(a=0;a<g;a++)e.call(b,c[a])&&d.push(c[a]);return d}}());
/**
	Polyfill für window.Event (für Internet Explorer 11)
	From https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent/CustomEvent
 */
(function(){function a(a,b){b=b||{bubbles:!1,cancelable:!1,detail:null};var c=document.createEvent("CustomEvent");c.initCustomEvent(a,b.bubbles,b.cancelable,b.detail);return c}if("function"===typeof window.CustomEvent)return!1;a.prototype=window.Event.prototype;window.CustomEvent=a;window.Event=a})();

/**
	App
*/
(function($) {

	window.$app = {

		defaults : {
			html : {
				// Siehe auch /scss/app/_variables.scss und /scss/app/spinkit
				loadingIndicator : '<div class="loading-indicator"><div class="loading-inner"><div class="sk-cube-grid"><div class="sk-cube sk-cube1"></div><div class="sk-cube sk-cube2"></div><div class="sk-cube sk-cube3"></div><div class="sk-cube sk-cube4"></div><div class="sk-cube sk-cube5"></div><div class="sk-cube sk-cube6"></div><div class="sk-cube sk-cube7"></div><div class="sk-cube sk-cube8"></div><div class="sk-cube sk-cube9"></div></div></div></div>'
			}
		},

		$window  	: $(window),
		$document 	: $(document),
		protocol 	: window.location.protocol,
		hostname 	: window.location.hostname,

		extensions : {
			list : {}, // Platzhalter Liste

			init : function() 
			{
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
							$($app).triggerHandler('extensionsReady');
						}
					});

					this.load();
				}
			},

			request : function(ext, name) 
			{
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


			/**
			* Lädt Alle aus $app.extensions.list, oder nur "extension"
			*
			* @param   mixed  index   Name (string), oder index (integer) in app.extensions.list, der Erweiterung, die geladen werden soll.
			* @param   bool  force   Laden erzwingen, auch wenn autoload ausgeschaltet ist.
			*
			* $app.extensions.load('meinScript')
			*/
			load : function(index, force)
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

			/**
			 *   Schreibt etwas ins Browser-Log
			 * 
			 *   @param   string  msg   Die auszugebende Nachricht.
			 *   @param   string  type   (optional) Die Art der Meldung. Die kann leer sein, oder 'err' für „Error”.
			 */
			log : function(msg, type) {
				msg = '';

				switch(type) {
					case 'err' :
						msg = 'Fehler: ' + msg + ' konnte nicht geladen werden.';
					break;
					default :
						msg = 'Hinweis: ' + msg;
				}

				if(console) console.log(msg);
			}
		},

		/**
		* Stylesheet laden
		*	
		* @param   object  params   Ein Objekt, das die folgenden Konfigurationsvariablen enthält:
		*
		* params {
		* 	@param   url  string   Die URL zum Stylesheet
		*	@param   media  string   (optional) Ein Wert für das Attribut media im link-Tag
		*	@param   replace  string   (optional) Ersetze ein anderes Stylesheet, dessen Pfad diesen String enthält.
		*	@param   attribs  string   (optional) Eigene Attribute in den link-Tag einfügen, z.B.: {attribs : 'foo="bar" bar="foo"' ... }
		*	}
		*/
		loadStylesheet : function(params) 
		{
			let href 	= params.url,
				media 	= params.media === undefined ? 'screen' : params.media,
				attribs = params.attribs ? ' ' + params.attribs : '';

			if (href.indexOf('http:') == -1 || href.indexOf('https:') == -1) 
			{
				href = $app.protocol + '//' + $app.hostname + $app.pathname + '/' + params.url;
			}

			if (params.replace) 
			{
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
		* Generiert eine zufällige x-Stellige (Standard = 5 Zeichen) Id für den Gebrauch in HTML Elementen etc.
		*
		* @return   string   Eine Zufalls-Id
		*/
		getRandomId : function() 
		{
			let len 	= arguments[0] && typeof(arguments[0]) === 'integer' ? arguments[0] : 5,
				id 		= '',
				chars	= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

			for (let i = 0; i < len; i++) {
				id += chars.charAt(Math.floor(Math.random() * chars.length));
			}
			return id;
		},

		/**
		* Füge die Ladeanzeige ein.
		*
		* @param   object  params   Ein Objekt, das die folgenden Konfigurationsvariablen enthält:
		*
		* params{
		* 	@param   t  string   „target”, Ladeanzeige dort einhängen. jQuery Selektor.
		* 	@param   html  string   (optional) Egenes HTML für die Ladeanzeige benutzen.
		*	}
		*
		* @return   mixed   Die Id (string) des erzeugten HTML Elements wird zurückgegeben, oder Boolesch false, wenn es nicht geklappt hat.
		*/
		showLoadingIndicator : function(params) 
		{
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
		* Verberge Ladeanzeige
		*
		* @param   string  id   Mit showLoadingIndicator erhälst du eine Id, die du an hideLoadingIndicator übergibst, damit die entsprechende Ladeanzeige aus dem DOM entfernt wird.
		*
		* @return   boolean   true, wenn es geklappt hat, oder false wenn nicht.
		*/
		hideLoadingIndicator : function (id) 
		{
			id = typeof id === 'object' ? id.id : id; // Warum?!
			if (id)	{
				$((id.indexOf('#') == 0 ? id : '#' + id)).remove();
				return true;
			}
			return false;
		},

		/**
		* Größe des Anzeigebereichs (Viewport) ausgeben
		*
		* @return   object   Es wird ein Objekt mit den Eigenschaften „w” (Breite) und „h” (Höhe) zurückgegeben.
		*/
		getVps : function() 
		{
			let w = window,
				e = document.documentElement,
				b = document.getElementsByTagName('body')[0],
				x = w.innerWidth || e.clientWidth || b.clientWidth,
				y = w.innerHeight|| e.clientHeight|| b.clientHeight;

			return {w : x, h : y};
		},

		/**
		* Abfragen ob "el" im Anzeigebereich (Viewport) zu sehen ist.
		*	
		* @param   string  el   String jQuery-Selektor oder jQuery Objekt, oder HTMLElement Objekt (alles was jQuery vearbeiten kann).
		*
		* @return   boolean   Ist Boolesch true, wenn das Element zu sehen ist, und false wenn nicht.
		*/
		isInViewport : function(el) 
		{
			el = $(el);

			let scroll 	= $(document).scrollTop(),
				vIn 	= el.offset().top,
				vOut 	= el.offset().top + el.outerHeight();

			if( scroll + $app.getVps().h >= vIn && scroll < vOut ) 
			{
				return true;
			}
			return false;
		},


		init : function() 
		{
			$($app).one('extensionsReady.app', function() 
			{
				$($app).triggerHandler('ready');
			});
			$app.extensions.init();	// Erweiterungen laden
		}
	}

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
			beispiel : {
				autoload	: true|false
				file		: 'templates/head/js/...',
				options		: {},
				//condition : function() {return true|false},
				error		: function() {$app.extensions.log('err', this.file);},
				success		: function() {}
			},
		*/

		/**
			Systemnachrichten
		*/
		messages : {
			autoload	: true
			,file		: 'templates/head/js/app/app.messages.js'
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
				$app.scroll.defaults.smallDevice = 1;
				$app.scroll.defaults.force = true;
				$app.scroll.defaults.offsetElement = '#site-header, .blog-jumpnav';

				$app.scroll.init();
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
			Protoslider + HTML5 Video Plugin
		*/
		protoslider : {
			autoload	: true
			,file 		: 'templates/head/js/protoslider.min.js'
			,condition 	: function() {
				if($('.ptslider').length) return true;
				return true;
			}
			,error		: function() { $app.extensions.log('err', this.file); }
			,success	: function() {
				$.Protoslider.defaults.pauseonhover = false;	// Schalte „Pause bei Berührung” (mit dem Mauszeiger) aus.
				$.Protoslider.defaults.navigation = false;		// Verberge die Vor- und Zurück-Buttons
				//$.Protoslider.defaults.autoplay = false; 		// Deaktiviere autoplay zum testen.
				//$.Protoslider.defaults.preloader.demo = true; // Schalte den PreLoader in den Demo-Modus zum testen und stylen.

				$.Protoslider.prototype.initSprites = function()
				{
					this.spritesReset( this.$node.find('.sprite') );
				};
			
				$.Protoslider.prototype.getSprites = function( slide )
				{
					var sprites = slide.find('.sprite');
			
					if( sprites.length )
					{
						return sprites;
					}
					return false;
				};
				$.Protoslider.prototype.spritesIn = function( sprites )
				{
					for( var i = 0, len = sprites.length; i < len; i++ )
					{
						var sprite = sprites.eq(i);
						sprite.css(sprite.data('end'));
					}
				};
				$.Protoslider.prototype.spritesReset = function( sprites )
				{
					for( var i = 0, len = sprites.length; i < len; i++ )
					{
						var sprite = sprites.eq(i);
						sprite.css(sprite.data('start'));
					}
				};
			}
		},
		

		//
		// Navigation-Grid (navgrid)
		// Ist im Template integriert: <a data-navgrid-toggle> Menü </a>
		//
		// Siehe: index.php
		// Siehe auch: /scss/app/_navgrid.scss
		//
		navgrid : {
			autoload	: true
			,file 		: 'templates/head/js/app/app.navgrid.js'
			,condition 	: function() {
				if( $('.navgrid').length ) return true; // Nur laden, wenn ein „.navgrid” existiert.
				return false;
			}
			,error		: function() { $app.extensions.log('err', this.file); }
			,success	: function() {
				/* Optionen:
					selector 		: '',				// CSS Selektor des navgrid = #navgrid wenn leer
					useCookieState 	: true,				// Benutze einen Cookie zum speichern des Zustands (offen oder zu)
					autoCollapse 	: true,				// Navgrid nach dem laden einer Seite automatisch ausblenden
					triggerActive 	: 'active',			// CSS Klasse für die Auslöser, wenn navgrid geöffnet ist/wird
					cookiename 		: 'navgrid'			// Cookie Name
					closeOnWindowResize : false 		// Navgrid Schließen, wenn die Größe des Viewport verändert wird.
				*/
	 
				$('#navgrid').on('open', function() {
					$('#site-header').addClass('active');
				})
				.on('close', function() {
					$('#site-header').removeClass('active');
					
					// Bsp.: Die Suchleiste schließen, wenn navgrid-slide geschlossen wird:
					// $app.searchbar.close();

					// Bsp.: Schließe alle Protomenüs in navgrid-slide, wenn navgrid-slide geschlossen wird:
					// $(this).find('.ptmenu').protomenu().closeRootLevel();
				});

				// Instanz
				//$app.navgrid.defaults.useCookieState = false;
				let ngrid = new $app.navgrid();
				
				// 2. Protomenü im „Slide-Header”: Ein Klick auf dessen Links soll NavGrid schließen.
				$('.quicknav [data-ptm-child] a').on('click', function() {
					ngrid.toggleClosed();
				});
			}
		},


		//
		// Featherlight Lightbox
		// Ist im Template integriert. Siehe Featherlight Doku: https://github.com/noelboss/featherlight/#usage
		//
		// Siehe /scss/app/_featherlight.scss
		//
		// z.B.:
		// Eine Seite öffnen: <a href="index.php?option=..." data-featherlight="ajax"></a>
		// Ein Bild: <a href="#" data-featherlight="/images/meinbild.png"></a>
		//
		// Spezial:
		// AJAX, mit Template „ajax_loadcomponent.php”: <a href="..." data-featherlight="ajax" data-joomla-tmpl="ajax_loadcomponent">...</a> 
		// Dieser Link kann dann auch in einem neuen Tab/Fenster geöffnet werden, und wird dann mit dem Standardtemplate angezeigt.
		//
		featherlight : {
			autoload	: true
			,file 		: 'templates/head/js/featherlight.min.js'
			,error		: function() { $app.extensions.log('err', this.file); }
			,success	: function() {
				$.featherlight.defaults.loading 	= $app.defaults.html.loadingIndicator; // Bei AJAX die Ladeanzeige von $app benutzen
				$.featherlight.defaults.openSpeed 	= 450;
			   
				// Prüfen, ob der href Eigenschaft der Parameter „tmpl” angehängt werden soll:
				$.featherlight.defaults.beforeOpen = function(ev) 
				{
					if(this.$currentTarget && this.$currentTarget.data('joomla-tmpl')) 
					{
						var href    = this.$currentTarget.attr('href'),
							trunc   = href.indexOf('?') > -1 ? '&' : '?',
							tmpl    = trunc + 'tmpl=' + this.$currentTarget.data('joomla-tmpl');

						if(href.indexOf(tmpl) === -1) 
						{
							this.$currentTarget.attr('href', href + tmpl); 	// href überschreiben, dann featherlight öffnen
							this.afterOpen = function() 
							{
								this.$currentTarget.attr('href', href); 	// href zurücksetzen
							}
						}
					}
				}
			}
		},

		//
		// Lightbox Video (lädt auch app.extensions.list.featherlight falls erforderlich)
		// Öffnet ein Video in Featherlight.js.
		// <xyz data-fullvideo="images/videos/meinvideo.mp4"> Irgendetwas </xyz>
		//
		// Siehe auch: /scss/app/_videos.scss
		//
		lightboxvideo : {
			autoload    : true
			,file       : 'templates/head/js/app/app.videolightbox.js'
			,error		: function() { $app.extensions.log('err', this.file); }
			,success	: function() 
			{
				$app.lightboxVideo.init();
			}
		},


		// 
		// Google Maps API Wrapper
		// Ist im Template integriert. 
		//
		// Siehe Beispiel-Template: /html/googlemap.php
		// Siehe auch: /scss/app/_googlemaps.scss
		//
        googlemap : {
			autoload    : false
			,file       : 'templates/head/js/app/app.googlemap.js'
			,error		: function() { $app.extensions.log('err', this.file); }
			,success	: function() {}
		},


		//
		// Sticky Observer 
		// (Der Name spiegelt nicht wirklich die eigentliche Funktion wider)
		//
		// Ein Intersection Observer der Elementen eine CSS Klasse zuweisen kann, wenn:
		//
		// 1. das Element die Oberkannte des Viewports (+/- manuellem Offset) erreicht. D.h. das Element ist dabei aus dem Viewport zu verschwinden.
		// 2. das Element die Unterkannte des Viewports erreicht. D.h. das Element dabei ist in den Viewport zu gelangen.
		//
		// Zusätzlich zur Zuweisung von Klassen emittiert der Observer das Ereignis „sticky-change” an das Zielelement, welcher z.B. mit jQuery oder addEventListener abgefangen werden kann:
		//
		// $('#site-header').on('sticky-change', function(object Event, object „details”) {
		// 		let istSticky = details.stuck;
		// }
		// 
		// let siteHeader = document.getElementById('#site-header');
		// siteHeader.addEventListener('sticky-change', function(ev, details)) { ... });
		//
		stickyObserver : {
			autoload    : true
			,file       : 'templates/head/js/app/app.stickyobserver.js'
			,error		: function() { $app.extensions.log('err', this.file); }
			,success	: function() 
			{
				// Überwacht alle Elemente mit der CSS Klasse „observe-sticky”:
				let soDefaultOffset = -(Math.round(document.getElementById('site-header').offsetHeight)); // Höhe von #site-header berücksichtigen
				new $app.stickyObserver({debug: false, offset : soDefaultOffset});
			
				// Den Header überwachen:
				let headerSentinelOffset  = Math.round(document.querySelector('main').getBoundingClientRect().top) - document.getElementById('site-header').offsetHeight; // Position relativ zum Viewport.
					headerSentinelOffset += window.pageYOffset ? window.pageYOffset : window.scrollY; // Position absolut im Dokument. pageYOffset für Explorer.
				
				new $app.stickyObserver({debug: false, elements : '#site-header', offset : headerSentinelOffset, className : 'offset'}); 
				
				// Die Breadcrumbs überwachen:
				new $app.stickyObserver({debug: false, elements : '.breadcrumbs', threshold : 1, className : 'offset'}); // Mit threshold : 1 sagen wir dem Observer das className eingefügt werden soll, sobald das Element in den Viewport gelangt.
			}
		},


		//
		// Suchleiste
		// Eine ausfahrbare Leiste, die ein Suchmodul enthält (oder irgendwas Anderes).
		// <xyz data-toggle-search> Suchleiste öffnen </xyz>
		//
		searchbar : {
			autoload    : true
			,file       : 'templates/head/js/app/app.searchbar.js'
			,error		: function() { $app.extensions.log('err', this.file); }
			,success	: function() 
			{
				$app.searchbar.init();
			}
		}
	}
})(jQuery);

// Bootstrap
(function($){
	$(function () {
		$('[data-toggle="tooltip"]').tooltip();
	});
})(jQuery);
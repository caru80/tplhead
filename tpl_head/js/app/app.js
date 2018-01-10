
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
	CRu.: 2017-06-12
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
						ext.available = true

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

				if (! ext.enable && ! force)
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
			}
		},

		/*
			Stylesheet laden

			@param sheet 	– String : Pfad zur Datei
			@param media 	– String : Media Attribut f. Stylesheet
			@param replace 	– String : Dieses Stylesheet ersetzen ( z.B. 'main.css' )
		*/
		loadStylesheet : function(sheet, media, replace)
		{
			if (sheet.indexOf('http') == -1)
			{
				sheet = $app.protocol + '//' + $app.hostname + '/' + $app.pathname + '/' + sheet;
			}

			media = media === undefined ? 'screen' : media;

			if (replace)
			{
				let $styles = $('link[type="text/css"]');
				if ($styles.length)
				{
					for (let i = 0, len = $styles.length; i < len; i++)
					{
						let $this = $styles.eq(i);
						if ($this.attr('href').indexOf(replace) > -1)
						{
							$this.remove();
						}
					}
				}
			}

			let $sheet = $('<link rel="stylesheet" type="text/css" media="' + media + '" href="' + sheet + '" />');

			$('head').append($sheet);
		},


		// Generiert eine zufällige x-Stellige (Standard = 5 Zeichen) Id für den Gebrauch in HTML Elementen etc.
		getRandomId : function()
		{
		    let len 	= arguments[0] && typeof(arguments[0]) === 'integer' ? arguments[0] : 5,
				id 		= '',
				chars	= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

		    for (let i = 0; i < len; i++)
		    {
		        id += chars.charAt(Math.floor(Math.random() * chars.length));
			}

		    return id;
		},

		/*
			Zeige Ladeanzeige

			params{
				t 		: target; dort einhängen, jQuery Selektor,
				html 	: HTML Ladeanzeige (optional) – Siehe $app.defaults.html ...
			}

		*/
		showLoadingIndicator : function( params )
		{
			if (params.t)
			{
				var	id = 'loading-' + $app.getRandomId(),
					el = params.html ? $(params.html) : $(this.defaults.html.loadingIndicator),
					$t = $(params.t);

				el.attr("id", id);
				$t.append(el);
				return id;
			}
			return false;
		},

		/*
			Verberge Ladeindikator

			params{
				id : String – Id der zu verbergenden Ladeanzeige
			}

		*/
		hideLoadingIndicator : function (id)
		{
			if (id)
			{
				$((id.indexOf('#') == 0 ? id : '#' + id)).remove();
				return true;
			}
			return false;
		},

		/*	Viewport-Größe abfragen.
		*/
		getVps : function(){
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
		isInViewport : function( el )
		{
			let $el 	= typeof el === 'object' ? el : $(el),
				scroll 	= $(document).scrollTop(),
				vIn 	= $el.offset().top,
				vOut 	= $el.offset().top + $el.outerHeight();

			if( scroll + $app.getVps().h >= vIn && scroll < vOut )
			{
				return true;
			}
			return false;
		},



		init : function()
		{
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
	/*
		Erweiterungen

		Diese Scripts werden nachgeladen.
	*/
	$app.extensions.list = {
		/*
			foo : {
				enable		: true,					Ein/Aus – Bzw. die Richtige Bezeichnung wäre "autoload".
				(available : false,) 				Wird nach dem Laden gesetzt und sagt aus, ob das Script verfügbar ist.
				file 	: 'templates/js/foo.js',	Der Pfad zur Datei
				options : {}, 						Optionen, auf die man zugreifen könnte via: $app.extebnsions.list.foo.options.meineOption
				condition : function()
				{
					return false; = Dieses Script wird nicht geladen, weil die Bedingung nicht erfüllt wurde!
					return true; = Dieses Script wird geladen, weil die Bedingung erfüllt wurde!
				},
				error	: function()
				{
					Laden hat nicht geklappt!
				},
				success : function()
				{
					Laden hat geklappt!
				}
			}

			Das erfolgreiche laden von "foo" löst an $app den Event "fooReady" aus, den man wie folgt nutzen könnte:

			$($app).one('fooReady', function(){...});
		*/



		/*
			Systemnachrichten
			$app.systemMessage
		*/
		messages : {
			enable		: true
			,file		: 'templates/head/js/app/app.messages.js'
			,options	: {}
			,error		: function(){ if (console) console.log('$app: ' + this.file + ' konnte nicht initialisert werden.') }
			,success	: function()
			{
				$app.messages.init();
			}
		},

		/*
			Animiertes Scrollen
			$app.scroll
		*/
		scroll : {
			enable		: true
			,file 		: 'templates/head/js/app/app.scroll.js'
			,options	: {bla : 'blub'}
			,error 		: function(){ if (console) console.log('$app: ' + this.file + ' konnte nicht initialisert werden.') }
			,success 	: function()
			{
				$app.scroll.init(this.options);
			}
		},

		/*
			AJAX
			$app.ajax
		*/
		ajax : {
			enable		: true
			,file 		: 'templates/head/js/app/app.ajax.js'
			,error		: function(){ if( console ) console.log('$app: ' + this.file + ' konnte nicht initialisert werden.') }
			,success	: function()
			{
				/* $app.ajax initialsieren
				*/
				$app.ajax.init();
			}
		},

		/*
			$app.equalColumns
		*/
		equalColumns : {
			enable		: true
			,file 		: 'templates/head/js/app/app.equalcols.js'
			,options	: {}
			,error		: function(){ if(console) console.log('$app: ' + this.file + ' konnte nicht initialisert werden.') }
			,success	: function()
			{
				// Das Ding kann jederzeit neu abgefeuert werden. Wenn z.B. neue Dinge per AJAX geladen wurden, oder so.
				$app.equalColumns.init(this.options)
			}
		},


		/*
			Sticky
		*/
		sticky : {
			enable		: false
			,file 		: 'templates/head/js/sticky.js'
			,options	: {}
			,error		: function(){ if (console) console.log('$app: ' + this.file + ' konnte nicht initialisert werden.') }
			,success	: function()
			{
				let $elems = $('[data-sticky]');

				for (let i = $elems.length; i--;)
				{
					$elems.eq(i).sticky(this.options);
				}
			}
		},

		/*
			Joomla! Suchindex

			$app.finder
		*/
		finder: {
			enable		: false
			,file 		: 'templates/head/js/app/app.finder.js'
			,options	: { form : '#mod-finder-searchform', queryinput : '#mod-finder-searchword', results : '#job-search-results' }
			,condition	: function()
			{
				if( $(this.options.form).length === 0 ) return false; // Suchmaske ist nicht da
				return true;
			}
			,error 		: function(){ if (console) console.log('$app: ' + this.file + ' konnte nicht initialisert werden.') }
			,success 	: function()
			{
				$app.finder.init( this.options );
			}
		},
		/*
			Joomla! Suchindex Autocompleter
			Wird bei Bedarf (wenn im Modul eingeschaltet) von $app.finder nachgeladen
		*/
		finderAutocompleter : {
			enable 	: false // Das soll NICHT(!) eingeschaltet werden, weil es bei Bedarf nachgeladen wird.
			,file	: 'media/jui/js/jquery.autocomplete.min.js'
			,error	: function(){ if (console) console.log('$app: ' + this.file + ' konnte nicht initialisiert werden.') }
		},


		/*
			Wegpunkte
			$app.waypoints
		*/
		jqueryNav : {
			enable		: false
			,file 		: 'templates/head/js/jquery.nav.js'
			,error		: function(){ if (console) console.log('$app: ' + this.file + ' konnte nicht initialisert werden.') }
			,success	: function(){}
		},
		waypoints : {
			enable		: false
			,file 		: 'templates/head/js/app/app.waypoints.js'
			,options	: 	{
								node	 		: '#waypoints',
								targets			: 'section',
								ignore 			: '',
								offsetElement 	: '',
								topId			: 'top'
							}
			,error		: function(){ if (console) console.log('$app: ' + this.file + ' konnte nicht initialisert werden.') }
			,success	: function()
			{
				let opts = $.extend({makeNew : true}, this.options);
				$app.waypoints.init(opts);
			}
		},

		ganalytics : {
			enable		: true
			,file 		: 'templates/head/js/app/app.ganalytics.js'
			,options	: 	{
								key 		: '', // Hier den Key eingeben
								cookiename 	: 'gaoptout'
							}
			,condition	: function()
			{
				let gacookie = Cookies.get(this.options.cookiename);
				if(gacookie || this.options.key == '') {
					return false;
				}
				return true;
			}
			,error		: function(){ if (console) console.log('$app: ' + this.file + ' konnte nicht initialisert werden.') }
			,success	: function(){
				console.log('$app notice: Google Analytics loaded.');
			}
		},

		/*
			Protoslider
		*/
		protoslider : {
			enable		: false
			,file 		: 'templates/head/js/protoslider.min.js'
			,options	: {autoplay : true, timeout : 3000}
			,condition	: function()
			{
				if ($('.ptslider').length) return true; // Nur laden, wenn ein Element mit Klasse ptslider da ist
				return false;
			}
			,error		: function(){ if (console) console.log('$app: ' + this.file + ' konnte nicht initialisert werden.') }
			,success	: function()
			{
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
	// Ende Liste

})(jQuery);

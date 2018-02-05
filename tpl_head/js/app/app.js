'use strict';
/*
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
		protocol 	: window.location.protocol,	// URL Schema (http:, https:)
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

			/** Gibt etwas in der Konsole aus: */
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
			el = $(el);

			let scroll 	= $(document).scrollTop(),
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
			emptyTemplate : {
				autoload	: true
				file		: 'templates/head/js/',
				options		: {},
				//condition 	: function() {},
				error		: function() { $app.extensions.log('err', this.file); },
				success		: function() {

				}
			},
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
	}/*********** Ende Liste */
})(jQuery);


/**
	Google Analytics

	<a tabindex="0" data-gaoptout>Klick mich für Opt-Out</a>
*/
(function() {
	$app.gaConfig = {
		gaProperty 		: 'UA-XXXX-Y',	// Property Id
		optOutCookie 	: 'gaoptout',	// Opt Out Cookie Name
		expires 		: 3650,			// Tage
		showMessage 	: true 			// Zeige eine Nachricht, wenn der User den Link klickt.
	};

	if(Cookies.get($app.gaConfig.optOutCookie)) {
		window['ga-disable-' + $app.gaConfig.gaProperty] = true;
	}

	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
	ga('create', $app.gaConfig.gaProperty, 'auto');
	ga('set', 'anonymizeIp', true);
	ga('send', 'pageview');
})();

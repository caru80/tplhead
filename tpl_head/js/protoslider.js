/*
	Protoslider 1.0.6
	Carsten Ruppert

	1.0.6 - 2018-05-30
	+ Fix: Einen Bug in play() korrigiert, weil Internet Explorer (alle Versionen) kein Number.isInteger kann, und einen Fehler in die Konsole geschrieben hat: https://docs.microsoft.com/en-us/scripting/javascript/reference/number-isinteger-function-number-javascript

	1.0.5 – 2017-10-16
	+ Fix in changeRow: Animation-Delay Timeout nur wenn this.State.cols > 1
	+ Fix: Die Custom-Events werden jetzt mit triggerHandler() ausgelöst, weil z.B. trigger('play') sowohl den Event, als auch die Methode ausgelöst hat.
	+ Fix: Die Events afterSlideIn und afterSlideOut können jetzt auf die Protoslider-Instanz, sowie die einzelnen Slides angewendet werden.


	1.0.4 – 2017-10-05
	+ Neuer Custom-Event afterRender für Plugins
	+ Neuer Custom-Event resize in observeViewport für Plugins


	1.0.3 – 2017-04-20
	+ delay für Spaltenwechsel kann nun für jeden Auslöser separat eingestellt werden (in options.animations.[Auslöser].delay)
	+ Fix: Korrektur einer Bedingung, die zum Abbruch von Protoslider führt.
	+ Fix: Korrektur einer Bedingung, die das erneute Ausführen von _setup (was niemals passieren sollte) verhindert.
	+ 2. Deklaration einer bereits existierenden Variable in _setup entfernt (bei Startverzögerung).


	1.0.2 – 2017-04-11
	- Fix: Fehler in observeViewport wenn autoplay an. Es wird jetzt pausiert während der Viewport verändert wird.


	1.0.1 – 2017-03-24
	- In strict-Modus umgeschaltet...
	- Fix: ... danach Ein paar Var.-Deklarationen korrigiert.


	v 0.25 – 2017-03-23
	- Plugin-Schnittstelle eingebaut
	- VideoJs Plugin gebaut


	v 0.24 – 2017-03-10
	- Einen Bug gefixt, der dafür sorgte, dass für einen Slide definierte Animationen (in data-ptoptions) für alle Slides angewendet wurden.
	- Einen Bug gefixt, weil autoHeight von observeViewport ausgeführt wurde, auch wenn es abgeschaltet war.

	v 0.23 - 2016-11-04
	- Wegen Bug in mobile Firefox die Erkennung von Events mit oder ohne Präfix verbessert.


	v 0.22 - 2016-10-31
	- Bug in .play() korrigiert, wenn nur ein Slide vorhanden ist.


	v 0.21 - 2016-10-19
	- Labels für Pagination eingebaut: <div class="ptslider-item" data-ptoptions='{"label":"Name in der Paginierung", ... }'>
	- Animationen können nun einzelnen Slides zugewiesen werden: <div class="ptslider-item" data-ptoptions='{"animations":{ ... }}'>
	- Preload für MSIE modifiziert, und Prozentanzeige eingebaut (yeah!) – Firefox kann auch mal 150% anzeigen, das ist halt so.


	v 0.20 - 2016-07-05
	- Autoplay "loop" eingebaut.


	v 0.19 - 2016-07-04
	- Autoplay "onlyInViewport" hinzugefügt. Nur wenn Protoslider im Viewport sichtbar wird animiert/autoplay durchgeführt.


	v 0.18 - 2016-06-08
	- on, one, off hizugefügt. Events können nun in jQuery Syntax an Protoslider gebunden werden. Die Events werden an $node (= <div class="ptslider">) angehängt, die handler-Funktionen werden mit $.proxy kopiert, und in deren Kontext wird this durch die Protoslider Instanz ersetzt (this = protoslider anstatt $node).


	v 0.17 – 2016-06-02
	- Vendor-Erkennung für animationend Event (webkitAnimationEnd, mozAnimationEnd etc) eingebaut, weil Chrome (manchmal!?) sowohl animationend als auch webkitAnimationEnd triggert.


	v 0.16 – 2016-05-17
	- Preloader Bug bis Internet Explorer 11 repariert


	v 0.15 – 2016-04-04
	- Swipe eingebaut


	v 0.14 – 2016-03-11
	- paar Kleinigkeiten...


	v 0.13 - 2016-03-09
	- Viele Teile neugeschrieben...


	v 0.12
	- Preloader eingebaut
	- Ladeanzeige eingebaut
*/
'use strict';

(function($){

	$.Protoslider = function( options, node ){
		this.$node = $(node);

		if( ! this.$node.length ){
			console.log('Kein node');
			return false;
		}

		this.$node.data('ptslider', this);

		this.$wrap = this.$node.children('.ptslider-wrapper');

		this._init( options );

		return this;
	};


	$.Protoslider.Plugins = [],


	$.Protoslider.defaults = {
		autoheight	 				: true,				// Automatische Höhe – Das wird Sinnlos wenn keine Höhe ermittelt werden kann

		/*
			Image Preload

			Zu beachten ist, dass der Preloader "dumm" ist. Wenn eine Datei nicht geladen werden kann, wird die Ladeanzeige für immer angezeigt.
		*/
		preload		 				: true,				// Preload ein/aus
		loadingIndicator 			: true,				// Ladeanzeige


		/* Responsive */
		size  : [1200,979,767,0],						// Bildschirmbreiten = 1200 +, 979 bis 1200, 767 bis 979, 0 bis 767
		items : [1,1,1,1],								// Anzahl Spalten analog zu Bildschrimbreiten = bei 1200+, 979 bis 1200, 767 bis 979, 0 bis 767


		/* Autoplay */
		autoplay 					: true,				// Autoplay ...
		loop						: true,				// Loop ...
		randomstart 				: false,			// Mit zufalliger Zeile Starten
		timeout 					: 5000,				// Zeit zwischen Zeilenwechsel bei autoplay
		delaystart 					: 0,				// Startverzögerung (wird zu timeout addiert!)
		direction 					: 'forwards', 		// Laufrichtung: 'forwards' | 'backwards' | 'random'
		pauseonhover 				: true,				// Pausiere Protoslider solange der Mauszeiger drauf zeigt.
		onlyInViewport 				: true,				// Nur animieren/autoplay wenn im Viewport sichtbar

		/* Steuerelemente */
		pagination 					: true,				// Paginierung
		paginationLabels 			: true, 			// Paginierung Beschriftung
		navigation 					: true,				// Vor und Zurück

		/* Touch / Swipe */
		swipe						: true,				// Touch wischen – nur links und rechts
		swipeThreshold 				: 80,				// Schwelle in Pixeln, die gewischt werden müssen, bevor irgendwas passiert.
		pauseonswipe				: true,				// Protoslider stoppt, wenn gewischt wurde.
		swipepause					: 20000,			// Nach pauseonswipe Zeit in Millisekunden nach dem der Autoplay wieder anläuft. 0 = niemals.

		plugins 					: [],

		/* Z-Achsen für Pagninierung und Navigation (wird zu Z-Achsen von slides addiert) */
		zAxis : {
			pagination : 2,
			navigation : 1
		},

		/*
			Animationen

			Kann auch pro Slide definiert werden:	<div class="ptslider-item" data-ptoptions='{"animations":{"autoplay":{"in":"flipInRight"},"pagination":{"in":"flipInLeft"}}}'>
		*/
		delay 		: 0,								// Verzögerung zwischen dem Spaltenwechsel (Slides) bei einem Zeilenwechsel – macht nur Sinn, wenn auch mehr als eine Spalte angezeigt wird

		animations : {
				autoplay : {							// Animationen bei Autoplay
					in 			:	'fadeIn',			// Beim einblenden – String oder Array: 'fadeIn' oder ['fadeIn','pulse','blub']
					out 		:	'fadeOut',			// Beim ausblenden – String oder Array
					duration 	:	'1s',				// Dauer
					random		: 	true,				// Wenn in und/oder out ein Array mit Animationsnamen enthalten, wird per Zufall eine daraus gewählt.
														// Random wird erst interessant, wenn mehr als eine Spalte engezeigt wird.
					delay		:	0
				},
				pagination : {							// Animation bei Klick auf einen Pager in der Paginierung
					in 			:	'fadeIn',
					out 		:	'fadeOut',
					duration 	:	'1s',
					delay		:	0
				},
				navigation : {							// Animation beim Klick auf Vor und Zurück
					nextIn 		: 	'slideInRight',		// Beim einblenden, wenn der Slide als nächstes eingeblendet wird (Klick auf Vorwärts, dieser Slide wird eingeblendet)
					nextOut 	: 	'slideOutLeft',		// Beim ausblenden, wenn der Slide gerade angezeigt wird, und auf Vorwärts gelickt wird
					prevIn 		: 	'slideInLeft',		// Beim einblenden, wenn der Slide der Vorherige ist (Klick auf Zurück, dieser Slide wird eingeblendet)
					prevOut 	: 	'slideOutRight',		// Beim ausblenden, wenn der Slide gerade angezeigt wird, und auf Zurück geklickt wird.
					duration 	:	'0.75s',
					delay		:	0
				},
				swipe : {								// Animation beim Wischen auf Touch-Geräten – wie navigation
					nextIn 		: 	'slideInRight',
					nextOut 	: 	'slideOutLeft',
					prevIn 		: 	'slideInLeft',
					prevOut 	: 	'slideOutRight',
					duration 	:	'0.3s',
					delay		:	0
				}
		},


		html : {
			// Zurück-Button, kann auch zur Laufzeit überschrieben werden: protoslider().options.html.btnPrev = '...'; protoslider.removeNavigation(); protoslider.addNavigation();
			btnPrev 	: '<span class="ptslider-nav ptslider-nav-prev" />',
			// Vorwärts-Button:
			btnNext 	: '<span class="ptslider-nav ptslider-nav-next" />',
			// Wrapper für die Paginierung:
			pagination  : '<div class="ptslider-pagination" />',
			// Ein Element in der Paginierung:
			pager 		: '<span class="ptslider-pager" />',
			// Ein Element in der Paginierung mit einem Label:
			pagerLabel  : '<span class="ptslider-pager-label">%s</span>',
			// Die Ladeanzeige:
			loading		: 	'<div class="ptslider-preload">' +
								'<div class="ptslider-indicator">' +
									'<div class="spinner">' +
										'<div class="dot1"></div>' +
										'<div class="dot2"></div>' +
									'</div>' +
									'<span class="percent-loaded"></span>' +
								'</div>' +
							'</div>'
		},

		// CSS Klassen und Regeln
		css : {
			classActive 	: 'ptin',			// Aktiver Slide

			labeledPager 	: 'labeled',

			classAnimated	: 'animated',		// Slide, welcher gerade animiert wird (Analog zur CSS-Animationsbibliothek; hier Animated.css)
			classInfinite	: 'infinite',		// CSS Klasse für endlose Animation (Analog zur CSS-Animationsbibliothek; hier Animated.css)

			//styleActive 	: { display: 'block', visibility: 'visible', position: 'absolute' }, 	// Regeln für aktive Slides – auch während der Ein- und Ausblende-Animation
			//styleInactive 	: { display: 'none', visibility: 'hidden', position: 'absolute' }		// Regeln für inaktive Slides
			styleActive 	: { visibility: 'visible', position: 'absolute' }, 	// Regeln für aktive Slides – auch während der Ein- und Ausblende-Animation
			styleInactive 	: { visibility: 'hidden', position: 'absolute' }		// Regeln für inaktive Slides
		},

		eventNamespace 	: '.ptslider', 			// jQuery Event-Namespace

		// Standard-Events, die in jedem Browser funktionieren
		bulkEvents : {
			resize 				: 'resize',
			orientationchange 	: 'orientationchange'
		},

		// Herstellerspezifische Event-Bezeichnungen (Protoslider ermittelt automatisch was benutzt werden kann)
		vendorEvents : {
			'animation' : { // Standard
				animationEnd 		: 'animationend',
				animationIteration 	: 'animationiteration',
				transitionEnd		: 'transitionend'
			},
			'webkitAnimation' : { // Google (Apple, MS-Edge)
				animationEnd 		: 'webkitAnimationEnd',
				animationIteration 	: 'webkitAnimationIteration',
				transitionEnd		: 'webkitTransitionEnd'
			},
			'oAnimation' : { // Opera
				animationEnd 		: 'oanimationend',
				animationIteration 	: 'oanimationiteration',
				transitionEnd		: 'otransitionend'
			}
		},


		/*
			Public Callback-Funktionen
			$('.xyz').protoslider({ onXYZ : function(){...} });
		*/
		onBeforeAnimation 		: false,	// Wird ausgeführt, bevor eine Animation startet
		onBeginAnimation 		: false,	// Wird ausgeführt, wenn eine Animation startet
		onAfterAnimation 		: false,	// nachdem eine Animation beendet wurde
		onBeginSlideAnimation 	: false,	// Bevor die Animation eines Slides startet
		onAfterSlideAnimation 	: false,	// Nachdem die Animation eines Slides beendet wurde
		onPause 				: false,	// wenn Pause gefeuert wurde
		onPlay 					: false,	// wenn Play gefeuert wurde
		onInit					: false,	// Während der INitialisierung, der frühstmögliche Punkt um einzugreifen
		onAfterInit 			: false,	// nachdem init beendet wurde, noch bevor Protoslider sich selbst returned hat
		onAfterImagePreload		: false		// Nachdem der Bilder-Preload beendet wurde (wenn er denn lief)
	};

	$.Protoslider.prototype = {
		_init : function( options )
		{
			/* Optionen und Defaults zusammenführen zu this.options */
			this.options = $.extend( true, {}, $.Protoslider.defaults, options );

			/* Optionen und inline-Optionen aus Attribut "data-ptoptions" zusammenführen */
			if( this.$node.data('ptoptions') )
			{
				$.extend( true, this.options, this.$node.data('ptoptions') );
			}

			// Zustand:
			this.State		= {
				ready 		: false,	/* Setup beendet; Protoslider ist bereit */
				isIdle		: false,	/* Animiert gerade? */
				trigger 	: {},		/* Auslöser des letzten Navigationsvorgangs { name : 'autoplay' | 'navigation' | 'pagination' | 'swipe', direction : 'prev' | 'next' | INTEGER } – wird u.a. in prepareSlideAnimation verarbeitet, verschiedende Trigger können verschiedene Slide Animationen auslösen */

				rows		: 0,		/* Anzahl Zeilen */
				row 		: 0,		/* Derzeit sichtbare Zeile */
				rowNext		: 0,		/* Nächste Zeile, die eingeblendet wird */
				columns		: this.options.items[0], /* Anzahl derzeit sichtbarer Spalten */

				stage		: [],		/* Array mit derzeit sichtbaren Slides */
				touch		: {}		/* Habe ich vergessen! Geht auch so. */
			};


			this.getVendorEvents();


			this.Controls 	= {
				pagination	: false,
				prev 		: false,
				next 		: false,
				swipe		: false
			}


			this.Preload = {
				entities	: false, 	/* zu laden */
				loaded		: 0,		/* geladen */
			},


			this.playtimer 	= undefined; 	/* Autoplay Timeout Id */

			this.$slides 	= this.$node.find('.ptslider-item'); /* Slides/Items */


			if( ! this.$slides.length )
			{
				if( console ) console.log('Protoslider hat keine Slides in #' + this.$node.attr('id') + ' (' + this.$node.attr('class') + ')');
				return false;
			}


			if( typeof this.options.onInit === 'function' )
			{
				this.options.onInit.apply(this);
			}

			// Plugins instanzieren:
			this.options.plugins = this.options.plugins.concat($.Protoslider.Plugins);
			this.options.plugins = this.options.plugins.filter(function(value, index, self){return self.indexOf(value) === index;}); // https://stackoverflow.com/questions/1960473/get-all-unique-values-in-an-array-remove-duplicates
			if( this.options.plugins.length > 0 )
			{
				for( var i = 0, len = this.options.plugins.length; i < len; i++ )
				{
					this[this.options.plugins[i]] = new $[this.options.plugins[i]](this);
				}

				if( typeof this.options.onAfterInitPlugins === 'function' )
				{
					this.options.onAfterInitPlugins.apply(this);
				}
			} // ende Plugins

			if(this.options.preload)
			{
				this._preload();
			}
			else
			{
				this._setup();
			}
		},

		/*
			Proxy für jQuery.on

			Weil protoslider kein jQuery-Objekt ist, würde z.B.

			var pt = $('#blub').protoslider();
			pt.on('afterSlideIn', function(){...});

			nicht funktionieren. Sondern es müsste sein:

			pt.$node.on(...) – $node ist = <div class="ptslider"> = jQuery-Objekt

			Die Proxy-Funktionen machen nichts anderes als pt.$node.on(), aber übergeben nicht $node als "this" an die auszuführende Funktion, sondern die Protoslider-Instanz.
		*/
		on : function(){
			for( var arg in arguments )
			{
				if( typeof arguments[arg] === 'function' ) arguments[arg] = $.proxy(arguments[arg], this);
			}
			var args = Array.prototype.slice.call(arguments);
			this.$node.on.apply(this.$node, args);

			return this;
		},
		/*
			Proxy für jQuery.one
		*/
		one : function(){
			for( var arg in arguments )
			{
				if( typeof arguments[arg] === 'function' ) arguments[arg] = $.proxy(arguments[arg], this);
			}
			var args = Array.prototype.slice.call(arguments);
			this.$node.one.apply(this.$node, args);

			return this;
		},
		/*
			Proxy für jQuery.off
		*/
		off : function(){
			var args = Array.prototype.slice.call(arguments);
			this.$node.off.apply(this.$node, args);

			return this;
		},

		getViewportSize : function(){
			var w = window,
				e = document.documentElement,
				b = document.getElementsByTagName('body')[0],
				x = w.innerWidth || e.clientWidth || b.clientWidth,
				y = w.innerHeight|| e.clientHeight|| b.clientHeight;
			return {w : x, h : y};
		},

		// Ist Protoslider zu sehen?
		isInViewport : function()
		{
			var vp = this.getViewportSize(),
				sp = $(document).scrollTop();

			if( vp.h + sp >= this.$node.offset().top && this.$node.offset().top + this.$node.height() > sp )
			{
				return true;
			}

			return false;
		},

		// Benutzbare Event-Namen ermitteln
		getVendorEvents : function(){
			for( var key in this.options.vendorEvents )
			{
				if( this.options.vendorEvents.hasOwnProperty(key) )
				{
					if( this.$node.get(0).style[key] !== undefined )
					{
						this.State.events = this.options.vendorEvents[key];

						$.extend( this.State.events, this.options.bulkEvents );

						for(var event in this.State.events)
						{
							if( this.State.events.hasOwnProperty(event) )
							{
								this.State.events[event] = this.State.events[event] + this.options.eventNamespace;
							}
						}

						return;
					}
				}
			}
		},


		/*
			Bilder-Preload
		*/
		_preload : function()
		{
			this.Preload.entities = this.$wrap.find('img');

			if( this.Preload.entities.length > 0 )
			{
				var self = this;

				if( this.options.loadingIndicator )
				{
					this.showIndicator();
				}

				// for( var x = this.Preload.entities.length; x > 0; x-- ) // Der Browser wird vermutl. nicht Rückwärts laden
				for( var x = 0, len = this.Preload.entities.length; x < len; x++ )
				{
					/*
						https://support.microsoft.com/de-de/kb/167820
					*/
					if( window.navigator.userAgent.indexOf("MSIE ") > -1 )
					{
						this.Preload.entities.eq(x-1).attr("src", this.Preload.entities.eq(x-1).attr("src")); // OnLoad Bug von IE >5 und < Edge(?) übergehen.
						/*
							https://www.google.de/search?q=internet+explorer+image+onload+bug

							Ohne "Vendor-Erkennung" sorgt das dafür, dass der Load-Event in den anderen – richtigen – Browsern doppelt so oft ausgelöst wird. Und die Prozentanzeige entsprechend 200% anzeigt.
						*/
					}

					if( this.Preload.entities.get(x-1).complete )
					{
						this.Preload.entities.eq(x-1).one('load' + this.options.eventNamespace, function() // Der Feuert wenn Resourcen, die sich im Cache befinden, nochmal per AJAX geladen werden – das wird von Firefox benötigt!
						{
							++self.Preload.loaded;
							self._entityLoaded();
						});

						++this.Preload.loaded;
						this._entityLoaded();
					}
					else // Noch nicht geladen ... Internet Explorer bis einschl. 11 läuft immer hier rein
					{
						this.Preload.entities.eq(x-1).one('load' + this.options.eventNamespace, function()
						{
							++self.Preload.loaded;
							self._entityLoaded();
						});
					}
				}
			}
			else
			{
				this._onAfterPreload();
			}

		},


		_entityLoaded : function()
		{
			if( this.options.loadingIndicator )
			{
				this.updateIndicator();
			}

			if( this.Preload.loaded == this.Preload.entities.length )
			{
				this._onAfterPreload();
			}
		},

		_onAfterPreload : function()
		{
			if( this.options.loadingIndicator )
			{
			 	this.hideIndicator();
			}

			if( typeof this.options.onAfterImagePreload === 'function' )
			{
				this.options.onAfterImagePreload.apply(this);
			}

			this._setup();
		},

		// Zeige Ladeindikator
		showIndicator : function()
		{
			if( ! this.$indicator )
			{
				this.$indicator = $(this.options.html.loading);

				var $text = this.$indicator.find('.percent-loaded');
				$text.append('0%');

				this.$wrap.append(this.$indicator);
			}

			this.$indicator.addClass('visible');
		},

		updateIndicator : function()
		{
			//if( this.$indicator )
			//{
				var $text 	= this.$indicator.find('.percent-loaded'),
					val 	= Math.round(this.Preload.loaded / this.Preload.entities.length * 100 + 0.4);

				val = val > 100 ? 100 : val; // Firefox zeigt auch mal mehr als 100%

				$text.off(this.State.events.transitionEnd).on(this.State.events.transitionEnd, function()
				{
					$text.removeClass('updated');
				});
				$text.addClass('updated').html( val + '%');
			//}
		},

		// Verberge Ladeindikator
		hideIndicator : function()
		{
			if( this.$indicator )
			{
				this.$indicator.removeClass('visible');
			}
		},

		_setup : function(){
			// var self = this;

			if( this.State.ready ) /* Das sollte niemals passieren ... */
			{
				if(console) console.log('Protoslider ist bereit, wollte _setup aber erneut ausführen!');
				return;
			}

			var d = { ptslider : this };
			for( var i = 0, len = this.$slides.length; i < len; i++ )
			{
				this.$slides.eq(i).data('ptslider', d);
			}


			/*
				Animations-Helper installieren
			*/
			if( !$.fn._ptsSetAnimation && !$.fn._ptsClearAnimation )
			{
				this.installAnimationHelpers();
			}


			if( this.render() )
			{
				this.observeViewport();

				if(this.options.autoplay)
				{
					if(this.options.pauseonhover)
					{
						this.setPauseEvents(); /* Pause bei mouseover */
					}

					if( this.options.delaystart > 0 )
					{ // verzögerter Start
						var self = this;
						this.State.starttimer = window.setTimeout( function(){ self.play() }, this.options.delaystart );
					}
					else
					{
						this.play();
					}
				}

				this._onAfterSetup();
			}
			else if( console )
			{
				console.log('Protoslider Rendern fehlgeschlagen!');
			}
		},

		_onAfterSetup : function()
		{
			this.State.ready = true;

			this.$node.addClass('ptready');

			this.$node.triggerHandler('sliderReady');

			if( typeof this.options.onAfterInit === 'function' )
			{
				this.options.onAfterInit.apply(this);
			}
		},


		installAnimationHelpers : function()
		{
			/*
				!!!! Unzuverlässig, überarbeiten!
			*/
			$.fn._ptsSetAnimation = function(ani, infinite, infCallback)
			{
				var $slide 	= $(this),
					d 		= $slide.data('ptslider') || {};

				if( ! d.ptslider ) return;


				/*if(!d.ac)
				{*/
					$slide.addClass(d.ptslider.options.css.classAnimated);
					d.ac = 0;
					d.anims = new Array();
				/*}*/

				$slide.addClass(ani + (infinite ? ' ' + d.ptslider.options.css.classInfinite : ''));

				++d.ac;

				if( !infinite )
				{
					$slide.one(d.ptslider.State.events.animationEnd, function()
					{
						$(this)._ptsClearAnimation( ani );
					})
				}
				else if(typeof infCallback === 'function')
				{
					infCallback.apply(this);
				}

				d.anims[d.ac] = { name : ani, loop : infinite };

				$slide.data('ptslider', $.extend($slide.data('ptslider'), d));

				return this;
			}


			$.fn._ptsClearAnimation = function(ani, infinite)
			{
				var $slide 	= $(this),
					d		= $slide.data('ptslider');

				$slide.removeClass(ani + (infinite ? ' ' + d.ptslider.options.css.classInfinite : ''));

				if(d.ac > 0)
				{
					--d.ac;
				}

				if(!d.ac)
				{
					$slide.removeClass( d.ptslider.options.css.classAnimated );
				}

				$slide.data('ptslider', $.extend($slide.data('ptslider'), d));

				return this;
			}


			$.fn._ptsClearAllAnimations = function()
			{
				var $slide 	= $(this),
					d 		= $slide.data('ptslider');

				if( $slide && d )
				{
					$slide.off(d.ptslider.options.eventNamespace);

					if(d.anims)
					{
						//for( var x = d.anims.length; x--; )
						for( var x = 0, len = d.anims.length; x < len; x++ )
						{
							if( d.anims[x] )
							{
								//console.log(d.anims[x]);
								$slide._ptsClearAnimation(d.anims[x].name, d.anims[x].loop);
							}
						}
					}
				}
			}
		},

		getRandomRow : function()
		{
			return Math.floor( Math.random() * this.map.length );
		},

		/*
			Mit Vorsicht zu genießen...
		*/
		destroy : function()
		{
			this.clearAutoplayTimeout();
			this.clearSlideAnimations();

			if( this.options.pauseonhover ) this.unsetPauseEvents();

			if( this.options.pagination ) this.removePagination();

			if( this.options.navigation ) this.removeNavigation();

			this.$slides.each(function()
			{
				$slide = $(this);
				$slide.removeData('ptslider');
			});

			this.$node.removeData('ptslider');
		},

		// -- Pause/Play Event (options.pauseonhover)
		setPauseEvents : function(){
			var self = this;
			this.$node.on('mouseover' + this.options.eventNamespace, function()
			{
				if( ! self.State.paused )
				{
					self.pause();
					self.$node.one('mouseout' + self.options.eventNamespace, function()
					{
						self.play();
					});
				}

			});
		},

		unsetPauseEvents : function()
		{
			this.$node.off('mouseover' + this.options.eventNamespace);
			this.$node.off('mouseout' + this.options.eventNamespace);
		},

		// 1.
		getSliderColumns : function()
		{
			var width = this.getViewportSize().w;

			for( var i = 0, len = this.options.size.length; i < len; i++ )
			{
				if( width < this.options.size[i] && width >= this.options.size[i+1] )
				{
					return this.options.items[i+1];
				}
				else if( width >= this.options.size[i] )
				{
					return this.options.items[i];
				}
			}

			return this.State.columns;
		},


		// 2.
		getSliderRows : function()
		{
			return Math.round(this.$slides.length / this.State.columns + 0.4);
		},


		// 3.
		getSliderMap : function()
		{
			var sliderMap = new Array();

			for( var i = 0, ilen = this.State.rows; i < ilen; i++ )
			{
				sliderMap[i] = new Array();

				for( var x = 0, xlen = this.State.columns; x < xlen; x++ )
				{
					var $slide = this.$slides.eq( i * this.State.columns + x );

					sliderMap[i][x] = $slide;
				}
			}

			return sliderMap;
		},

		// 4.
		prepareSlidesOnStage : function()
		{
			var self = this;

			for( var row = 0, rowlen = this.map.length; row < rowlen; row++ )
			{
				for( var col = 0, collen = this.map[row].length; col < collen; col++ )
				{
					var $slide 	= this.map[row][col],
						d 		= $slide.data('ptslider') || {};

					$slide.css({
						zIndex : this.State.rows - row
						,width : (100 / this.State.columns) + '%'
						,left  : col * (100 / this.State.columns) + '%'
					});

					if( row == this.State.row ) // Aktuell sichtbare Zeile
					{
						this.State.stage[col] = $slide;
						$slide.addClass( self.options.css.classActive ).css( self.options.css.styleActive );
					}
					else
					{
						$slide.removeClass( self.options.css.classActive ).css( self.options.css.styleInactive );
					}
				}
			}
		},

		/*
			Fordert die Informationen zum anzeigen an, und mach den Slider betriebsbereit.

			Wird auch beim ändern der Anzahl der Spalten aufgerufen (responsive...), und führt einen vollständigen Reset des Sliders durch.
		*/
		render : function()
		{
			this.clearAutoplayTimeout();

			this.clearSlideAnimations();

			var columns = this.getSliderColumns(); // 1. Anzahl anzuzeigender Spalten ermitteln
			if( this.State.columns != columns )
			{
				this.State.columns = columns;
			}

			this.State.rows = this.getSliderRows(); // 2. Anzahl Zeilen ermitteln

			if( this.State.columns > 0 ){
				this.map = this.getSliderMap(); // 3. "Karte" aufbauen – Array[Zeilen][Slides]
			}

			if( this.map ){ // Ohne this.map funktioniert gar nichts

				this.State.row 	= this.options.randomstart ? this.getRandomRow() : 0; // (neue) Startzeile ermitteln

				this.prepareSlidesOnStage(); // 4. Slides im Vordergrund vorbereiten

				// Navigation bauen, wenn nicht vorhanden
				if( this.options.navigation && ! this.Controls.next && ! this.Controls.prev )
				{
					this.addNavigation();
				}

				// Paginierung bauen / aktualisieren
				if( this.options.pagination )
				{
					this.addPagination(); // Ersetzt auch bereits existierene Paginierung
				}

				// Swipe aktivieren, wenn nicht schon aktiv
				if( this.options.swipe && ! this.Controls.swipe )
				{
					this.addSwipe();
				}

				// Automatische Höhe
				if( this.options.autoheight )
				{
					this.autoHeight();
					this.$node.addClass('autoheight');
				}

				this.State.isIdle = false;

				this.$node.triggerHandler('afterRender');

				return true;
			}
			else
			{
				return false;
			}
		},


		observeViewport : function()
		{
			var self = this;

			$(window).one( this.State.events.resize, function()
			{
				if( self.options.autoplay ) self.pause();

				if( self.getSliderColumns() != self.State.columns )
				{
					if( self.render() )
					{
						if( self.options.autoplay ) self.play();
					}
				}
				else
				{
					if( self.options.autoheight ) self.autoHeight();
					if( self.options.autoplay ) self.play();
				}

				self.$node.triggerHandler('resize');

				self.observeViewport();
			});
		},

		autoHeight : function( row )
		{
			var r = row === undefined ? this.State.row : row, // Ziel-Items aus Reihe row
				h = 0, // Zielhöhe
				$slide;

			for( var i = 0; i < this.map[r].length; i++ )
			{
				$slide = this.map[r][i];

				if(h === 0)
				{
					h = $slide.outerHeight();
				}
				else if( h < $slide.outerHeight() )
				{
					h = $slide.outerHeight();
				}

				if( this.$wrap.height() != h && h > 0)
				{
					this.$wrap.height(Math.round(h));
				}
			}
		},


		// -- Entfernt alle Animationen
		clearSlideAnimations : function()
		{
			for( var i = 0, len = this.$slides.length; i < len; i++ )
			{
				var $slide = this.$slides.eq(i);

				$slide._ptsClearAllAnimations();
			}
		},



		/*
			-- Interaktion --------------------------------------------------
		*/

		// Vor und Zurück bauen
		addNavigation : function()
		{
			if( this.$slides.length == 1 ) // Keine Navigation wenn nur ein Slide
			{
				return;
			}

			var self = this;

			var navs   	= new Array(),
				dir 	= new Array('prev','next'),
				navcss 	= { zIndex : this.$slides.length + this.options.zAxis.navigation };

			navs[0] = $(this.options.html.btnPrev).css(navcss);
			navs[1] = $(this.options.html.btnNext).css(navcss);

			$.each(navs, function(i, $nav)
			{
				$nav.on('click' + self.options.eventNamespace, function()
				{
					if(!self.State.isIdle)
					{
						self.State.trigger = { name : 'navigation', direction : dir[i] };
						self.navigate(dir[i]);
					}
				});
				self.$node.append($nav);
			});

			this.Controls.prev = navs[0];
			this.Controls.next = navs[1];
		},


		removeNavigation : function()
		{
			if( this.Controls.prev && this.Controls.next )
			{
				this.Controls.prev.remove();
				this.Controls.prev = false;

				this.Controls.next.remove();
				this.Controls.next = false;
			}
		},


		// -- Paginierung bauen
		addPagination : function()
		{
			if( this.$slides.length == 1 ) // Keine Paginierung wenn nur ein Slide
			{
				return;
			}


			var self = this;

			if( this.$pagination )
			{
				this.removePagination();
			}

			this.$pagination = $(this.options.html.pagination).css({ zIndex : this.$slides.length + this.options.zAxis.pagination });

			for( var i = 0, len = this.map.length; i < len; i++ )
			{
				var $pager = $(this.options.html.pager);

				$pager.data('ptslider', { targetRow : i }).addClass('target-row'+i);

				if( this.options.paginationLabels )
				{
					var label = '';

					for( var x = 0, xlen = this.map[i].length; x < xlen; x++ )
					{
						if( this.map[i] ){
							var xd = this.map[i][x].data('ptoptions') || {};
							if( xd.label )
							{
								label += this.options.html.pagerLabel.replace("%s", xd.label);
								$pager.addClass(this.options.css.labeledPager);
							}
						}
					}
					$pager.append(label);
				}



				if( i == this.State.row )
				{
					$pager.addClass('current');
				}

				$pager.on('click' + this.options.eventNamespace, function()
				{
					var target = $(this).data('ptslider').targetRow;

					if( target != self.State.row && !self.State.isIdle )
					{
						self.State.trigger = { name : 'pagination', direction : target };

						self.navigate( target );
					}
				})

				this.$pagination.append($pager);
			}

			this.$node.append(this.$pagination);

			this.Controls.pagination = true;
		},


		/*
			Paginierung entfernen
		*/
		removePagination : function()
		{
			if( this.$pagination )
			{
				this.$pagination.remove();
			}

			// this.options.pagination = false;

			this.Controls.pagination = false;
		},


		/*
			Paginierung aktualisieren
		*/
		updatePagination : function(index)
		{
			if( this.Controls.pagination )
			{
				if( index === undefined )
				{
					index = this.State.row;
				}

				this.$pagination.find('.current').removeClass('current');
				this.$pagination.children().eq(index).addClass('current');
			}
		},


		/*
			Touch-Events/-Steuerung hinzufügen
		*/
		addSwipe : function()
		{
			var self = this;

			this.on('touchstart' + this.options.eventNamespace, function(ev)
			{
				this.State.touch.start = parseInt(ev.originalEvent.changedTouches[0].clientX);

				if( this.options.pauseonswipe == true && this.options.autoplay == true )
				{
					this.pause();
				}
			});

			this.on('touchmove' + this.options.eventNamespace, function(ev)
			{
				this.State.trigger.name = 'swipe';

				var distance = parseInt(ev.originalEvent.changedTouches[0].clientX) - this.State.touch.start;

				if( distance > this.options.swipeThreshold ) // Rückwärts – Nach rechts gewischt
				{
					this.State.trigger.direction = 'prev';

					this.navigate( 'prev' );
				}
				else if( distance < -(this.options.swipeThreshold) ) // Vorwärts – Nach links gewischt
				{
					this.State.trigger.direction = 'next';

					this.navigate( 'next' );
				}

			});

			this.$wrap.on('touchend' + this.options.eventNamespace, function()
			{
				self.on('afterRowChange', function(ev, data)
				{
					if( this.options.pauseonswipe == true && this.State.paused == true && this.options.swipepause > 0 )
					{
						this.play( this.options.swipepause );
					}
				});
			});

			this.Controls.swipe = true;
		},


		/*
			Touch-Events/-Steuerung entfernen
		*/
		removeSwipe : function()
		{
			this.$wrap.off('touchstart' + this.options.eventNamespace);

			this.$wrap.off('touchmove' + this.options.eventNamespace);

			this.Controls.swipe = false;
		},




		/*
			-- Animation  --------------------------------------------------
		*/



		/*
			Slide-Animationen vorbereiten (Klassennamen in Datenobjekt in Slide speichern)
		*/
		prepareSlideAnimation : function( $slide )
		{
			var d 			= $slide.data('ptslider') || {},
				opt 		= $slide.data('ptoptions') || {},
				config  	= {};

			if( opt.animations )
			{
				var animations = $.extend( true, {}, this.options.animations, opt.animations );
			}
			else{
				var animations = this.options.animations;
			}


			if( !this.State.trigger.name ) // Wenn Navigieren z.B. von extern ausgeführt wird
			{
				this.State.trigger = {name : '', direction : ''};
			}

			switch( this.State.trigger.name )
			{
				case 'navigation' :
					switch( this.State.trigger.direction )
					{
						case 'next' :
							config = {
								 animationIn 	: animations.navigation.nextIn
								,animationOut 	: animations.navigation.nextOut
								,duration		: animations.navigation.duration
							}
						break;
						case 'prev' :
							config = {
								 animationIn 	: animations.navigation.prevIn
								,animationOut 	: animations.navigation.prevOut
								,duration		: animations.navigation.duration
							}
						break;
					}

				break;

				case 'swipe' :

					switch( this.State.trigger.direction )
					{
						case 'next' :
							config = {
								 animationIn 	: animations.swipe.nextIn
								,animationOut 	: animations.swipe.nextOut
								,duration		: animations.swipe.duration
							}
						break;
						case 'prev' :
							config = {
								 animationIn 	: animations.swipe.prevIn
								,animationOut 	: animations.swipe.prevOut
								,duration		: animations.swipe.duration
							}
						break;
					}

				break;

				case 'pagination' :

					config = {
						 animationIn 	: 	animations.pagination.in
						,animationOut 	:	animations.pagination.out
						,duration		:	animations.pagination.duration
					};

				break;

				case 'autoplay'   :
				default :

					var ain = animations.autoplay.in,
						out = animations.autoplay.out;

					if( animations.autoplay.random && typeof ain === 'object' && typeof out === 'object' )
					{
						ain = ain[ Math.floor( Math.random() * ain.length ) ];
						out = out[ Math.floor( Math.random() * out.length ) ];
					}

					config = {
						animationIn 	: 	ain
						,animationOut 	:  	out
						,duration	 	:	animations.autoplay.duration
					};

				break;
			}

			$slide.data('ptslider', $.extend(true, d, config));
		},


		/*
			Slide (Spalte) einblenden
		*/
		animateSlide : function(slide, direction, callback) {
			var self 	= this;
			

			this.prepareSlideAnimation(slide);
			var d = slide.data('ptslider');

			var context;

			switch(direction) {
				case 'in' :
					context = {direction : 'in', animation : slide.data('ptslider').animationIn, eventBefore : 'beforeSlideIn', eventAfter : 'afterSlideIn', cssClass : this.options.css.classActive, cssInline : this.options.css.styleActive, callback : callback};
				break;
				case 'out' :
					context = {direction : 'out', animation : slide.data('ptslider').animationOut, eventBefore : 'beforeSlideOut', eventAfter : 'afterSlideOut', cssClass : this.options.css.classInactive, cssInline : this.options.css.styleInactive, callback : callback};
				break;
			}

			if(typeof self.options.onBeginSlideAnimation === 'function') {
				self.options.onBeginSlideAnimation.apply(slide);
			}

			this.$node.triggerHandler(context.eventBefore, [{slide : slide}]);

			slide.one(this.State.events.animationEnd, function(slide, ev) {
				
				slide.css(context.cssInline);
				
				if(this.direction == 'in') {
					slide.addClass(self.options.css.classActive)
				}
				else{
					slide.removeClass(self.options.css.classActive)
				}

				if( typeof context.callback === 'function' ) {
					context.callback();
				}
	
				if( typeof self.options.onAfterSlideAnimation === 'function' ) {
					self.options.onAfterSlideAnimation.apply(slide); // Public Callback
				}
	
				self.$node.triggerHandler(context.eventAfter, [{slide : slide}]); // In Firefox und Safari funktioniert das nicht. Dort ist $slide der Slide, der gerade angezeigt wird, und nicht der, der zuletzt angezeigt wurde.
				slide.triggerHandler(context.eventAfter);

			}.bind(context, slide))
			.css($.extend(this.options.css.styleActive, {"-webkit-animation-duration" : slide.data('ptslider').duration, "animation-duration" : slide.data('ptslider').duration}))
			._ptsSetAnimation(context.animation);
		},

		changeColumn : function( col )
		{
			var self 	= this,
				islast	= false,
				slides 	= 	{
								current : this.State.stage[col],
								next 	: this.map[this.State.rowNext][col]
							};

			/*
				Hier passiert nichts wenn:

				- slides.next nicht existiert, weil die nächste Zeile an dieser Position keinen Slide hat – das passiert z.B. bei 3 Spalten mit insg. 8 Slides im Protoslider

				- wenn eine Zeile erreicht wird, an der slides.next wieder existiert, der aber bereits angezeigt wird – Das passiert z.B. bei 3 Spalten mit nur insg. 5 Slides im Protoslider

			*/
			if( slides.next.get(0) && slides.next !== slides.current )
			{
				var nz = this.State.rowNext == 0 ? 1 : this.map.length - this.State.rowNext;

				if( col + 1 == this.map[this.State.rowNext].length )
				{
					islast = true; // Letzter Slide in Zeile "nextRow"
				}

				slides.current.css({ zIndex : nz });
				slides.next.css({ zIndex : self.map.length });

				this.animateSlide(slides.current, 'out');

				if( islast )
				{
					/*
					this.animateIn( slides.next, function()
					{
						self._onAfterRowChange();
					});*/
					this.animateSlide(slides.next, 'in', function()
					{
						self._onAfterRowChange();
					});
				}
				else
				{
					//this.animateIn( slides.next );
					this.animateSlide(slides.next, 'in');
				}

				this.State.stage[col] = slides.next;

				if( !islast )
				{
					this.changeRow(col + 1);
				}

			}
			else
			{
				// slides.next in Spalte "col" entspricht slides.current, weil die derzeitige Zeile weniger Slides enthält, als die anderen Zeilen.
				self._onAfterRowChange();
			}
		},


		changeRow : function( col )
		{
			this._onRowChange();

			if(!col)
			{
				col = 0;
			}

			var delay = this.options.animations[this.State.trigger.name].delay;

			if( col > 0 && delay > 0 && this.State.columns > 1 )
			{
				var self = this;

				window.setTimeout( function()
				{
					self.changeColumn( col );
				}, delay);
			}
			else
			{
				this.changeColumn( col );
			}
		},


		/*
			Beim starten eines Zeilenwechsels:
		*/
		_onRowChange : function()
		{
			this.State.isIdle 	= true;

			if( this.options.autoheight )
			{
				this.autoHeight(this.State.rowNext);
			}

			if( typeof this.options.onBeginAnimation === 'function' )
			{
				this.options.onBeginAnimation.apply(this); // Public Callback
			}

			this.$node.triggerHandler('rowChange', [{row : this.State.rowNext}]);

			if( this.options.pagination )
			{
				this.updatePagination(this.State.rowNext);
			}
		},


		/*
			Nach einem Zeilenwechsel:
		*/
		_onAfterRowChange : function()
		{
			this.State.row 			= this.State.rowNext;
			this.State.isIdle 		= false;

			if( this.options.autoplay && !this.State.paused )
			{
				this.play();
			}

			if( typeof this.options.onAfterAnimation === 'function' )
			{
				this.options.onAfterAnimation.apply(this); // Public Callback
			}

			this.$node.triggerHandler('afterRowChange', [{row : this.State.row}]);
		},



		/*
			-- Steuerung ---------------------------------------------------------------------------
		*/


		/*
			Nächste Zeile für Autoplay
		*/
		getNavDirection : function()
		{
			var navTo = 0;

			switch( this.options.direction )
			{
				case 'forwards' :
					navTo = 'next';
				break;
				case 'backwards' :
					navTo = 'prev';
				break;
				case 'random' :
					do{
						navTo = Math.floor( Math.random() * this.map.length );
					}
					while (navTo == this.State.row);
				break;
				default :
					navTo = 'next';
				break;
			}

			return navTo;
		},


		/*
			Blende Zeile "to" ein
		*/
		navigate : function( to )
		{
			if( this.State.isIdle )
			{
				return;
			}


			this.clearAutoplayTimeout(); // Falls Navigation oder Paginierung geklickt wird, ist das hier sicherer

			switch(to)
			{
				case 'next' :
					this.State.rowNext = this.State.row + 1 < this.map.length ? this.State.row + 1 : 0;
				break;
				case 'prev' :
					this.State.rowNext = this.State.row - 1 < 0 ? this.map.length - 1 : this.State.row - 1;
				break;
				default :
					if( !isNaN(to) && to != this.State.row )
					{
						this.State.rowNext = to > 0 && to < this.map.length ? to : 0;
					}
				break;
			}

			if( typeof this.options.onBeforeAnimation === 'function' )
			{
				this.options.onBeforeAnimation.apply(this); // Public Callback
			}

			this.changeRow(); // Zeilenwechsel
		},


		// -- Lösche autoplay Timeout
		clearAutoplayTimeout : function()
		{
			window.clearTimeout( this.State.playtimer ); // Autoplay Timeout
		},


		clearDelayedStart : function()
		{
			window.clearTimeout( this.State.starttimer );  // Delay Start Timeout
		},


		/*
			Autoplay starten

			@param delay – Optionale Zeit in Millisekunden – anstelle von options.timeout
		*/
		play : function(delay)
		{
			this.clearAutoplayTimeout();

			// if( this.$slides.length < 2 ) return;

			if( !this.options.loop && (this.State.row + 1) == this.State.rows || this.$slides.length < 2 )
			{
				this.pause();
				return;
			}

			this.State.paused = false;

			var self 	= this;
			
			delay = !isNaN(parseInt(delay)) ? delay : this.options.timeout;

			this.State.playtimer = window.setTimeout( function()
			{
				self.run();

			}, delay );


			if( typeof this.options.onPlay === 'function' ) // <------- Ist das noch an der richtigen Stelle?
			{
				this.options.onPlay.apply( this );
			}

			this.$node.triggerHandler('play'); // <------- Ist das hier an der richtigen Stelle?
		},


		/*
			Sofort einen Zeilenwechsel einleiten, mit Richtung aus this.options.direction
		*/
		run : function()
		{
			if( this.options.onlyInViewport && !this.isInViewport() )
			{
				this.play();
				return;
			}

			var navigateTo = this.getNavDirection()

			this.State.trigger = { name : 'autoplay', direction : navigateTo };

			this.navigate( navigateTo );


		},


		/*
			Autoplay Pause
		*/
		pause : function()
		{
			this.clearAutoplayTimeout();

			if( this.State.starttimer )
			{
				this.clearDelayedStart();
			}

			this.State.paused = true;

			if( typeof this.options.onPause === 'function' )
			{
				this.options.onPause.apply(this); // Public Callback
			}
			this.$node.triggerHandler('pause');
		},

	}


	$.fn.protoslider = function( options ){
		var self = $(this).data( 'ptslider' );

		if( self === undefined )
		{
			self = new $.Protoslider(options, this);
		}
		return self;
	}

})(jQuery);


/*
	Protoslider Plugin HTML5 Video 0.2
	Carsten Ruppert
	2017-10-13

	- Funktioniert derzeit nur mit der ersten Spalte, die angezeigt wird (siehe auch Optionen "size" und "items" in Protoslider).

	Optionen an Plugin übergeben:

	$('#mein-slider').protoslider({html5video : {pauseOnPlay : false, ...}});
*/
'use strict';

(function($) {

	$.Protoslider.Plugins.push('ProtosliderHtml5video');

	$.Protoslider.defaults.html5video = {
		pauseOnPlay 			: true,		// Protoslider beim Abspielen eines Videos anhalten, wenn autoplay an ist. Nach dem Ende des Videos läuft Protoslider weiter.
		autoplay 				: false,	// Videos automatisch abspielen (erzwungen, ansonsten mit: <video data-ptoptions='{"autoplay":true}'  ... >) – !!! <video autoplay ...> würde das Video starten, selbst wenn es noch gar nicht zu sehen ist !!!
		timeoutAfterPlayback 	: 100
	};


	$.ProtosliderHtml5video = function(parent)
	{
		this.parent = parent;
		this._init();
	}

	$.ProtosliderHtml5video.prototype = {

		// this.parent ist in diesem Kontext eine protoslider Instanz

		_init : function()
		{
			this.parent.on('beforeSlideIn.ptslider.html5video sliderReady.ptslider.html5video', function(ev, context)
			{
				// afterRender wird ausgelöst wenn Protoslider instanziert wird, oder sich die Anzahl der sichtbaren Spalten ändert.
				this.tick(context);
			}.bind(this));
		},

		tick : function(context)
		{
			var slide = context ? context.slide : this.parent.State.stage[0],
				video = this.getVideoObject(slide);

			if(video)
			{
				if(video.readyState >= 2) // Mindestens die Metadaten und ein Frame des Videos stehen zur Verfügung.
				{
					this.playVideo(slide);
					if(this.parent.options.autoheight) { 
						this.parent.autoHeight();
					}
				}
				else // Noch nix da. Müssen warten.
				{
					$(video).one('canplay.ptslider.html5video', function(slide, ev)
					{
						this.playVideo(slide);
						if(this.parent.options.autoheight) this.parent.autoHeight();

					}.bind(this, slide));
				}
			}
		},

		getVideoObject : function(slide)
		{
			var video = slide.find('video');
			if( video.length ) return video.get(0);
			return false;
		},

		playVideo : function(slide)
		{
			var video = this.getVideoObject(slide);

			if(video)
			{
				var opt = $(video).data('ptoptions') || {};

				// Video Zurückspulen wenn ein Slide ausgeblendet wurde.
				if(!video.hasAttribute('loop'))
				{
					slide.one('afterSlideOut.ptslider.html5video', function(ev)
					{
						var video = this.getVideoObject($(ev.target));

						if(video) {
							video.pause();
							video.currentTime = 0;

							// -- Den ended-Handler des Videos zurücksetzen. Und Protoslider autoplay starten
							// Es könnte sein, dass der User manuell navigiert hat, und der ended Event nicht eintreten konnte.
							$(video).off('.ptslider.html5video');
							if(this.parent.options.autoplay && this.parent.options.html5video.pauseOnPlay) {
								this.parent.play();
							}
						}
					}.bind(this));
				}

				// -- Wenn Videos automatisch abgespielt werden sollen
				if(this.parent.options.html5video.autoplay || opt.autoplay)
				{
					// -- Protoslider autoplay ist an, und Protoslider muss für die Dauer des Videos angehalten werden.
					if(this.parent.options.html5video.pauseOnPlay && this.parent.options.autoplay)
					{
						$(video).one('ended.ptslider.html5video', function()
						{
							this.parent.play(this.parent.options.html5video.timeoutAfterPlayback);
						}.bind(this));
						this.parent.pause();
					}
					video.play();
				}
			}
		}

	}
})(jQuery);
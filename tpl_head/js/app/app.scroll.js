/*
	Cru.: 2017-04-20

	2017-06-13
	+ Offset Element hinzugefügt



	Scrollen zu Sprungmarke/Anker:
		<a name="top"></a>

	Auslöser:
		<a href="#top" data-scroll>Nach oben</a>



	Scrollen zu beliebigen HTML Element:
		<div id="div">...</div>

	Auslöser:
		<a href="#div" data-scroll></a>

		<a tabindex="0" data-scroll="#div"></a>

		<button data-scroll="#div">...

		<a data-scroll='{"el":"#div","force":true}' tabindex="0"></a>

		<a data-scroll='{"el":"#div","force":true}' href="Eigentlich–gehts-beim-Klick-woanders-hin.html"></a>



	JavaScript:

		Wenn z.B. neuer Content in die Seite geladen wurde (AJAX...), kann neu Initialisiert werden:

			$app.scroll.init({ node : $('#NEUER-CONTENT') [, triggers : 'MEIN NEUER, optionaler, JQUERY SELECTOR'] });

			$app.ajax.on('afterLoad', function(data)
			{
				$app.scroll.init({ node : data.target });
			}):

	oder:

		$app.scroll.scrollTo('#hierhier-scrollen');

	oder:

		var x = $('#hierher-scrollen');

		$app.scroll.scrollTo({el : x, force : true, speed : 0.5 });

*/
(function($){

	$app.scroll = {

		defaults : {
			speed 					: 0.7,					// Kleiner = schneller!
			triggers 				: '[data-scroll]',		// Auslöser, siehe oben
			force					: false,				// false = Es wird nur gescrollt, wenn das Element NICHT im Vieport zu sehen ist
			instantOnSmallDevice 	: true,					// true	 = Bei einer horizontalen Auflösung kleiner defaults.smallDevice wird nicht animiert, sondern direkt gesprungen
			smallDevice 			: 767,					// Bis zu dieser Bildschrimbreite wird nicht animiert
			offsetElement 			: false					// Ein jQuery-Selektor von einem Element, dessen Höhe beim Scrollen abgezogen wird (z.B. ein fixierter Seiten-Header...), oder false.
		},

		init : function(options){

			this.options = $.extend({}, this.defaults, options);

			this.setTriggers();
		},


		setTriggers : function()
		{
			var $triggers = this.options.node ? $(this.options.node).find(this.options.triggers) : $(this.options.triggers);

			for( var i = 0, len = $triggers.length; i < len; i++ )
			{
				var $t = $triggers.eq(i);

				if( ! $t.data('app-scroll-listen') )
				{
					$t.data('app-scroll-listen', true);

					$t.on('click.app.scroll', function(ev)
					{
						ev.preventDefault();

						var $this 	= $(this),
							data 	= $this.data('scroll'),
							href	= $this.attr('href');

						switch( typeof data )
						{
							case 'string':

								if( data == '' )
								{
									var el = href !== undefined ? href : '';
								}
								else
								{
									var el = data;
								}

								$app.scroll.scrollTo({el : el, trigger : $this});

							break;
							case 'object':

								data.trigger = $this;

								data.el = data.el || ( href !== undefined ? href : '' );

								$app.scroll.scrollTo(data);

							break;
						}



					});

				}
			}
		},



		/*
			Scrolle nach data.el, wenn es NICHT im Viewport zu sehen ist. Oder "data.force", um auf jeden Fall zu scrollen.

			data{
				@param el 		– String jQuery-Selektor oder jQuery Objekt
				@param force	- Boolesch, optional. Auch wenn das Element zu sehen ist, ein Scrollen an die Oberkante des Elements auslösen

				Optionen:

				speed : Nummer (0.000∞ bis x.x) – Die Scrollgeschwindigkeit wird errechnet aus diesem Wert, der aktuellen Scrollposition und des Offset von "el". Kleinerer Wert = schnelleres Scrollen
				instantOnSmallDevice : Boolesch  – Auf Bildschirmbreiten kleiner 768 wird keine Animation ausgelöst, sondern direkt zur Marke gesprungen
			}
		*/
		scrollTo : function( data )
		{
			if( typeof data === 'string' )
			{
				var opt = {el : $(data + ',[name="'+data.replace('#','')+'"]')};
				opt = $.extend( {}, this.options, opt );
			}
			else
			{
				var opt = $.extend( {}, this.options, data ),
					el  = typeof opt.el === 'object' ? opt.el : $(opt.el + ',[name="'+opt.el.replace('#','')+'"]');
			}

			if( !el.length || $app.isInViewport(el) == true && opt.force != true )
			{
				return;
			}

			var velocity;

			if( $app.getVps().w < opt.smallDevice && opt.instantOnSmallDevice )
			{
				velocity = 0;
			}
			else
			{
				if( el.offset().top > $app.$document.scrollTop() )
				{ // Scrollt nach unten
					velocity = el.offset().top - $app.$document.scrollTop();
				}
				else
				{ // Scrollt nach oben
					velocity = $app.$document.scrollTop() - el.offset().top;
				}
				velocity = velocity * opt.speed;
			}

			var opt = {
				duration : velocity,
				easing : 'swing'
			};
			if( typeof callback === 'function' ) opt.complete = callback;

			var y = el.offset().top;
			if( this.options.offsetElement )
			{
				y -= $(this.options.offsetElement).outerHeight();
			}

			$('html, body').stop().animate({ scrollTop : y }, opt );
		}
	}
})(jQuery);

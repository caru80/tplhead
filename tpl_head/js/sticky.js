/*
	sticky.js 1.0.2
	CRu. 2017-05-26



	$('#mein-element').sticky();

	Oder:

	$('#mein-element').sticky( {option1 : "wert1"[, option2 : "wert2" ...] } );

	Oder (von $app geregelt):

	<div data-sticky></div>

	Oder (von $app geregelt):

	<div data-sticky='{"option1":"wert1"}'></div>



	Das Sticky-Element wird automatisch an seinem Elternelement ausgerichtet. Wenn das unerwünscht ist, kann mit der Option "stickTo" bestimmt werden, woran
	das Sticky-Element ausgerichtet wird.



	1.0.2 – 2017-05-26
	- Kein sticky, wenn das sticky-Element größer Viewporthöhe (dynamisch)

	1.0.1 – 2017-05-16
	- delayedTick Verzögerung für die Änderung der Position des sticky-Elements eingebaut (nur mit useTranslate true). Weil die Positionsänderung in Browsern ohne "Smooth-Scrolling" (z.B. Chrome, Safari, Opera) sehr ruckelig ist.



*/
(function($){

	$.Sticky = function( options, node ){
		this.$node = $(node);
		this.$node.data('sticky-obj', this);
		this.init( options );
	};


	$.Sticky.defaults = {
		useTranslate 	: true,				/* 3d Tranformation benutzen, oder position */
		position		: 'absolute', 		/* hat keinen Effekt wenn useTransate eingeschaltet ist */
		classSticky		: 'is-sticky', 		/* Klasse für das Sticky-Element, wenn es sticky ist */
		breakpoint		: 767,			 	/* Kleiner und gleich dieser Bildschirmbreite kein Sticky mehr (Responsive...) */
		offsetElement	: false,			/* jQuery Selektor (oder Objekt) von einem Element dessen Höhe berücksichtigt wird (z.B. ein fixierter Seiten-Header), oder false. */
		delayTick		: true,
		delayCount		: 100,
		delayTransition : '0.25s'
	};


	$.Sticky.prototype = {
		init : function( options )
		{
			/* Optionen und Defaults zusammenführen zu this.options */
			this.options = $.extend( true, {}, $.Sticky.defaults, options );

			/* Optionen und inline-Optionen aus Attribut "data-sticky" zusammenführen */
			if( this.$node.data('sticky') )
			{
				$.extend( true, this.options, this.$node.data('sticky') );
			}

			this.$parent 	= this.$node.parent();
			this.$stickTo 	= this.options.stickTo ? $(this.options.stickTo) : this.$parent;

			this.id	= $app.getRandomId(); // Für die Events

			if( this.options.offsetElement )
			{
				this.$offsetEl = $(this.options.offsetElement);
			}

			if( this.options.delayCount )
			{
				this.timeout = null;
			}

			this.prepareNode();
			this.Tick();
		},


		getVps : function(){
			let w = window,
		    	e = document.documentElement,
		    	b = document.getElementsByTagName('body')[0],
		    	x = w.innerWidth || e.clientWidth || b.clientWidth,
		    	y = w.innerHeight|| e.clientHeight|| b.clientHeight;
			return {w : x, h : y};
		},


		isTaller : function()
		{
			let height = this.options.offsetElement ? this.$node.outerHeight() + this.$offsetEl.outerHeight() : this.$node.outerHeight()

			if( height > this.$stickTo.height() || height > this.getVps().h )
			{
				return true
			}
			return false
		},


		isBottomBoundary : function()
		{
			let hy = $(document).scrollTop() + this.$node.outerHeight() - this.$stickTo.offset().top;
			if( this.options.offsetElement )
			{
				hy += this.$offsetEl.outerHeight();
			}

			if( hy >= this.$stickTo.height() )
			{
				return true;
			}
			return false;
		},


		prepareNode : function()
		{
			if(!this.options.useTranslate)
			{
				this.setWidth();
			}
			else
			{
				this.$node.css({transform : 'translate3d(0,0,0)', '-webkit-transform' : 'translate3d(0,0,0)'});
			}
		},


		setWidth : function()
		{
			this.$node.css({ width : this.$parent.width() });
		},


		stick : function()
		{
			if(!this.$node.hasClass(this.options.classSticky))
			{
				this.$node.addClass(this.options.classSticky);
			}

			if( this.options.useTranslate && this.options.delayTick )
			{
				this.$node.css({'transition' : 'transform '+this.options.delayTransition+' ease', '-webkit-transition' : 'transform '+this.options.delayTransition+' ease'});
			}

			if( !this.options.useTranslate && this.$node.css('position') != this.options.position )
			{
				this.$node.css({position : this.options.position});
			}
		},


		unstick : function()
		{
			if( this.options.useTranslate )
			{
				this.$node.css({'transform' : 'translate3d(0,0,0)', '-webkit-transform' : 'translate3d(0,0,0)'});
			}
			else
			{
				this.$node.css({
					position 	: '',
					top			: ''
				});
			}

			this.$node.removeClass(this.options.classSticky);
		},


		moveTo : function( val )
		{
			if( this.options.useTranslate )
			{
				this.stick();
				this.$node.css({'transform' : 'translate3d(0,' + val + 'px,0)', '-webkit-transform' : 'translate3d(0,' + val + 'px,0)' });
			}
			else
			{
				this.stick();
				this.$node.css({ top : val });
			}
		},


		Tick : function()
		{
			let self 		= this,
				scrollTop 	= $(document).scrollTop();

			if( this.isTaller() || this.getVps().w <= this.options.breakpoint )
			{
				this.unstick();
				this.bindEvents();
				return;
			}

			if( this.isBottomBoundary() )
			{
				this.moveTo( this.$stickTo.height() - this.$node.outerHeight() );
				this.bindEvents();
				return;
			}

			if( this.$offsetEl )
			{
				scrollTop += this.$offsetEl.outerHeight();
			}

			if( this.$stickTo.offset().top < scrollTop)
			{
				this.moveTo( scrollTop - this.$stickTo.offset().top );
			}
			else
			{
				this.unstick();
			}

			this.bindEvents();
		},

		bindEvents : function()
		{
			let self = this;

			$(window).off('.app.sticky-' + this.id);

			$(window).one('scroll.app.sticky-' + this.id + ' resize.app.sticky-' + this.id, function()
			{
				if( self.options.delayTick )
				{
					window.clearTimeout(self.timeout);

					self.timeout = window.setTimeout(function(){
						self.Tick();
					}, self.options.delayCount);
				}
				else
				{
					self.Tick();
				}
			});

			if( !this.options.useTranslate )
			{
				$(window).one('resize.app.sticky-' + this.id, function(){
					self.setWidth();
				});
			}
		},


		refresh : function()
		{
			$(window).off('.app.sticky-' + this.id);
			this.Tick();
		}

	};


	$.fn.sticky = function( options ){
		let self = $(this).data( 'sticky-obj' );

		if( self === undefined ){
			self = new $.Sticky( options, this );
		}
		return self;
	}

})(jQuery);

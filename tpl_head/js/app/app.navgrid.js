'use strict';
(function () {

	function NavGrid (options) 
	{
		this.opt = NavGrid.defaults;

		for(let prop in options) {
			if(options.hasOwnProperty(prop)) {
				this.opt[prop] = options[prop];
			}
		}

		this.grid = this.opt.selector != '' ? document.querySelector(this.opt.selector) : document.getElementById('navgrid');

		if(this.grid._navgrid)
		{
			return this.grid._navgrid;
		}
		else 
		{
			this.grid._navgrid = this;
		}

		this.triggers = this.opt.selector != '' ? document.querySelectorAll('[data-navgrid-toggle="' + this.opt.selector + '"]') : document.querySelectorAll('[data-navgrid-toggle]');
		this._init(options);
	}
	
	NavGrid.defaults = {
		selector 		: '',				// CSS Selektor des navgrid = #navgrid wenn leer
		useCookieState 	: false,			// Benutze einen Cookie zum speichern des Zustands (offen oder zu)
		autoCollapse 	: true,				// Navgrid nach dem laden einer Seite automatisch ausblenden, wenn Cookie benutzt wird
		triggerActive 	: 'active',			// CSS Klasse für die Auslöser, wenn navgrid geöffnet ist
		cookiename 		: 'navgrid',		// Cookie Name
		disableScroll 	: true,
		closeOnWindowResize : false
	}

	NavGrid.prototype = {

		_init : function (options) 
		{
			if(this.triggers.length) 
			{
				this.setupTriggers();
			}

			if(this.opt.useCookieState)
			{
				switch(this.getCookie())
				{
					case undefined : // Kein Status, das Script läuft zum 1. mal
						if(this.grid.classList.contains('collapsed')) {
							this.setCookie('closed');
						}
						else {
							this.setCookie('open');
							this.setTriggerState(true);
						}
					break;

					case 'open' :
						this.toggleOpen();
					break;

					case 'closed' :
						this.toggleClosed();
					break;
				}
			}

			if(this.opt.closeOnWindowResize)
			{
				window.addEventListener('resize', function() {
					if(!this.grid.classList.contains('collapsed'))
					{
						this.toggleClosed();
					}
				}.bind(this));
			}

			if(this.opt.autoCollapse) 
			{
				window.setTimeout(function(){
					this.toggleClosed();
				}.bind(this), 300);
			}

			if(window.Event) 
			{
				let events 	= ['open','close'];
				this.events = {};
				events.forEach(function(name) {
					this.events[name] = new Event(name);
				}, this);
			}
		},

		setupTriggers : function () 
		{
			this.triggers.forEach(function(t) {
				t.addEventListener('click', function() {
					this.toggle();
				}.bind(this));
			}, this);
		},

		setTriggerState : function (active) 
		{
			for(let i = 0, len = this.triggers.length; i < len; i++) 
			{
				let classes = this.triggers[i].classList;

				if(active) 
				{
					classes.add(this.opt.triggerActive);
				}
				else 
				{
					classes.remove(this.opt.triggerActive);
				}
			}
		},

		toggleOpen : function () 
		{
			this.grid.classList.remove('collapsed');
			this.setTriggerState(true);

			if(this.opt.disableScroll)
			{
				document.body.style.overflow = 'hidden';
			}

			if(this.opt.useCookieState) 
			{
				this.setCookie('open');
			}

			if(this.events) this.grid.dispatchEvent(this.events.open);
		},

		toggleClosed : function () 
		{
			this.grid.classList.add('collapsed');
			this.setTriggerState(false);

			if(this.opt.disableScroll)
			{
				document.body.style.overflow = '';
			}

			if(this.opt.useCookieState) 
			{
				this.setCookie('closed');
			}

			if(this.events) this.grid.dispatchEvent(this.events.close);
		},

		toggle : function ()
		{
			if(this.grid.classList.contains('collapsed'))
			{
				this.toggleOpen();
			}
			else 
			{
				this.toggleClosed();
			}
		},

		setCookie : function (state) {
			if (Cookies) {
				Cookies.set(this.opt.cookiename, state, { expires: 7 });
			}
		},

		getCookie : function () {
			if (Cookies) {
				return Cookies.get(this.opt.cookiename);
			}
			return false;
		}
	}

	$app.navgrid = NavGrid;

})();

/**
	2018-01-10
	- options.html entfernt weil Obsolet.
	
*/
(function($){

	$app.ajax = {

		options : {
			tmplComponent 	: 'ajax_loadcomponent',
			tmplModules		: 'ajax_loadmodule',
			useIndicator	: true,
			css 			: 	{
									targetShow : 'show'
								}
		},


		init : function()
		{
			if( arguments[0] )
			{
				var $node = $(arguments[0]);
			}
			else
			{
				var $node = $('body');
			}

			this.State = {};

			this.setTriggers($node);
		},


		setTriggers : function()
		{
			if( arguments[0] )
			{
				var $node = typeof arguments[0] === 'object' ? arguments[0] : $(arguments[0]);
			}
			else
			{
				var $node = $('body');
			}

			var triggers = $node.find('[data-ajax]');

			for( var i = 0, len = triggers.length; i < len; i++ )
			{
				var self 	= this,
					$t 		= triggers.eq(i);

				if( !$t.data('ajax-listen') ) // Event noch nicht gesetzt...
				{
					$t.data('ajax-listen', true);

					$t.on('click', function(ev){
						ev.preventDefault();

						var $this	= $(this),
							params	= $this.data('ajax');

						if( params )
						{
							params.trigger = $this;

							if( params.rtrigger )
							{
								$this.remove();
							}

							if( params.module )
							{
								// Lade Modul
								self.loadModule( params );
							}
							else
							{
								params.url 		= params.url || $this.attr('href');
								params.target 	= params.target || document.body;
								params.only		= params.only || false;

								if( params.url )
								{
									self.load( params );
								}
							}
						}

						return false;
					});
				}
			}
		},


		// Zeige Ladeindikator
		showLoadingIndicator : function( params )
		{
			if( this.options.useIndicator )
			{
				this.State.indicatorId = $app.showLoadingIndicator({t : params.target});
			}
		},

		// Verberge Ladeindikator
		hideLoadingIndicator : function( params )
		{
			if( this.State.indicatorId !== undefined )
			{
				$app.hideLoadingIndicator({id : this.State.indicatorId});
			}
		},


		callback : function( params )
		{
			if(params)
			{
				this.setTriggers( params.target );

				$(this).trigger('callback', [{params : params}]);
			}
		},


		getQuerySeparator : function(url)
		{
			if(url.indexOf('?') > -1)
			{
				return '&';
			}
			return '?';
		},

		/*
			Lade Komponente...

			params : {
				url – String URL
				target – String, jQuery Selektor vom Ziel-Container
				only - String, jQuery Selektor von einem Element in den geladenen Daten, das eingefügt werden soll (der Rest wird verworfen)
			}

			Siehe auch jQuery .load()

			Siehe auch /templates/head/ajax_loadcomponent.php

		*/
		load : function( params )
		{
			var self = this;

			if( params.only )
			{
				url = params.url + this.getQuerySeparator(params.url) + 'tmpl=' + encodeURIComponent(this.options.tmplComponent) + ' ' + encodeURIComponent(params.only);
			}
			else
			{
				url = params.url + this.getQuerySeparator(params.url) + 'tmpl=' + encodeURIComponent(this.options.tmplComponent);
			}

			this.showLoadingIndicator(params);

			$(this).trigger('beforeLoad', [{params : params}]);

			$(params.target).load(url, function()
			{

				$(self).trigger('afterLoad', [{params : params}]);

				self.callback( params.target );

				$(params.target).prepareTransition().addClass(self.options.css.targetShow);
			});
		},

		/*
			Sende Request

			params : {
				url – String URL
				data – Objekt mit Daten
				callback – function, nach Erfolg ausführen
			}

			Siehe auch jQuery .ajax()

		*/
		sentRequest : function( params )
		{
			var self = this;

			if( params.url )
			{
				// this.showLoadingIndicator(params); // Ohne params.target kann der nicht angezeigt werden

				$.ajax({
					url 	: params.url,
					data 	: params.data || {},

					error : function()
					{
						if( console ) console.log('$app.ajax.sentRequest hat nicht funktioniert.');
					},
					success : function(data)
					{
						if( params.callback && typeof params.callback === 'function') params.callback.call(self);
					},
					complete : function(data)
					{
						$(self).trigger('afterSentRequest', [{params : params, data : data}]);
					}
				});
			}
		},

		/*
			loadModule:

			@params params – Objekt

			params{
				id : Integer – Modul Id
				position : String – Modulposition
				chrome : String – moduleChrome
				target : String – jQuery Selektor vom Element in welches das Modul engehängt wird.
				purge : Boolean – Inhalt von container vor dem Einhängen leeren.
				callback : Function – Wenn Request beendet diese Funktion ausführen
			}

			Entweder id oder position angeben.

			Siehe auch jQuery.ajax()

			Siehe auch /templates/head/ajax_loadmodule.php
		*/
		loadModule : function( params )
		{
			var self 	= this,
				request = $app.protocol + '//' + $app.hostname + $app.pathname + '/index.php?tmpl=' + encodeURIComponent(this.options.tmplModules),
				loaded = false;

			if( params.id && ! isNaN(params.id) ) request += '&i=' + encodeURIComponent(params.id); // Modul Id

			var pkeys = Object.keys(params),
				rdata  = {};

			for( var i = 0, len = pkeys.length; i < len; i++ )
			{
				if( pkeys[i] != 'id' && pkeys[i] != 'module' && pkeys[i] != 'trigger' )
				{
					rdata[pkeys[i]] = params[pkeys[i]];
				}
			}

			this.showLoadingIndicator(params);

			$(this).trigger('beforeLoadModule', [{params : params}]);

			$.ajax({
				url 	: request,
				data	: rdata,
				contentType : "application/x-www-form-urlencoded; charset=UTF-8",
				error 	: function( xhr, textStatus, errorThrown )
				{
					if( console )
					{
						console.log('Joomla! Modul konnte nicht geladen werden: ');
						console.log(textStatus);
						console.log(errorThrown);
					}
				},
				success : function(data,s)
				{
					if( params.purge )
					{
						$(params.target).empty();
					}

					self.hideLoadingIndicator( params );

					$(params.target).append(data);
				},
				complete : function(data)
				{
					$(self).trigger('afterLoadModule',[{params : params, data : data}]);

					if( typeof params.callback === 'function' ) params.callback.call(self, data);
					self.callback( params );
				}
			});

		}
	}
})(jQuery);

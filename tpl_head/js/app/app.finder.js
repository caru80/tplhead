(function($){

	/*
		$app.finder

		AJAX-Wrapper für den Joomla! Suchindex.

		Siehe auch: /html/mod_finder – Das Modul muss vorhanden sein, damit dieses Script funktioniert. UND es funktioniert nur mit EINEM Suchindexmodul auf der aktuell angezeigten Seite!
		Siehe auch: /less/components/app.finder.less


		JavaScript:

			$app.finder.init(); Startet die Überwachung der Suchmaske, wenn vorhanden. Suchergebnisse werden per AJAX nach options.results geladen.

			$app.finder.find('Suchbegriff'); Löst die Suche nach 'Suchbegriff' aus.

			$app.finder.find(); Löst die Suche mit dem Wert des Feldes options.queryinput aus. Oder macht nichts, wenn das Feld leer ist.

		HTML:

			Wenn option.enableLinks = true:

			<a tabindex="0" data-findquery="Mein Suchbegriff">...</a> Löst bei Klick die Suche nach "Mein Suchbegriff" aus.

			<div data-findquery="Mein Suchbegriff">...</div>

			Oder auch, bei Suche im Overlay (müsste funktionieren): <a tabindex="0" data-overlay data-findquery="Mein Suchbegriff">...</a>

		Events:

			find 		= Die Suche wurde ausgelöst, die Ergebnisse werden geladen
			afterFind 	= Die Ergebisse wurden geladen

			Bspw.:

				$($app.finder).on('afterLoad', function(){
					...
				})
	*/

	$app.finder = {

		defaults : {
			form		: '#mod-finder-searchform',
			overlay 	: '#search-overlay',
			queryinput 	: '#mod-finder-searchword',
			filterinput	: '#mod-finder-filterid',
			taxoinput	: '#mod-finder-taxonomyid',
			minqlen		: 3, // Query Mindestlänge
			results		: '#search-results',
			tmpl		: 'ajax_plain',
			enableLinks	: true,


			// Der Autocompleter wird in den Moduleinstellungen ein- oder ausgeschaltet!
			// Siehe auch: https://github.com/devbridge/jQuery-Autocomplete
			autocompleter : {
				containerClass	: 'autocomplete-suggestions',
				paramName		: 'q',
				minChars		: 1,
				maxHeight		: 400,
				width			: 'auto',
				zIndex			: 9999,
				deferRequestBy	: 500
			},
			html			: 	{
									indicator : '<div class="loading-indicator"><div class="loading-inner"><div class="sk-cube-grid"><div class="sk-cube sk-cube1"></div><div class="sk-cube sk-cube2"></div><div class="sk-cube sk-cube3"></div><div class="sk-cube sk-cube4"></div><div class="sk-cube sk-cube5"></div><div class="sk-cube sk-cube6"></div><div class="sk-cube sk-cube7"></div><div class="sk-cube sk-cube8"></div><div class="sk-cube sk-cube9"></div></div></div></div>'
								},
			useLoadingIndicator 	: true
		},

		init : function( options )
		{
			this.options 	= $.extend( true, {}, this.defaults, options );
			this.form		= $(this.options.form);

			this.State = {};

			if( this.form.length )
			{
				this.qfield 	= this.form.find(this.options.queryinput);
				this.ffield 	= this.form.find(this.options.filterinput);
				this.tfield 	= this.form.find(this.options.taxoinput);

				this.bindFieldEvents();

				this.results 	= $(this.options.results);

				this.bindFormEvent();

				if( this.options.enableLinks )
				{
					this.bindLinkEvents();
				}
			}
		},

		autocompleter : function()
		{
			var self = this;

			$($app).one('finderAutocompleterReady', function()
			{
				var url 	= 'index.php?option=com_finder&task=suggestions.suggest&format=json&tmpl=' + self.options.tmpl,
					options = $.extend( true, {serviceUrl : url}, self.options.autocompleter );

				self.qfield.autocomplete(options);
				self.autoCompleterNode = $('.' + self.options.autocompleter.containerClass);

				if( self.autoCompleterNode.length )
				{
					self.autoCompleterNode.on('mousedown.app.finder', function(ev){ // onclick will es nicht
						// Wir müssen einen kurzen Moment warten, weil das Feld erst noch mit dem Wert belegt werden muss.
						window.setTimeout(function(){self.find();}, 200);
					});
				}
			});
			$app.extensions.load('finderAutocompleter', true);
		},

		bindFormEvent : function()
		{
			var self = this;
			this.form.on('submit.app.finder', function(ev){
				ev.stopPropagation();
				self.find();
				return false; // Form nicht abschicken.
			});
		},


		bindFieldEvents : function()
		{
			this.qfield.on('focus', function(){
				var q = $(this);
				if( q.val() != '' ) q.val('');
			});
		},

		unbindFormEvent : function()
		{
			this.form.off('submit.app.finder');
		},



		bindLinkEvents : function()
		{
			var finder		= this,
				$node 		= arguments[0] && typeof arguments[0] === 'object' ? arguments[0] : $(document.body);
				$triggers 	= $node.find("[data-findquery]");

			for( var i = 0, len = $triggers.length; i < len; i++ )
			{
				var $t = $triggers.eq(i);
				$t.on('click.app.finder', function(ev)
				{
					ev.preventDefault();
					var query = $(this).data('findquery');
					if( query != '' )
					{
						finder.find(query);
					}
				});
			}
		},

		// AJAX Pagination
		setFinderPagination : function()
		{
			var self 	= this,
				pagina 	= $(this.options.results).find('.pagination');

			if( pagina.length )
			{
				var items = pagina.find('a');
				for( var i = 0, len = items.length; i < len; i++ )
				{
					var pager = items.eq(i);

					pager.on('click', function(ev)
					{
						ev.preventDefault();
						var $this = $(this);
						self.load($this.attr('href'));
					});
				}
			}
		},

		showLoading : function()
		{
			if( this.options.useLoadingIndicator )
			{
				this.State.indicatorId = $app.showLoadingIndicator({t : this.options.results})
			}
		},

		hideLoading : function()
		{
			if( this.useIndicator && this.State.indicatorId !== undefined )
			{
				$app.hideLoadingIndicator({id : this.State.indicatorId})
			}
		},

		load : function(url)
		{
			$(this).triggerHandler('find')

			var self = this;

			this.results.empty()
			this.showLoading()

			this.results.load(url, function( response, status, xhr )
			{
				if ( status == "error" ) {
					return;
				}

				self.hideLoading()

				self.setFinderPagination()
				$(self).triggerHandler('afterFind')
			});
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
			Neue Objektnotation: {q : query, f : Filter-Id}
		*/

		find : function()
		{
			var q, f;

			if( arguments[0] && typeof arguments[0] === 'string' )
			{
				q = arguments[0];
				this.qfield.val(q);
			}
			else if( arguments[0] && typeof arguments[0] === 'object' )
			{
				var params = arguments[0];

				q = params.q || '';
				this.qfield.val(q);

				f = params.f || '';
				t = params.t || '';
			}
			else
			{
				q = this.qfield.val();
				f = this.ffield.length ? this.ffield.val() : '';
				t = this.tfield.length ? this.tfield.val() : '';

				this.qfield.blur();
			}

			this.lastQuery 		= q;
			this.lastFilter 	= f;
			this.querystring 	= 'q=' + encodeURIComponent(q) + '&f='+f+'&t[]=&t[]='+t+'&t[]=&t[]=&tmpl=' + this.options.tmpl;

			//console.log(this.querystring);

			this.route  = this.form.attr('action');
			this.route += this.getQuerySeparator(this.route) + this.querystring;

			//console.log(this.route);

			this.load(this.route);
		}

	}
})(jQuery, $app);

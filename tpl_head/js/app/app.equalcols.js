'use strict';

(function($){
	/*
		Equal Columns
		CRu. 2017-11-03

		2017-11-03:
		+ Nur Elemente mit dem gleichen Offset auf der y-Achse erhalten nun die gleiche Höhe, und nicht mehr alle Elemente in einer "Zeile"



		options{
			rowSelector : String – jQuery-Selektor für Zeilen
			colSelector : String – jQuery-Selektor für Spalten
			minWidth 	: Int - Minimale Viewportbreite, ab der equalColumns ausgeführt wird.
		}

		z.B.:

		<div class="row equal">
			<div></div>
		</div>

		rowSelector : '.row.equal'
		colSelector : 'div'


	*/
	$app.equalColumns = {

		defaults : {
			rowSelector : '.row.equal, .row-equal',
			colSelector	: '.col-equal, [class*="col-"], div',
			minWidth	: 768
		},

		init : function(options)
		{
			this.options = $.extend({}, this.defaults, options);

			this.equalize();
		},

		destroy : function()
		{
			this.reset();
			$app.$window.off('resize.app.equalcols');
		},

		reset : function()
		{
			let rows = $(this.options.rowSelector);

			for( let i = 0, len = rows.length; i < len; i++ )
			{
				let row 	= rows.eq(i),
					cols 	= row.children(this.options.colSelector);

				cols.css({height : ''});
			}
		},

		setResizeHandler : function() {
			let self = this;

			$app.$window.one('resize.app.equalcols', function() {
				self.equalize();
			});
		},

		equalize : function()
		{
			//$(window).off('resize.app.equalcols');

			let self = this;

			if( $app.getVps().w < this.options.minWidth) {
				this.reset();
				this.setResizeHandler();
				return;
			}

			let rows = $(this.options.rowSelector);

			for(let i = 0, len = rows.length; i < len; i++) {
				let row 		= rows.eq(i),
					cols 		= row.children(this.options.colSelector),
					h 			= 0,
					offsetMap 	= new Array();

				let offset = -1;
				for(let x = 0; x < cols.length; x++) {

					let col = cols.eq(x),
						ot  = parseInt(col.offset().top);

					if( ot != offset ) {
						offsetMap[ot] = new Array(col);
					}
					else {
						offsetMap[offset].push(col);
					}

					offset = ot;
				}

				offsetMap.forEach(function(line) {
					let height 	= 0;

					for(let y = 0; y < line.length; y++) {
						line[y].css({'height' : ''});

						let cHeight = line[y].outerHeight();

						if( cHeight > height ) height = cHeight;
					}

					for(let y = 0; y < line.length; y++) {
						line[y].css({'height' : height});
					}
				});
			}

			$($app).triggerHandler('afterEqualCols');
			/*
			$app.$document.one('afterUpdateRender', function()
			{
				self.equalize();
			});
			*/

			this.setResizeHandler();
		}
	};

})(jQuery);

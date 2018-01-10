/*
	Onepage-Nav Wegpunkte
*/
(function($){
	$app.waypoints = {

		defaults : {
			node	 		: '#waypoints',
			targets			: 'section',
			ignore 			: '',
			offsetElement 	: '',
			topId			: 'top'
		},

		init : function(options)
		{
			this.options = $.extend({}, this.defaults, options);
			this.$node 	= $(this.options.node);

			if( this.options.makeNew )
			{
				if( this.$node.length )
				{
					this.$waypoints = $(this.options.targets).not(this.options.ignore);

					if( this.$waypoints.length > -1 )
					{
						this.draw();
					}
				}
			}
			else
			{
				this.makeWaypoints( this.$node );
			}
		},

		draw : function()
		{
			var $current = this.$node.children('ul');

			if( $current.length > 0 )
			{
				$current.remove();
			}

			var $top = $(this.options.topId);
			if( !$top.length )
			{
				$top = $('<a name="top" id="top"></a>');
				$('body').prepend($top);
			}

			var $list = $('<ul />'),
				waypoints = new Array('<li class="current"><a href="#'+this.options.topId+'"></a></li>');

			for( var i = 0, len = this.$waypoints.length; i < len; i++ )
			{
				var	wp = this.$waypoints.eq(i),
					id = wp.attr('id');

				if( id === undefined )
				{
					id = 'waypoint-' + i;
					wp.attr('id', id);
				}

				waypoints[waypoints.length] = '<li><a class="wp" href="#' + id + '"></a></li>';
			}

			$list.append($(waypoints.join('')));

			this.makeWaypoints($list);

			this.$node.append($list);
		},

		makeWaypoints : function( $list )
		{
			$list.onePageNav({
			    currentClass: 'current',
			    changeHash: false,
			    scrollSpeed: 750,
			    scrollThreshold: 0.5,
			    filter: '',
			    easing: 'swing',
			    offsetElement : this.options.offsetElement
			});
		}
	}
})(jQuery);

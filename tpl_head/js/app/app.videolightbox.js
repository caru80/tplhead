(function($){

	$app.lightboxVideo = {

		defaults : {
			trigger : '[data-fullvideo]' //'.lightbox-video'
		},

		init : function () 
		{
			this.triggers = document.querySelectorAll(this.defaults.trigger);

			if(this.triggers.length) 
			{
				if(!$app.extensions.list.featherlight.available 
					&& !$app.extensions.list.featherlight.autoload ) 
				{
					$($app).one('featherlightReady.app.videos', function() {
						this.bindTriggerEvents();
					}.bind(this));

					$app.extensions.load('featherlight', true);
				}
				else 
				{
					this.bindTriggerEvents();
				}
			}
		},

		bindTriggerEvents : function() 
		{
			this.triggers.forEach(function(el) 
			{
				el.addEventListener('click', function(ev) {
					ev.preventDefault();

					let item = ev.target;

					if(item.hasAttribute('data-fullvideo'))
					{
						this.playVideo(item.getAttribute('data-fullvideo'));
					}
				}.bind(this));
			}, this);
		},

		playVideo : function (video)
		{
			var tmpl =   '<div class="video-player">' +
			'   <video class="featherlight-video" autoplay controls volume="70" type="video/mpeg">' +
			'       <source src="'+ $app.protocol + '//' + $app.hostname + $app.pathname + '/' + video + '" type="video/mp4" />' +
			'   </video>' +
			'</div>';

			$.featherlight(tmpl, {openSpeed : 750});
		}
	}

})(jQuery);
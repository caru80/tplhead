/*
	Protoslider Plugin HTML5 Video 0.1
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
		pauseOnPlay 		: true,		// Protoslider beim Abspielen eines Videos anhalten, wenn autoplay an ist. Nach dem Ende des Videos läuft Protoslider weiter.
		autoplay 			: false,		// Videos automatisch abspielen (erzwungen, ansonsten mit: <video data-ptoptions='{"autoplay":true}'  ... >) – !!! <video autoplay ...> würde das Video starten, selbst wenn es noch gar nicht zu sehen ist !!!
		timeoutAfterPlayback : 100
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
			var self = this;
			this.parent.on('afterSlideIn.ptslider.html5video afterRender.ptslider.html5video', function(ev)
			{
				// afterRender wird ausgelöst wenn Protoslider instanziert wird, oder sich die Anzahl der sichtbaren Spalten ändert.
				self.tick();
			});
		},

		tick : function()
		{
			var self	= this,
				slide 	= this.parent.State.stage[0],
				video 	= this.getVideoObject(slide);

			if(video)
			{
				if(video.readyState >= 2) // Mindestens die Metadaten und ein Frame des Videos stehen zur Verfügung.
				{
					this.playVideo();
				}
				else // Noch nix da. Müssen warten.
				{
					$(video).one('canplay.ptslider.html5video', function()
					{
						self.playVideo();
					});
				}
			}
		},

		getVideoObject : function(slide)
		{
			var video = slide.find('video');
			if( video.length ) return video.get(0);
			return false;
		},

		playVideo : function()
		{
			var self  = this,
				slide = this.parent.State.stage[0],
				video = this.getVideoObject(slide);

			if( video )
			{
				var opt = $(video).data('ptoptions') || {};

				// Video Zurückspulen wenn ein Slide ausgeblendet wurde.
				if(!video.hasAttribute('loop'))
				{
					slide.one('afterSlideOut.ptslider.html5video', function(ev)
					{
						var video = self.getVideoObject($(this));
						if(video)
						{
							video.pause();
							video.currentTime = 0;
						}
					});
				}

				// Autoplay
				if(this.parent.options.html5video.autoplay || opt.autoplay)
				{
					if(this.parent.options.html5video.pauseOnPlay && this.parent.options.autoplay)
					{
						$(video).one('ended.ptslider.html5video', function()
						{
							self.parent.options.autoplay = true;
							self.parent.play(self.parent.options.html5video.timeoutAfterPlayback);
						});
						this.parent.options.autoplay = false; // Der afterRender-Event passiert vor dem Protoslider-Autostart, dann hat this.parent.pause() keine funktion. Deshalb wird autoplay hier komplett abgeschaltet.
						this.parent.pause();
					}
					video.play();
				}
			}
		}

	};

})(jQuery);

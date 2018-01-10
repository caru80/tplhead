'use strict';

(function($){
	/*
		CRu. 2017

		$app.messages.init( options );

		@param options – Objekt:
		{
			container		: '#system-message-container',		jQuery Selektor für Joomla! Systemnachrichten-Container
			selector 		: '.system-message',				jQuery Selektor für Nachrichten
			defaultType		: 'message',						Standard Nachrichtentyp

			btnLabel		: 'OK, danke.',						Standardbeschriftung vom Button

			css				: {
								visible : 'visible', 			CSS Klasse für sichtbare Nachrichten
								button : 'msg-close'			CSS Klasse für den Schließen-Knopf
							},

			autoHide		: true,								Nachrichten automatisch ausblenden...
			hideAfter		: 15000								... nach x Millisekunden
		}




		$app.messages.show( message );

		message – Objekt:
		{
			text 				: String Text,
			type [optional] 	: String Art – 'message'|'error'|'notice'|'warning'|'html'
			btn [optional] 		: String Button-Beschriftung oder Boolesch false, um keinen Button anzuzgeigen
			hide [optional]		: Integer – Nach dieser Zeit in Millisekunden ausblenden, oder Boolesch false wenn autoHide eingeschaltet ist, und die Nachricht nicht automatisch ausgeblendet werden soll
			container [optional] : String jQuery Selektor – Nachricht hier anzeigen (Experimentell)
			subject [optional] : String – Nachrichtenbetreff
			replace [optional] : Boolesch – Alle anderen Nachrichten mit dem gleichen "subject" werden ausgeblendet
		}


		z.B.: Standart Typen : 'message'|'error'|'notice'|'warning'

		$( '#mein-button' ).on('click', function(){

			$app.messages.show({
				type : 'warning',
				text : 'Dies soll eine Warnung sein!',
				btn  : 'Mir doch egal!'
				hide : false
			});

		});

		z.B. Typ 'html' (siehe auch: /html/cookienotice.php):

			Dein Custom-Code muss in einem <div class="system-message"> eingepackt sein. Es sei denn, du hast den Standardwert von $app.messages.options.selector, welcher '.system-message' ist, geändert.
			Dann muss du – logischer Weise – deinen neuen Wert als Klasse o.ä. benutzen.

			$app.messages.show({
				type : 'html',
				text : '<div class="system-message[ meine-Klasse]"[ data-subject="betreff"]>' +
						'<span>Dies ist eine Nachricht</span>' +
						'<span><a tabindex="0" class="msg-close btn">.msg-close Schließt die Nachricht...</a> <a class="btn" href="#">2. Knopf</a></<span>' +
						'</div>',
				hide : 5000, // Nach 5 Sek. autom. ausblenden
				replace : true // Alle anderen mit gleichem "subject" ausblenden
			});

	*/

	$app.messages = {

		defaults : {
			stage			: '#system-message-container',
			selector 		: '.system-message',
			defaultType		: 'message',

			btnLabel		: 'OK, danke.',

			css 			: {visible : 'visible', button : 'msg-close'},

			autoHide		: true,
			hideAfter		: 5000
		},

		init : function( options )
		{
			if( options )
			{
				this.options = $.extend(true, {}, this.defaults, options);
			}
			else
			{
				this.options = this.defaults;
			}

			this.container = $(this.options.stage);

			let $messages = $(this.options.selector);

			if( $messages.length )
			{
				for(let i = 0, len = $messages.length; i < len; i++) {
					let $msg = $messages.eq(i);
					this.setTrigger( $msg );
					this.showMessage( $msg );
				}
			}
		},

		/*
			Entfernt eine Nachricht von this.options.stage
		*/
		removeMessage : function(msg)
		{
			if( arguments.length > 1 ) // >>> Mal bei gucken, warum das so sein kann.
			{
				msg = arguments[1];
			}

			let self 	= this,
				removed = false; // Evtl. Chromes doppeltes Event-Abfeuern fixen

			$(msg).one('transitionend webkitTransitionEnd oTransitionEnd', function() {
				if(removed) return;
				$(this).remove();
				removed = true;
			}).removeClass(this.options.css.visible);
		},

		/*
			Entfernt alle Nachrichten mit "subject" aus this.options.stage
		*/
		removeSubject : function(subject, stage)
		{
			let self = this;

			stage = stage === undefined ? this.options.stage : stage;

			$('[data-subject="'+subject+'"]', stage).each(function()
			{
				self.removeMessage(this);
			});
		},

		/*
			Macht eine Nachricht sichtbar, und setzt einen Timeout, wenn autoHide an ist.
		*/
		showMessage : function(msg, duration)
		{
			let self = this;

			msg.addClass(self.options.css.visible);

			let time = false;

			if( typeof duration !== undefined )
			{
				time = duration;
			}
			else if( this.options.autoHide )
			{
				time = this.options.hideAfter;
			}

			if( time )
			{
				this.setMsgTimeout( msg, time );
			}
		},

		setMsgTimeout : function( msg, time )
		{
			let self = this;

			msg.data('app', {timeout : window.setTimeout(this.removeMessage.bind(this, msg), time)} );
		},

		setTrigger : function( msg )
		{
			let self 	= this,
				trigger = msg.find('.' + this.options.css.button);

			if(trigger.length)
			{
				trigger.data('app-message', msg).on('click.app', function(ev)
				{
					ev.preventDefault();
					let msg = trigger.data('app-message');
					self.removeMessage( msg );
					return false;
				});
			}
		},

		/*
			m{
				text 					: String Text
				type [optional] 		: String Art – 'message'|'error'|'notice'|'warning'|'html'
				btn [optional] 			: String Button-Beschriftung. Oder Boolesch false, um keinen Button anzuzgeigen. Wird btn nicht angegeben, wird options.btnLabel als Beschriftung angezeigt.
				hide [optional]			: Integer – Nach diese Zeit in Millisekunden ausblenden, oder Boolesch false, wenn autoHide eingeschaltet ist, und die Nachricht nicht automatisch ausgeblendet werden soll
				container [optional] 	: String jQuery-Selektor oder -Objekt – Nachricht in dieses Element einhängen
				class [optional]		: String – ZUsätzliche CSS-Klasse für div.system-message
				subject					: String – "Betreff"
				replace					: Bool – Ersetze alle Anderen mit dem gleichen Betreff
			}
		*/
		show : function( m )
		{
			if(m.text == '') return;

			let type 	= m.type || this.options.defaultType,
				$c		= m.container || this.options.stage;

			$c = $($c);

			if($c.length) {
				let $msg;

				if(type === 'html') {
					$msg = $(m.text);
				}
				else {
					let btn = m.btn ? m.btn : ( m.btn !== false ? this.options.btnLabel : false );

					btn = btn ? '<span><a tabindex="0" class="' + this.options.css.button + ' btn btn-success">' + btn + '</a></span>' : '';
					$msg = $('<div' + (m.subject ? ' data-subject="'+ m.subject +'"' : '') + ' class="system-message ' + type + ( m.class ? ' ' + m.class : '') + '"><i class="fa"></i><span>' + m.text + '</span>' + btn + '</div>');
				}

				if(m.subject && m.replace) {
					this.removeSubject(m.subject, $c);
				}

				$c.append($msg);
				$msg.get(0).offsetWidth; // Browser zwingen das Ding zu rendern, ganz wichtig!

				this.setTrigger($msg);
				this.showMessage($msg, m.hide);
			}
		}
	}
})(jQuery);

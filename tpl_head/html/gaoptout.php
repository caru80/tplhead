<?php
	defined('_JEXEC') or die;
	/*
		Google Analytics Opt-Out

		Bsp.: <a tabindex="0" data-gaoptout>Klick mich für Opt-Out</a>

		Siehe auch: app.js  ->  $app.extensions.list  ->  ganalytics
	*/
?>
<script type="text/javascript">
	(function($) {
		$('[data-gaoptout]').on('click.app.gaoptout', function() {
			let gac = Cookies.get($app.extensions.list.ganalytics.options.cookieName);
			if(!gac) {
				Cookies.set($app.extensions.list.ganalytics.options.cookieName, '1', $app.extensions.list.ganalytics.options.cookieParams);
				$app.messages.show({
					type : 'html',
					text : '<div id="gaoptout-msg" class="system-message anayltics message">' +
								'<span><i></i> <?php echo JText::_('TPL_HEAD_GAOPTOUT_SUCCESS');?></span>' +
								'<span>' +
									'<a tabindex="0" class="btn ' + $app.messages.options.css.button + '"><?php echo JText::_('TPL_HEAD_SYSMESSAGE_FRONT_BTN_OK');?></a>' +
								'</span>' +
							'</div>',
					hide : false
				});

				$('#gaoptout-msg').find('.'+$app.messages.options.css.button).on('click.app.gaoptout', function() {
					window.location.reload();
				});
			}
			else {
				$app.messages.show({text : '<?php echo JText::_('TPL_HEAD_GAOPTOUT_ALLREADY_DONE');?>', subject : 'gaoptout', replace : true, hide : 5000 });
			}
		});
	})(jQuery);
</script>

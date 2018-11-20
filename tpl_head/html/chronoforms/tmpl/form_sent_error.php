<?php
	defined('_JEXEC') or die();
	/**
		Dieses Script gibt die Fehler aus, die bei der Validierung auf dem Server auftretten.
	*/

	if(count($form->errors)):
?>
		<dl class="fancy-form-errors">
			<?php
				foreach($form->errors as $error):
			?>
					<dd class="error">
						<?php echo $form->translate($error); ?>
					</dd>
			<?php
				endforeach;
			?>
		</dl>
		<script>
			<?php // An (ungefähr) die Oberkante der Fehlermeldung(en) scrollen ?>
			'use strict';
			(function($) {
				if($app.scroll) {
					$app.scroll.scrollTo({el : '.fancy-form-errors', offset : 100});
				}
			})(jQuery);
		</script>
<?php
		$form->errors = array();
	endif;
?>

<?php
	defined('_JEXEC') or die();
?>
<div class="fancy-form-submitted">
	<div class="success-msg">
		<i class="success-icon fas fa-check-circle"></i>
		<div class="success-text">
			[FORM_SENT]
		</div>
	</div>
</div>
<script>
	<?php // An (ungefähr) die Oberkante der Erfolgsmeldung scrollen ?>
	'use strict';
	(function($) {
		if($app.scroll) {
			$app.scroll.scrollTo({el : '.fancy-form-submitted', offset : 100});
		}
		let success = document.querySelector('.fancy-form-submitted');
		success.classList.add('in');
	})(jQuery);
</script>
<?php
	defined('_JEXEC') or die();
	/**
		Dieses Template zeigt den Datenschutzhinweis, sowie den Senden-Button.
	*/
	use \Joomla\CMS\Language;
?>
<div class="checkbox privacy-statement">
	<label for="kontakt_privacy">
		<input 
			name="kontakt_privacy" id="kontakt_privacy" type="checkbox"
			value="<?php echo Language\Text::_("JYES");?>" 
			<?php echo $form->data['kontakt_privacy'] == JText::_("JYES") ? 'checked' : '';?>
		>
		<i></i>
		<span class="privacy-statement-text">
			[PRIVACY_NOTICE]
		</span>
	</label>
	<script>
		/* Zustand überwachen und Senden-Button ein- und ausschalten */
		'use strict';
		(function($) {
			let isPrivacyRead = function(checkmark)
			{
				let $btn = $('#submit-button');
				if($(checkmark).is(':checked'))
				{
					$btn.removeClass('btn-secondary').addClass('btn-primary').prop('disabled', false);
				}
				else{
					$btn.removeClass('btn-primary').addClass('btn-secondary').prop('disabled', true);
				}
			}
			// onchange:
			$('#kontakt_privacy').on('change.fancyform', function() 
			{
				isPrivacyRead(this);
			});
			// onload, wenn z.B. die Servervalidierung das Formular zurückgeschickt hat:
			$(function() 
			{
				isPrivacyRead($('#kontakt_privacy'));
			});
		})(jQuery);
	</script>
</div>
<div class="form-controls">
	<button disabled id="submit-button" type="submit" class="btn btn-secondary btn-aws btn-lg">
		<i>
			<i class="fa fa-envelope-open"></i>	
			<i class="fa fa-envelope"></i>
		</i>[BUTTON_SUBMIT_FORM]
	</button>
</div>
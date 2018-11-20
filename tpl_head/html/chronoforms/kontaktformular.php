<?php
	defined('_JEXEC') or die();
	/**
		Importiere diese Datei im Chronoforms Designer in ein Custom Feld:
		<?php
			$template = 'kontaktformular.php';

			include JPATH_THEMES . DIRECTORY_SEPARATOR . \Joomla\CMS\Factory::getApplication()->getTemplate() . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'chronoforms' . DIRECTORY_SEPARATOR . $template;
		?>
	*/

	// Die Id dieses Formulars. ACHTUNG: $form_id wird von Chronoforms als Id des <form> eingefügt!
	$formid = $form->form["Form"]["id"];
?>
<div id="fancy-write-me">
	<a href="#fancy-write-me" data-scroll class="write-me btn btn-primary btn-lg btn-aws" data-toggle="button" aria-pressed="false" autocomplete="off">
		<i>
			<i class="fas fa-envelope"></i>	
			<i class="fas fa-envelope-open"></i>
		</i> [CHRONOFORMS_BTN_WRITE_ME]
	</a>
</div>
<script>
	'use strict';
	(function($) {

		$(function(){
			$('.write-me').on('click', function() {
				$('.fancy-form').toggleClass('show');
			});
		});

	})(jQuery);
</script>

<div id="fancyform-<?php echo $formid;?>" class="fancy-form">
	
	<?php
		// Fehlermeldungen vom Server ausgeben
		include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'tmpl' . DIRECTORY_SEPARATOR . 'form_sent_error.php';
	?>

	<div class="form-group inline honigtopf">
		<label for="subject">Subject</label>
		<input name="subject" id="subject" value="" maxlength="" size="" class="form-control A input-lg" title="" style="" data-inputmask="" data-load-state="" data-tooltip="" type="text" />
	</div>

	<div class="row">
		<div class="col-sm-12 col-md-6">

			<div class="radio">
				<label for="kontakt_anrede0">
					<input type="radio" name="kontakt_anrede" id="kontakt_anrede0" 
						value="[FIELD_KONTAKT_ANREDE_OPT_HERR]"
					/>
					<i></i>
					<span>[FIELD_KONTAKT_ANREDE_OPT_HERR]</span>
				</label>
				<label for="kontakt_anrede1">
					<input type="radio" name="kontakt_anrede" id="kontakt_anrede1" 
						value="[FIELD_KONTAKT_ANREDE_OPT_FRAU]"
					/>
					<i></i>
					<span>[FIELD_KONTAKT_ANREDE_OPT_FRAU]</span>
				</label>
			</div>

		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-12 col-md-6">
			
			<div class="form-group inline">
				<label for="kontakt_vorname">[FIELD_LABEL_KONTAKT_VORNAME]</label>
				<input name="kontakt_vorname" id="kontakt_vorname" type="text" value="" />
			</div>

		</div>
		<div class="col-sm-12 col-md-6">
			
			<div class="form-group inline">
				<label for="kontakt_nachname">[FIELD_LABEL_KONTAKT_NACHNAME]</label>
				<input name="kontakt_nachname" id="kontakt_nachname" type="text" value="" />
			</div>

		</div>
	</div>

	<div class="row">
		<div class="col-sm-12 col-md-6">
			
			<div class="form-group inline">
				<label for="kontakt_firma">[FIELD_LABEL_KONTAKT_FIRMA]</label>
				<input name="kontakt_firma" id="kontakt_firma" type="text" value="" />
			</div>

		</div>
	</div>

	<div class="row">
		<div class="col-sm-12 col-md-6">

			<div class="form-group inline">
				<label for="kontakt_telefon">[FIELD_LABEL_KONTAKT_TELEFON]</label>
				<input name="kontakt_telefon" id="kontakt_telefon" type="text" value="" />
				<div class="checkbox">
					<label for="kontakt_callback" class="small">
						<input name="kontakt_callback" id="kontakt_callback" value="<?php echo JText::_("JYES");?>" type="checkbox">
						<i></i>
						<span>[FIELD_LABEL_KONTAKT_CALLBACK]</span>
					</label>
				</div>
			</div>

		</div>
		<div class="col-sm-12 col-md-6">
			
			<div class="form-group inline">
				<label for="kontakt_email">[FIELD_LABEL_KONTAKT_EMAIL]</label>
				<input name="kontakt_email" id="kontakt_email" type="text" value="" />
			</div>

		</div>
	</div>
	
	<div class="form-group">
		<label for="kontakt_nachricht">[FIELD_LABEL_KONTAKT_NACHRICHT]</label>
		<textarea name="kontakt_nachricht" id="kontakt_nachricht" rows="3" cols="40"></textarea>
	</div>

	<p>
		<div class="checkbox">
			<label for="kontakt_privacy">
				<input name="kontakt_privacy" id="kontakt_privacy" value="<?php echo JText::_("JYES");?>" type="checkbox">
				<i></i>
				<span class="privacy-statement">
					[PRIVACY_NOTICE]
				</span>
			</label>
			<script>
				/* Zustand überwachen und Senden-Button ein- und ausschalten */
				'use strict';
				(function($) {
					$('#kontakt_privacy').on('change.fancyform', function() {
						$('#submit-button').toggleClass('btn-primary btn-secondary').prop('disabled', !$(this).is(':checked'));
					});
				})(jQuery);
			</script>
		</div>
	</p>
	<div class="form-controls">
		<button id="submit-button" type="submit" disabled class="btn btn-secondary btn-aws btn-lg">
			<i>
				<i class="fa fa-envelope-open"></i>	
				<i class="fa fa-envelope"></i>
			</i>[BUTTON_SUBMIT_FORM]
		</button>
	</div>

</div>
<?php 
	$lang = \Joomla\CMS\Factory::getLanguage()->getTag();
	$lang = substr($lang, 0, strpos($lang, '-'));
?>
<script src="<?php echo JUri::root(); ?>libraries/cegcore/assets/gplugins/gvalidation/lang/<?php echo $lang;?>.js"></script>

<?php // Felder (.form-group) bei Fokus highlighten: ?>
<script src="<?php echo JUri::root(); ?>templates/<?php echo \Joomla\CMS\Factory::getApplication()->getTemplate(); ?>/html/chronoforms/js/highlighter.js"></script>

<?php // Formular initialisieren ?>
<script src="<?php echo JUri::root();?>templates/head/html/chronoforms/js/formcontroller.js"></script>
<script>
'use strict';
(function($) {
	const formConfig = {
		id : <?php echo $formid;?>
	};
	let Controller = new fancyFormController(formConfig);
})(jQuery);
</script>
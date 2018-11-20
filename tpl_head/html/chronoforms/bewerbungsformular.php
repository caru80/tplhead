<?php
	defined('_JEXEC') or die();
	/**
		Importiere diese Datei im Chronoforms Designer in ein Custom Feld:
		<?php
			$template = 'bewerbungsformular.php';

			include JPATH_THEMES . DIRECTORY_SEPARATOR . CMS\Factory::getApplication()->getTemplate() . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'chronoforms' . DIRECTORY_SEPARATOR . $template;
		?>
	*/
	
	use \Joomla\CMS;
	use \Joomla\CMS\Language;
	use \Joomla\CMS\MVC\Model;

	// Die Id dieses Formulars. ACHTUNG: $form_id wird von Chronoforms als Id des <form> eingefügt!
	$formid = $form->form["Form"]["id"]; // formcontroller.js braucht das
?>
<div id="fancyform-<?php echo $formid;?>" class="fancy-form show">

	<?php
		// Fehlermeldungen vom Server ausgeben
		include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'tmpl' . DIRECTORY_SEPARATOR . 'form_sent_error.php';
	?>
     
	<fieldset>
		<?php
			// „Ihre Bewerbung zum ” + Beitragstitel
			$input = CMS\Factory::getApplication()->input;
			if($input->get('option','','string') === 'com_content' && $input->get('view','','string') === 'article')
			{
				Model\BaseDatabaseModel::addIncludePath(JPATH_ROOT . '/administrator/components/com_content/models', 'ContentModel');
				$model 	 = Model\BaseDatabaseModel::getInstance('Article', 'ContentModel');
				$article = $model->getItem($input->get('id','','int'));

				// !!! sprintf, mit oder ohne JText (\Joomla\CMS\Language\Text), funktioniert nicht mit Chronoforms Locales, weil die zum Schluss ersetzt werden (Yeah!).
				$formTitle = Language\Text::sprintf("CHRONO_FORM_TITLE_BEWERBUNG", $article->title);
			?>
				<legend><?php echo $formTitle;?></legend>
				<?php // Die beiden nachfolgenden Felder enthalten den E-Mail-Betreff für 1. Host und 2. Absender ?>
				<input readonly type="hidden" name="custom_subject_host" value="[CUSTOM_SUBJECT] <?php echo $article->title;?>">
				<input readonly type="hidden" name="custom_subject_sender" value="<?php echo $formTitle;?>">
			<?php
			}
		?>

		<?php // Das nachfolgende Feld ist der Honypot. Es wird per CSS versteckt, und Serverseitig wird geprüft ob es leer ist. ?>
		<div class="form-group inline honigtopf">
			<label for="subject">Subject</label>
			<input name="subject" id="subject" type="text" value="" />
		</div>

		<div class="radio">
			<label for="kontakt_anrede0">
				<input type="radio" name="kontakt_anrede" id="kontakt_anrede0" 
					<?php echo $form->data['kontakt_anrede'] == $form->translate('[FIELD_KONTAKT_ANREDE_OPT_HERR]') ? 'checked' : '';?> 
					value="[FIELD_KONTAKT_ANREDE_OPT_HERR]"
					class="validate['group:anrede']"
				/>
				<i></i>
				<span>[FIELD_KONTAKT_ANREDE_OPT_HERR]</span>
			</label>
			<label for="kontakt_anrede1">
				<input type="radio" name="kontakt_anrede" id="kontakt_anrede1" 
					<?php echo $form->data['kontakt_anrede'] == $form->translate('[FIELD_KONTAKT_ANREDE_OPT_FRAU]') ? 'checked' : '';?>
					value="[FIELD_KONTAKT_ANREDE_OPT_FRAU]"
					class="validate['group:anrede']"
				/>
				<i></i>
				<span>[FIELD_KONTAKT_ANREDE_OPT_FRAU]</span>
			</label>
		</div>

		<div class="form-group inline">
			<label for="kontakt_vorname">[FIELD_LABEL_KONTAKT_VORNAME]</label>
			<input name="kontakt_vorname" id="kontakt_vorname" type="text" value="<?php echo $form->data['kontakt_vorname']; ?>" />
		</div>
		<div class="form-group inline">
			<label for="kontakt_nachname">[FIELD_LABEL_KONTAKT_NACHNAME]</label>
			<input name="kontakt_nachname" id="kontakt_nachname" type="text" value="<?php echo $form->data['kontakt_nachname']; ?>" />
		</div>
		<div class="form-group inline">
			<label for="kontakt_telefon">[FIELD_LABEL_KONTAKT_TELEFON]</label>
			<input name="kontakt_telefon" id="kontakt_telefon" type="text" value="<?php echo $form->data['kontakt_telefon']; ?>" />
		</div>
		<div class="form-group inline">
			<label for="kontakt_email">[FIELD_LABEL_KONTAKT_EMAIL]</label>
			<input name="kontakt_email" id="kontakt_email" type="text" value="<?php echo $form->data['kontakt_email']; ?>" />
		</div>
		<div class="form-group">
			<label for="kontakt_nachricht">[FIELD_LABEL_KONTAKT_NACHRICHT]</label>
			<textarea name="kontakt_nachricht" id="kontakt_nachricht"><?php echo $form->data['kontakt_nachricht']; ?></textarea>
		</div>
	</fieldset>


	<?php // Dropzone ?>
	<fieldset>
		<legend>[SECTION_TITLE_DROPZONE]</legend>
		<p class="small">
			[DROPZONE_HINT_UPLOAD]
		</p>
		<?php // Das Feld dropzone_files speichert die Dateinamen. Nur dieses Feld wird an Chronoforms übertragen. Und es wird von der Client-/Server-Validierung auf Inhalt geprüft, falls erfordelich. ?>
		<input readonly name="dropzone_files" id="dropzone_files" type="text" 
			value="<?php echo $form->data['dropzone_files']; ?>" 
			style="visibility: hidden !important; position: absolute !important;" 
		/>
		<?php 
			// Dropzone ausgeben, oder nur eine Liste der bereits übertragenen Dateien:

			if(isset($form->data['dropzone_files']) && $form->data['dropzone_files'] !== ''):
				// Der Server hat bereits Dateien empfangen, aber irgendwas ging bei der Validierung des Formulars schief, und das Formular wurde zur Korrektur erneut ausgegeben.
				// Wir zeigen keine Dropzone mehr an, sondern nur eine Liste der Dateinamen, die auf dem Server liegen:
		?>
				<p><strong>[DROPZONE_FILES_ON_SERVER]</strong></p>
				<ol>
					<li><?php echo implode('</li><li>', explode(',', $form->data["dropzone_files"])); ?></li>
				</ol>
		<?php
			else:
				// Wir zeigem eine Dropzone an:				
		?>
				<div id="drop-uploader" class="dropzone"></div>
		<?php
			endif;
		?>
	</fieldset>
	
	<?php
		// Datenschutz und Senden
		include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'tmpl' . DIRECTORY_SEPARATOR . 'form_privacy_and_submit.php';
	?>

</div>


<?php 
	// JS Validierung in Anzeigesprache (Chronoforms5 Bug):
	$lang = CMS\Factory::getLanguage()->getTag();
	$lang = substr($lang, 0, strpos($lang, '-'));
?>
<script src="<?php echo CMS\Uri\Uri::root(); ?>libraries/cegcore/assets/gplugins/gvalidation/lang/<?php echo $lang;?>.js"></script>

<?php // Felder (bzw. <div class="form-group">) bei :focus hervorheben ?>
<script src="<?php echo CMS\Uri\Uri::root(); ?>templates/<?php echo CMS\Factory::getApplication()->getTemplate(); ?>/html/chronoforms/js/highlighter.js"></script>

<?php // Dropzone.js ?>
<script src="<?php echo CMS\Uri\Uri::root(); ?>templates/<?php echo CMS\Factory::getApplication()->getTemplate(); ?>/js/dropzone.js"></script>
<script>
	<?php // Dropzone.js würde ohne die folgende Anweisung nach allen .dropzone schauen, und sich automatisch starten, allerdings ohne unsere Konfiguration (unten) zu berücksichtigen. ?>
	Dropzone.autoDiscover = false;
</script>

<?php // Formular initialisieren ?>
<script src="<?php echo CMS\Uri\Uri::root(); ?>templates/<?php echo CMS\Factory::getApplication()->getTemplate(); ?>/html/chronoforms/js/formcontroller.js"></script>
<script>
'use strict';
(function($) {
	const formConfig = {
		id : <?php echo $formid;?>,
		// Custom Lokalisierung (optional)
		locale : {
			iamUploadingFiles : '[IAM_UPLOADING_FILES]'
		},
		// Dropzone (optional)
		dropzone : {
			url 					: '<?php echo CMS\Uri\Uri::current();?>?chronoform=<?php echo $form->form["Form"]["title"]; ?>&event=dropupload',
			autoProcessQueue 		: false,
			createImageThumbnails 	: false,
			addRemoveLinks 			: true,
			maxFilesize 			: 8,
			maxFiles 				: 4,
			acceptedFiles 			: 'application/pdf,application/x-compressed,application/x-zip-compressed,application/zip,,application/x-zip,application/x-rar-compressed',
			parallelUploads 		: 1,
			// Lokalisierung:
			'dictDefaultMessage'	: '[DROPZONE_DROP_FILES_HERE]',
			'dictFallbackMessage'	: '[DROPZONE_FALLBACK_MESSAGE]',
			'dictFallbackText'		: '[DROPZONE_FALLBACK_TEXT]',
			'dictFileTooBig'		: '[DROPZONE_FILE_TO_BIG]',
			'dictInvalidFileType'	: '[DROPZONE_INVALID_FILE_TYPE]',
			'dictResponseError'		: '[DROPZONE_RESPONSE_ERROR]',
			'dictCancelUpload'		: '[DROPZONE_CANCEL_UPLOAD]',
			'dictCancelUploadConfirmation'	: null,
			'dictRemoveFile'		: '[DROPZONE_REMOVE_FILE]',
			'dictMaxFilesExceeded'	: '[DROPZONE_MAX_FILES_EXCEEDED]'
		}
	};
	let formController = new fancyFormController(formConfig);
})(jQuery);
</script>
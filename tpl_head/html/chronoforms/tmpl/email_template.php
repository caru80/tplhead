<?php
	// defined(_JEXEC) or die; // _JEXEC ist hier niemals definiert.
	/**
		E-Mail-Betreff in der Sprache des Absenders
		--------------------
		In Chronoforms, in deiner „Email” Aktion, trägst du im Reiter „Advanced” in das Feld „Dynamic Subject” folgendes ein: email_subject

		In deinen Locales hast du einen Eintrag EMAIL_SUBJECT_SENDER, der den Betreff definiert.
	*/

	if(!isset($mailOptions)) $mailOptions = array();
	
	if(!class_exists('emailTemplateHelper'))
	{
		include dirname(__FILE__) . '/helper.php';
	}
	$helper = new emailTemplateHelper($form, $mailOptions);

	$form->data["email_subject"] = $helper->options->for_sender ? $helper->translate($helper->options->email_subject_sender) : $helper->translate($helper->options->email_subject_host);
?>
<style media="screen">
	body{
		background-color: #95b8a2;
	}
</style>
<table style="
	border-collapse: collapse; 
	border-spacing: 0; 
	border: 1px solid #8cac97; 
	width: 750px; 
	margin: 50px auto 50px auto; 
	background-color: #ffffff; 
	<?php echo $helper->styles->font;?>"
>
	<tr>
		<td style="padding: 50px;">

		<?php
			// Logo ausgeben, wenn vorhanden.
			if($helper->options->logo_path != '') 
			{
				echo '<p style="text-align: right;"><img style="max-width: 200px;" src="' . JUri::root() . $helper->options->logo_path .'" /></p>';
			}

			// Grußformel ausgeben.
			if($helper->options->show_salutation) 
			{
				echo $helper->getSalute();
			}

			if($helper->options->for_sender) 
			{	
				echo '<p style="' . $helper->styles->font . ' ' . $helper->styles->p . '">' . $helper->translate($helper->options->email_introtext) . '</p>';
			}

			// Sektionen aus $helper->options->sections ausgeben:
			foreach(explode(',', $helper->options->sections) as $i => $section_key) {
				$section_key = str_replace(' ','', $section_key);
				echo $helper->makeSection($section_key, array(), array());
			}

			if($helper->options->for_sender) 
			{	
				echo '<p style="' . $helper->styles->font . ' ' . $helper->styles->p . '">' . $helper->translate($helper->options->kind_regards) . '</p>';
				echo '<p style="' . $helper->styles->font . ' ' . $helper->styles->p . '">' . $helper->translate($helper->options->email_host_name) . '</p>';
				echo '<p style="' . $helper->styles->font . ' ' . $helper->styles->p_small . '">' . $helper->translate($helper->options->email_host_address) . '</p>';
			}
		?>

		</td>
	</td>
</table>

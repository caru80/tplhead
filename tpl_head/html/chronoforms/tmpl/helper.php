<?php
	// defined(_JEXEC) or die;
	/**
		Chronoforms E-Mail-Template Helper
	*/
	// global $helper, $mOpt; // Siehe: https://stackoverflow.com/questions/764274/php-global-variable-modifier-not-working
	/*
		E-Mail-Template Helper
	*/
	class emailTemplateHelper {

		private $form;

		public $options = array();
		public $langauge;

		public $mailConfigDefault = array(
			"for_sender" 		=> false,	// Soll dies die Bestätigungsmail für den Absender sein?
			
			"language" 			=> 'de-DE',
			"sections" 			=> 'kontakt_',
	
			"logo_path" 		=> 'images/layout/logo-for-emails.png',
	
			"email_subject_host" 	=> '[EMAIL_SUBJECT_HOST]',		// Betreff für Seitenbetreiber
			"email_subject_sender" 	=> '[EMAIL_SUBJECT_SENDER]',	// Betreff für Absender
			
			"email_introtext" 		=> '[EMAIL_INTROTEXT_SENDER]', 	// E-Mail Introtext
			"email_host_name" 		=> '[EMAIL_HOST_NAME]', 		// Absender Name
			"email_host_address" 	=> '[EMAIL_HOST_ADDRESS]', 	// Absender Adresse, Steuernr. etc.
	
			// Anrede in der E-Mail
			"show_salutation" 	=> false,	// Anrede anzeigen?
	
			"field_salutation"  => 'kontakt_anrede', 	// Feld mit Wert der Anrede (Herr/Frau)
			"field_name" 		=> 'kontakt_nachname',	// Feld mit Wert des Namen
	
			"salute_male"		=> '[FIELD_KONTAKT_ANREDE_OPT_HERR]',	// Locales: Wert für Anrede männlich („Herr”)
			"salutation_male" 	=> '[EMAIL_SALUTATION_MALE]',			// Locales: Grußformel männlich
			
			"salute_female"		=> '[FIELD_KONTAKT_ANREDE_OPT_FRAU]', 	// Locales: Wert für Anrede weiblich („Frau”)
			"salutation_female"	=> '[EMAIL_SALUTATION_FEMALE]',			// Locales: Grußformel weiblich
	
			"salutation_none" 	=> '[EMAIL_SALUTATION_NONE]', 			// Locales: Grußformel ohne Geschlecht und ohne Name
	
			"kind_regards" 		=> '[EMAIL_KIND_REGARDS]'				// Locales: Mit freundlichen...
		);

		public $styles = array (
			'font' 			=> "font-family: 'Helvetica Neue', 'Segoe UI', Arial, Helvetica, Sans, Sans-Serif;",
			'p' 			=> 'font-size: 16px; line-height: 24px; color: #222222; margin: 0 0 16px 0;',
			'p_small' 		=> 'font-size: 13px; line-height: 21px; color: #666666;',
			'data_table' 	=> 'width: 100%; margin-bottom: 30px; border: 1px solid #dae4de; border-spacing: 0; border-collapse: collapse;', // Daten-Tabelle
			'odd_row'		=> 'border: 1px solid #dae4de; background-color: #e7f1eb;',							// Alternierende Zeilen
			'td_key' 		=> 'font-size: 14px; font-weight: bold; padding: 12px 8px; width: 180px;',	// Formularfeld-Beschriftung
			'td_val'		=> 'font-size: 14px; padding: 12px 8px;',									// Formularfeld-Wert
			'section_title' => 'color: #666666; font-weight: normal; font-size: 24px; margin-bottom: 10px;'
		);

		public $text_keys = array (
			'field_labels' 		=> 'FIELD_LABEL_',
			'section_titles' 	=> 'SECTION_TITLE_'
		);

		function __construct($form, $options = array())
		{
			$this->form 		= $form;
			$this->options 		= (object) array_merge($this->mailConfigDefault, $options); 	// „mailOptions” $options;
			$this->styles 		= (object) $this->styles;
			$this->text_keys 	= (object) $this->text_keys;
		}

		public function translate($string)
		{
			\GCore\Libs\Base::setConfig('site_language', $this->options->language);
			$string = $this->form->translate($string);
			return $string;
		}

		/**
			Baut die Anrede zusammen.
		*/
		public function getSalute()
		{
			$name 	= $this->form->data[$this->options->field_name];
			$salute = $this->form->data[$this->options->field_salutation];

			if(!empty($name) && !empty($salute))
			{
				$salutation = $salute === $this->translate($this->options->salute_male) ? 
								sprintf($this->translate($this->options->salutation_male), $name) :
								sprintf($this->translate($this->options->salutation_female), $name);
			}
			else
			{
				// Geschlechtslose Anrede ohne Name (z.B.: „Hallo,”)
				$salutation = $this->translate($this->options->salutation_none);
			}

			return '<p style="' . $this->styles->font . ' ' . $this->styles->p . '">' . $salutation . '</p>';
		}


		/**
			Diese Hilfsfunktion baut HTML:

			$section_key 	– Ein Schlüssel aus $section_keys
			$exclude 		– Array mit Feldnamen (aus einer section), die nicht in die Mail eingebaut werden sollen: z.B. array('kontakt_name', ...)
			$replace 		– Assoziatives Array mit "Suchen" => "Ersetzen" Paaren, tauscht Werte von Feldern: array("Herr" => "Frau", "Bla" => "Blub") = Herr wird durch Frau ersetzt, Bla durch Blub.
		*/
		public function makeSection($section_key = '', $exclude = array(), $replace = array())
		{
			$html = '';

			$data = array();
			foreach( $this->form->data as $key => $val )
			{
				if(strpos($key, $section_key) !== false) $data[$key] = $val;
			}

			if(count($data))
			{
				$c = 0;
				foreach( $data as $field_name => $field_value )
				{
					// Feld Auslassen, weil in Array $exclude enthalten.
					if(in_array($field_name, $exclude)) continue;

					// Suchen und ersetzen im Feld-Wert.
					if(count($replace)) $field_value = str_replace(array_keys($replace), array_values($replace), $field_value);

					// Feldbeschriftung für Ausgabe.
					$field_label = $this->translate('[FIELD_LABEL_' . strtoupper($field_name) . ']');

					// Ausgabe
					$oddrow = $c % 2 !== 0 ? '' : $this->styles->odd_row;
					$field_value = $field_value === '' ? '-' : $field_value;

					$html .= <<<TMPL
						<tr style="$oddrow">
							<td style="{$this->styles->td_key} {$this->styles->font}">$field_label</td>
							<td style="{$this->styles->td_val} {$this->styles->font}">$field_value</td>
						</tr>
TMPL;
					++$c;
				}

				// Titel von Bereich für Ausgabe
				$legend = $this->translate('[' . $this->text_keys->section_titles . strtoupper(str_replace('_', '', $section_key)) . ']'); 

				$html = <<<TMPL
					<h4 style="{$this->styles->font} {$this->styles->section_title}">$legend</h4>
					<table style="{$this->styles->data_table}">
						$html
					</table>
TMPL;
			}
			return $html;
		}
	}
?>

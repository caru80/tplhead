<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
	X-Fields 0.2.0
	CRu.: 2018-02-02

	To do:
	- Neben dem Kontext auch Anhand des Formularsnamens (siehe API JForm und Ausgabe von $this->form) die zu ladenden Dateien ermitteln.

*/


class plgContentXfields extends JPlugin {

/**
* Load the language file on instantiation.
* Note this is only available in Joomla 3.1 and higher.
* If you want to support 3.0 series you must override the constructor
*
* @var boolean
* @since 3.1
*/
	protected 	$autoloadLanguage = true;

	private 	$xmlPath;	// string interner Pfad zu den XML Dateien mit den Felddefinitionen.
	private 	$form; 		// object Das Formular, dessen Ausgabe bevorsteht.
	private 	$option; 	// string Die aktuell aufgerufene Komponente (der Kontext, z.B. com_content)

	/**
		Parameters (https://docs.joomla.org/Plugin/Events/Content#onContentPrepareForm)

	    form The JForm object to be displayed. Use the $form->getName() method to check whether this is the form you want to work with.
	    data An object containing the data for the form.
	*/
	public function onContentPrepareForm($form, $data)
	{
		$app = JFactory::getApplication();

		if( $app->isAdmin() )
		{
			$this->form 	= $form;
			$this->xmlPath 	= __DIR__ . '/forms';
			$this->option 	= $app->input->get('option');

			$this->inject( $this->getFormDefinitions() );
		}

		return true;
	}

	/**
		Fügt die Felddefinitionen ein.
	*/
	private function inject($forms = array())
	{
		JForm::addFormPath($this->xmlPath);
		/**
			loadFile Arguments:
			$file 			- string The filesystem path of an XML file.
			$reset (true) 	- string Flag to toggle whether form fields should be replaced if a field already exists with the same group/name.
			$xpath (false) 	- string An optional xpath to search for the fields.
		*/
		foreach($forms as $file)
		{
			$this->form->loadFile($this->xmlPath . DIRECTORY_SEPARATOR . $file, false);
		}
	}


	/**
		Gibt ein Array mit den je nach Kontext zu ladenden Felddefinitionen zurück, oder ein leeres Array.
	*/
	private function getFormDefinitions()
	{
		jimport('joomla.filesystem.folder');

		$forms 		= JFolder::files($this->xmlPath, '.xml');
		$contextual	= array();

		if(count($forms))
		{
			foreach($forms as $i => $filename)
			{
				$contextPrefix = substr($filename, 0, strpos($filename,'.'));
				if($contextPrefix === $this->option)
				{
					$contextual[] = $filename;
				}
			}
		}
		return $contextual;
	}
}
?>

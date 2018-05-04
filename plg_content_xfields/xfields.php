<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
*	X-Fields 0.3.0
*	CRu.: 2018-04-30
*
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

	private 	$xmlPath;	
	private 	$form; 		// object Das Formular, dessen Ausgabe bevorsteht.
	private 	$option; 	// string Die aktuell aufgerufene Komponente (der Kontext, z.B. com_content)
	private		$view; 		// string Der aktuelle View (z.B. article)

	/**
		Suche in folgenden Verzeichnissen nach weiteren Felddefinitionen:
	*/
	private static $additonalPath = array(
										JPATH_SITE . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'mod_articles_head' . DIRECTORY_SEPARATOR . 'xfields'
									);

	/**
		Parameters (https://docs.joomla.org/Plugin/Events/Content#onContentPrepareForm)

	    form The JForm object to be displayed. Use the $form->getName() method to check whether this is the form you want to work with.
	    data An object containing the data for the form.
	*/
	public function onContentPrepareForm($form, $data)
	{
		$app = JFactory::getApplication();

		if(!$app->isAdmin()) return true;
		
		$this->form 	= $form;
		$this->option 	= $app->input->get('option','STRING','');
		$this->view 	= $app->input->get('view','STRING','');

		$this->xmlPath = array(__DIR__ . '/forms');
		$this->getAdditionalPath();
		$this->loadFormDefinitions();
		
		return true;
	}

	/**
		Prüfe, ob die Ordner aus $additonalPath existieren, und füge Sie in $this->xmlPath ein.
	*/
	private function getAdditionalPath()
	{
		foreach(self::$additonalPath as $path) {
			if(JFolder::exists($path)) $this->xmlPath[] = $path;
		}
	}

	/**
		Fügt die Felddefinitionen ein, und lädt ggf. Sprachdateien.
	*/
	private function loadFormDefinitions()
	{
		JForm::addFormPath($this->xmlPath);
		$forms = array();

		foreach($this->xmlPath as $path)
		{
			$files = JFolder::files($path, '.xml');

			if(count($files))
			{
				foreach($files as $i => $filename)
				{
					$fileContext = explode('.', $filename);

					if($fileContext[0] === $this->option) {
						// Lade die Felddefinitionen
						$this->form->loadFile($path . DIRECTORY_SEPARATOR . $filename, false);
						// Lade die Sprachdatei, wenn vorhanden
						JFactory::getLanguage()->load(implode('.', array_slice($fileContext, 0, count($fileContext) -1)), $path, JFactory::getLanguage()->getTag());
					}
				}
			}
		}
		return;
	}
}
?>

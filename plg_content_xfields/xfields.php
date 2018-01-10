<?php
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class plgContentXfields extends JPlugin {

/**
* Load the language file on instantiation.
* Note this is only available in Joomla 3.1 and higher.
* If you want to support 3.0 series you must override the constructor
*
* @var boolean
* @since 3.1
*/
	protected $autoloadLanguage = true;
	
	
	function onContentPrepareForm($form, $data) {
		$app = JFactory::getApplication();
		if( $app->isAdmin() ){
			$option = $app->input->get('option');
			
			switch($option) {
				case 'com_content' :
					JForm::addFormPath(__DIR__ . '/forms');
					$form->loadFile('content', false); 
				break;
				case 'com_menus' : 
					JForm::addFormPath(__DIR__ . '/forms');
					$form->loadFile('item', false);
				break;
			}
		}
		return true;
	}

}
?>
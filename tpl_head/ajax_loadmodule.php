<?php
	defined('_JEXEC') or die();
	/*
		Dieses Template wird von $app.ajax benutzt und rendert ein Modul.

		CRu.: 2017-05-18


		JavaScript:

			$app.ajax.loadModule({

				// Entweder Modul-Id oder Position angeben, position hat eine höhere Relevanz

				id : Integer – Modul Id,
				position : String – Modulposition

				// Optionale:

				,chrome : String – moduleChrome
				,target : String – Hier wird das Modul eingehängt. jQuery Selektor vom Element in welches das Modul engehängt wird.
				,purge : Boolean – Inhalt von target vor dem Einhängen des Moduls leeren.
				,callback : Function – Wenn Request ERFOLGREICH beendet, diese Funktion ausführen
			});

			Anstatt "callback" geht auch:

			$($app.ajax).on('afterLoadModule', ...);

			Und vorher:

			$($app.ajax).on('beforeLoadModule', ...);


		HTML:

			<a tabindex="0" data-ajax='{"module":true,"id":123,"target":"#hier-hinein-laden","purge":true,"chrome":"html5"}'>Lade Modul 123</a>

			oder

			<a tabindex="0" data-ajax='{"module":true,"position":"position-von-meinem-modul","target":"#hier-hinein-laden","purge":true,"chrome":"html5"}'>Lade Modul 123</a>

	*/

	$app 	= JFactory::getApplication();
	$input 	= $app->input;


	$position 	= $input->get('position', false, 'STRING');
	$chrome 	= $input->get('chrome', false, 'STRING');
	$modid		= $input->get('i', false, 'INT');

	$start		= $input->get('s', 0, 'INT');
	$limit		= $input->get('l', 0, 'INT');

	if( $position ):
?>
		<jdoc:include type="modules" name="<?php echo $position;?>"<?php echo $chrome ? ' style="'.$chrome.'"' : '';?> />
<?php
	elseif( $modid ):

		$dbo = JFactory::getDbo();
		$q	 = $dbo->getQuery(true);

		$q->select('*')
			->from( $dbo->quoteName('#__modules') )
			->where( $dbo->quoteName('id') . ' = ' . $dbo->quote($modid) )
			->limit(1);

		$dbo->setQuery( $q );

		if( $dbo->query() )
		{
			$result = $dbo->loadObject();

			if( $result )
			{
				$mod = JModuleHelper::getModule( $result->module, $result->title );

				$params = new JRegistry( $mod->params );
				$params->set('ajax', 1); // Das Modul erhält hier den Parameter ajax. Dieser kann z.B. im Modultemplate verarbeitet werden.

				if( $start )
				{
					$params->set('start', $start); // Modul ModArticlesHead (aufgebohrtes Newsflash) kann damit arbeiten.
				}

				if( $limit )
				{
					$params->set('limit', $limit); // Dem Modul einen Parameter "limit" übergeben.
				}

				// CRu.: 2017-05-18
				// Wenn in den Modulparametern ein Chrome/Style eingestellt ist, wird dieses bevorzugt benutzt, und der Style, der an renderModule übergeben wird, wird ignoriert (siehe Quellcode von JModuleHelper)
				// Deshalb wird der Parameter hier überschrieben, wenn $chrome gesetzt ist.
				if( isset($chrome) )
				{
					$params->set('style', $chrome);
				}
				$mod->params = $params->toString();
				$modattribs	= array();
				/*
					Siehe oben...
				if( isset($chrome) )
				{
					$modattribs["style"] = $chrome;
				}
				*/
				echo JModuleHelper::renderModule($mod, $modattribs);
			}
		}
	endif;
?>

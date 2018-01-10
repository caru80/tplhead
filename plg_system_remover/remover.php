<?php
defined( '_JEXEC' ) or die();

jimport('joomla.plugin.plugin');

class plgSystemRemover extends JPlugin
{

	public function __construct( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}


	public function onBeforeCompileHead()
	{
		$app = JFactory::getApplication();
		if( $app->isAdmin() ) return;

		$doc = JFactory::getDocument();

		$queries = explode("\n", $this->params->get('queries'));

		if( !count($queries) ) return;

		$header = $doc->getHeadData();
		$areas	= explode("\n", str_replace(' ', '', $this->params->get('removein','scripts')) );

		foreach( $areas as $index => $area )
		{
			$area = trim(rtrim($area));

			if( !isset($header[$area]) ) continue;

			switch( $area )
			{
				case 'scripts' :
				case 'styleSheets' :
				case 'metaTags' :
				case 'links' :

					$temp = $header[$area];

					foreach( $queries as $needle )
					{
						$needle = '%' . rtrim($needle) . '%';

						foreach( $header[$area] as $haystack => $stuff )
						{
							if( gettype($haystack) === "string" && preg_match($needle, $haystack) )
							{
								unset($temp[$haystack]);
							}
						}
					}

					// $doc->setHeadData kann ändern, aber nicht löschen
					if( !count($temp) )
					{
						$temp[0] = array();
					}
					$header[$area] = $temp; // leeres Array für $doc->setHeadData

				break;
				case 'script' :
				case 'style' :
				case 'custom' :

					foreach( $queries as $needle )
					{
						$needle = '%' . rtrim($needle) . '%';

						foreach( $header[$area] as $idx => $haystack )
						{
							$temp = $header[$area][$idx];
							$temp = preg_replace($needle, '', $haystack);

							/*if( $temp == '' )
							{
								unset( $header[$area][$idx] );
							}
							else
							{*/
								$header[$area][$idx] = $temp;
							//}
						}
					}
				break;
			}
		}

		$doc->setHeadData($header);
	} // onBeforeCompileHead

	public function onAfterRender()
	{
		/*
			Überreste entfernen
		*/
		$app = JFactory::getApplication();

		if( $app->isAdmin() ) return;

		$buffer = JResponse::getBody();

		$needles = array(
					"\s*<link href=\"0\" rel=\"stylesheet\" \/>\s*",
					"\s*<link href=\"" . JUri::root() . "\" media=\"screen\" rel=\"stylesheet\" type=\"text/css\" \/>\s*",
					"\s*<script src=\"0\"><\/script>\s*",
					"\s*<script type==\"text\/javascript\"><\/script>\s*",
					"\s*<script src=\"" . JUri::root() . "\" type=\"text/javascript\"></script>\s*"
					);

		foreach( $needles as $needle )
		{
			$needle = '%' . $needle . '%';
			$buffer = preg_replace($needle,"\n	", $buffer);
		}

		JResponse::setBody( $buffer );
	}

}

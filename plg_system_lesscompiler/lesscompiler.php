<?php
/**

 **/

// no direct access
defined('_JEXEC') or die();

if (!class_exists('Less_Parser'))
{
	require_once( dirname(__FILE__).'/less.php/Less.php' );
}

/**
 * Plugin checks and compiles updated .less files on page load. No need to manually compile your .less files again.
 * Less compiler lessphp; see http://leafo.net/lessphp/
 */
class plgSystemLessCompiler extends JPlugin
{
	/**
	 * Compile .less files on change
	 */
	function onBeforeRender()
	{
		$app = JFactory::getApplication();

		//path to less file
		$lessFile = '';

		// 0 = frontend only
		// 1 = backend only
		// 2 = front + backend
		$mode = $this->params->get('mode', 0);

		//only execute frontend
		if ($app->isSite() && ($mode == 0 || $mode == 2))
		{
			$templatePath = JPATH_BASE . DIRECTORY_SEPARATOR . 'templates/' . $app->getTemplate() . DIRECTORY_SEPARATOR;

			//entrypoint for main .less file, default is less/template.less
			$lessFile = $templatePath . $this->params->get('site-less', 'less/template.less');
			
			//destination .css file, default css/template.css
			$cssFile = $templatePath . $this->params->get('site-css', 'css/template.css');

		}

		//execute backend
		if ($app->isAdmin() && ($mode == 1 || $mode == 2))
		{
			$templatePath = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'templates/' . $app->getTemplate() . DIRECTORY_SEPARATOR;

			//entrypoint for main .less file, default is less/template.less
			$lessFile = $templatePath . $this->params->get('admin-less', 'less/template.less');

			//destination .css file, default css/template.css
			$cssFile = $templatePath . $this->params->get('admin-css', 'css/template.css');

		}

		//check if .less file exists and is readable
		if (is_readable($lessFile))
		{
			switch( $this->params->get('compiler-type', 'CLIENT') ){
				case 'CLIENT' :
					// Kompiliere auf client mit less.js
					$this->clientsideLess();
				break;
				case 'SERVER' :
					// Kompiliere auf Server mit Less.php
					try{
						$this->autoCompileLess($lessFile, $cssFile);
					}
					catch (Exception $e){
						echo "lessphp error: " . $e->getMessage();
					}
				break;
			}
		}

		return false;
	}

	/**
	 * Checks if .less file has been updated and stores it in cache for quick comparison.
	 *
	 * This function is taken and modified from documentation of lessphp
	 *
	 * @param String $inputFile
	 * @param String $outputFile
	 */
	function autoCompileLess($inputFile, $outputFile)
	{
		// CRu.: New Less Parser
		$parserOptions 	= array( 
						'compress' 	=> (bool)$this->params->get('compiler-compress', false),
						'cache_dir' => JFactory::getConfig()->get('tmp_path')
						);
		$Parser 		= new Less_Parser( $parserOptions );
		
		$Parser->parseFile( $inputFile, '' );

		file_put_contents($outputFile, $Parser->getCss() );
	}

	/**
		CRu.: Dieses Gekröse umbauen!
	
	
	
	 * Configure and add Client-side Less library
	 * @author   piotr-cz
	 * @return   void
	 *
	 * @see      LESS: Ussage  http://lesscss.org/#usage
	 */
	function clientsideLess()
	{
		// Initialise variables
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();


		// Early exit
		if ($doc->getType() !== 'html')
		{
			return;
		}

		// Get asset paths
		$templateRel = 'templates/' . $doc->template . '/';
		$templateUri = JUri::base() . $templateRel;


		// Determine which param to use (admin/ site)
		$mode 		= $this->params->get('mode', 0);
		$lessKey 	= 'less';
		$cssKey 	= 'css';

		if ($app->isAdmin() && ($mode == 1 || $mode == 2))
		{
			$lessKey 	= 'admin-' . $lessKey;
			$cssKey 	= 'admin-' . $cssKey;
		}
		else if( $app->isSite() && ($mode == 0 || $mode == 2 )) {
			$lessKey 	= 'site-' . $lessKey;
			$cssKey 	= 'site-' . $cssKey;
		}


		// Get template css filenames
		$lessUri 	= $templateRel . $this->params->get($lessKey, 'less/template.less');
		$cssUri 	= $templateRel . $this->params->get($cssKey, 'css/template.css');

		// Add less file to document
		$doc->addHeadLink($lessUri, 'stylesheet/less', 'rel', array('type' => 'text/css'));

		

		/*
		 * Configure Less options
		 *  async			: false,
		 *  fileAsync		: false,
		 *  poll			: 1500,
		 *  relativeUrls	: false,
		 *  rootpath		: $templateUrl
		 */
		$options = array(
			'env' => 'development',
			'dumpLineNumbers' => 'mediaquery', // default: 'comments'
		);

		$doc->addScriptDeclaration('
				// Less options
				var less = ' . json_encode($options, JSON_FORCE_OBJECT | (defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : false)) . ';
		');

		
		
		
		// CRu.: Lade less.js
		switch( $this->params->get('compiler-clientsrc', 'CDN') ){
			case 'CDN' : 
				$doc->addCustomTag('<script src="' . $this->params->get('compiler-clientcdnurl') . '" type="text/javascript"></script>');
			break;
			case 'LOCAL' : 
				$doc->addCustomTag('<script src="' . JUri::root(true) . '/media/lesscompiler/js/less-1.7.3.min.js' . '" type="text/javascript"></script>');
			break;
		}









		/*
		 * Remove template.css from document head
		 *
		 * Note:  Css file must be added either using `JFactory::getDocument->addStylesheet($cssFile)` or `JHtml::_('stylesheet', $cssFile)`
		 * Note:  Cannot rely on removing stylesheet using JDocumentHTML methods.
		 * Note:  Passes ignore cache trick (template.css?1234567890123)
		 * Note:  Template.css may be added to $doc['stylesheets'] using following keys:
		 *	- relative						: `templates/...`
		 *	- semi		JUri::base(true)	: `/[path-to-root]/templates/...`
		 * 	- absolute 	JUri::base()		: `http://[host]/[path-to-root]/templates/...`
		 *	- or outside $doc->_styleSheets
		 */
		$lookups = array($cssUri, JUri::base(true) . '/' . $cssUri, JUri::base() . $cssUri);

		// Loop trough all registered document stylesheets...
		foreach ($doc->_styleSheets as $stylesSheetUri => $styleSheetInfo)
		{
			// ...and compare to every lookup...
			foreach ($lookups as $lookup)
			{
				// ...that starts like a lookup
				if (strpos($stylesSheetUri, $lookup) === 0)
				{
					unset($doc->_styleSheets[$stylesSheetUri]);
					return;
				}
			}
		}

		// Didn't find a css file in JDocument instance, register event to remove in from rendered html body.
		$app->registerEvent('onAfterRender', array($this, 'removeCss'));

		return;
	}

	/**
	 * Remove template.css from document html
	 * Stylesheet href may include query string, ie template.css?1234567890123
	 * @author   piotr-cz
	 *
	 * @return   void
	 */
	public function removeCss()
	{
		// Initialise variables
		$doc = JFactory::getDocument();
		$body = JResponse::getBody();

		// Get Uri to template stylesheet file
		$templateUri = JUri::base(true) . '/templates/' . $doc->template . '/';
		$cssUri = $templateUri . $this->params->get('cssfile', 'css/template.css');

		// Replace line with link element and path to stylesheet file
		$replaced = preg_replace( '~(\s*?<link.* href=".*?' . preg_quote($cssUri) . '(?:\?.*)?".*/>)~', '', $body, -1, $count);

		if ($count)
		{
			JResponse::setBody($replaced);
		}

		return;
	}
}

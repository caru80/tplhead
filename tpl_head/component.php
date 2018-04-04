<?php
	defined('_JEXEC') or die;

	/**
		Template Head 1.1.0
	*/

	$tpl_use_app_icons = false; // true = App-Icons, Android Manifest und "Apple Mobile Web App" anwenden. false = nur /templates/head/images/icons/favicon.png benutzen

	/**
		Optional!
		JLoader::register('TemplateHelper', JPATH_THEMES . DIRECTORY_SEPARATOR . $this->template . DIRECTORY_SEPARATOR . 'helper' . DIRECTORY_SEPARATOR . 'helper.php');*/

	$app = JFactory::getApplication();
	$doc = JFactory::getDocument();

	$_Input 	= $app->input;
	$_Cookie 	= $_Input->cookie;
	$_Env		= (object)$_Input->getArray(array(
					'option' 	=> 'STRING',
					'ctrl'		=> 'STRING',
					'task'		=> 'STRING',
					'view'		=> 'STRING',
					'layout'	=> 'STRING',
					'cid'		=> 'INT'
				));

	/**
		Seitenklassen – CSS Suffixe*/
		$pageclass = array(
							($_MenuItem === $app->getMenu()->getDefault(JFactory::getLanguage()->getTag()) ? 'home' : 'deeper' )
						);

	$_MenuItem = $app->getMenu()->getActive();
	if(!empty($_MenuItem))
	{
		$pageclass[] = $_MenuItem->params->get('pageclass_sfx','');
	}

	/**
		JavaScript Frameworks laden*/
	JHtml::_('bootstrap.framework');
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction;?>">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<link rel="dns-prefetch" href="//fonts.googleapis.com" />
	<link rel="dns-prefetch" href="//ajax.googleapis.com" />
	<meta name="robots" content="index, follow" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=3.0, user-scalable=yes" />
<?php
	if( $tpl_use_app_icons ) :
		include JPATH_THEMES . DIRECTORY_SEPARATOR . $this->template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'web_app_icons.php';
	else :
?>
	<link rel="icon" type="image/png" href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/images/icons/favicon.png">
<?php
	endif;
?>
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/css/animate.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/css/main.css" />
  <jdoc:include type="head" />
</head>
<body class="component-page">
	<jdoc:include type="component" />
	<jdoc:include type="message" />
	<script src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/js/app/app.js"></script>
	<script>
		"use strict";
		$app.pathname = '<?php echo JUri::root(true); ?>';
	</script>
</body>
</html>

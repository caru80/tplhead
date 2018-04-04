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

	/**
		JavaScript Frameworks laden*/
	JHtml::_('bootstrap.framework');
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction;?>">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta charset="utf-8" />
	<title><?php echo $this->error->getCode(); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<meta name="robots" content="noindex" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/css/error.css" />
</head>
<body class="error-page">
	<div id="error-page">
		<div>
			<div id="error-intro">

				<h1 class="error-num"><?php echo $this->error->getCode(); ?></h1>
				<p class="error-text">
					<?php
						echo JText::_('TPL_HEAD_FRONT_ERROR_NOT_FOUND');
					?>
				</p>
				<p class="error-text-internal">
				 	(<?php echo $this->error->getMessage(); ?>)
				</p>
			</div>
			<div id="error-outro">
				<p>
					<?php
						echo JText::sprintf('TPL_HEAD_FRONT_ERROR_GOHOME', JUri::root(true));
					?>
				</p>
			</div>
		</div>
	</div>
</body>
</html>

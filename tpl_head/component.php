<?php
	defined('_JEXEC') or die;

	/*
		Template Head 4.2
	*/
	$tpl_use_app_icons = false; // App-Icons, Android Manifest und Apple Mobile Web App. Oder nur /templates/head/images/icons/favicon.png

	JLoader::register('TemplateHelper', JPATH_THEMES . DIRECTORY_SEPARATOR . $this->template . DIRECTORY_SEPARATOR . 'helper' . DIRECTORY_SEPARATOR . 'helper.php');
	$app = JFactory::getApplication();
	$doc = JFactory::getDocument();

	// Lade /js/jquery.min.js & /js/bootstrap.min.js
	JHTML::_('bootstrap.framework');
?>
<!DOCTYPE html>
<html lang="<?php echo TemplateHelper::getLangShortcode(); ?>" dir="<?php echo $this->direction;?>">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<link rel="dns-prefetch" href="//fonts.googleapis.com" />
	<link rel="dns-prefetch" href="//ajax.googleapis.com" />
	<meta name="robots" content="index, follow" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=3.0, user-scalable=yes" />
<?php
	if( $tpl_use_app_icons ):
?>
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="<?php echo JFactory::getConfig()->get('sitename');?>"/>
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="application-name" content="<?php echo JFactory::getConfig()->get('sitename');?>"/>
	<link rel="manifest" href="app_manifest.json">
	<link rel="icon" type="image/x-icon" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/favicon.ico">
	<link rel="icon" type="image/gif" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/favicon.gif">
	<link rel="icon" type="image/png" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/favicon.png">
	<link rel="icon" type="image/png" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/favicon-16x16.png" sizes="16x16">
	<link rel="icon" type="image/png" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/favicon-96x96.png" sizes="96x96">
	<link rel="icon" type="image/png" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/favicon-160x160.png" sizes="160x160">
	<link rel="icon" type="image/png" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/favicon-192x192.png" sizes="192x192">
	<link rel="icon" type="image/png" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/favicon-196x196.png" sizes="196x196">
	<link rel="apple-touch-icon" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/apple-touch-icon.png">
	<link rel="apple-touch-icon" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/apple-touch-icon-57x57.png" sizes="57x57">
	<link rel="apple-touch-icon" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/apple-touch-icon-60x60.png" sizes="60x60">
	<link rel="apple-touch-icon" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/apple-touch-icon-72x72.png" sizes="72x72">
	<link rel="apple-touch-icon" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/apple-touch-icon-76x76.png" sizes="76x76">
	<link rel="apple-touch-icon" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/apple-touch-icon-114x114.png" sizes="114x114">
	<link rel="apple-touch-icon" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/apple-touch-icon-120x120.png" sizes="120x120">
	<link rel="apple-touch-icon" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/apple-touch-icon-128x128.png" sizes="128x128">
	<link rel="apple-touch-icon" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/apple-touch-icon-144x144.png" sizes="144x144">
	<link rel="apple-touch-icon" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/apple-touch-icon-152x152.png" sizes="152x152">
	<link rel="apple-touch-icon" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/apple-touch-icon-180x180.png" sizes="180x180">
	<link rel="apple-touch-icon" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/apple-touch-icon-precomposed.png">
<?php
	else:
?>
	<link rel="icon" type="image/png" href="<?php echo TemplateHelper::getUri(); ?>/images/icons/favicon.png">
<?php
	endif;
?>
	<link rel="stylesheet" type="text/css" media="screen, handheld" href="<?php echo TemplateHelper::getUri(); ?>/css/animate.css" />
	<link rel="stylesheet" type="text/css" media="screen, handheld" href="<?php echo TemplateHelper::getUri(); ?>/css/main.css" />
  <jdoc:include type="head" />
</head>
<body>

	<jdoc:include type="component" />

<?php
	// -- Cookiehinweis
	if( (bool) $this->params->get('cookienotice', false) ) include JPATH_THEMES . '/' . $this->template . '/html/cookienotice.php';
?>
	<jdoc:include type="message" />
	<script type="text/javascript" src="<?php echo TemplateHelper::getUri(); ?>/js/app/app.js"></script>
	<script type="text/javascript">
		$app.pathname = '<?php echo JUri::root(true); ?>';
	</script>
</body>
</html>

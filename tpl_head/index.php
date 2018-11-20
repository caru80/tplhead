<?php
	defined('_JEXEC') or die;

    use \Joomla\CMS;
	use \Joomla\CMS\Layout;
	use \Joomla\CMS\HTML;
    use \Joomla\CMS\Uri;
    
	$tpl_use_app_icons = false; // true = App-Icons, Android Manifest und "Apple Mobile Web App" anwenden. false = nur /templates/head/images/icons/favicon.png benutzen

	$app = CMS\Factory::getApplication();
	$doc = CMS\Factory::getDocument();

	$_Input 	= $app->input;
	$_Cookie 	= $_Input->cookie;
	$_Env		= (object) $_Input->getArray(array(
					'option' 	=> 'STRING',
					'ctrl'		=> 'STRING',
					'task'		=> 'STRING',
					'view'		=> 'STRING',
					'layout'	=> 'STRING',
					'cid'		=> 'INT'
				));

    //
    // Seitenklassen – CSS Suffixe
    //
	$_MenuItem = $app->getMenu()->getActive();
	$pageclass = array(($_MenuItem === $app->getMenu()->getDefault(CMS\Factory::getLanguage()->getTag()) ? 'home' : 'deeper')); // Start- oder Unterseite?
	if(!empty($_MenuItem))
	{
		$pageclass[] = $_MenuItem->params->get('pageclass_sfx','');
	}

	//
    // JavaScript Frameworks laden (Bootstrap und jQuery)
    //
	HTML\HTMLHelper::_('bootstrap.framework');
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction;?>">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="robots" content="index, follow" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
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
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/css/template.css" />
  <jdoc:include type="head" />
</head>
<body<?php echo isset($pageclass) ? ' class="'.implode(" ", $pageclass).'"' : '';?>>

	<?php // Eine belanglose Hülle – die macht nix! ?>
	<div id="site-wrapper">

		<?php // Der Seitenkopf ?>
		<header id="site-header">
			<?php // Die header-controls enthalten z.B. den Menü-Kopf, und sind nur bei kleinen Auflösungen sichtbar ?>
			<div class="header-controls">
				<a data-navgrid-toggle tabindex="0" id="site-menu-btn" class="app-btn btn-menu">
					<i></i>
					<span>
						<span>Menü</span>
					</span>
				</a>
				<div class="quicknav">
					<jdoc:include type="modules" name="position-menu-quicknav-mobile" />
				</div>
			</div>
			<div class="site-header-inner">
				<a href="<?php echo $this->baseurl; ?>">
					<picture id="site-logo">
                        <source type="image/svg+xml" srcset="images/layout/logo-sensoplast.svg" />
                        <source srcset="images/layout/logo-sensoplast-1x.png, images/layout/logo-sensoplast-2x.png, images/layout/logo-sensoplast-3x.png, images/layout/logo-sensoplast-4x.png" />
                        <img src="images/layout/logo-sensoplast-1x.png" alt="<?php echo CMS\Factory::getConfig()->get('sitename');?>" />
                    </picture>
				</a>
				<div class="servicemenu">
					<div class="sparung"></div>
					<span class="slogan">we care.</span>
					<jdoc:include type="modules" name="position-servicemenu" />
				</div>
				<div class="mainmenu">
					<jdoc:include type="modules" name="position-mainmenu" />
				</div>
			</div>
			<?php // Die Suchleiste ($app.searchbar) ?>
			<div id="site-searchbar" class="site-searchbar">
				<div class="site-searchbar-inner">
					<jdoc:include type="modules" name="position-site-header-searchbar" />
				</div>
			</div>
		</header>

		<?php // Das Navigations-Grid ($app.navgrid) ?>
		<div class="navgrid collapsed" id="navgrid">
			<div class="navgrid-slide">
				<jdoc:include type="modules" name="position-navgrid-slide" />
			</div>
			<div class="navgrid-content">
				<?php
				/**
					Protoslider für com_content (Kategorien und Beiträge) */
					if($_Env->option === 'com_content') :
						echo Layout\LayoutHelper::render('head.protoslider');
					endif;
				?>
				<jdoc:include type="modules" name="position-before-main" />
				<main>
					<jdoc:include type="modules" name="position-before-component" />
					<?php // Die Komponenten-Sektion ?>
					<section class="component <?php echo $_Env->option;?> view_<?php echo $_Env->view;?>">
						<jdoc:include type="component" />
					</section>
					<jdoc:include type="modules" name="position-after-component" />
				</main>
				<jdoc:include type="modules" name="position-after-main" />
			</div>
		</div>
		<footer id="site-footer">
			<div class="site-footer-inner">
				<jdoc:include type="modules" name="position-site-footer" />
			</div>
		</footer>
	</div>

	<jdoc:include type="message" />
	<script src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/js/app/app.js"></script>
	<script>
		$app.pathname = '<?php echo $this->baseurl;?>';
	</script>
</body>
</html>

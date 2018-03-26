<?php
	defined('_JEXEC') or die;

	/*
		Template Head 4.2
	*/
	$tpl_use_app_icons = false; // true = App-Icons, Android Manifest und "Apple Mobile Web App" anwenden. false = nur /templates/head/images/icons/favicon.png benutzen

	JLoader::register('TemplateHelper', JPATH_THEMES . DIRECTORY_SEPARATOR . $this->template . DIRECTORY_SEPARATOR . 'helper' . DIRECTORY_SEPARATOR . 'helper.php');
	$app = JFactory::getApplication();
	$doc = JFactory::getDocument();

	$_Input 	= $app->input;
	$_Cookie 	= $Input->cookie;
	$_Env		= (object)$_Input->getArray(array(
					'option' 	=> 'STRING',
					'ctrl'		=> 'STRING',
					'task'		=> 'STRING',
					'view'		=> 'STRING',
					'layout'	=> 'STRING',
					'cid'		=> 'INT'
				));

	// Lade /js/jquery.min.js & /js/bootstrap.min.js
	JHTML::_('bootstrap.framework');
	/**
		Seitenklassen – CSS Suffixe	*/
	$pageclass = array(
		(TemplateHelper::getItemParams() !== false ? TemplateHelper::getItemParams()->get('pageclass_sfx','') : ''),
		(TemplateHelper::isDefaultMenuItem(TemplateHelper::getLangTag()) == false ? 'deeper' : 'home')
	);
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
<body<?php echo count($pageclass) ? 'class="'.implode(" ", $pageclass).'"' : '';?>>

	<div class="container">
		<div class="row row-equal">
			<div>
				Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum.
			</div>
			<span></span>
			<div>

			</div>
			<span></span>
			<div>

			</div>
			<span></span>
			<div>
				Denean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum.
			</div>
			<span></span>
			<div>

			</div>
			<span></span>
			<div>

			</div>
			<span></span>
		</div>
	</div>

	<!--

	Scrollen:

	JS:
		/js/app/app.scroll.js

	$app Extension:
		$app.extensions.list.scroll

	Bsp.:

		<a name="top"></a>

		<a href="#top" data-scroll>Nach oben</a>

	-->



	<!--
	Sticky

	JS + Doku:
		/js/sticky.js

	$app Extension:
		$app.extensions.list.sticky

	Bsp.:

	<div id="#hautpcontainer" style="height: 2000px;">
		<div data-sticky style="border: 1px solid #000000;">
			<h2>Sticky</h2>
			<p>
				Sticky. Angeheftet an Elternelement div#hauptcontainer. Mit data-sticky='{"stickTo":"jQuery Selektor"}' kann ein alternatives Elternelement angegeben werden.
			</p>
			<jdoc:include type="modules" name="position-7" />
		</div>
	</div>
	-->




	<!--
		AJAX Module laden

		Siehe auch: ajax_loadmodule.php und /js/app/app.ajax.js > loadModule

	<div id="my-modules">
		Ein Container in den wir Module laden
	</div>

	<script type="text/javascript">
		(function($){
			$(function()
			{
				$($app).one('ready', function()
				{
					$app.ajax.loadModule({ position: 'position-7', target : '#my-modules', purge : true }); // Mit "purge" leeren wir #my-modules vor dem Einhängen des Moduls
				});
			});
		})(jQuery);
	</script>
	-->

	<!--
		Mit einem Anker (bei Klick) alle Module an position-7 nach #my-modules laden:

		<a tabindex="0" data-ajax='{"module":true,"position":"position-7","target":"#my-modules","rtrigger":true,"chrome":"html5"}'> // Mit "rtrigger" entfernen wir den Link, nachdem er angeklickt wurde, und "chrome" ist gleich <jdoc:include ... style="html5">
			position-7 laden
		</a>
	-->

	<!--
		Modul 87 nach #my-modules laden:

		<a tabindex="0" data-ajax='{"module":true,"id":87,"target":"#my-modules","rtrigger":true,"chrome":"html5"}'>
			Modul 87 laden
		</a>
	-->


	<!--
		Ajax Komponente laden (z.B. Artikel)


		<a href="link zum Artikel" data-ajax='{"target":"#irgendwohin"}'></a>

		Oder, wenn wir – aus irgendeinem Grund – nur die Headline haben wollen:

		<a href="link zum Artikel" data-ajax='{"only":".page-header","target":"#irgendwohin"}'></a>


		Oder in JS z.B.:

		<script type="text/javascript">
		(function($){
			$(document).ready(function()
			{
				$($app).one('ajaxReady.app', function()
				{
					$app.ajax.load({ url : 'URL zum Artikel', target : '#irgendwo', only : '.page-header' });
				});
			});
		})(jQuery);
	</script>

	-->




	<!--

	Systemnachrichten

	Template:
		/html/layout/joomla/system/message.php
		/html/cookienotice.php

	LESS:
		/less/components/app.messages.less

	JS:
		/js/app/app.messages.js

	$app Extension:
		$app.extensions.list.messages


	<script type="text/javascript">
		(function($){
			$(document).ready(function(){

				/*
					Eine Systemnachricht bei jedem Klick auf ein <a> auslösen!
				*/

				$('a').on('click', function(ev){

					if( ! $app.extensions.list.messages.available ) return; // Die Systemnachrichten-Erweiterung ist (noch) nicht verfügbar.

					$app.messages.show({
						text : 'Du hast ein ' + this.nodeName.toLowerCase() + ' angeklickt.',
						btn  : false,
						hide : 1200,
						type : 'notice'
					});
				});


				/*
					Sobald app.messages geladen wurde, lassen wir 2 weitere Meldungen ausgeben:
				*/
				$($app).on('messagesReady', function(){

					// Die langweilige Methode:

					$app.messages.show({
						text 	: 'Lorem <strong>Ipsum</strong> dolor!',
						btn		: 'Ich bin ein Knopf',
						hide	: false,
						type	: 'error'
					});


					// Und die Custom-HTML Methode:

					$app.messages.show({
						type : 'html',
						text :
							'<div class="system-message notice">' +			// Die Custom-Nachricht kann natürlich auch eigene Klassen haben, und ganz anders aussehen als die anderen. z.B. der Cookiehinweis.
								'<div class="bla">' +
									'<p>' +
										'Ich bin eine Custom-Nachricht, und das ist auch gut so!' +
									'</p>' +
								'</div>' +
								'<div class="blub">' +
									'<a tabindex="0" class="msg-close">Schließen</a><br />' + 					// Der a.msg-close macht die Nachricht zu.
									'<a href="http://www.headmarketing.de/" target="_blank">Website</a>' +
								'</div>' +
							'</div>',
						hide : false
					});

				});



			});
		})(jQuery);
	</script>
	-->
	<jdoc:include type="message" />
	<script type="text/javascript" src="<?php echo TemplateHelper::getUri(); ?>/js/app/app.js"></script>
	<script type="text/javascript">
		$app.pathname = '<?php echo JUri::root(true); ?>';
	</script>
</body>
</html>

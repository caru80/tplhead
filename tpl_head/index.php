<?php
	defined('_JEXEC') or die;

	/**
		Template Head 1.2.0
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
	$_MenuItem = $app->getMenu()->getActive();
	$pageclass = array(($_MenuItem === $app->getMenu()->getDefault(JFactory::getLanguage()->getTag()) ? 'home' : 'deeper'));
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
<body<?php echo isset($pageclass) ? ' class="'.implode(" ", $pageclass).'"' : '';?>>

	<?php
		/**
			Protoslider für com_content (Kategorien und Beiträge)*/
		if($_Env->option === 'com_content') 
		{
			echo JLayoutHelper::render('head.protoslider');
		}
	?>


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
	Sticky – Opt-In!

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
		AJAX Module laden – Opt-In!

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
	<script src="<?php echo $this->baseurl . '/templates/' . $this->template; ?>/js/app/app.js"></script>
	<script>
		"use strict";
		$app.pathname = '<?php echo JUri::root(true); ?>';
	</script>
</body>
</html>

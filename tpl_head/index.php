<?php
	defined('_JEXEC') or die;

	/*
		Template Head 4.2
	*/
	$tpl_use_app_icons = false; // true = App-Icons, Android Manifest und "Apple Mobile Web App" anwenden. false = nur /templates/head/images/icons/favicon.png benutzen

	JLoader::register('TemplateHelper', JPATH_THEMES . DIRECTORY_SEPARATOR . $this->template . DIRECTORY_SEPARATOR . 'helper' . DIRECTORY_SEPARATOR . 'helper.php');
	$app = JFactory::getApplication();
	$doc = JFactory::getDocument();

	$Input 	= $app->input;
	$Cookie = $Input->cookie;
	$Env	= (object)$Input->getArray(array(
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
<body>

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
	Wegpunkte/One-Page-Navigation

		<div id="waypoints" style="position: fixed; right: 20px; top: 100px;"></div>

	LESS:
		/less/components/app.waypoints.less

	JS:
		/js/jquery.nav.js
		/js/app/app.waypoints.js

	$app Extension:
		$app.extensions.list.jqueryNav
		$app.extensions.list.waypoints


	z.B. Einen Wegpunkt (Anker...) für jede <section> in div#waypoints anlegen

	<script type="text/javascript">
		$(function(){
			$($app).one('waypointsReady', function(){
				$app.waypoints.init({ makeNew : true, node : '#waypoints', targets : 'section' });
			});
		});
	</script>
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

		Overlay


		Bsp.: Standart-Overlay, das für alles benutzt wird, wenn nicht anders angegeben.

		Die Id des Standart-Overlay (ist = overlay) ist definiert in $app.extensions.list.appOverlays.options.defaultOverlay oder auch in den "defaults" von /js/app/app.overlay.js

		data-options='{"trigger":"jQuery Selektor"}' – Der "Trigger" bekommt die Klasse "active", wenn das Overlay geöffnet wird.

		Das Aussehen und die Animationen des Overlays, können komplett in /less/overlay.less geändert werden. Aber überall wo transitions drin sind, müssen auch transitions drin bleiben!

		Die Ladeanzeige kann in den "defaults" von /js/overlay.js überschrieben werden und in /less/components/app.loading.less stehen ein paar zur Auswahl


		<div id="overlay" class="overlay" data-options='{"trigger":".btn-nav.btn-stripes"}'>
			<div class="overlay-header"><div>Ich bin eine optionale Kopfleiste!</div></div>
			<div class="overlay-wrapper">
				<div class="overlay-outer">
					<div class="overlay-inner">
						<div class="overlay-static">

							Hier statischer Inhalt.

						</div>
						<div class="overlay-dynamic">

							Hier hineien lädt die Methode load des Overlay den Kram.

						</div>
					</div>
				</div>
			</div>
		</div>


		Der Trigger für #overlay:

		<a class="btn-nav btn-stripes" tabindex="0" data-overlay><i></i></a>

	-->

	<!--
		Bsp.: Overlay Suche


		data-options='{"hideStaticOnLoad":false}' – Das Element <div class="overlay-static"> (wo das Suchfeld drin ist) soll NICHT ausgeblendet werden, wenn Inhalt nach <div class="overlay-dynamic"> geladen wird.


	Der Trigger für #overlay-search:

	<a class="btn-nav btn-search" tabindex="0" data-overlay="#overlay-search"><i></i></a>

	Das Overlay:

	<div id="overlay-search" class="overlay search" data-options='{"trigger":".btn-nav.btn-search","hideStaticOnLoad":false}'>
		<div class="overlay-wrapper">
			<div class="overlay-outer">
				<div class="overlay-inner">
					<div class="overlay-static">
						<jdoc:include type="modules" name="position-search" />
						<script type="text/javascript">
							/*
								Die Methode "load" von $app.finder, welche die Suchergebnisse lädt und in $app.finder.options.results anzeigt, überschreiben, und durch den Overlay mit der id "#search-overlay"
								laden und anzeigen lassen – wie auf headmarketing.dem oder im HEAD.WEB

								siehe auch: $app.extensions.list.finder
							*/
							(function($){
								/*
									Wir müssen darauf warten, dass das DOM geladen wurde, weil $app erst danach zur Verfügung steht. Alternativ müsste app.js im <head> verlinkt werden
								*/
								$(document).ready(function(){
									/*
										Dann müssen wir warten, bis $app.finder geladen wurde.

										Jedes erfolreiche laden einer "Erweiterung" löst an $app einen Event aus mit dem Namen: erweiterungReady
									*/
									$($app).one('finderReady.app', function(){
										$app.finder.load = function( url )
										{
											$('#overlay-search').overlay().load( url );
										};
									});
								});
							})(jQuery);
						</script>
					</div>
					<div class="overlay-dynamic"></div>
				</div>
			</div>
		</div>
	</div>


		Weil das Overlay #overlay-search jetzt die Suchergebnisse lädt und anzeigt, funktioniert nun auch folgendes:

		<a tabindex="0" data-overlay="#overlay-search" data-findquery="Testartikel">Suche nach Testartikel im Overlay</a>

		Der Klick auf den <a> öffnet #overlay-search und löst die Suche nach Testartikel aus.



		Und eine sinnfreie Nachricht lassen wir anzeigen, wenn #overlay-search geöffnet wird.

	<script type="text/javascript">
		(function($){
			$(document).ready(function(){
				$($app).one('overlayReady.app', function(){
					$('#overlay-search').overlay().on('afterOpen', function(){
						$app.messages.show({
							text : 'Du hast das Such-Overlay aufgemacht, und das hast du toll gemacht!',
							btn  : 'Danke!',
							//,btn : false – Kein Button
							type : 'warning'
							//,hide : 5000 – Nach 5 Sek. ausblenden
							//,hide : false – Nicht automatisch ausblenden
						});

					});
				});
			});
		})(jQuery);
	</script>
	-->




	<!--

		VIDEOJS

		Wenn in $app.extensions.list eingeschaltet reicht es einfach den video-Tag – inklusive video.js CSS Klassen (16:9 etc.) – irgendwo zu plazieren...

			<video id="meinvideo" autoplay controls class="video-js vjs-default-skin vjs-16-9 vjs-big-play-centered vjs-skin-colors-green">
				<source src="https://ia800201.us.archive.org/12/items/BigBuckBunny_328/BigBuckBunny_512kb.mp4" type="video/mp4">
			</video>



		Wenn es nicht eingeschaltet ist:

		video.js Script + Stylesheet nachträglich laden, und Big Buck Bunny von archive.org streamen:


		<div id="video-container"></div>

		<a tabindex="0" data-video="https://ia800201.us.archive.org/12/items/BigBuckBunny_328/BigBuckBunny_512kb.mp4"><strong>Film gucken, jetzt!</strong></a>

		<script type="text/javascript">
			(function($){

				// Das macht nur einen Player...

				var machPlayer = function(datei)
				{

					$vid = $('<video id="meinvideo" autoplay controls class="video-js vjs-default-skin vjs-16-9 vjs-big-play-centered vjs-skin-colors-green">');

					$datei = $('<source src="' + datei + '" type="video/mp4">');

					$vid.append($datei);

					$('#video-container').append($vid);

					videojs('#meinvideo');
				}



				// Das macht den Spaß

				$(document).ready(function(){

					$('[data-video]').on('click.app', function(){

						var $self = $(this);

						if( !$app.extensions.list.videojs.available ) // Video JS ist nicht verfügbar, und muss noch geladen werden.
						{
							$app.loadStylesheet( $app.protocol + $app.pathname + '/templates/head/assets/video-js-5.19.0/video-js.css', 'screen');

							$($app).one('videojsReady.app', function(){

								$app.messages.show({ text : 'Gleich geht es los!' }); 			// Noch eine Nachricht, damit der User auch Bescheid weiß!

								machPlayer($self.data('video'));
							});

							$app.extensions.load('videojs', true); // load( Name Erweiterung, force (weil abgeschaltet) )
						}
						else
						{
							machPlayer($self.data('video'));
						}

						$self.off('click.app'); // Weil ein Video mir der gleichen id (und in Chrome sogar die gleiche Datei) nur einmal in die Seite eingefügt werden kann, wird hier onclick entfernt.
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

	<?php
		/**
			Cookiehinweis (/html/cookienotice.php) von $app.messages anzeigen lassen. siehe auch /html/cookienotice.php */
		if((bool)$this->params->get('cookienotice', false) && (bool)$Cookie->get('acceptcookies',false) === false ) include JPATH_THEMES . DIRECTORY_SEPARATOR . $this->template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'cookienotice.php';
		/**
			Google Analytics Opt-Out (/html/gaoptout.php) */
		include JPATH_THEMES . DIRECTORY_SEPARATOR . $this->template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'gaoptout.php';
	?>
	<jdoc:include type="message" />
	<script type="text/javascript" src="<?php echo TemplateHelper::getUri(); ?>/js/app/app.js"></script>
	<script type="text/javascript">
		$app.pathname = '<?php echo JUri::root(true); ?>';
	</script>
</body>
</html>

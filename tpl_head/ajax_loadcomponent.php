<?php
	defined('_JEXEC') or die();
	/*
		Rendert eine Komponente inkl. JavaScript Deklarationen, aber ohne Links zu externen Scripts

		CRu.: 2016-09-06


		Script:

			$app.ajax.load({
				url – String URL
				target – String, jQuery Selektor vom Ziel-Container
				only - String, jQuery Selektor von einem Element innerhalb der geladenen Daten, das eingefügt werden soll (der Rest wird verworfen)
			})


		HTML:

			<a href="index.php?option=com_content&view=article&id=123&Itemid=456" data-ajax='{"target":"#hierher"}'>Lade einen Artikel nach #hierher</a>

			<a href="index.php?option=com_users&view=login" data-ajax='{"target":"#hierher"}'>Lade die Anmeldemaske nach #hierher</a>

			<div class="diesesdiv" data-ajax='{"url":"index.php?option=com_content&view=article&id=123&Itemid=456","target":".diesesdiv"}'>Klick mich an um einen Artikel zu laden!</div>

	*/
?>
<jdoc:include type="component" />
<?php
	$doc 	= JFactory::getDocument();
	$header = $doc->getHeadData();

	if( count($header['script']) ):
		/*
			ACHTUNG !
			Internet Explorer ist sehr empfindlich beim evaluieren von Scripts, die per AJAX in die Seite eingefügt werden.
			- NIEMALS sollten Kommentare in Form von "// Kommentar" in den Scripts vorkommen.
			- NIEMALS sollten die Scripts mit Steinzeit-Konstruktionen wie "<!-- (code) -->" oder [[CDATA] escaped sein.
		*/
?>
	<script>
	<?php
		foreach( $header['script'] as $declaration )
		{
			echo $declaration;
		}
	?>
	</script>
<?php
	endif;
?>

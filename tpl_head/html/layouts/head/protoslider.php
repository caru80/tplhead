<?php
/**
	Protoslider Layout 
	CRu.: 2018
	
	Wie lade ich dieses Layout in einem Blog und einem Beitrag?

		Du forderst das Layout ohne Parameter an:
		<?php echo JLayoutHelper::render('head.protoslider'); ?>


	Wie lade ich dieses Layout in einem Blog-Beitrag (/components/com_content/category/blog_item.php)?

		Du übergibts zusätlich den Beitrag an das Layout: 
		<?php echo JLayoutHelper::render('head.protoslider', $this->item); ?>


	Wie lade ich dieses Layout in einer Blog-Kindkategorie (/components/com_content/category/blog_children.php)?
		
		Du übergibts zusätlich die Kategorie an das Layout: 
		<?php echo JLayoutHelper::render('head.protoslider', $child); ?>

*/
use \Joomla\CMS;

JLoader::register('HeadProtosliderLayoutHelper', __DIR__ . '/helper.php');

$app = CMS\Factory::getApplication();

$field_prefix = '';
$slider_class = 'heroslider';

$requestVars = array(
	'view' 		=> 'string',
	'layout' 	=> 'string',
	'id' 		=> 'int'
);
$requestVars = (object) $app->input->getArray($requestVars);

if(isset($displayData)) // $displayData (ist so ein Joomla Ding) wurde übergeben und enthält einem Blog-Beitrag, eine Kindkategorie im Blog, oder einen Beitrag aus einem mod_articles_head.
{
	$field_prefix = 'preview_'; // Daten aus dem Feld „preview_protoslider” holen
	$slider_class = 'preview'; 	// Class-Suffix für Protoslider

	$requestVars->id = $displayData->id;

	// Kindkategorie im Blog, oder Blogbeitrag, oder Beitrag in ModArticlesHead?
	if('Joomla\CMS\Categories\CategoryNode' === get_class($displayData)) 
	{
		// Kind-Kategorie im Blog
		$params 	= new JRegistry($displayData->params);
	}
	else 
	{
		// Blogbeitrag oder Beitrag in ModArticlesHead
		JLoader::register('ContentModelArticle', JPATH_SITE . '/components/com_content/models/article.php'); // <<< Prüfen ob der schon da ist, und das raus kann.
		
		$model 		= new ContentModelArticle();
		$item 		= $model->getItem($requestVars->id);
		$params 	= new JRegistry($item->attribs);
	}
}
else 
{
	switch($requestVars->view) 
	{
		case 'article' :
			$model	 	= new ContentModelArticle();
			$item 	 	= $model->getItem($requestVars->id);
			$params 	= new JRegistry($item->attribs);
		break;

		case 'category' :
			$model 		= JCategories::getInstance('content');
			$item 		= $model->get($requestVars->id);
			$params 	= new JRegistry($item->params);
		break;
	}
}



if(isset($params))
{
	$config = (object) array(
		"class_list" => array(
			$slider_class,
			$requestVars->view != '' ? 'view-' . $requestVars->view : '',
			$requestVars->layout != '' ? 'layout-' . $requestVars->layout : ''
		),
		"slider_id" 	=> 'ptslider-' . $requestVars->id . '-' . rand(0, 100000),
		"options" 		=> $params->get($field_prefix . 'protoslider_options', ''),
		"items" 		=> $params->get($field_prefix . 'protoslider', array()),
		"url_prefix"	=> JUri::root(true)
	);

	if(count($config->items)):
?>
<div class="ptslider-layout <?php echo implode(' ', $config->class_list); ?>">
	<div 
		id="<?php echo $config->slider_id; ?>"
		class="ptslider <?php echo implode(' ', $config->class_list); ?>"
		<?php 
			// JSON Optionen
			echo $config->options != '' ? ' data-ptoptions=\''.$config->options.'\'' : '';
		?>
	>

		<div class="ptslider-wrapper">
<?php
		// Items/Slides bauen:
		foreach($config->items as $i => $item) :

			// Lightbox Video anzeigen?
			$video_lightbox = '';
			if( $item->lightbox_video_url != -1 
				&& JFile::exists(JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'videos' . DIRECTORY_SEPARATOR . $item->lightbox_video_url) )
			{
				$button_label 		= JText::_('TPL_HEAD_PLAY_VIDEO');

				$video_lightbox = <<<HTML
	<div class="lightbox-video" data-fullvideo="images/videos/$item->lightbox_video_url" title="$button_label"></div>
HTML;
			}


			// Video Player
			$video_player = '';
			if ( $item->video_url != -1 
				&& JFile::exists(JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'videos' . DIRECTORY_SEPARATOR . $item->video_url))
			{
				$attributes  = '';
				$attributes .= $item->video_loop ? ' loop' : '';
				$attributes .= $item->video_mute ? ' muted' : '';

				$video_container = pathinfo($item->video_url, PATHINFO_EXTENSION);

				$video_player = <<<HTML
	<video preload="metadata" $attributes controls poster="$item->image_url">
		<source src="$config->url_prefix/images/videos/$item->video_url" type="video/$video_container" />
	</video>
HTML;
			}


			// Bild
			$background_image = '';
			$foreground_image = '';
			if($item->image_background)
			{
				$background_image = ' style="background-image: url(' . $config->url_prefix . '/' . $item->image_url . ');?>"';
			}
			else
			{
				$foreground_image = '<img class="item-image" src="' . $config->url_prefix . '/' . $item->image_url . '" />';
			}

			// „Caption”
			if($item->show_item_caption && $item->image_caption != '')
			{
				// Weiterlesen URL – In der Caption wird der String {readmore_url} ersetzt.
				if(isset($item->readmore) && $item->readmore != '') 
				{
					$readmore_url = HeadProtosliderLayoutHelper::getReadmoreUrl($item);
					$item->image_caption = preg_replace("#" . preg_quote("{readmore_url}", "/") . "#", $readmore_url, $item->image_caption);
				}

				$caption = <<<HTML
	<div class="item-caption">
		<div class="item-caption-inner">
			$item->image_caption
		</div>
	</div>
HTML;
			}


			/* Item/Slide Ausgeben */
?>
			<div class="ptslider-item item-<?php echo $i; ?>"<?php echo $background_image; ?>>
				<?php echo $background_image === '' ? $foreground_image : ''; ?>
				<?php echo $video_lightbox; ?>
				<?php echo $video_player; ?>
				<?php echo $item->slide_html;?>
				<?php echo $caption; ?>
			</div>
<?php
		endforeach;
?>
		</div>
	</div>

<script>
	'use strict';
	(function($) {
		var initProtoslider = function() 
		{
			let slider = $('#<?php echo $config->slider_id; ?>').protoslider({
				onInit : function()
				{
					this.initSprites();
				},

				onAfterInit : function()
				{
					var $slide  = this.State.stage[this.State.columns -1],
						sprites = this.getSprites( $slide );
	
					if( sprites.length )
					{
						this.spritesIn( sprites );
					}
				}
			});
			slider.on('afterSlideIn', function( e, data )
			{
				let sprites = this.getSprites( data.slide );
				if( sprites.length )
				{
					this.spritesIn( sprites );
				}
			});
			slider.on('afterSlideOut', function( e, data )
			{
				let sprites = this.getSprites( data.slide );
				if( sprites.length )
				{
					this.spritesReset( sprites );
				}
			});
		}

		$(function() {
			$($app).one('protosliderReady', function() {
				initProtoslider();
			});
			
			<?php /* Wir laden Protoslider manuell, falls er abgeschaltet ist */?>
			$($app).one('extensionsReady', function() {
				if(!$app.extensions.list.protoslider.available) { 
					//if(console) console.log('Protoslider-Layout wartet auf protoslider.js von $app...');
					$app.extensions.load('protoslider', true);
				}
			});
		});

		<?php /* Hier setzen wir einen Event Listener auf jedes mod_articles_head */?>
		$('.mod-intro').one('afterLoad.ptslider', function(ev) {
			
			// Vielleicht hat ein mod_articles_head einen Beitrag geladen, welcher dieses Layout angefordert hat.
			
			if(!$app.extensions.list.protoslider.available) {
				$($app).one('protosliderReady', function() {
					initProtoslider();
				});
				//if(console) console.log('Protoslider-Layout velangt protoslider.js von $app für mod_articles_head nach AJAX-Aufruf...');
				$app.extensions.load('protoslider', true);
			}
			else {
				initProtoslider();
			}			
		});
	})(jQuery);
</script>

    <div class="protoslider-layout-modules">
    <?php
		if($slider_class !== 'preview') 
		{
            $modules = \Joomla\CMS\Helper\ModuleHelper::getModules('position-protoslider-layout');
            if(count($modules))
            {
                foreach($modules as $mod)
                {
                    echo \Joomla\CMS\Helper\ModuleHelper::renderModule($mod, array("style" => ""));
                }
            }
        }
	?>
    </div>
</div>
<?php
	endif;
}
?>

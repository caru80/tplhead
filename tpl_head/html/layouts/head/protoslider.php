<?php
/**
	Protoslider Layout - 1.0.0
	CRu.: 2018-05-04
*/
$settings_prefix = '';
$ptslider_class  = 'heroslider';

$contentId = 0;

if(isset($displayData)) // $displayData wurde übergeben und enthält einem Blog-Beitrag oder einen Beitrag aus einem mod_articles_head.
{
	// Blgobeitrag
	JLoader::register('ContentModelArticle', JPATH_SITE . '/components/com_content/models/article.php');

	$settings_prefix = 'preview_';
	$ptslider_class  = 'preview';

	$contentId = $displayData->id;

	$artModel 	= new ContentModelArticle();
	$article 	= $artModel->getItem($contentId);
	$params 	= new JRegistry($article->attribs);
}
else if(JFactory::getApplication()->input->get('view', '', 'STRING') === 'article')
{
	// Beitrag (option=com_content&view=article)
	$contentId	= JFactory::getApplication()->input->get('id', 0, 'INT');

	$artModel 	= new ContentModelArticle();
	$article 	= $artModel->getItem($contentId);
	$params 	= new JRegistry($article->attribs);
}
else if(JFactory::getApplication()->input->get('view', '', 'STRING') === 'category')
{
	// Kategorie: Blog/Category (option=com_content&view=category)
	$contentId = JFactory::getApplication()->input->get('id', 0, 'INT');

	$catModel 	= JCategories::getInstance('content');
	$category 	= $catModel->get($contentId);
	$params 	= new JRegistry($category->params);
}

if(isset($params))
{
	$slideroptions 	= $params->get($settings_prefix . 'protoslider_options','');
	$sliderdata 	= $params->get($settings_prefix . 'protoslider','');
	if($sliderdata != ''):
?>
<div class="ptslider-layout">
	<div id="ptslider-<?php echo JFactory::getApplication()->input->get('view', '', 'STRING');?>-<?php echo $contentId;?>" class="ptslider <?php echo $ptslider_class;?> <?php echo JFactory::getApplication()->input->get('view', '', 'STRING');?> <?php echo JFactory::getApplication()->input->get('layout', '', 'STRING');?>"
		<?php echo $slideroptions != '' ? ' data-ptoptions=\''.$slideroptions.'\'' : '';?>
		>
		<div class="ptslider-wrapper">
<?php
		foreach($sliderdata as $i => $item):

			$class 		= '';
			$attributes = '';

			if ($item->video_url != -1 && JFile::exists(JPATH_SITE . DIRECTORY_SEPARATOR . 'videos' . DIRECTORY_SEPARATOR . $item->video_url)) :
				// -- Mit Video
				if($item->video_cover) {
					$class 		.= ' cover';
					$attributes .= ' data-ptoptions=\'{"autoplay":true}\'';
				}
				else
				{
					$attributes .= ' style="width: 100%;"';
					$attributes .= $item->video_controls ? ' controls' : '';
					$attributes .= $item->video_autoplay ? ' data-ptoptions=\'{"autoplay":true}\'' : '';
				}
				$attributes .= $item->video_loop ? ' loop' : '';
				$attributes .= $item->video_mute ? ' muted' : '';

				$class = $class != '' ? ' class="'.$class.'"' : '';
			?>
					<div class="ptslider-item"<?php echo $item->image_url != '' ? ' style="background-image: url('.$item->image_url.');"' : '';?>>
						<video preload="metadata" <?php echo $class; echo $attributes?> poster="<?php echo $item->image_url;?>">
							<source src="videos/<?php echo $item->video_url;?>" type="video/mp4" />
						</video>

			<?php
				elseif($item->image_url != ''):
					// -- Ohne Video
			?>
					<div class="ptslider-item">
						<img src="<?php echo $item->image_url;?>" alt="" />
			<?php
				endif;

				if($item->image_caption != ''):
			?>
						<div class="item-caption">
							<?php echo $item->image_caption;?>
						</div>
			<?php
				endif;
			?>
					</div> <?php // Ende von .ptslider-item;?>
<?php
		endforeach;
?>
		</div>
	</div>
</div>
<script>
	'use strict';
	(function($) {
		var initProtoslider = function() {
			$('#ptslider-<?php echo JFactory::getApplication()->input->get('view', '', 'STRING');?>-<?php echo $contentId;?>').protoslider();
			if($app.equalColumns) {
				$app.equalColumns.destroy();
				$app.equalColumns.init();
			}
		}

		$(function() {
			$($app).one('extensionsReady', function() {
				$($app).one('protosliderReady', function() {
					initProtoslider();
				});
				<?php /* Wir laden Protoslider manuell, falls er abgeschaltet ist */?>
				if(!$app.extensions.list.protoslider.available) { 
					if(console) console.log('Protoslider layout is requesting protoslider.js...');
					$app.extensions.load('protoslider', true);
				}
			});
		});

		<?php /* Hier setzen wir einen Event Listener auf jedes mod_articles_head */?>
		$('.mod-intro').one('afterLoad', function() {
			// Ein mod_articles_head hat per AJAX einen Beitrag geladen, welcher dieses Layout angefordert hat. Wir starten Protoslider.
			if(!$app.extensions.list.protoslider.available) {
				$($app).one('protosliderReady', function() {
					initProtoslider();
				});
				if(console) console.log('Protoslider layout is requesting protoslider.js for mod_articles_head after AJAX call...');
				$app.extensions.load('protoslider', true);
			}
			else {
				initProtoslider();
			}			
		});
	})(jQuery);
</script>
<?php
	endif;
}
?>

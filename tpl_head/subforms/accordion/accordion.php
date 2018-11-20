<?php

	$app = \Joomla\CMS\Factory::getApplication();
	$view = $app->input->get('view', '', 'string');
	$id   = $app->input->get('id', 0, 'int');

	$list = json_decode($value);

	$a = 0; // Zähler Accordions
	$c = 0; // Zähler Items
?>
<!--div class="accordion" id="item-page-accordion" role="tablist" aria-multiselectable="true"-->
<?php
	foreach($list as $i => $field) :
		
		if($c == 0) :
	?>
			<?php echo $field->accordion_heading != '' ? '<h4>' . $field->accordion_heading . '</h4>' : ''; ?>
			<div class="accordion" id="item-page-accordion-<?php echo $a;?>" role="tablist" aria-multiselectable="true">
	<?php
		elseif((bool)$field->separator && $c > 0) :
			
			++$a; // !!!
	?>
			</div>
			<?php echo $field->accordion_heading != '' ? '<h4>' . $field->accordion_heading . '</h4>' : ''; ?>
			<div class="accordion" id="item-page-accordion-<?php echo $a;?>" role="tablist" aria-multiselectable="true">
	<?php
			continue;
		endif;


        $enh        = (bool) !empty($field->enhanced_settings) ? $field->enhanced_settings : false;
        $classList  = !empty($field->enhanced_classlist) && $enh ? ' ' . $field->enhanced_classlist : '';
		$closed     = (bool) !empty($field->enhanced_closed) && $enh ? $field->enhanced_closed : false;
?>
			<div class="card panel-<?php echo $c;?><?php echo $classList;?>">
				<div class="card-header" id="heading-<?php echo $c;?>" role="tab" 
					data-toggle="collapse" 
					data-target="#panel-content-<?php echo $c;?>" 
					aria-expanded="<?php echo $closed != true ? 'true' : 'false';?>"
				>
					<h5><?php echo $field->panel_heading;?></h5>
				</div>
				<div class="panel-collapse collapse<?php echo $closed != true ? ' show' : '';?>" 
					aria-expanded="<?php echo $c == 0 && $closed != true ? 'true' : 'false';?>" 
					role="tabpanel" id="panel-content-<?php echo $c;?>"
					aria-labelledby="heading-<?php echo $c;?>" 
					data-parent="#item-page-accordion-<?php echo $a;?>"
				>
					<div class="card-body">
						<?php 
							// Content Events auslösen
							if($view == 'article' && $id > 0)
							{
								$item = (object) array(
									"id" 	=> $id,
									"introtext" => '', // Sonst gibt es eine Fehlermeldung
									"text" => $field->panel_content
								);
								$params = new JObject;
								
								JPluginHelper::importPlugin('content');
								// Note JDispatcher is deprecated in favour of JEventDispatcher in Joomla 3.x however still works.
								JEventDispatcher::getInstance()->trigger('onContentBeforeDisplay', array('content.article', &$item, &$params, 0));

								echo $item->text;
							}
							else
							{
								echo $field->panel_content;
							}
						?>
					</div>
				</div>
			</div>
<?php		
        $c++;
	endforeach;
?>
</div>
<script>
	(function($) 
	{
		// Auf kleinen Screens klappen wir alles im Accordion zu
		$(function() 
		{
			if($app.getVps().w <= 768)
			{
				$('.accordion').find('.collapse').each(function() 
				{
					$(this).collapse('hide');
				});
			}
		});

		// Scrollen an Oberkante von .card-header, wenn es geöffnet wird (nur kleine Screens).
		
		$('.accordion').on('shown.bs.collapse', function(e)
		{
			if($app.getVps().w < 769) 
			{
				$app.scroll.scrollTo({el : $(this).find('.card-header[aria-expanded="true"]'), force : true});
			}
		});
	})(jQuery);
</script>
<section class="modsection item-page-section<?php echo !empty($item->alternate_bg) ? ' alt' : '';?>">
	<div class="section-inner">
		<?php
			if(!empty($item->kachel_title)):
		?>    
				<header class="section">
					<<?php echo $item->kachel_heading_tag;?>>
						<?php echo $item->kachel_title;?>
						<?php
							if(!empty($item->kachel_subtitle)):
						?>
								<span><?php echo $item->kachel_subtitle;?></span>
						<?php
							endif;
						?>
					</<?php echo $item->kachel_heading_tag;?>>
				</header>
		<?php
			endif;
		?>    

		<?php
			if(!empty($item->kachel_description)):

				// Content Events auslösen
				if($view == 'article' && $id > 0)
				{
					$temp = (object) array(
						"id" 	=> $id,
						"introtext" => '', // Sonst gibt es eine Fehlermeldung
						"text" => $item->kachel_description
					);
					$params = new JObject;
					
					JPluginHelper::importPlugin('content');
					// Note JDispatcher is deprecated in favour of JEventDispatcher in Joomla 3.x however still works.
					JEventDispatcher::getInstance()->trigger('onContentBeforeDisplay', array('content.article', &$temp, &$params, 0));
					$item->kachel_description = $temp->text;
				}

				$columns = array();
				if(!empty($item->kachel_image)):
					$columns['image'] = '<img src="'. $item->kachel_image .'">';
					$columns['text'] = $item->kachel_description;
				endif;
		?> 
				<div class="section-content">
				<?php
					if(count($columns)):
				?>
						<div class="content-row">
							<?php
								foreach($columns as $key => $content):
							?>
									<div class="col <?php echo $key;?>">
										<?php echo $content;?>
									</div>
							<?php
								endforeach;
							?>
						</div>
				<?php
					else:
				?>
						<?php echo $item->kachel_description;?>
				<?php
					endif;
				?>
				</div>
		<?php
			endif;
		?>
	</div>
</section>
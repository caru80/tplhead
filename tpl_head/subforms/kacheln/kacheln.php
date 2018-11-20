<?php
	// Brauch ich um Content-Events zu triggern:
	$app = \Joomla\CMS\Factory::getApplication();
	$view = $app->input->get('view', '', 'string');
	$id   = $app->input->get('id', 0, 'int');
	// --

	$list 	= json_decode($value);
	$items 	= array();

	// ?
	$i = 0;
	foreach($list as $item)
	{
		$items[$i] = $item;
		$i++;
	}
	unset($list);

	$container_open = false;
	foreach($items as $i => $item):
		switch($item->kachel_type):
			case 'tile' :

				if(!$container_open): // Kachel-Container aufmachen
					$container_open = true;
				?>
						<section class="modsection mod-intro extratiles blog-layout">
							<div class="section-inner mod-intro-items">
								<div class="list-row">
				<?php
				endif;

				require dirname(__FILE__) . '/layout_' . $item->kachel_type . '.php';

				$next = $i + 1 < count($items) ? $items[$i + 1] : null;
				if((!$next || $next->kachel_type !== 'tile') && $container_open): // Kachel-Container zumachen
					$container_open = false;
				?>
								</div>
							</div>
						</section>
				<?php
				endif;
			break;

			case 'section' :
				require dirname(__FILE__) . '/layout_' . $item->kachel_type . '.php';
			break;
			
		endswitch;
	endforeach;
?>
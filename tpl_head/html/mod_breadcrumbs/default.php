<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_breadcrumbs
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// JHtml::_('bootstrap.tooltip');

/**
	Für Bootstrap 4
	
	Die Kommentare zwischen den <li> verhindern, dass unerwünschte White-Spaces erzeugt werden: https://css-tricks.com/fighting-the-space-between-inline-block-elements/
*/
?>
<nav class="breadcrumbs<?php echo $moduleclass_sfx;?>" aria-label="breadcrumb">
	<ol itemscope itemtype="https://schema.org/BreadcrumbList" class="breadcrumb"><!--
<?php 
	if ($params->get('showHere', 1)) : 
?>
--><li class="breadcrumb-item intro active">
	<?php echo JText::_('MOD_BREADCRUMBS_HERE'); ?>
</li><!--
<?php 
	endif; 

	// Get rid of duplicated entries on trail including home page when using multilanguage
	for ($i = 0; $i < $count; $i++)
	{
		if ($i === 1 && !empty($list[$i]->link) && !empty($list[$i - 1]->link) && $list[$i]->link === $list[$i - 1]->link)
		{
			unset($list[$i]);
		}
	}

	// Find last and penultimate items in breadcrumbs list
	end($list);
	$last_item_key = key($list);
	prev($list);
	$penult_item_key = key($list);

	// Make a link if not the last item in the breadcrumbs
	$show_last = $params->get('showLast', 1);

	// Generate the trail
	foreach ($list as $key => $item) :
		if ($key !== $last_item_key) :

			$item->name = strlen($item->name) > 50 ? trim(substr($item->name, 0, 50)) . '...' : $item->name;

			// Render all but last item - along with separator 
?>
		--><li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><!--
			--><meta itemprop="position" content="<?php echo $key + 1; ?>"><!--
			<?php 
				if (!empty($item->link) && $item->link != '#') : 
			?>
				--><a itemprop="item" href="<?php echo $item->link; ?>" class="pathway"><!--
					--><span itemprop="name"><?php echo trim($item->name); ?></span>
				</a>
			<?php
				else : 
			?>
					--><span itemprop="name"><?php echo trim($item->name);?></span>
			<?php 
				endif;
			?>
			<?php 
				//if (($key !== $penult_item_key) || $show_last) : 
			?>
					<!--i class="separator"></i-->
			<?php 
				//endif; 
			?>
			</li><!--
<?php 
		elseif ($show_last) :
			$item->name = strlen($item->name) > 50 ? trim(substr($item->name, 0, 50)) . '...' : $item->name;
?>
		--><li class="breadcrumb-item active" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><!--
			--><meta itemprop="position" content="<?php echo $key + 1; ?>"><!--
				--><span itemprop="name"><?php echo $item->name; ?></span>
			</li><!--
		<?php endif;
	endforeach; ?>
--></ol>
</nav>

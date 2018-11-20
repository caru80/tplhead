<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
/**
 
Schiss! 
JHtml::_('formbehavior.chosen', 'select'); 

*/

/**
	CRu.: 
	<h1 class="page-title">...
	Geändert zu 
	<div class="pager-header"><h1>...</h1></div>

	div.search-inner hinzugefügt
  */

?>
<div class="search<?php echo $this->pageclass_sfx; ?>">
	<div class="component-inner search-inner">
		<?php if ($this->params->get('show_page_heading')) : ?>
			<div class="page-header">
				<h1>
					<?php if ($this->escape($this->params->get('page_heading'))) : ?>
						<?php echo $this->escape($this->params->get('page_heading')); ?>
					<?php else : ?>
						<?php echo $this->escape($this->params->get('page_title')); ?>
					<?php endif; ?>
				</h1>
			</div>
		<?php endif; ?>
		<?php echo $this->loadTemplate('form'); ?>
		<?php if ($this->error == null && count($this->results) > 0) : ?>
			<?php echo $this->loadTemplate('results'); ?>
		<?php else : ?>
			<?php echo $this->loadTemplate('error'); ?>
		<?php endif; ?>
	</div>
</div>

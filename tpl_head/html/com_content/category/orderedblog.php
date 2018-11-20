<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

JHtml::_('behavior.caption');

$dispatcher = JEventDispatcher::getInstance();

$this->category->text = $this->category->description;
$dispatcher->trigger('onContentPrepare', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$this->category->description = $this->category->text;

$results = $dispatcher->trigger('onContentAfterTitle', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$afterDisplayTitle = trim(implode("\n", $results));

$results = $dispatcher->trigger('onContentBeforeDisplay', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$beforeDisplayContent = trim(implode("\n", $results));

$results = $dispatcher->trigger('onContentAfterDisplay', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$afterDisplayContent = trim(implode("\n", $results));





$itemsByCategory = array($this->category->id => (object) array("category_title" => "", "intro_items" => array(), "lead_items" => array()));
$jumpNav = array();

// -- Items in der Hauptkategorie (die im Menüeintrag zur Anzeige gewählte Kategorie)
foreach($this->lead_items as $item) 
{
	if($item->catid === $this->category->id) 
	{
		$itemsByCategory[$this->category->id]->lead_items[] = $item;
	}
}
foreach($this->intro_items as $item) 
{
	if($item->catid === $this->category->id) 
	{
		$itemsByCategory[$this->category->id]->intro_items[] = $item;
	}
}

// -- Items in Kind-Kategorien
// if($this->maxLevel != 0 && !empty($this->children[$this->category->id])) 
if($this->params->get('show_subcategory_content', 0) != 0 && !empty($this->children[$this->category->id])) 
{
	foreach($this->children[$this->category->id] as $i => $children) 
	{
		$itemsByCategory[$children->id] = (object) array("children" => $children, "intro_items" => array(), "lead_items" => array());
		$jumpNav[] = (object) array("id" => $children->id, "title" => $children->title, "href" => 'category-' . $children->id);

		foreach($this->lead_items as $item) 
		{
			if($item->catid === $children->id) 
			{
				$itemsByCategory[$children->id]->lead_items[] = $item;
			}
		}
		
		foreach($this->intro_items as $item) 
		{
			if($item->catid === $children->id) 
			{
				$itemsByCategory[$children->id]->intro_items[] = $item;
			}
		}
	}
}



?>
<div class="blog ordered <?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/Blog">
	<div class="component-inner blog-inner">
		<?php if ($this->params->get('show_page_heading')) : ?>
			<div class="page-header">
				<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
			</div>
		<?php endif; ?>

		<?php if ($this->params->get('show_category_title', 1) or $this->params->get('page_subheading')) : ?>
			<div class="page-header">
				<h2> <?php echo $this->escape($this->params->get('page_subheading')); ?>
					<?php if ($this->params->get('show_category_title')) : ?>
						<span class="subheading-category"><?php echo $this->category->title; ?></span>
					<?php endif; ?>
				</h2>
			</div>
		<?php endif; ?>
		<?php echo $afterDisplayTitle; ?>

		<?php if ($this->params->get('show_cat_tags', 1) && !empty($this->category->tags->itemTags)) : ?>
			<?php $this->category->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
			<?php echo $this->category->tagLayout->render($this->category->tags->itemTags); ?>
		<?php endif; ?>

		<?php if ($beforeDisplayContent || $afterDisplayContent || $this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
			<div class="category-desc clearfix">
				<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
					<img src="<?php echo $this->category->getParams()->get('image'); ?>" alt="<?php echo htmlspecialchars($this->category->getParams()->get('image_alt'), ENT_COMPAT, 'UTF-8'); ?>"/>
				<?php endif; ?>
				<?php echo $beforeDisplayContent; ?>
				<?php if ($this->params->get('show_description') && $this->category->description) : ?>
					<?php echo JHtml::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
				<?php endif; ?>
				<?php echo $afterDisplayContent; ?>
			</div>
		<?php endif; ?>

		<?php if (empty($this->lead_items) && empty($this->link_items) && empty($this->intro_items)) : ?>
			<?php if ($this->params->get('show_no_articles', 1)) : ?>
				<p><?php echo JText::_('COM_CONTENT_NO_ARTICLES'); ?></p>
			<?php endif; ?>
		<?php endif; ?>
	</div>

	<div class="blog-jumpnav observe-sticky">
		<div class="jumpnav-inner">
			<ul class="jumpnav-list nav menu">
				<?php
					foreach($jumpNav as $i => $data):
				?>
						<li class="item-cat-<?php echo $data->id;?>">
							<a href="#<?php echo $data->href;?>" data-scroll><i class="fas fa-angle-right"></i> <?php echo $data->title;?></a>
						</li>
				<?php
					endforeach;
				?>
			</ul>
		</div>
	</div>

	<?php $leadingcount = 0; ?>
	<?php if (!empty($this->lead_items)) : ?>
	<section class="items-leading blog-section modsection">
		<div class="section-inner">
			<?php 
				// Wir ändern hier das Layout, um das Default-Layout für Items im Blog zu benutzen (blog_item.php).
				$this->set('_layout', 'blog');
			
				foreach ($this->lead_items as &$item):
			?>
					<div class="leading-<?php echo $leadingcount; ?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?>"
						itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
						<?php
							$this->item = &$item;
							echo $this->loadTemplate('item');
						?>
					</div>
			<?php 
					$leadingcount++;
				endforeach;

				$this->set('_layout', 'orderedblog');
			?>
		</div>
	</section><!-- end items-leading -->
	<?php endif; ?>

	<?php
	$introcount = count($this->intro_items);
	$counter = 0;
	?>


	<?php
		foreach($itemsByCategory as $catid => $items) :
			if(count($items->intro_items)) :
	?>
				<section id="category-<?php echo $catid;?>" class="items-intro blog-section modsection <?php echo $catid !== (int) $this->category->id ? 'cat-children' : 'parent-category';?>">
					<div class="section-inner">
					<?php
						if(!empty($items->children)) : // Nur die Titel etc. von Kind-Kategorien ausgeben
					?>
							<header class="category-header section">
								<h2><?php echo $items->children->title; ?></h2>
							</header>
							<?php
								if($this->params->get('orderedblog_show_readonly', 0)):
							?>
									<div class="readmore show-category">
										<a href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($catid)); ?>">
											<?php echo JText::sprintf('TPL_HEAD_ORDEREDBLOG_ONLY_SHOW_THIS_CATEGORY', $items->children->title); ?>
											<i class="fas fa-angle-right"></i>
										</a>
									</div>
							<?php
								endif;
							?>
							<?php
								if($this->params->get('orderedblog_show_children_desc', 0)) :
							?>
									<div class="category-desc">
										<?php echo $items->children->description;?>
									</div>
							<?php
								endif;
							?>
					<?php
						endif;
					?>

					<div class="items-row">
						<?php 
							// Wir ändern hier das Layout, um das Default-Layout für Items im Blog zu benutzen (blog_item.php).
							$this->set('_layout', 'blog');

							foreach ($items->intro_items as $key => &$item) : 
						?>
						<div class="item-column">
							<?php
								$this->item = &$item;
								echo $this->loadTemplate('item');
							?>
						</div>
						<?php 
							endforeach; 

							$this->set('_layout', 'orderedblog');
						?>
					</div>
				</div>
			</section>
	<?php
			endif;
		endforeach;
	?>



	<?php if (!empty($this->link_items)) : ?>
		<div class="items-more">
			<?php 
				$this->set('_layout', 'blog');
			
				echo $this->loadTemplate('links');

				$this->set('_layout', 'orderedblog');
			?>
		</div>
	<?php endif; ?>

	<?php if ($this->maxLevel != 0 && !empty($this->children[$this->category->id])) : ?>
		<section class="blog-section cat-children">
			<div class="section-inner">
				<?php if ($this->params->get('show_category_heading_title_text', 1) == 1) : ?>
					<h3> <?php echo JText::_('JGLOBAL_SUBCATEGORIES'); ?> </h3>
				<?php endif; ?>
				<div class="items-row">
					<?php 
						$this->set('_layout', 'blog');
						
						echo $this->loadTemplate('children');
						
						$this->set('_layout', 'orderedblog');
					?> 
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
		<div class="pagination">
			<?php if ($this->params->def('show_pagination_results', 1)) : ?>
				<p class="counter pull-right"> <?php echo $this->pagination->getPagesCounter(); ?> </p>
			<?php endif; ?>
			<?php echo $this->pagination->getPagesLinks(); ?> </div>
	<?php endif; ?>
</div>

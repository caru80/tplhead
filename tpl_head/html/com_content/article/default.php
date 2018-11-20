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

// Create shortcuts to some parameters.
$params  = $this->item->params;
$images  = json_decode($this->item->images);
$urls    = json_decode($this->item->urls);
$canEdit = $params->get('access-edit');
$user    = JFactory::getUser();
$info    = $params->get('info_block_position', 0);

// Check if associations are implemented. If they are, define the parameter.
$assocParam = (JLanguageAssociations::isEnabled() && $params->get('show_associations'));
JHtml::_('behavior.caption');

// Hat der Beitrag einen Inhalt?
$emptyBody = ($this->item->text === '' || $this->item->text === ' ') ? true : false;

/**
	Custom Fields umbauen 
	
	Zugriff per: $this->item->cfields->FELNAME->WERT
*/
if(count($this->item->jcfields))
{
	$cfields = array();
	foreach($this->item->jcfields as $i => $field)
	{
		$cfields[$field->name] = $field;
	}
	$this->item->cfields = (object)$cfields;
}

$columnLayout = false;
$customFields = false;

$this->item->leftColCustoms = array(
	// "feldname" => "template" – leerer String = kein extra Template
	"contenleftcol" => ""
);

// -- Die folgenden Joomla Custom Fields in der rechten Spalte anzeigen
$this->item->rightColCustoms = array(
	// "feldname" => "template" – leerer String = kein extra Template
	"accordion" => "accordion" // Template ist hier z.B. „default_accordion.php”
);

$this->item->showCustomFields = array_merge($this->item->leftColCustoms, $this->item->rightColCustoms);
foreach($this->item->showCustomFields as $fname => $tmpl) {
	if(isset($this->item->cfields->$fname) 
		&& $this->item->cfields->$fname->value != '') 
	{
		$columnLayout = true;
		$customFields = true;
	}
}
unset($this->item->showCustomFields);

// -- Bilder
$itemImages = json_decode($this->item->images);
if($itemImages->image_fulltext != '') {
	$columnLayout = true;
}

// -- Module in Spalten
$itemModules = (object) array(
	"leftcol" 	=> JModuleHelper::getModules('position-article-leftcol'),
	"rightcol" 	=> JModuleHelper::getModules('position-article-rightcol')
);
if(count($itemModulesRight->leftcol) || count($itemModulesRight->rightcol)) 
{
	$columnLayout = true;
}
?>
<div class="item-page<?php echo $this->pageclass_sfx; ?><?php echo $emptyBody ? ' empty-body' : '';?>" itemscope itemtype="https://schema.org/Article">
    <div class="component-inner item-page-inner">
        <meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />
        <?php if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
        </div>
        <?php endif;
        if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && $this->item->paginationrelative)
        {
            echo $this->item->pagination;
        }
        ?>

        <?php // Todo Not that elegant would be nice to group the params ?>
        <?php $useDefList = ($params->get('show_modify_date') || $params->get('show_publish_date') || $params->get('show_create_date')
        || $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author') || $assocParam); ?>

        <?php if (!$useDefList && $this->print) : ?>
            <div id="pop-print" class="btn hidden-print">
                <?php echo JHtml::_('icon.print_screen', $this->item, $params); ?>
            </div>
            <div class="clearfix"> </div>
        <?php endif; ?>
        <?php if ($params->get('show_title') || $params->get('show_author')) : ?>
        <div class="page-header">
            <?php if ($params->get('show_title')) : ?>
                <h2 itemprop="headline">
                    <?php echo $this->escape($this->item->title); ?>
                </h2>
            <?php endif; ?>
            <?php if ($this->item->state == 0) : ?>
                <span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
            <?php endif; ?>
            <?php if (strtotime($this->item->publish_up) > strtotime(JFactory::getDate())) : ?>
                <span class="label label-warning"><?php echo JText::_('JNOTPUBLISHEDYET'); ?></span>
            <?php endif; ?>
            <?php if ((strtotime($this->item->publish_down) < strtotime(JFactory::getDate())) && $this->item->publish_down != JFactory::getDbo()->getNullDate()) : ?>
                <span class="label label-warning"><?php echo JText::_('JEXPIRED'); ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if (!$this->print) : ?>
            <?php if ($canEdit || $params->get('show_print_icon') || $params->get('show_email_icon')) : ?>
                <?php echo JLayoutHelper::render('joomla.content.icons', array('params' => $params, 'item' => $this->item, 'print' => false)); ?>
            <?php endif; ?>
        <?php else : ?>
            <?php if ($useDefList) : ?>
                <div id="pop-print" class="btn hidden-print">
                    <?php echo JHtml::_('icon.print_screen', $this->item, $params); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php // Content is generated by content plugin event "onContentAfterTitle" ?>
        <?php echo $this->item->event->afterDisplayTitle; ?>

        <?php if ($useDefList && ($info == 0 || $info == 2)) : ?>
            <?php // Todo: for Joomla4 joomla.content.info_block.block can be changed to joomla.content.info_block ?>
            <?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'above')); ?>
        <?php endif; ?>

        <?php if ($info == 0 && $params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
            <?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>

            <?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
        <?php endif; ?>

        <?php // Content is generated by content plugin event "onContentBeforeDisplay" ?>
        <?php echo $this->item->event->beforeDisplayContent; ?>

        <?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '0')) || ($params->get('urls_position') == '0' && empty($urls->urls_position)))
            || (empty($urls->urls_position) && (!$params->get('urls_position')))) : ?>
        <?php echo $this->loadTemplate('links'); ?>
        <?php endif; ?>
        <?php if ($params->get('access-view')) : ?>
		<?php 
			if(!$columnLayout):
				echo JLayoutHelper::render('joomla.content.full_image', $this->item);
			endif;
		?>
        <?php
        if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && !$this->item->paginationrelative) :
            echo $this->item->pagination;
        endif;
        ?>
        <?php if (isset ($this->item->toc)) :
            echo $this->item->toc;
        endif; ?>

		<div itemprop="articleBody">
			<?php
				echo $this->item->text;

				// Cru.: Column Layout start
				if($columnLayout) :
			?>
					<section> <!--class="modsection item-page-section"-->
						<div class="item-content-row">
							<div class="item-content-column left">
								<?php
									foreach($this->item->leftColCustoms as $fname => $tmpl)
									{
										if(isset($this->item->cfields->$fname))
										{
											if($tmpl !== '')
											{
												$this->item->_currentCustomField = $this->item->cfields->$fname;
												echo $this->loadTemplate($tmpl);
											}
											else
											{
												// Content Plugins Triggern
												$temp = (object) array(
													"id" 	=> $this->item->id,
													"introtext" => '', // Sonst gibt es eine Fehlermeldung
													"text" => $this->item->cfields->$fname->value
												);
												JEventDispatcher::getInstance()->trigger('onContentBeforeDisplay', array('content.article', &$temp, &$params, 0));
												echo $temp->text; // $this->item->cfields->$fname->value;
											}
										}
									}

									foreach($itemModules->left as $module) 
									{
										echo JModuleHelper::renderModule($module, array());
									}
								?>
							</div>
							<div class="item-content-column right">
								<?php if($itemImages->image_fulltext != '') : ?>
									<div class="item-image">
										<img src="<?php echo $itemImages->image_fulltext;?>" alt="<?php echo $itemImages->image_fulltext_alt;?>" />
									</div>
								<?php endif; ?>
								<?php
									foreach($this->item->rightColCustoms as $fname => $tmpl)
									{
										if(isset($this->item->cfields->$fname))
										{
											if($tmpl !== '')
											{
												$this->item->_currentCustomField = $this->item->cfields->$fname;
												echo $this->loadTemplate($tmpl);
											}
											else
											{
												// Content Plugins Triggern
												$temp = (object) array(
													"id" 	=> $this->item->id,
													"introtext" => '', // Sonst gibt es eine Fehlermeldung
													"text" => $this->item->cfields->$fname->value
												);
												JEventDispatcher::getInstance()->trigger('onContentBeforeDisplay', array('content.article', &$temp, &$params, 0));
												echo $temp->text; // $this->item->cfields->$fname->value;
											}
										}
									}

									foreach($itemModules->right as $module) 
									{
										echo JModuleHelper::renderModule($module, array());
									}
								?>
							</div>
						</div>
					</section>
			<?php
				endif;
			?>
		</div>
		
		<?php if ($info == 1 || $info == 2) : ?>
            <?php if ($useDefList) : ?>
                    <?php // Todo: for Joomla4 joomla.content.info_block.block can be changed to joomla.content.info_block ?>
                <?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'below')); ?>
            <?php endif; ?>
            <?php if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
                <?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
                <?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php
        if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && !$this->item->paginationrelative) :
            echo $this->item->pagination;
        ?>
        <?php endif; ?>
        <?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '1')) || ($params->get('urls_position') == '1'))) : ?>
        <?php echo $this->loadTemplate('links'); ?>
        <?php endif; ?>
        <?php // Optional teaser intro text for guests ?>
        <?php elseif ($params->get('show_noauth') == true && $user->get('guest')) : ?>
        <?php echo JLayoutHelper::render('joomla.content.intro_image', $this->item); ?>
        <?php echo JHtml::_('content.prepare', $this->item->introtext); ?>
        <?php // Optional link to let them register to see the whole article. ?>
        <?php if ($params->get('show_readmore') && $this->item->fulltext != null) : ?>
        <?php $menu = JFactory::getApplication()->getMenu(); ?>
        <?php $active = $menu->getActive(); ?>
        <?php $itemId = $active->id; ?>
        <?php $link = new JUri(JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false)); ?>
        <?php $link->setVar('return', base64_encode(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language))); ?>
        <p class="readmore">
            <a href="<?php echo $link; ?>" class="register">
            <?php $attribs = json_decode($this->item->attribs); ?>
            <?php
            if ($attribs->alternative_readmore == null) :
                echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
            elseif ($readmore = $attribs->alternative_readmore) :
                echo $readmore;
                if ($params->get('show_readmore_title', 0) != 0) :
                    echo JHtml::_('string.truncate', $this->item->title, $params->get('readmore_limit'));
                endif;
            elseif ($params->get('show_readmore_title', 0) == 0) :
                echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
            else :
                echo JText::_('COM_CONTENT_READ_MORE');
                echo JHtml::_('string.truncate', $this->item->title, $params->get('readmore_limit'));
            endif; ?>
            </a>
        </p>
        <?php endif; ?>
        <?php endif; ?>
        <?php
        if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && $this->item->paginationrelative) :
            echo $this->item->pagination;
        ?>
        <?php endif; ?>
	</div>
	
	<?php // Content is generated by content plugin event "onContentAfterDisplay" ?>
	<?php echo $this->item->event->afterDisplayContent; ?>
</div>
<?php
	//
	//	Module nach Item-Page
	//
	$modulesAfter = \Joomla\CMS\Helper\ModuleHelper::getModules('position-after-item-page');
	if(count($modulesAfter))
	{
		foreach($modulesAfter as $i => $module)
		{
			echo \Joomla\CMS\Helper\ModuleHelper::renderModule($module);
		}
	}
?>
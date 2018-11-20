<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');

// CRu.: Weitere Sprachschlüssel (von com_finder!) laden.
$lang = JFactory::getLanguage();
$lang->load('com_finder'); 
$upper_limit = $lang->getUpperLimitSearchWord();

/**
	Template angepasst an „fancy-form” (/scss/app/_fancyform.scss)
*/
?>
<form class="fancy-form" id="searchForm" action="<?php echo JRoute::_('index.php?option=com_search'); ?>" method="post">
	
	<div class="search-tools">
		<div class="tool-group">
			<input type="text" name="searchword" title="<?php echo JText::_('COM_SEARCH_SEARCH_KEYWORD'); ?>" placeholder="<?php echo JText::_('COM_SEARCH_SEARCH_KEYWORD'); ?>" id="search-searchword" size="30" maxlength="<?php echo $upper_limit; ?>" value="<?php echo $this->escape($this->origkeyword); ?>" class="form-control form-control-lg" />
		</div>
		<div class="tool-group">
			<button name="Search" type="submit" class="btn btn-lg btn-aws btn-primary hasTooltip" title="<?php echo JHtml::_('tooltipText', 'COM_SEARCH_SEARCH');?>">
				<i>
					<i class="fas fa-search"></i>
					<i class="fas fa-check"></i>
				</i>
				<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
			</button>
		</div>
		<input type="hidden" name="task" value="search" />
		<?php if($this->params->get('search_phrases',1) || $this->params->get('search_areas', 1)): ?>
			<div class="toggle-extended">
				<a data-toggle="collapse" href="#extendedSearchParams" aria-expanded="false" aria-controls="extendedSearchParams">
					<?php echo JText::_("COM_FINDER_ADVANCED_SEARCH_TOGGLE");?>
				</a>
			</div>
		<?php endif;?>
	</div>

	<?php
		/**
			CRu.: Erweiterte Suche zum ausklappen 
		*/	
		if($this->params->get('search_phrases',1) || $this->params->get('search_areas', 1)):
	?>
		<div class="collapse" id="extendedSearchParams">
			<div class="fieldsets extended-search-params">
			<?php if ($this->params->get('search_phrases', 1)) : ?>
				<fieldset class="phrases">
					<legend>
						<?php echo JText::_('COM_SEARCH_FOR'); ?>
					</legend>
					<div class="phrases-box">
						<?php
							// CRu.: Entnommen aus /components/com_search/views/search/view.html.php
							// Standard: echo $this->lists["searchphrase"];
							$searchphrases = array();
							$searchphrases[]       = JHtml::_('select.option', 'all', JText::_('COM_SEARCH_ALL_WORDS')); // == stdClass Object ( [value] => all [text] => Alle Wörter [disable] => ) 
							$searchphrases[]       = JHtml::_('select.option', 'any', JText::_('COM_SEARCH_ANY_WORDS'));
							$searchphrases[]       = JHtml::_('select.option', 'exact', JText::_('COM_SEARCH_EXACT_PHRASE'));
						?>
						<div class="radio">
							<?php
								foreach($searchphrases as $i => $f):
							?>
								<label class="radio" for="searchphrase<?php echo $f->value;?>">
									<?php 
										$checked = JFactory::getApplication()->input->get('searchphrase','','STRING') == $f->value ? 'checked="checked"' : '';
										//echo $checked;
									?>
									<input type="radio" name="searchphrase" id="searchphrase<?php echo $f->value;?>" value="<?php echo $f->value;?>" <?php echo $checked;?>>
									<i></i>
									<span><?php echo $f->text;?></span>
								</label>
							<?php
								endforeach;
							?>
						</div>
					</div>
					<div class="ordering-box">
						<label for="ordering" class="ordering">
							<?php echo JText::_('COM_SEARCH_ORDERING'); ?>
						</label>
						<?php 
							// CRu.: Entnommen aus /components/com_search/views/search/view.html.php
							echo $this->lists['ordering'];
						?>
					</div>
				</fieldset>
			<?php endif; ?>
			<?php if ($this->params->get('search_areas', 1)) : ?>
				<fieldset class="only">
					<legend>
						<?php echo JText::_('COM_SEARCH_SEARCH_ONLY'); ?>
					</legend>
					<div class="checkbox">
						<?php foreach ($this->searchareas['search'] as $val => $txt) : ?>
							<?php $checked = is_array($this->searchareas['active']) && in_array($val, $this->searchareas['active']) ? 'checked="checked"' : ''; ?>
							<label for="area-<?php echo $val; ?>" class="checkbox">
								<input type="checkbox" name="areas[]" value="<?php echo $val; ?>" id="area-<?php echo $val; ?>" <?php echo $checked; ?> />
								<i></i>
								<span><?php echo JText::_($txt); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				</fieldset>
			<?php endif; ?>
			</div>

		</div>
	<?php
		endif;
	?>
	
	<?php if ($this->total > 0) : ?>
		<div class="form-limit">
			<label for="limit">
				<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
			</label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
		<!--p class="counter">
			<?php echo $this->pagination->getPagesCounter(); ?>
		</p-->
	<?php endif; ?>

	<div class="searchintro<?php echo $this->params->get('pageclass_sfx'); ?>">
		<?php if (!empty($this->searchword)) : ?>
			<h5>
				<?php echo JText::plural('COM_SEARCH_SEARCH_KEYWORD_N_RESULTS', $this->total); ?>
			</h5>
		<?php endif; ?>
	</div>

</form>

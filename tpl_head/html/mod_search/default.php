<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Including fallback code for the placeholder attribute in the search field.
JHtml::_('jquery.framework');
JHtml::_('script', 'system/html5fallback.js', array('version' => 'auto', 'relative' => true, 'conditional' => 'lt IE 9'));

if ($width)
{
	$moduleclass_sfx .= ' ' . 'mod_search' . $module->id;
	$css = 'div.mod_search' . $module->id . ' input[type="search"]{ width:auto; }';
	JFactory::getDocument()->addStyleDeclaration($css);
	$width = ' size="' . $width . '"';
}
else
{
	$width = '';
}

/**
	Habe nur unnötige Ausgaben entfernt.	
*/
?>
<div class="search<?php echo $moduleclass_sfx; ?>">
	<form name="search-form" action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form-inline">
		<?php
			$output  = '<div class="input-wrapper">';
			$output .= '<input name="searchword" id="mod-search-searchword' . $module->id . '" maxlength="' . $maxlength . '"  class="form-control input-lg search-query" type="search"';
			$output .= ' placeholder="' . $text . '" />';
			if ($button) :
				if ($imagebutton) :
					$output .= ' <input type="image" alt="' . $button_text . '" class="button" src="' . $img . '" onclick="this.form.searchword.focus();"/>';
				else :
					//$output .= ' <button class="button btn btn-primary" onclick="this.form.searchword.focus();">' . $button_text . '</button>';
					$output .= ' <div class="search-buttons">';
					//$output .= ' <button onclick="this.form.searchword.focus();"><i class="fas fa-search"></i></button><!--';
					//$output .= '--><a class="btn-search"><i class="fas fa-times"></i></a>';
					$output .= '<a onclick="document.forms[\'search-form\'].submit(true);"><i class="fas fa-check"></i></a><!--';
					$output .= '--><a tabindex="0" data-toggle-search><i class="fas fa-times"></i></a>';
					$output .= ' </div>';
				endif;
			endif;
			$output .= '</div>';
			/*
			if ($button) :
				if ($imagebutton) :
					$btn_output = ' <input type="image" alt="' . $button_text . '" class="button" src="' . $img . '" onclick="this.form.searchword.focus();"/>';
				else :
					$btn_output = ' <button class="button btn btn-primary" onclick="this.form.searchword.focus();">' . $button_text . '</button>';
				endif;

				switch ($button_pos) :
					case 'top' :
						$output = $btn_output . '<br />' . $output;
						break;

					case 'bottom' :
						$output .= '<br />' . $btn_output;
						break;

					case 'right' :
						$output .= $btn_output;
						break;

					case 'left' :
					default :
						$output = $btn_output . $output;
						break;
				endswitch;
			endif;
			*/
			echo $output;
		?>
		<input type="hidden" name="task" value="search" />
		<input type="hidden" name="option" value="com_search" />
		<input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>" />
	</form>
</div>

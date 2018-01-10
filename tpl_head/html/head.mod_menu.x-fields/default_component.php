<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Note. It is important to remove spaces between elements.
$class = $item->anchor_css ? 'class="' . $item->anchor_css . '" ' : '';
$title = $item->anchor_title ? 'title="' . $item->anchor_title . '" ' : '';

if ($item->menu_image)
{
	$item->params->get('menu_text', 1) ?
	$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" /><span class="image-title">' . $item->title . '</span> ' :
	$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" />';
}
else
{
	$linktype = $item->title;
}

$xattribs 	= $item->params->get('xfields_attributes','') != '' ? ' '.$item->params->get('xfields_attributes','') : '';
$xhash		= $item->params->get('xfields_hashvalue','');
$xoverride 	= $item->params->get('xfields_override_link',false);
$xhidehref	= $item->params->get('xfields_hide_href',false);

if( $xoverride )
{
	$oitem 		= JFactory::getApplication()->getMenu()->getItem( $xoverride );
	$itemLink 	= $oitem->flink;
}
else{
	$itemLink 	= $item->flink; 
	$itemLink 	= JFilterOutput::ampReplace(htmlspecialchars($itemLink));
}

if( !$xhidehref )
{
	$xhref = ' href="'. $itemLink . $xhash . '"';
}
else
{
	$xhref = ' tabindex="0"';	
}


switch ($item->browserNav)
{
	default:
	case 0:
?><a <?php echo $class; echo $xattribs; echo $xhref; ?> <?php echo $title; ?>><?php echo $linktype; ?></a><?php
		break;
	case 1:
		// _blank
?><a <?php echo $class; echo $xattribs; echo $xhref; ?> <?php echo $title; ?>><?php echo $linktype; ?></a><?php
		break;
	case 2:
	// Use JavaScript "window.open"
?><a <?php echo $class; echo $xattribs; echo $xhref; ?> onclick="window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes');return false;" <?php echo $title; ?>><?php echo $linktype; ?></a>
<?php
		break;
}

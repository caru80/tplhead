<?php
/**
 * @package        HEAD. Article Module
 * @version        1.8.1
 * 
 * @author         Carsten Ruppert <webmaster@headmarketing.de>
 * @link           https://www.headmarketing.de
 * @copyright      Copyright © 2018 HEAD. MARKETING GmbH All Rights Reserved
 * @license        http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright    Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 */
/**
 	TEMPLATE FÜR DIE „ANWENDUNGSBEREICHE” AUF DER STARTSEITE
*/
defined('_JEXEC') or die;

if(!isset($layoutConf)) {
	$layoutConf = (object)array(
					"class_sfx"		=> "intro-heroarticles",
					"item_layout" 	=> "heroarticles_item"
					);
}

require \Joomla\CMS\Helper\ModuleHelper::getLayoutPath('mod_articles_head', 'default');
?>
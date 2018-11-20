<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_languages
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
/**
	Dieses Template erzeugt ein Dropdown-Menü für Protomenü.
	Es werden quasi alle verfügbaren Sprachen als Untermenü-Einträge in Protomenü injeziert.

	Du erstellst ein Sprachumschalter-Modul, wählst dieses Template zur Ausgabe, und in einem Menüeintrag wählst du unter dem Reiter „Protomenü”:
	
	Verhalten: Modul laden

	Und wählst dein Modul... fertig.
*/
?>
<div class="mod-languages<?php echo $moduleclass_sfx; ?>">
	<ul class="nav-sub">
<?php
	foreach($list as $language):
?>
		<li>
			<a class="nav-item" href="<?php echo $language->link;?>">
				<span class="item-label">
					<?php
						if ($params->get('image', 1)):
							echo JHtml::_('image', 'mod_languages/' . $language->image . '.gif', '', null, true);
						endif;
					?>
					<?php echo $language->title_native;?>
				</span>
			</a>
		</li>
<?php
	endforeach;
?>
	</ul>
</div>
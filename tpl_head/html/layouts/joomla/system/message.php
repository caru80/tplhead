<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

$msgList = $displayData['msgList'];
?>
<div id="system-message-container-wrapper">
	<div id="system-message-container">
		<?php
			foreach ($msgList as $type => $msgs) :
		?>
				<div class="system-message <?php echo $type; ?>">
					<i></i>
					<span>
						<?php foreach ($msgs as $msg) : ?>
							<p><?php echo $msg; ?></p>
						<?php endforeach; ?>
					</span><span>
						<a class="btn msg-close" tabindex="0"><?php echo JText::_("TPL_HEAD_SYSMESSAGE_FRONT_BTN_OK");?></a>
					</span>
				</div>
		<?php
			endforeach;
		?>
	</div>
</div>

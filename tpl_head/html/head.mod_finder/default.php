<?php
/**

	Template Override um mit $app.finder suchen zu können.

	Siehe auch /less/app.finder.less
	Siehe auch /js/app/app.finder.js

*/
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_SITE . '/components/com_finder/helpers/html');

// Load the smart search component language file.
$lang = JFactory::getLanguage();
$lang->load('com_finder', JPATH_SITE);

$suffix = $params->get('moduleclass_sfx');
?>
<form autocomplete="off" id="mod-finder-searchform" name="mod-finder-searchform" action="<?php echo JRoute::_($route); ?>" method="get" class="form-search">
<div class="finder<?php echo $suffix; ?>">
	<div class="search-tools"><label for="mod-finder-searchword" class="finder"><i></i></label><input type="text" name="q" id="mod-finder-searchword" placeholder="<?php echo JText::_('MOD_FINDER_SEARCH_VALUE', true); ?>" value="" /><button type="submit" class="finder-start"><i></i></button></div>
	<?php if ($params->get('show_autosuggest', 1)) : ?>
		<div class="finder-suggestions">

		</div>
	<?php endif; ?>
</div>
	<?php echo modFinderHelper::getGetFields($route, (int) $params->get('set_itemid')); ?>
	<?php
	// Show the form fields.
	// echo $output;
	?>

	<?php $show_advanced = $params->get('show_advanced'); ?>
	<?php if ($show_advanced == 2) : ?>
		<br />
		<a href="<?php echo JRoute::_($route); ?>"><?php echo JText::_('COM_FINDER_ADVANCED_SEARCH'); ?></a>
	<?php elseif ($show_advanced == 1) : ?>
		<div id="mod-finder-advanced">
			<?php echo JHtml::_('filter.select', $query, $params); ?>
		</div>
	<?php endif; ?>
</form>
<?php
	// Auto Completer
	if( $params->get('show_autosuggest', 1) ):
?>
	<script type="text/javascript">
		(function($){
			$(document).ready(function(){
				$($app).on('finderReady', function()
				{
					$app.finder.autocompleter();
				});
			});
		})(jQuery);
	</script>
<?php
	endif;
?>

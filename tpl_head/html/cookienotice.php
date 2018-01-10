<?php
	defined('_JEXEC') or die;
	/**
		Cookie Hinweis
	*/
	$cookieName = 'acceptcookies';

	if( !(bool)$Cookie->get($cookieName) ):

		JLoader::register('MenusHelper', JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php');

		$mainLang 	= JComponentHelper::getParams('com_languages')->get('site'); 	// Frontend Hauptsprache
		$item 		= $this->params->get('item-cookieinfo',''); 					// Menüeintrag Datenschutz aus Template-Einstellungen

		if( $item )
		{
			$item 		 = TemplateHelper::getMenuItem( $item );
			$currentLang = JFactory::getApplication()->getLanguage()->getTag();

			if( $currentLang != $mainLang ) {
				// Zu mit "$item" verknüpftem Menüeintrag in aktueller Anzeigesprache verlinken
				$assocs = MenusHelper::getAssociations( $item->id );
				$item   = JFactory::getApplication()->getMenu( $assocs[$currentLang] );
			}

			$blank = '';
			if($item->type === 'url') {
				$route = $item->link;
				$blank = ' target="_blank"';
			}
			else {
				$route = JRoute::_( $item->link."&Itemid=".$item->id );
			}
		}
?>
		<script type="text/javascript">
			(function($){
				$(window).on('appReady.app', function()
				{
					$($app).on('messagesReady.app', function()
					{
						var message = '<?php echo JText::_('TPL_HEAD_COOKIENOTICE_FRONT_TEXT');?>',
							info	= <?php if( isset($route) ) : ?>'<a class="btn" href="<?php echo $route;?>"<?php echo $blank;?>><?php echo JText::_('TPL_HEAD_COOKIENOTICE_FRONT_BTN_INFO');?></a>'<?php else : ?>''<?php endif; ?>;

						$app.messages.show({
							type : 'html',
							text : '<div id="cookienotice" class="system-message cookie">' +
										'<span><i></i>' + message + '</span>' +
										'<span>' +
											'<a tabindex="0" class="btn ' + $app.messages.options.css.button + '"><?php echo JText::_('TPL_HEAD_COOKIENOTICE_FRONT_BTN_OK');?></a>' +
											info +
										'</span>' +
									'</div>',
							hide : false
						});

						$('#cookienotice').find('.' + $app.messages.options.css.button).on('click.app.cookienotice', function()
						{
							Cookies.set('<?php echo $cookieName;?>', '1', { expires: 7 });
						});
					});
				});
			})(jQuery);
		</script>
<?php
	endif;
?>

<?php
	defined('_JEXEC') or die;

	/*
		Template Head 4.0

		Fehlerseite
	*/

	JLoader::register('TemplateHelper', JPATH_THEMES . '/' . $this->template . '/helper/helper.php');
	$app = JFactory::getApplication();
	$doc = JFactory::getDocument();

	JHtml::_('bootstrap.framework');
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta charset="utf-8" />
	<title><?php echo $this->error->getCode(); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<meta name="robots" content="noindex" />
	<link rel="stylesheet" type="text/css" media="screen, handheld" href="<?php echo TemplateHelper::getUri(); ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" media="screen, handheld" href="<?php echo TemplateHelper::getUri(); ?>/css/error.css" />
</head>
<body>
	<div id="error-page">
		<div>
			<div id="error-intro">

				<h1 class="error-num"><?php echo $this->error->getCode(); ?></h1>
				<p class="error-text">
					<?php
						echo JText::_('TPL_HEAD_FRONT_ERROR_NOT_FOUND');
					?>
				</p>
				<p class="error-text-internal">
				 	(<?php echo $this->error->getMessage(); ?>)
				</p>
			</div>
			<div id="error-outro">
				<p>
					<?php
						echo JText::sprintf('TPL_HEAD_FRONT_ERROR_GOHOME', JUri::root(true));
					?>
				</p>
			</div>
		</div>
	</div>
</body>
</html>

<?php
/*
*	Module Chrome für Template HEAD
*/
use \Joomla\CMS\Helper;


//
//	Skelett für Section-Ausgabe
//
function makeSectionSkeleton(&$module, &$params, &$attribs) {

	$section = (object) array(
		"id" 			=> 'modsection-' . $module->id,
		"class" 		=> 'modsection ' . htmlspecialchars($params->get('moduleclass_sfx', '')),

		// Überschrift
		"headerTag" 	=> htmlspecialchars($params->get('header_tag', 'h1')),
		"headerClass"	=> 'section-header section ' . htmlspecialchars($params->get('header_class', '')),
		"headerId"		=> 'section-header-' . $module->id,
		"headerHtml" 	=> '',

		// Modulinhalt
		"moduleTag" 	=> htmlspecialchars($params->get('module_tag', 'div')),
		"moduleClass" 	=> 'moduletable section ' . htmlspecialchars($params->get('moduleclass_sfx', '')),
		"moduleId" 		=> 'moduletable-' . $module->id,
		"moduleHtml" 	=> '',

		// Andere Module an Position: position-module-<Modul-Id>
		"otherModules" 		=> Helper\ModuleHelper::getModules('position-module-' . $module->id),
		"otherModulesHtml" 	=> ''
	);

	// Andere Module
	foreach($section->otherModules as $i => $mod) 
	{
		if($mod->id !== $module->id) 
		{
			$section->otherModulesHtml .= Helper\ModuleHelper::renderModule($mod, array());
		}
	}

	// Header
	if($module->showtitle) 
	{
		$section->headerHtml = <<<HEADER
			<header class="$section->headerClass" id="$section->headerId">
				<$section->headerTag>
					$module->title
				</$section->headerTag>
			</header>
HEADER;
	}

	// Modul
	$section->moduleHtml = <<<MODULE
		<$section->moduleTag class="$section->moduleClass" id="$section->moduleId">
			$module->content
		</$section->moduleTag>
MODULE;

	return $section;
}


//
//	Section Module Chrome
//
function modChrome_section($module, &$params, &$attribs) {

	$section = makeSectionSkeleton($module, $params, $attribs);

	$html = <<<HTML
	<section id="$section->id" class="$section->class">
		<div class="section-inner">
			$section->headerHtml
			$section->moduleHtml
			$section->otherModulesHtml
		</div>
	</section>
HTML;

	echo $html;
}


//
//	Section mit 2 Spalten
//
function modChrome_sectioncols($module, &$params, &$attribs){

	$section = makeSectionSkeleton($module, $params, $attribs);
	
	$html = <<<HTML
	<section id="$section->id" class="$section->class columns">
		<div class="section-inner">
			$section->headerHtml
			<div class="section-row">
				<div class="section-col col0">
					$section->moduleHtml
				</div>
				<div class="section-col col1">
					$section->otherModulesHtml
				</div>
			</div>
		</div>
	</section>
HTML;

	echo $html;
}


//
//	Nackt
//
function modChrome_plain($module, &$params, &$attribs)
{
	if ($module->content)
	{
		echo $module->content;
	}
}

?>
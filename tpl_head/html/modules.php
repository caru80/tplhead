<?php
/*
	Module Chromes für Template HEAD
*/



/**
	Section Module Chrome
	Mit Header
*/
function modChrome_section($module, &$params, &$attribs){
	// Module
	$mtag   = htmlspecialchars($params->get('module_tag', 'div'));
	$class  = 'modsection ';
	$class .= htmlspecialchars($params->get('moduleclass_sfx',''));

	// Header
	$htag   = htmlspecialchars($params->get('header_tag', 'h3'));
	$hclass = htmlspecialchars($params->get('header_class',''));
	$hclass = $hclass != '' ? ' class="'.$hclass.'"' : '';

	// HTML
	$html  = '<section id="modsection-'.$module->id.'"'.($class != '' ? ' class="'.$class.'"' : '');
	$html .= '>';
	$html .= '<div>';

	$html .= $module->showtitle ? '<header class="section"><' . $htag . $hclass . '>' . $module->title . '</' . $htag . '></header>' : '';
	$html .= '<'.$mtag.' class="moduletable'.($class != '' ? ' '.$class : '').'">';
	$html .= $module->content;
	$html .= '</'.$mtag.'>';
	$html .= '</div></section>';

	echo $html;
}

/**
	Section mit Modulcontent ohne Alles
*/
function modChrome_sectionplain($module, &$params, &$attribs){
	// Module
	// $mtag   = htmlspecialchars($params->get('module_tag', 'div'));
	$class  = 'modsection plain ';
	$class .= htmlspecialchars($params->get('moduleclass_sfx',''));

	// Header
	$htag   = htmlspecialchars($params->get('header_tag', 'h3'));
	$hclass = htmlspecialchars($params->get('header_class',''));
	$hclass = $hclass != '' ? ' class="'.$hclass.'"' : '';

	// HTML
	$html  = '<section id="modsection-'.$module->id.'"'.($class != '' ? ' class="'.$class.'"' : '');
	$html .= '>';
	$html .= $module->showtitle ? '<header><' . $htag . $hclass . '>' . $module->title . '</' . $htag . '></header>' : '';
	$html .= $module->content;
	$html .= '</section>';

	echo $html;
}

/**
	Nackig
*/
function modChrome_plain($module, &$params, &$attribs)
{
	if ($module->content)
	{
		echo $module->content;
	}
}

/**
	Minimal
*/
function modChrome_minimal( $module, &$params, &$attribs  ){

	$mtag   = htmlspecialchars($params->get('module_tag', 'div'));
	$htag   = htmlspecialchars($params->get('header_tag', 'h3'));
	$mclass = htmlspecialchars($params->get('moduleclass_sfx',''));
	$mclass = $mclass != '' ? ' class="'. $mclass .'"' : '';
	$hclass = htmlspecialchars($params->get('header_class',''));
	$hclass = $hclass != '' ? ' class="' . $hclass . '"' : '';

	$html  = '<' . $mtag . $class . '>';
	$html .= $module->showtitle ? '<' . $htag . $hclass . '>' . $module->title . '</' . $htag . '>' : '';
	$html .= $module->content;
	$html .= '</' . $mtag . '>';

	echo $html;
}

?>

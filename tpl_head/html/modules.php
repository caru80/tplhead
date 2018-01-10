<?php
/*
	Module Chromes für Template HEAD
*/


/*
	Nackig
*/
function modChrome_minimal($module, &$params, &$attribs)
{
	if ($module->content)
	{
		echo $module->content;
	}
}

function modChrome_plain( $module, &$params, &$attribs  ){

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

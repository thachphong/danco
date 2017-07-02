<?php

function smarty_function_html_hidden($params, &$smarty)
{
	$names = array();
	
	if (isset($params['name'])) {
		$spl = split(',', $params['name']);
		foreach ($spl as $val) {
			$names[$val] = 1;
		}
	}
	if (isset($params['noval'])) {
		$spl = split(',', $params['noval']);
		foreach ($spl as $val) {
			$names[$val] = 0;
		}
	}
	
	$i = 0;
	$buf = '';
	
	foreach ($names as $key => $val) {
		if ($i > 0) {
			$buf .= "\n";
		}
		$buf .= '<input type="hidden"';
	    $buf .= ' name="' . htmlspecialchars($key) . '"';
		if ($val == 1) {
			$hiddenval = htmlspecialchars($smarty->getTemplateVars($key));
		} else {
			$hiddenval = '';
		}
	    $buf .= ' value="' . $hiddenval . '">';
		$i ++;
	}
    return $buf;
}

?>

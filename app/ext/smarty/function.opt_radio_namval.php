<?php

function smarty_function_opt_radio_namval($params, &$smarty)
{
    $name = $params['name'];
    $val = $params['value'];
    $chkVal = $smarty->getTemplateVars($name);
	
	if ($val == $chkVal) {
		$checked = ' checked ';
	} else {
		$chaecked = '';
	}
	return 'name="' . htmlspecialchars($name). '" value="' . htmlspecialchars($val) . '"' . $checked;
}

?>

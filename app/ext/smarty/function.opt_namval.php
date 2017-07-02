<?php

function smarty_function_opt_namval($params, &$smarty)
{
    $name = $params['name'];
    $var = $smarty->getTemplateVars($name);
	return 'name="' . htmlspecialchars($name). '" value="' . htmlspecialchars($var) . '"';
}

?>

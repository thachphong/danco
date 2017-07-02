<?php

function smarty_function_opt_name($params, &$smarty)
{
    $name = $params['name'];
	return 'name="' . htmlspecialchars($name). '"';
}

?>

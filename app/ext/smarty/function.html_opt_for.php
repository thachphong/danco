<?php

function smarty_function_html_opt_for($params, &$smarty)
{
	$names = array();
	
	if (isset($params['from'])) {
		$from = $params['from'];
	} else {
		$from = 1;
	}

	if (isset($params['to'])) {
		$to = $params['to'];
	} else {
		$to = 1;
	}
	
	if (isset($params['selected'])) {
		$selected = $params['selected'];
	} else {
		$selected = null;
	}
	
	$i = 0;

	$buf = '';
	
	for ($i = $from; $i <= $to; $i ++) {
		$buf .= '<option value="' . $i . '"';

		if ($i == $selected) {		
			$buf .= ' selected>';
		} else {
			$buf .= '>';
		}
	    $buf .= $i . '</option>';
	}
    return $buf;
}

?>

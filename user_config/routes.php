<?php

function acw_routes(&$level, &$model, &$action, &$dir_level)
{
	
	if (count($level) == 0) {
		return true;
	}

	if (count($level) == 1) {
		$model = array_shift($level);
		return true;
	}
	
	$model = array_shift($level);
	$action = array_shift($level);
	return true;
	
}


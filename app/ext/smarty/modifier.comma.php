<?php
/**
 * Smarty plugin
 */


/**
 * Smarty plugin
 *
 * Type:     modifier
 * Name:     comma
 * Date:     2006/09/19
 * Purpose:  convert [1000], [1,000]
 * Input:
 *         - contents = 
 *         - preceed_test = 
 * Example:  {$text|comma}
 * @link 
 * @version  1.0
 * @author   C-RISE
 * @param string
 * @return string
 */
function smarty_modifier_comma($string)
{
	return number_format($string);
}

/* vim: set expandtab: */

?>

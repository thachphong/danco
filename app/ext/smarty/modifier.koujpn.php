<?php
/**
 * Smarty plugin
 */
/**
 * Smarty plugin
 *
 * Type:     modifier
 * Name:     koujpn
 */
function smarty_modifier_koujpn($string)
{
	// あかがねで校Noを日本語で出力する
	// 他のプロジェクトに持って行っても意味がない
	if (isset($string) == false) {
		return '';
	}
	if ($string == '0') {
		return '-';
	}
	if ($string == '-1') {
		return '校了';
	}
	if ($string == '1') {
		return '初校';
	}
	if ($string == '2') {
		return '再校';
	}
	if ($string == '999') {
		return '校了';
	}

	return $string . '校';
}

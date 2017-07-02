<?php
/**
 * Smarty plugin
 */
/**
 * Smarty plugin
 *
 * Type:     modifier
 * Name:     transjpn
 */
function smarty_modifier_transjpn($string)
{
	// あかがねで翻訳ステータスを日本語で出力する
	// 他のプロジェクトに持って行っても意味がない
	if (isset($string) == false) {
		return '---';
	}
	if ($string == '1') {
		return '翻訳不要';
	}
	if ($string == '2') {
		return '未翻訳';
	}
	if ($string == '3') {
		return '翻訳中';
	}
	if ($string == '4') {
		return '翻訳済';
	}
	if ($string == '5') {
		return '先行';
	}
	return '?' . $string;
}

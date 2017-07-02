<?php
/**
 * SQLで使用する
 */
class SQL_lib
{
	/**
	 * likeのエスケープ
	 */
	public static function escape_like($text)
	{
		$wildcards = array('%', '_');
		$escape_pattern = '\\';
		foreach ($wildcards as $wildcard) {
			$text = str_replace($wildcard, $escape_pattern . $wildcard, $text);
		}
		return $text;
	}
}

/* ファイルの終わり */
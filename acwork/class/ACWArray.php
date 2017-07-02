<?php
/**
 * 配列を便利に操作するクラス
 *
 * 
 *
 * @category   ACWork
 * @copyright  2013 
 * @version    0.9
*/
class ACWArray
{
    /**
     * 連想配列の値をフィルタリングする（DB設定用）
	 * @param array $source 元の連想配列
	 * @param string $filter 残すキー値の配列
	 * @param string $add 加算する連想配列 sourceに同じkeyがあったらこちらが優先
	 * @return array フィルタリングされた配列
     */
	public static function filter($source, $filter, $add=null)
	{
		$new_array = array();
		foreach ($filter as $key) {
			$new_array[$key] = $source[$key];
		}
		if (is_null($add) == false) {
			$new_array = static::merge($new_array, $add);
		}
		return $new_array;
	}

    /**
     * 二つの連想配列を結合する phpの＋演算子と同様だが怖いので自分で作る
	 * @param array $array1 元の連想配列
	 * @param array $array2 元の連想配列　array1と同じkeyがあったらこちらが優先
	 * @return array 結合した配列
     */
	public static function merge($array1, $array2)
	{
		foreach ($array2 as $key => $val) {
			$array1[$key] = $val;
		}
		return $array1;
	}
}
/* ファイルの終わり */
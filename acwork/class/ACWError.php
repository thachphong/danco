<?php
/**
 * エラーを管理するクラスです。
 * 
 *
 * @category   ACWork
 * @copyright  2013 
 * @version    0.9
*/
class ACWError
{
	/**
	* エラーを追加
	*/
	public static function add($key, $info)
	{
		ACWCore::$my->error[] = array('key' => $key, 'info' => $info);
	}

	/**
	* エラーを削除
	*/
	public static function delete($key)
	{
		$max = count(ACWCore::$my->error);
		for ($i = 0; $i < $max; $i ++) {
			if (ACWCore::$my->error[$i]['key'] == $key) {
				array_splice(ACWCore::$my->error, $i, 1);
			}
		}
	}

	/**
	* エラー数
	*/
	public static function count()
	{
		return count(ACWCore::$my->error);
	}
	
	/**
	* 全消去
	*/
	public static function clear()
	{
		ACWCore::$my->error = array();
	}

	/**
	* 連想配列で返す
	*/
	public static function get_all()
	{
		$all = array();
		foreach (ACWCore::$my->error as $err) {
			$key = $err['key'];
			$all[$key] = $err['info'];
		}
		return $all;
	}

	/**
	* 配列で順番を保ちつつ返す
	*/
	public static function get_list()
	{
		return ACWCore::$my->error;
	}
}
/* ファイルの終わり */
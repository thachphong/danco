<?php
/**
 * ログを取るクラス
 *
 * @category   ACWork
 * @copyright  2013 
 * @version	0.9
*/
class ACWLog
{
	/*
	 * ログインユーザーごとにログを分ける
	 */
	private static $_user_file_suffix = "";

	/**
	 * エラーログを取る
	 * @param string $suffix ファイル名の末尾
	 * @param string $msg メッセージ
	 */
	public static function err($file_suffix, $e)
	{
		$msg = $e->getMessage();
		$msg .= "\n" . var_export(ACWCore::$my, true);
		$msg .= "\n" . $e->getTraceAsString();
		static::write_file($file_suffix, $msg);
	}

	/**
	 * エラーログを取る
	 * @param string $suffix ファイル名の末尾
	 * @param string $msg メッセージ
	 */
	public static function debug($msg)
	{
		$msg = print_r($msg, true);
		static::write_file("Debug", $msg);
	}

	/**
	 * SQLエラーログを取る
	 * @param string $suffix ファイル名の末尾
	 * @param string $msg メッセージ
	 */
	public static function sql_err($msg)
	{
		static::write_file('SQLERROR', $msg);
	}

	/**
	 * SQL文だけのログを取る
	 * @param string $filename ファイル名
	 * @param string $msg メッセージ
	 */
	public static function sql_statement($sql)
	{
		static::write_file('SQLLOG', $sql);
	}

	/**
	 * SQLパラメタけのログを取る
	 * @param string $filename ファイル名
	 * @param string $msg メッセージ
	 */
	public static function sql_param($param)
	{
		static::write_file('SQLLOG', var_export($param, true));
	}

	/**
	* SQL文とパラメタの両方のログを取る
	* @param string $filename ファイル名
	* @param string $msg メッセージ
	*/
	public static function sql($sql, $param)
	{
		$line = $sql . "\r\n";
		if (is_null($param) == false) {
			$line .= var_export($param, true);
		}
		static::write_file('SQLLOG', $line);
	}

	/**
	 * ログファイル出力
	 * @param string $suffix ファイル名の末尾
	 * @param string $str メッセージ
	 */
	public static function write_file($suffix, $str)
	{
		$filepath = static::make_filepath($suffix);
		if (($fp = @fopen($filepath, "a")) === false) {
			return;
		}

		if (!flock($fp, LOCK_EX)) {
			@fclose($fp);
			return;
		}

		if (fwrite($fp, static::make_header() . $str . "\n") === false) {
			@flock($fp, LOCK_UN);
			@fclose($fp);
			return;
		}

		if (!fflush($fp)) {
			@flock($fp, LOCK_UN);
			@fclose($fp);
			return;
		}

		if (!flock($fp, LOCK_UN)) {
			@fclose($fp);
			return;
		}

		if (!fclose($fp)) {
			return;
		}
	}
        
        //Add Start - NBKD-765 - Trung VNIT - 2014/11/20
    public static function write_log($filepath, $str, $lv = 'OK')
	{
		$filepath = mb_convert_encoding($filepath, 'sjis-win', 'UTF-8');
		if (($fp = @fopen($filepath, "a")) === false) {
			return;
		}

		if (!flock($fp, LOCK_EX)) {
			@fclose($fp);
			return;
		}

		if (fwrite($fp, /*static::make_header_custom($lv) .*/ $str . "\r\n") === false) {
			@flock($fp, LOCK_UN);
			@fclose($fp);
			return;
		}

		if (!fflush($fp)) {
			@flock($fp, LOCK_UN);
			@fclose($fp);
			return;
		}

		if (!flock($fp, LOCK_UN)) {
			@fclose($fp);
			return;
		}

		if (!fclose($fp)) {
			return;
		}
	}
        
        protected static function make_header_custom($lv)
	{
                $txt = '[情報]';
                if($lv == 'ERROR'){
                    $txt = '[警告]';
                }
		return date('Y/m/d H:i') . ' '.$txt.' ';
	}
	//Add End - NBKD-765 - Trung VNIT - 2014/11/20
	/**
	 * ユーザー用ファイル末尾設定
	 * ログインユーザー毎にログファイルを分ける
	 * @param string $suffix ファイル名の末尾
	 */
	public static function set_user_suffix($suffix)
	{
		static::$_user_file_suffix = $suffix;
	}

	/**
	 * ファイル名作成
	 * @param string $suffix ファイル名の末尾
	 */
	protected static function make_filepath($suffix)
	{
		if (static::$_user_file_suffix != '') {
			$suffix .= '_' . static::$_user_file_suffix;
		}

		return ACW_LOG_DIR . '/' . date('Ymd') . '_' . $suffix . '.log';
	}

	/**
	 * ファイルのヘッダ部分を作成
	 */
	protected static function make_header()
	{
		return date('Y/m/d H:i:s') . ' access ip -> ' . $_SERVER["REMOTE_ADDR"] . "\n";
	}
        
	/**
	 *
	 * デバッグ用
	 *
	 */
	public static function debug_var($predix, $var)
	{
		static::write_file($predix, var_export($var, true));
	}

}

/**
 * ACWLog::debug のエイリアス
 * @param 出力内容 $obj
 */
function var_log($obj)
{
	ACWLog::debug($obj);
}
/* ファイルの終わり */
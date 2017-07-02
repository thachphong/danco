<?php
/**
 * コアファイル
 *
 * 変更してはいけません
 *
 * @category   ACWork
 * @copyright  2013 
 * @version    0.9
*/
class ACWCore
{
	/**
	* 環境設定
	*/
    protected $config = array();

	/**
	* グローバルな値
	*/
    protected $gvar = array();

	/**
	* 発生したエラー ACWErrorからアクセス
	*/
    public $error = array();

	/**
	* 自分のインスタンス
	*/
    public static $my;
	
	/**
	* dbコネクション
	*/
    public static $db_conn = array();
	
	/**
	* オブジェクトストレージコネクション
	* Add - miyazaki Argo - NBKD-1033 - 2015/02/27
	*/
	public static $obst_conn = array();

		/**
	* コントローラー
	*/
    public static $ctl;

	/**
	* 変数アクセス
	*/
    public static function get_var($name)
	{
		if (isset(static::$my->gvar[$name])) {
			return static::$my->gvar[$name];
		}
		return null;
	}
	
	/**
	* 変数アクセス
	*/
    public static function set_var($name, $val)
	{
		static::$my->gvar[$name] = $val;
	}
	
	/**
	* 動作環境設定
	*/
    public static function get_config($name)
	{
		if (isset(static::$my->config[$name])) {
			return static::$my->config[$name];
		}
		return null;
	}
	/**
	* 動作環境獲得
	*/
    public static function set_config($name, $val)
	{
		static::$my->config[$name] = $val;
	}

	/**
	* 初期化
	*/
    public static function init()
    {
		static::$my = new ACWCore();
	}
	
	/**
	* コントローラーを作る
	*/
    public static function create_controller()
    {
		$ctl_name = static::get_config('controller_class_name');
		static::$ctl = new $ctl_name;
	}

	/**
	* 実行
	*/
    public static function acwork($commodel=null)
    {
		/**
		* コントローラーを作る
		*/
		static::create_controller();
		/**
		* メイン実行
		*/
		static::$ctl->main($commodel);
		exit;
    }
}
/* ファイルの終わり */
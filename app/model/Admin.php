<?php
/**
 * Indexのサンプル
*/
class Admin_model extends ACWModel
{
	/**
	* 共通初期化
	*/
	public static function init()
	{
		Login_model::check_admin();	// ログインチェック
	}

	/**
	* インデックス トップページ
	*/
	public static function action_index()
	{
		//return ACWView::redirect(ACW_BASE_URL . 'home');
		return ACWView::template_admin('index.html');
	}
}
/* ファイルの終わり */
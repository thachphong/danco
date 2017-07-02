<?php
/**
 * ユーザ管理
 *
*/
class Home_model extends ACWModel
{
	/**
	* 共通初期化
	*/
	public static function init()
	{
		Login_model::check();	// ログインチェック
	}
	
	public static function validate($action, &$param)
	{		
		return true;
	}

	/**
	* 初期表示
	*/
	public static function action_index()
	{
		$param = self::get_param(array(
			'search_donvi_name'
		));
		$model = new Donvi_model();
		$rows = $model->get_donvi_rows($param);		
		return ACWView::template('donvi.html', array(
			'data_rows' => $rows,			
			'search_donvi_name'=>$param['search_donvi_name']
		));
	}
	public function get_don_rows($param)
	{
		$sql = " select * from don	";
		
		if (isset($param['s_donvi_name'])) {
			$sql_param = array(
					'donvi_name' =>  '%' . SQL_lib::escape_like($param['s_donvi_name']) . '%'
				);
			$sql .= " WHERE lower(t.donvi_name) like lower(:donvi_name) ";
		} else {
			$sql_param = array();
		}
		
		$sql .= "
			ORDER BY
				t.donvi_id
		";		
		return $this->query($sql, $sql_param);
	}
	
	
}
/* ファイルの終わり */
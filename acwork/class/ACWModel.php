<?php

class ACWModel extends ACWDB
{
	private static $validate_count = 0;

	public static function init()
	{

	}

	/**
	 * パラメタを持ってきます
	 * @param array $param パラメタ名の配列
	 * @param string $validate_id validateメソッドに渡す識別子
	 */
	protected static function get_param($param_key)
	{
		/* if (is_null($param_key)) {
		  $param_key = array();
		  } acw_url は　必ずではなくする */
		$param = ACWCore::$ctl->set_param_all($param_key);

		self::$validate_count++;
		if (self::$validate_count <= 1) { // validate内のget_paramで呼ばれないように
			$model_name = ACWCore::get_var('model_name');
			$validate_action = ACWCore::get_var('action');
			// call_user_funcでなく、call_user_func_arrayなのは引数が参照で渡せないから
			$ret = call_user_func_array(array($model_name, 'validate'), array($validate_action, &$param));
			ACWCore::set_var('last_validate', $ret);
			ACWCore::set_var('last_param', $param);
		}
		self::$validate_count--;
		return $param;
	}

	protected static function get_validate_result()
	{
		return ACWCore::get_var('last_validate');
	}

	public static function validate($validate_action, &$param)
	{
		return 'no validate!';
	}
	public static function convert_vi_to_en($str) {
	     $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
	     $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
	     $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
	     $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
	     $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
	     $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
	     $str = preg_replace("/(đ)/", 'd', $str);    

	     $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
	     $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
	     $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
	     $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
	     $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
	     $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
	     $str = preg_replace("/(Đ)/", 'D', $str);
	     return trim(strtolower($str));
	}
	public function convert_string_to_number($str){
		return str_replace(',','',$str);
	}  
	public static function currency_format($str){
		if(strlen($str)> 0){
			return number_format($str,0,".",",");
		}
		return "";
	}
	public static function get_youtube_key($str){
		if(strlen($str)> 0){
			$str = str_replace('https://youtu.be/','',$str);
			$str = str_replace('https://www.youtube.com/watch?v=','',$str);
			$str = str_replace('&feature=youtu.be','',$str);
			
			return $str ;//number_format($str,0,",",".");
		}
		return "";
	}
}

<?php

class Index_model extends ACWModel
{
	
	public static function init()
	{
		//Login_model::check();	
		$date= date('Ymd');
		if($date > '20170718'){
			echo 'Trang web của bạn đã bị khóa do chưa thanh toán tiền thiết kế web!';
			die;
		}
		
		//if()
	}
	
	public static function action_index()
	{		
		return ACWView::template('index.html');
	}
}

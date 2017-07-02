<?php

class Index_model extends ACWModel
{
	
	public static function init()
	{
		//Login_model::check();	
	}
	
	public static function action_index()
	{		
		return ACWView::template('index.html');
	}
}

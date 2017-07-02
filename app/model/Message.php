<?php
	  
class Message_model extends ACWModel
{
	
	public static function init()
	{
		Login_model::check();	
	}
	public function get_all($screen){	
		$lang = ACWSession::get('lang');
		$lang = 1;		
		$sql = "select msg_no, des from message_lang where screen=:screen and lang_key =:lang ";
		return $this->query($sql,array('screen'=>$screen,'lang'=>$lang));		
	}
	public static function get_message($creen){
		$db = new Message_model();
		$data = $db->get_all($creen);
		$res = array();
		
		foreach($data as $item){			
			$res[$item['msg_no']] = $item['des'];
		}		
		return $res;		
	}
	public static function get_msg($msg_no){
		$db = new Message_model();
		$data = $db->get_message_row($msg_no);
		$res = array();
		$lang = ACWSession::get('lang');
		$column ='des_vn';
		if(isset($lang)&& $lang == 2){
			$column='des_en';
		}
		if(count($data)>0)
		{
			return $data[0][$column];
		}
		return '';		
	}
	public static function action_change()
	{  
		$lang = ACWSession::get('lang');
		if($lang == 1){
			ACWSession::set('lang',2);
		}else{
			ACWSession::set('lang',1);
		}
		$url = $_SERVER['HTTP_REFERER'];
		header("Location: $url");
		exit;
		//return ACWView::json('sssss');
	}
	public static function action_index()
	{  
		$param = self::get_param(array(
			's_screen',
            's_des'
		));
		$model = new Message_model();
		$rows = $model->get_message_rows($param);		
		return ACWView::template('message.html', array(
			'data_rows' => $rows,			
			'data_search'=>$param,
			'screen' => $model->get_creen()
		));
	}
	public function get_message_row($msg_no){
		$sql = "select * from message_lang 
				where msg_no = :msg_no	";
		$sql_param['msg_no'] =$msg_no;		
		return $this->query($sql,$sql_param);
	}
	public function get_message_rows($param){
		$sql = "select l.*,s.screen_name from message_lang l
				LEFT JOIN screen s on s.screen_no = l.screen
				where screen like :screen
				and (des_vn like :des or des_en like :des) ";
		$sql_param['screen'] ='%';
		$sql_param['des'] ='%';
		if(isset($param['s_screen']) && strlen($param['s_screen'])> 0){
			$sql_param['screen'] ='%'.$param['s_screen'].'%';
		}
		if(isset($param['s_des']) && strlen($param['s_des'])> 0){
			$sql_param['des'] ='%'.$param['s_des'].'%';
		}
		return $this->query($sql,$sql_param);
	}
	public static function action_update()
	{
		$params = self::get_param(array(			
			'add_screen',
			'add_message_no',
            'add_des_vn',
            'add_des_en',
            'edit_id',
            'edit_screen',
            'edit_message_no',
            'edit_des_vn',
            'edit_des_en'
		));
		
		$model = new Message_model();
		$obj = $model->update($params);		
		
		if (ACWError::count() <= 0) {
		    $result['status'] = 'OK';
		} else {
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		}

		return ACWView::json($result);
	}
	public function update($params)
	{
		$this->begin_transaction();
		
		$login_info = ACWSession::get('user_info');
				
		$sql_params = array(
			//'id' => $params['may_id'],
			//'screen' => $params['screen'],    
			//'msg_no' => $params['may_no'],    
            //'des_vn' => $params['des_vn'],           
            //'des_en' => $params['des_en'],
            'add_user_id'=>$login_info['user_id']
		);		
		if (isset($params['add_message_no']) && count($params['add_message_no']) > 0) {		
			$sql = "
				INSERT INTO
					message_lang
				(	msg_no
					, screen
                    , des_vn
					, des_en
					, add_user_id
					, add_datetime                                        
				) VALUES (
					  :msg_no 
					, :screen 
                    , :des_vn					
                    , :des_en
					, :add_user_id 
					, NOW()
				);
			";	
			$msg_no = $params['add_message_no'];		
			$creen = $params['add_screen'];
			$des_vn = $params['add_des_vn'];
			$des_en = $params['add_des_en'];			
			foreach($msg_no as $key => $val){
				$sql_params['msg_no'] =  $val;	
				$sql_params['screen'] =  $creen[$key];	
				$sql_params['des_vn'] =  $des_vn[$key];	
				$sql_params['des_en'] =  $des_en[$key];					
				$res = $this->get_msg_no_count($sql_params);
				if ($res['cnt'] > 0) {
					ACWError::add('may_no', 'Mã Thông báo đã có, Vui lòng sử dụng mã khác !');
					return;
				}
				$this->execute($sql, $sql_params);	
			}
			
		} 
		if (isset($params['edit_id']) && count($params['edit_id']) > 0) {									
			
			$sql = "
				UPDATE
					message_lang
				SET
				
					 des_vn = :des_vn
					, des_en = :des_en
					, add_user_id = :add_user_id
					, add_datetime = NOW()
				WHERE	id = :id
			";
			$edit_id = $params['edit_id'];	
			$edit_screen = $params['edit_screen'];	
			$edit_msg_no = $params['edit_message_no'];
			$edit_des_vn = $params['edit_des_vn'];
			$edit_des_en = $params['edit_des_en'];		
			$sql_upd['add_user_id']	 = $login_info['user_id'];
			foreach($edit_id as $key => $val){
				$sql_upd['id'] =  $val;	
			//	$sql_upd['screen'] =  $creen[$key];	
				$sql_upd['des_vn'] =  $edit_des_vn[$key];	
				$sql_upd['des_en'] =  $edit_des_en[$key];					
				/*$res = $this->get_msg_no_count($sql_upd);
				if ($res['cnt'] > 0) {
					ACWError::add('may_no', 'Mã Thông báo đã có, Vui lòng sử dụng mã khác !');
					return;
				}*/
				$this->execute($sql, $sql_upd);	
			}
		}
		
		$this->commit();
		
		return true;
	}
	private function get_msg_no_count($param)
	{
		$sql = "
			SELECT
				COUNT(*) AS cnt
			FROM
				message_lang
			WHERE
				msg_no = :msg_no
		";
		
		$filter['msg_no'] = $param['msg_no'];
		$rows = $this->query($sql, $filter);
		return $rows[0];
	}
	public function get_creen(){
		$sql ="Select * from screen";
		return $this->query($sql);
	}
}

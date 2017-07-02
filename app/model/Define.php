<?php
/**
 * Define
*/
class Define_model extends ACWModel
{
	
	public static function init()
	{
		Login_model::check_admin();	
	}
	
	public static function action_index()
	{		
		return ACWView::template_admin('index.html');
	}
	public static function action_email()
	{	
		$db = new Define_model();
		$data = $db->get_define(DEFINE_KEY_EMAIL);
		if($data == NULL){
			$data['define_val']='';
			$data['define_key']=DEFINE_KEY_EMAIL;
			$data['define_name']='Email';
			$data['define_id']='';
		}	
		return ACWView::template_admin('define.html',$data);
	}
	public static function action_fanpage()
	{	
		$db = new Define_model();
		$data = $db->get_define(DEFINE_KEY_FACEBOOK_PAGE);
		if($data == NULL){
			$data['define_val']='';
			$data['define_key']=DEFINE_KEY_FACEBOOK_PAGE;
			$data['define_name']='Fan page';
			$data['define_id']='';
		}	
		return ACWView::template_admin('define.html',$data);
	}
	public static function action_update(){
		$param = self::get_param(array(
			  'define_id',	
			  'define_key',
			  'define_val',
			  'define_name',				
			));
		
		$result = array('status' => 'OK');
		$result['status'] = 'OK';	
		$result['msg'] = 'Cập nhật thành công!';		
		$db = new Define_model();
		$msg = $db->check_validate_update($param);
		if($msg == ""){
			if(strlen($param['define_id'])==0){
				$db->insert($param);
			}else{
				$db->update($param);
			}
		}else{
			$result['status'] = 'ER';	
			$result['msg'] = $msg;
		}
		return ACWView::json($result);
	}
	public function check_validate_update(&$param){
		if(strlen($param['define_val'])== 0){
			return "Bạn chưa nhập giá trị !";
		}		
		return "";
	}
	private function insert($param){		
		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['user_id'];
		
		$sql = "INSERT INTO define
					(					
					  define_key,
					  define_val,	
					  define_name,					 
					  upd_date,
					  upd_user		
					)
				VALUES
					(					  
					  :define_key,
					  :define_val,
					  :define_name,
					  now(),
					  :user_id					
					)
				";
		 
		$this->execute($sql, ACWArray::filter($param, array(					  		  
					  'define_key',
					  'define_val',	
					  'define_name'	,			 				  
					  'user_id'
					)));		
		return TRUE;
	}
	protected function update($param)
	{				
		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['user_id'];		
		$sql = "update define
					set define_val = :define_val,					  				 
					  upd_date = now(),
					  upd_user = :user_id	
					where define_id = :define_id
				";
		
 		$sql_par = ACWArray::filter($param, array(
 					'define_id',
					'define_val',					  					  
					  'user_id',
					));			
		$this->execute($sql,$sql_par );
		return TRUE;	
	}
	public function get_define($def_key){
		$sql ="select * from define where define_key = :define_key";
		$res = $this->query($sql,array('define_key'=>$def_key));
		if(count($res) > 0){
			return $res[0];
		}
		return NULL;
	}
	public function get_define_all(){
		$sql ="select define_key,define_val from define ";
		$res = $this->query($sql);		
		return $res;		
	}
}

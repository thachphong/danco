<?php

class Page_model extends ACWModel
{	
	public static function init()
	{
		//Login_model::check();	
	}
		
	public static function action_index()
	{
		$param = self::get_param(array(
			'search_user_name'
		));
		$model = new Page_model();
		$rows = $model->get_page_all();		
		return ACWView::template_admin('page.html', array(
			'pages' => $rows			
		));
	}
	public function get_page_all(){
		$sql="select page_id,
				  page_no,
				  page_name,				 
				  content,
				  del_flg,				
				  img_path,
				(case when disp_home= 1 then 'hiện' else 'không' end) disp_home
				from page
				";
		return $this->query($sql);
	}
	public static function action_new()
	{
		$param = self::get_param(array('parent_id'));
		$ctg = new Category_model();
		$param['page_id'] = null;
		$param['page_no'] = null;
		$param['page_name'] = "";
		$param['img_path'] = NULL;
		$param['disp_home'] = NULL;
		$param['des'] = "";
		$param['content'] = "";
		$param['del_flg'] = "";		
		return ACWView::template_admin('page/edit.html', $param);		
	}
	public static function action_update(){
		$param = self::get_param(array(
			  'page_id',
			  'page_no',
			  'page_name',			
			  'content',
			  'add_date',
			  'add_user',
			  'upd_date',
			  'upd_user',			 
			  'del_flg',
			  'img_path',
			  'disp_home'
			));
		
		$result = array('status' => 'OK');
		$result['status'] = 'OK';	
		$result['msg'] = 'Cập nhật thành công!';		
		$db = new Page_model();
		$msg = $db->check_validate_update($param);
		if($msg == ""){
			if(strlen($param['page_id'])==0){
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
		if(strlen($param['page_name'])== 0){
			return "Bạn chưa nhập tên trang !";
		}
		$param['page_no'] =str_replace(' ','-', ACWModel::convert_vi_to_en($param['page_name']));
		
		$page_no = $param['page_no'];
		if(strlen($param['page_id']) == 0){
			$sql = "select page_id from page	where page_no = :page_no";
			$row = $this->query($sql, array ('page_no' => $param['page_no'] ));
			//$row = $this->get_ctg_byno($param['page_no']);
			$i = 0;
			while($row != null){
				$i++;
				$param['page_no'] = $page_no.'-'.$i;
				$row = $this->query($sql, array ('page_no' => $param['page_no'] ));
			}
		}else{
			$sql = "select page_id from page	where page_no = :page_no	and page_id <> :page_id";
			$res = $this->query($sql, array ('page_no' => $param['page_no'] , 'page_id'=>$param['page_id']));
			$i = 0;
			while(count($res) > 0){
				$i++;
				$param['page_no'] = $page_no.'-'.$i;
				$res = $this->query($sql, array ('page_no' => $param['page_no'] , 'page_id'=>$param['page_id']));
			}
		}
		/*if(strlen($param['img_path']) > 0){
		}*/
		return "";
	}
	private function insert($param){
		$this->begin_transaction();

		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['user_id'];
		//$param['ctg_no'] =str_replace(' ','-', ACWModel::convert_vi_to_en( $param['ctg_name']));
		$sql = "INSERT INTO page
					(					
					  page_no,
					  page_name,
					  content,
					  add_date,
					  add_user,
					  upd_date,
					  upd_user,					 
					  del_flg,
					  img_path,
					  disp_home
					)
				VALUES
					(					  
					  :page_no,
					  :page_name,
					  :content,
					  now(),
					  :user_id,
					  now(),
					  :user_id,				 
					  :del_flg,
					  :img_path,
					  :disp_home
					)
				";
		 
		$this->execute($sql, ACWArray::filter($param, array(					  		  
					  'page_no',
					  'page_name',
					  'content',					  
					  'user_id',					 
					  'del_flg',
					  'content',
					  'img_path',
					  'disp_home'
					)));
		$this->commit();
		return TRUE;
	}
	protected function update($param){
		
		$this->begin_transaction();

		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['user_id'];		
		$sql = "update page
					set page_no = :page_no,
					  page_name = :page_name,
					  content = :content,					 
					  upd_date = now(),
					  upd_user = :user_id,					 
					  del_flg = :del_flg,
					  disp_home= :disp_home,
					  img_path = :img_path
					where page_id = :page_id
				";
		
 		$sql_par = ACWArray::filter($param, array(
 					'page_id',
					'page_no',
					  'page_name',
					  'content',					  
					  'user_id',					 
					  'del_flg',
					  'content',
					  'img_path',
					  'disp_home'
					));
		$this->execute($sql,$sql_par );			
		
		$this->commit();
		return TRUE;	
	}
	public function get_page_byno($page_no){
		$sql=" select page_no,
					  upper(page_name) page_name,
					  content
			  from page where page_no = :page_no";
		$res = $this->query($sql ,array('page_no'=>$page_no));
		if(count($res)> 0){
			return $res[0];
		}
		return null;
	}
	
	public static function action_delete()
	{
		$param = self::get_param(array(
			'acw_url'
		));
		$model = new Page_model();
			
		$result['status']="OK";
		if(!isset($param['acw_url'][0]) || strlen($param['acw_url'][0])== 0 ){
			$result['status']="NOT";
			$result['msg']= $msg;
		}else
		{
			$model->delete($param['acw_url'][0]);	
		}
		
		return ACWView::json($result);
	}
	private function delete($page_id){
		$sql ="delete from page where page_id = :page_id"; 
		return $this->execute($sql,array('page_id' =>$page_id));
		
	}
	public static function action_edit()
	{
		$param = self::get_param(array('acw_url'));	
		$db = new Page_model();
		$result = $db->get_page_info($param['acw_url'][0]);		
		return ACWView::template_admin('page/edit.html', $result);
	}
	public function get_page_info($page_id){
		$sql=" select page_id,
				  page_no,
				  page_name,				 
				  content,
				  add_date,
				  add_user,
				  upd_date,
				  upd_user,				  
				  del_flg,
				  disp_home,
				  img_path
			  from page where page_id = :page_id";
		$res = $this->query($sql ,array('page_id'=>$page_id));
		if(count($res)> 0){
			return $res[0];
		}
		return null;
	}
	public function get_page_list(){
		$sql="select page_no,page_name from page where del_flg= 0";
		return $this->query($sql);
	}
	public static function action_v()
	{
		$param = self::get_param(array(
			'acw_url'
		));
		$model = new Page_model();
		$rows = $model->get_page_byno($param['acw_url'][0]);		
		return ACWView::template('page.html',  $rows,FALSE);
	}
	public static function get_page_home(){
		$db = new Page_model();
		return $db->get_page_list_home(3);
	}
	public function get_page_list_home($limit){
		$sql="select page_no,page_name,img_path from page where del_flg= 0 and disp_home = 1 limit $limit";
		return $this->query($sql);
	}
	
}

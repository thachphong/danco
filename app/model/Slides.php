<?php

class Slides_model extends ACWModel
{
	
	public static function init()
	{
		Login_model::check();	
	}
	
	public static function action_list()
	{	
		$param = self::get_param(array('acw_url'));	
		$db = new Slides_model();
		$param['slides']=$db->get_slides_all($param['acw_url'][0]);
		$param['title'] ='slides';
		$param['banner_flg'] = $param['acw_url'][0];
		if($param['banner_flg'] ==1){
			$param['title'] ='banner';
		}
		return ACWView::template_admin('slides.html', $param);
	}
		
	public static function action_new()
	{
		$param = self::get_param(array('banner_flg'));	
		//$ctg = new Category_model();
		$param['slide_id'] = null;	
		$param['del_flg'] = 0;
		$param['img_path'] = null;	
		$param['link_page'] = null;	
		$param['banner_flg'] = $param['banner_flg'];
		if(strlen($param['banner_flg'])==0){
			$param['banner_flg'] = 0;
		}
		$param['sort'] = 1;
		return ACWView::template_admin('slides/edit.html', $param,FALSE);
	}
	
	public static function action_edit()
	{
		 $param = self::get_param(array('acw_url'));	
		 if (self::get_validate_result() == false)  {
		 	ACWError::add('acw_url', 'Tham số không hợp lệ');
		 }
		
		$db = new Slides_model();
		$result = $db->get_slide_info($param['my_id']);		
		return ACWView::template_admin('slides/edit.html', $result);
	}
	

	public static function action_update()
	{
		
		$param = self::get_param(array(
			'slide_id'
			,'img_path'				
			,'del_flg'	
			,'link_page'
			,'banner_flg'		
			));
		
		$result = array('status' => 'OK');
		$result['status'] = 'OK';	
		$result['msg'] = 'Cập nhật thành công!';		
		$db = new Slides_model();
		$msg = $db->check_validate_update($param);
		if($msg ==""){
			if(strlen($param['slide_id'])==0){
				$ctg_id = $db->insert($param);
			}else{
				$db->update($param);
			}
		}else{
			$result['status'] = 'ER';	
			$result['msg'] = $msg;
		}
		return ACWView::json($result);
	}
    
	public static function action_delete()
	{
		$param = self::get_param(array('acw_url'));	
		if (self::get_validate_result() == false)  {
			ACWError::add('acw_url', 'Tham số không hợp lệ');
		}
		$db = new Slides_model();
		$msg = $db->check_before_delete($param['my_id']);
		$result['status']="OK";
		if($msg== ""){
			$db->delete_slide($param['my_id']);
		}else{
			$result['status']="NOT";
			$result['msg']= $msg;
		}
		
		return ACWView::json($result);
	}
	
	
	public static function validate($action, &$param)
	{
		switch ($action) {
		case 'new':
			/*if (count($param['acw_url']) != 1) {
				return false;
			}
			// 一個目は親ID
			$param = array(
				'parent_id' => $param['acw_url'][0]
				,'my_id' => ''
				);*/
			break;
		case 'edit':
			if (count($param['acw_url']) != 1) {
				return false;
			}
			
			$param = array(
				'parent_id' => ''
				,'my_id' => $param['acw_url'][0]
				);
			break;
		case 'delete':
			if (count($param['acw_url']) != 1) {
				return false;
			}		
			$param = array(
				'my_id' => $param['acw_url'][0]
				);
			break;			
		}
		return true;
	}
		
	
	public function check_category_id($param)
	{
		$this->begin_transaction();

		$sel_param = ACWArray::filter($param, array('parent_id','folder_name'));
		$sql = "SELECT COUNT(*) cnt FROM folder WHERE parent_folder_id=:parent_id and folder_name = :folder_name ";
		if (isset($param['my_id'])) {
			$sel_param['folder_id'] = $param['my_id'];
			$sql .= ' AND folder_id <> :folder_id';	
		}
		$result = $this->query($sql, $sel_param);
		$this->commit();

		if ($result[0]['cnt'] > 0) {
			ACWError::add('folder_name', 'Tên Thư mục này đã có, vui lòng nhập tên khác !');
			return false;
		}
		return true;
	}
	public function insert($param)
	{
		$this->begin_transaction();

		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['user_id'];
		//$param['ctg_no'] =str_replace(' ','-', ACWModel::convert_vi_to_en( $param['ctg_name']));
		$sql = "INSERT INTO slides
					(
					img_path
					,link_page
					,del_flg
					,banner_flg
					,add_date
					,add_user
					,upd_date
					,upd_user					
					)
				VALUES
					(
					:img_path	
					,:link_page				
					,:del_flg
					,:banner_flg					
					,now()
					,:user_id
					,now()
					,:user_id					
					)
				";
		
 
		$this->execute($sql, ACWArray::filter($param, array(
					'img_path'
					,'del_flg'					
					,'user_id'	
					,'link_page'
					,'banner_flg'				
					)));			
		//$result = $this->query("SELECT LAST_INSERT_ID() AS menu_id");			
		//$new_id = $result[0]['menu_id'];	
		$this->commit();
		return TRUE; //$new_id;
	}
	
	public function update($param)
	{
		$this->begin_transaction();

		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['user_id'];
		//$param['ctg_no'] =str_replace(' ','-', ACWModel::convert_vi_to_en( $param['ctg_name']));
		$sql = "update slides
					set img_path = :img_path
					,del_flg = :del_flg	
					,link_page= :link_page				
					,upd_date =  now()
					,upd_user =:user_id					
					where slide_id = :slide_id
				";
		
 
		$this->execute($sql, ACWArray::filter($param, array(
					'slide_id'
					,'img_path'					
					,'user_id'
					,'del_flg'	
					,'link_page'			
					)));			
		
		$this->commit();
		return TRUE;
	}
	
	public function delete_slide($slide_id)
	{
		$this->execute("delete from slides where slide_id = :slide_id",
				array('slide_id' => $slide_id));		
	}

	
	
	public function get_slides_all($banner_flg= 0)
	{
		$sql = "select * from slides where banner_flg =$banner_flg";
		return $this->query($sql);
	}
	
	public function get_slide_info($slide_id)
	{
		$r = $this->query("
			select slide_id
					,img_path				
					,del_flg
					,link_page
					,'banner_flg'
				from slides
				where slide_id = :slide_id
			", array ('slide_id' => $slide_id));
		if(count($r) >0)
			return $r[0];
		else
			return null;
	}	
	
    public function check_before_delete($ctg_id){
		return "";
	}
	public function check_validate_update($param){
		if(strlen($param['img_path'])== 0){
			return "Bạn chưa upload hình !";
		}
		//$ctg_no =str_replace(' ','-', ACWModel::convert_vi_to_en($param['ctg_name']));
		/*if(strlen($param['menu_id']) == 0){
			$res = $this->query("select menu_id									
									from menu
									where lower(menu_name) = lower(:menu_name)									
								", array ('menu_name' => $param['menu_name'] ));
			if(count($res) > 0){
				return "Tên menu này đã có, vui lòng nhập menu khác !";
			}
		}else{
			$res = $this->query("select menu_id									
									from menu
									where lower(menu_name) = lower(:menu_name)
									and menu_id <> :menu_id
								", array ('menu_name' => $param['menu_name'] , 'menu_id'=>$param['menu_id']));
			if(count($res) > 0){
				return "Tên menu này đã có, vui lòng nhập menu khác !";
			}
		}*/
		return "";
	}
	public static function get_slides(){
		$db = new Slides_model();
		return $db->get_slides_head(0);
	}
	public function get_slides_head($banner_flg){
		$sql = "select slide_id,img_path,link_page from slides where del_flg = 0 and banner_flg=$banner_flg";
		return $this->query($sql);
	}
	
}

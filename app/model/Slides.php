<?php

class Slides_model extends ACWModel
{
	
	public static function init()
	{
		Login_model::check();	
	}
	
	public static function action_index()
	{		
		$db = new Slides_model();
		$param['slides']=$db->get_slides_all(TRUE);
		return ACWView::template_admin('slides.html', $param);
	}
	
	public static function action_new()
	{
		$param = self::get_param(array('parent_id'));
		//$ctg = new Category_model();
		$param['slide_id'] = null;	
		$param['del_flg'] = 0;
		$param['img_path'] = null;	
		//$param['ctg_list'] = addslashes(json_encode($ctg->get_category_rows()));
	//	$param['page_list'] = null;//$ctg->get_category_all();
		if(strlen($param['parent_id'])==0){
			$param['parent_id'] = 0;
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
		//$ctg = new Category_model();
		//$result['ctg_list'] = addslashes(json_encode($ctg->get_category_rows()));
		
		return ACWView::template_admin('slides/edit.html', $result);
	}
	public static function action_addchild()
	{
		$param = self::get_param(array('acw_url'));	
		
		if (self::get_validate_result() == false)  {
			ACWError::add('acw_url', 'Tham số không hợp lệ');
		}
		$parent_id  = $param['acw_url'][0];
		$ctg = new Category_model();
		$db = new Menu_model();
		$result = $db->get_menu_info($parent_id);
				
		$param['menu_id'] = null;
		$param['menu_name'] = null;
		$param['del_flg'] = 0;
		$param['menu_level'] = $result['menu_level'] + 1;;
		$param['page_flg'] = 0;
		$param['link'] = null;
		$param['sort'] = 1;
		$param['ctg_list'] = addslashes(json_encode($ctg->get_category_rows()));
		$param['page_list'] = null;
		$param['parent_id'] = $parent_id;
		return ACWView::template_admin('menu/edit.html', $param,FALSE);
	}

	public static function action_update()
	{
		
		$param = self::get_param(array(
			'slide_id'
			,'img_path'				
			,'del_flg'			
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
					,del_flg
					,add_date
					,add_user
					,upd_date
					,upd_user					
					)
				VALUES
					(
					:img_path					
					,:del_flg					
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
					,upd_date =  now()
					,upd_user =:user_id					
					where slide_id = :slide_id
				";
		
 
		$this->execute($sql, ACWArray::filter($param, array(
					'slide_id'
					,'img_path'					
					,'user_id'
					,'del_flg'				
					)));			
		
		$this->commit();
		return TRUE;
	}
	
	public function delete_slide($slide_id)
	{
		$this->execute("delete from slides where slide_id = :slide_id",
				array('slide_id' => $slide_id));		
	}

	
	
	public function get_slides_all($all_flg= FALSE)
	{
		$sql = "select * from slides";
		return $this->query($sql);
	}
	
	public function get_slide_info($slide_id)
	{
		$r = $this->query("
			select slide_id
					,img_path				
					,del_flg
					
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
		return $db->get_slides_head();
	}
	public function get_slides_head(){
		$sql = "select img_path from slides where del_flg = 0";
		return $this->query($sql);
	}
	
}

<?php

class Menu_model extends ACWModel
{
	
	public static function init()
	{
		Login_model::check();	
	}
	
	public static function action_index()
	{
		
		$db = new Menu_model();
		$param['menus']=$db->get_menu_all(TRUE);
		return ACWView::template_admin('menu.html', $param);
	}
	public static function action_list()
	{
		$param = self::get_param(array('acw_url'));	
		
		$level_id  = $param['acw_url'][0];
		$db = new Menu_model();
		$param['menus']=$db->get_menu_list($level_id);
		$param['level_flg'] = $level_id;
		if($level_id > 1){
			$param['parent_list1']= $db->get_menu_list(1);
			if($level_id > 2){
				$param['parent_list2']= json_encode($db->get_menu_list(2));
			}
		}
		return ACWView::template_admin('menu_list.html', $param,FALSE);
	}
	
	public static function action_new()
	{
		$param = self::get_param(array('menu_level'));
		$ctg = new Category_model();
		$pg = new Page_model();
		$param['menu_id'] = null;
		$param['menu_name'] = null;
		$param['del_flg'] = 0;
		//$param['menu_level'] = 1;
		$param['page_flg'] = 0;
		$param['parent_id'] = NULL;
		$param['link'] = null;
		$param['ctg_list'] = addslashes(json_encode($ctg->get_category_rows(0)));
		$param['news_list'] = addslashes(json_encode($ctg->get_category_rows(1)));
		$param['page_list'] = addslashes(json_encode($pg->get_page_list()));		
		if(strlen($param['menu_level'])==0){
			$param['menu_level'] = 1;
		}
		$level_id =$param['menu_level'];
		$param['parent_id_1']= NULL;
		$param['parent_id_2']= NULL;
		$db = new Menu_model();
		if($level_id > 1){			
			$param['parent_list1']= $db->get_menu_list(1);			
			if($level_id > 2){
				$param['parent_list2']= json_encode($db->get_menu_list(2));
			}
		}
		$param['sort'] = 1;
		return ACWView::template_admin('menu/edit.html', $param,FALSE);
	}
	
	public static function action_edit()
	{
		$param = self::get_param(array('acw_url'));	
		if (self::get_validate_result() == false)  {
			ACWError::add('acw_url', 'Tham số không hợp lệ');
		}
		
		$db = new Menu_model();
		$result = $db->get_menu_info($param['my_id']);
		$ctg = new Category_model();
		$pg = new Page_model();
		$result['ctg_list'] = addslashes(json_encode($ctg->get_category_rows(0)));
		$result['news_list'] = addslashes(json_encode($ctg->get_category_rows(1)));
		$result['page_list'] = addslashes(json_encode($pg->get_page_list()));
		$db = new Menu_model();
		if($result['menu_level'] > 1){			
			$result['parent_list1']= $db->get_menu_list(1);
			//$param['parent_id_1']= NULL;
			if($result['menu_level'] > 2){
				$result['parent_list2']= json_encode($db->get_menu_list(2));
			}
		}
		return ACWView::template_admin('menu/edit.html', $result,FALSE);
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
		$pg = new Page_model();
		$result = $db->get_menu_info($parent_id);
				
		$param['menu_id'] = null;
		$param['menu_name'] = null;
		$param['del_flg'] = 0;
		$param['menu_level'] = $result['menu_level'] + 1;;
		$param['page_flg'] = 0;
		$param['link'] = null;
		$param['sort'] = 1;
		$param['ctg_list'] = addslashes(json_encode($ctg->get_category_rows(0)));
		$param['news_list'] = addslashes(json_encode($ctg->get_category_rows(1)));
		$param['page_list'] = addslashes(json_encode($pg->get_page_list()));
		//$param['page_list'] = null;
		$param['parent_id'] = $parent_id;
		return ACWView::template_admin('menu/edit.html', $param,FALSE);
	}

	public static function action_update()
	{
		
		$param = self::get_param(array(
			'menu_id'
			,'menu_name'
			, 'menu_sort'	
			, 'del_flg'
			, 'parent_id_1'
			, 'parent_id_2'
			, 'menu_level'
			,'page_flg'
			,'link'
			));
		
		$result = array('status' => 'OK');
		$result['status'] = 'OK';	
		$result['msg'] = 'Cập nhật thành công!';		
		$db = new Menu_model();
		$msg = $db->check_validate_update($param);
		if($msg ==""){
			if(strlen($param['menu_id'])==0){
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
		$db = new Menu_model();
		$msg = $db->check_before_delete($param['my_id']);
		$result['status']="OK";
		if($msg== ""){
			$db->delete_menu($param['my_id']);
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
		$param['parent_id']=0;
		if(isset($param['parent_id_2']) && strlen($param['parent_id_2'])>0){
			$param['parent_id'] = $param['parent_id_2'];
		}else if(isset($param['parent_id_1']) && strlen($param['parent_id_1'])>0){
			$param['parent_id'] = $param['parent_id_1'];
		}
		$sql = "INSERT INTO menu
					(
					menu_name					
					,parent_id
					,del_flg
					,add_date
					,add_user
					,upd_date
					,upd_user
					,menu_level
					,sort
					,page_flg
					,link
					)
				VALUES
					(
					:menu_name					
					,:parent_id
					,0
					,now()
					,:user_id
					,now()
					,:user_id
					,:menu_level
					,:menu_sort
					,:page_flg
					,:link
					)
				";
		
 
		$this->execute($sql, ACWArray::filter($param, array(
					'menu_name'
					,'link'
					,'parent_id'					
					,'menu_sort'
					,'menu_level'
					,'user_id'
					,'page_flg'
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
		$param['parent_id']=0;
		if(isset($param['parent_id_2']) && strlen($param['parent_id_2'])>0){
			$param['parent_id'] = $param['parent_id_2'];
		}else if(isset($param['parent_id_1']) && strlen($param['parent_id_1'])>0){
			$param['parent_id'] = $param['parent_id_1'];
		}
		$sql = "update menu
					set menu_name = :menu_name	
					,page_flg = :page_flg	
					,parent_id =:parent_id			
					,del_flg = :del_flg					
					,upd_date =  now()
					,upd_user =:user_id
					,sort = :menu_sort
					,link = :link
					where menu_id = :menu_id
				";
		
 
		$this->execute($sql, ACWArray::filter($param, array(
					'menu_id'
					,'menu_name'	
					,'page_flg'								
					,'menu_sort'
					,'user_id'
					,'del_flg'
					,'link'
					,'parent_id'
					)));			
		
		$this->commit();
		return TRUE;
	}
	
	public function delete_menu($menu_id)
	{
		$this->execute("delete from menu where menu_id = :menu_id",
				array('menu_id' => $menu_id));	
	}

	
	
	public function get_menu_all($all_flg= FALSE)
	{
		$sql = "select * from(
				(select * ,CONCAT(LPAD(sort,5,'0'),'_',LPAD(menu_id,5,'0'))   sort2
				from menu
				where menu_level = 1)
				union
				(select m.* , CONCAT(m1.sort2,'_',LPAD(m.sort,5,'0'),'_',LPAD(m.menu_id,5,'0')) sort2
				from 
				menu m,
				(select * ,CONCAT(LPAD(sort,5,'0'),'_',LPAD(menu_id,5,'0'))   sort2
				from menu m
				where menu_level = 1) m1
				where m.parent_id = m1.menu_id
				and m.menu_level = 2 )
				union
				(select m3.*, CONCAT(m2.sort2,'_',LPAD(m3.sort,5,'0'),'_',LPAD(m3.menu_id,5,'0')) sort2
				from menu m3,
				(select m.* , CONCAT(m1.sort2,'_',LPAD(m.sort,5,'0'),'_',LPAD(m.menu_id,5,'0')) sort2
				from 
				menu m,
				(select * ,CONCAT(LPAD(sort,5,'0'),'_',LPAD(menu_id,5,'0'))   sort2
				from menu m
				where menu_level = 1) m1
				where m.parent_id = m1.menu_id
				and m.menu_level = 2 ) m2
				where m3.parent_id = m2.menu_id
				and m3.menu_level = 3)

				) t
				ORDER BY t.sort2";
		/*if($all_flg){
			$sql = " select * from category  order by sort	";
		}*/
		return $this->query($sql);
	}
	
	public function get_menu_info($ctg_id)
	{
		$r = $this->query("
			select m.menu_id
					,m.menu_name
		  	  ,(CASE m.menu_level WHEN  3 THEN   m1.parent_id else  m.parent_id END)  parent_id_1
					,m.del_flg					
					,m.sort 
					,m.menu_level
					,m.page_flg
					,m.link
					,m1.parent_id menu_name_2
					,(CASE m.menu_level WHEN  3 THEN   m.parent_id else  0 END)  parent_id_2
				from menu m
				LEFT JOIN menu m1 on m1.menu_id = m.parent_id
				where m.menu_id = :menu_id
			", array ('menu_id' => $ctg_id));
		if(count($r) >0)
			return $r[0];
		else
			return null;
	}
	public function get_ctg_byno($ctg_no)
	{
		$r = $this->query("
			select ctg_id
					,ctg_name
					,parent_id
					,del_flg					
					,sort 
					,ctg_level
				from category
				where ctg_no = :ctg_no
			", array ('ctg_no' => $ctg_no));
		if(count($r) >0)
			return $r[0];
		else
			return null;
	}
	
	public function get_ctg_child($ctg_parent_id,&$all_ctg_child = NULL)
	{
		if($all_ctg_child == NULL){
			$all_ctg_child = array();
		}
		$obj_select = $this->query("
			SELECT
				t_ctg_head_id,oya_t_ctg_head_id
			FROM
				t_ctg_head
			WHERE
				oya_t_ctg_head_id = :ctg_parent_id AND del_flg = 0
			", array ('ctg_parent_id' => $ctg_parent_id));
			
		if(count($obj_select) > 0){
			foreach($obj_select as $key => $value){
				$all_ctg_child[] = $value;
				$this->get_ctg_child($value["t_ctg_head_id"],$all_ctg_child);
			}
			
		}
			
		return $all_ctg_child;
	}
	
	public function get_child_folder($folder_id)
	{
		$sql="select * from folder where parent_folder_id = :folder_id and del_flg=0 order by folder_id";
		return $this->query($sql,array('folder_id'=>$folder_id));
	}
	
    public function get_ctg_head_id($ctg_id){
        $sql="select t_ctg_head_id from t_ctg_head where ctg_id =:ctg_id ";
        $res = $this->query($sql,array("ctg_id"=>$ctg_id));
        if(count($res)>0){
            return $res[0]['t_ctg_head_id'];
        }else{
            return 0;
        }
    }
    public function check_before_delete($ctg_id){
		return "";
	}
	public function check_validate_update($param){
		if(strlen($param['menu_name'])== 0){
			return "Bạn chưa nhập tên menu !";
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
	public function get_menu_head(){
		$sql = "select t.menu_id,menu_level,parent_id,link,page_flg,upper(menu_name) menu_name
				,(select count(*) from menu where  parent_id = t.menu_id) child_flg
				from menu t
				where del_flg = 0
				ORDER BY sort";
		return $this->query($sql);
	}
	public static function get_menus()
	{
		$db = new Menu_model();
		$res =  $db->get_menu_head();
		$menu = array();
		$db->set_child($menu,$res,0);
		return $menu;
		/*foreach($res as &$item){
			if($item['child_lfg'] == 0){
				$menu[] = $item ;				 
			}else{
				$data = $item;
				$data['child']= array();				
				$db->set_child($data['child'],$res);
				$menu[]= $data;
			}
			unset($item);
		}*/
	}
	public function set_child(&$menu,&$list,$parent_id){
		foreach($list as $key=>$item){
			if($item['parent_id'] == $parent_id){
				if($item['child_flg'] == 0){
					$menu[] = $item ;
					//unset($list[$key]);				 
				}else{
					$data = $item;
					//unset($list[$key]);	
					$data['child']= array();				
					$this->set_child($data['child'],$list,$data['menu_id']);
					$menu[]= $data;
				}
			}
		}
	}
	public static function get_menus_left()
	{
		$db = new Menu_model();
		$res =  $db->get_menu_byparent(29);
		$menu = array();
		$db->set_child($menu,$res,29);
		return $menu;
		
	}
	public function get_menu_byparent($parent_id){
		$sql = "select t.menu_id,menu_level,parent_id,link,page_flg,(menu_name) menu_name
				,(select count(*) from menu where  parent_id = t.menu_id) child_flg
				from menu t
				where del_flg = 0 
				ORDER BY sort";
		return $this->query($sql);
	}
	public static function get_menus_sp()
	{
		$db = new Menu_model();
		$res =  $db->get_menu_byparent(30);
		$menu = array();
		$db->set_child($menu,$res,30);
		return $menu;
		
	}
	public function get_menu_list($level)
	{
		return $this->query("select m.menu_id,m.parent_id, m.menu_name,m.menu_level,m.del_flg,m.sort,m1.menu_name menu_name_1, m2.menu_name menu_name_2
				from menu m
				LEFT JOIN menu m1 on m1.menu_id = m.parent_id 
				LEFT JOIN menu m2 on m2.menu_id = m1.parent_id
				where m.menu_level = $level
				ORDER BY m.sort " );
	}
	
}

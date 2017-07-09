<?php

class Category_model extends ACWModel
{
	
	public static function init()
	{
		Login_model::check();	
	}
	
	public static function action_index()
	{
		
		$db = new Category_model();
		$param['category']=$db->get_catgory_all(0);
		$param['news_flg']=0;
		return ACWView::template_admin('category.html', $param);
	}
	public static function action_list()
	{
		$param = self::get_param(array('acw_url'));
		$level_id  = $param['acw_url'][0];	
		$news_flg  = $param['acw_url'][1];
		$db = new Category_model();
		$param['category']=$db->get_ctg_list($level_id,$news_flg);
		$param['news_flg']=$news_flg;
		$param['level_flg']=$level_id;
		if($level_id > 1){
			$param['parent_list1']= $db->get_ctg_list(1,$news_flg);
		}
		return ACWView::template_admin('category_list.html', $param);
	}
	
	public static function action_new()
	{		
		$param = self::get_param(array('ctg_level','news_flg'));
		$param['ctg_id'] = null;
		$param['ctg_name'] = null;
		$param['del_flg'] = 0;	
		$param['sort'] = 1;	

		if(strlen($param['ctg_level'])==0){
			$param['ctg_level'] = 1;
		}
		$level_id =$param['ctg_level'];
		$param['parent_id_1']= NULL;
		$db = new Category_model();
		if($level_id > 1){			
			$param['parent_list1']= $db->get_ctg_list(1,$param['news_flg']);
		}
		
		return ACWView::template_admin('category/edit.html', $param);
	}
	public static function action_ctgnews()
	{
		$db = new Category_model();
		$param['category']=$db->get_catgory_all(1);
		$param['news_flg']=1;
		return ACWView::template_admin('category.html', $param);
	}
	
	public static function action_edit()
	{
		$param = self::get_param(array('acw_url'));	
		if (self::get_validate_result() == false)  {
			ACWError::add('acw_url', 'Tham số không hợp lệ');
		}
		
		$db = new Category_model();
		$result = $db->get_ctg_info($param['my_id']);
		if($result['ctg_level'] > 1){			
			$result['parent_list1']= $db->get_ctg_list(1,$result['news_flg'] );
		}
		return ACWView::template_admin('category/edit.html', $result);
	}
	public static function action_addchild()
	{
		$param = self::get_param(array('acw_url'));	
		
		if (self::get_validate_result() == false)  {
			ACWError::add('acw_url', 'Tham số không hợp lệ');
		}
		$parent_id  = $param['acw_url'][0];
		
		$db = new Category_model();
		$result = $db->get_ctg_info($parent_id);
		$res['ctg_id'] = null;
		$res['ctg_name'] = null;
		$res['del_flg'] = 0;
		$res['ctg_level'] = $result['ctg_level'] + 1;
		$res['news_flg'] = $result['news_flg'];
		$res['parent_id'] = $parent_id;
		$res['sort'] = 1;
		return ACWView::template_admin('category/edit.html', $res);
	}

	public static function action_update()
	{
		
		$param = self::get_param(array(
			'ctg_id'
			,'ctg_name'
			, 'ctg_sort'	
			, 'del_flg'		
			, 'ctg_level'			
			, 'parent_id_1'
			,'news_flg'
			));
		//$param['news_flg'] =0;
		$result = array('status' => 'OK');
		$result['status'] = 'OK';	
		$result['msg'] = 'Cập nhật thành công!';		
		$db = new Category_model();
		$msg = $db->check_validate_update($param);
		if($msg ==""){
			if(strlen($param['ctg_id'])==0){
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
		$db = new Category_model();
		$msg = $db->check_before_delete($param['my_id']);
		$result['status']="OK";
		if($msg== ""){
			$db->delete_category($param['my_id']);
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
		$param['parent_id']=0;
		if(isset($param['parent_id_2']) && strlen($param['parent_id_2'])>0){
			$param['parent_id'] = $param['parent_id_2'];
		}else if(isset($param['parent_id_1']) && strlen($param['parent_id_1'])>0){
			$param['parent_id'] = $param['parent_id_1'];
		}
		$sql = "INSERT INTO category
					(
					ctg_name
					,ctg_no
					,parent_id
					,del_flg
					,add_date
					,add_user
					,upd_date
					,upd_user
					,ctg_level
					,sort
					,news_flg
					)
				VALUES
					(
					:ctg_name
					,:ctg_no
					,:parent_id
					,0
					,now()
					,:user_id
					,now()
					,:user_id
					,:ctg_level
					,:ctg_sort
					,:news_flg
					)
				";
		
 
		$this->execute($sql, ACWArray::filter($param, array(
					'ctg_name'
					,'ctg_no'
					,'parent_id'					
					,'ctg_sort'
					,'ctg_level'
					,'user_id'
					,'news_flg'
					)));			
		$result = $this->query("SELECT LAST_INSERT_ID() AS ctg_id");			
		$new_id = $result[0]['ctg_id'];	
		$this->commit();
		return $new_id;
	}
	
	public function update($param)
	{
		$this->begin_transaction();

		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['user_id'];		
		$param['parent_id']=0;
		if(isset($param['parent_id_2']) && strlen($param['parent_id_2'])>0){
			$param['parent_id'] = $param['parent_id_2'];
		}else if(isset($param['parent_id_1']) && strlen($param['parent_id_1'])>0){
			$param['parent_id'] = $param['parent_id_1'];
		}
		$sql = "update category
					set ctg_name = :ctg_name	
					,ctg_no = :ctg_no				
					,del_flg = :del_flg					
					,upd_date =  now()
					,upd_user =:user_id
					,sort = :ctg_sort
					where ctg_id = :ctg_id
				";
		
 
		$this->execute($sql, ACWArray::filter($param, array(
					'ctg_id'
					,'ctg_name'	
					,'ctg_no'								
					,'ctg_sort'
					,'user_id'
					,'del_flg'
					)));			
		
		$this->commit();
		return TRUE;
	}
	
	public function delete_category($ctg_id)
	{
		$this->execute("delete from category where ctg_id = :ctg_id",
				array('ctg_id' => $ctg_id));	
	}

	
	
	public function get_catgory_all($news_flg)
	{
		$sql = "select * from(
				(select * ,CONCAT(LPAD(sort,5,'0'),'_',LPAD(ctg_id,5,'0'))   sort2
				from category
				where ctg_level = 1 and news_flg = $news_flg)
				union
				(select m.* , CONCAT(m1.sort2,'_',LPAD(m.sort,5,'0'),'_',LPAD(m.ctg_id,5,'0')) sort2
				from 
				category m,
				(select * ,CONCAT(LPAD(sort,5,'0'),'_',LPAD(ctg_id,5,'0'))   sort2
				from category m
				where ctg_level = 1 and news_flg = $news_flg) m1
				where m.parent_id = m1.ctg_id
				and m.ctg_level = 2) 
				union
				(select m3.*, CONCAT(m2.sort2,'_',LPAD(m3.sort,5,'0'),'_',LPAD(m3.ctg_id,5,'0')) sort2
				from category m3,
				(select m.* , CONCAT(m1.sort2,'_',LPAD(m.sort,5,'0'),'_',LPAD(m.ctg_id,5,'0')) sort2
				from 
				category m,
				(select * ,CONCAT(LPAD(sort,5,'0'),'_',LPAD(ctg_id,5,'0'))   sort2
				from category m
				where ctg_level = 1 and news_flg = $news_flg) m1
				where m.parent_id = m1.ctg_id
				and m.ctg_level = 2 ) m2
				where m3.parent_id = m2.ctg_id
				and m3.ctg_level = 3)

				) t
				ORDER BY t.sort2";
		/*if($all_flg){
			$sql = " select * from category  order by sort	";
		}*/
		return $this->query($sql);
	}
	
	public function get_ctg_info($ctg_id)
	{
		$r = $this->query("
			select ctg_id
					,ctg_name
					,parent_id as parent_id_1
					,del_flg					
					,sort 
					,ctg_level
					,news_flg
				from category
				where ctg_id = :ctg_id
			", array ('ctg_id' => $ctg_id));
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
	public function check_validate_update(&$param){
		if(strlen($param['ctg_name'])== 0){
			return "Bạn chưa nhập tên danh mục !";
		}
		$param['ctg_no'] =str_replace(' ','-', ACWModel::convert_vi_to_en($param['ctg_name']));
		if(strlen($param['ctg_id']) == 0){
			$row = $this->get_ctg_byno($param['ctg_no']);
			$i = 0;
			while($row != null){
				$i++;
				$param['ctg_no'] = $param['ctg_no'].'-'.$i;
				$row = $this->get_ctg_byno($param['ctg_no']);
			}
		}else{
			$sql = "select ctg_id from category	where ctg_no = :ctg_no	and ctg_id <> :ctg_id";
			$res = $this->query($sql, array ('ctg_no' => $param['ctg_no'] , 'ctg_id'=>$param['ctg_id']));
			$i = 0;
			while(count($res) > 0){
				$i++;
				$param['ctg_no'] = $param['ctg_no'].'-'.$i;
				$res = $this->query($sql, array ('ctg_no' => $param['ctg_no'] , 'ctg_id'=>$param['ctg_id']));
			}
		}
		return "";
	}
	public function get_category_rows($news_flg)
	{
		$sql = "select t.ctg_id,t.ctg_name,t.ctg_no,t.cnt_child,t.ctg_level,t.parent_id from(
				(select * ,CONCAT(LPAD(sort,5,'0'),'_',LPAD(ctg_id,5,'0'))   sort2,
				(SELECT count(*) from category where parent_id = x.ctg_id and del_flg = 0)  cnt_child
				from category x
				where ctg_level = 1 and news_flg = $news_flg)
				union
				(select m.* , CONCAT(m1.sort2,'_',LPAD(m.sort,5,'0'),'_',LPAD(m.ctg_id,5,'0')) sort2,
				(SELECT count(*) from category where parent_id = m.ctg_id and del_flg = 0)  cnt_child
				from 
				category m,
				(select * ,CONCAT(LPAD(sort,5,'0'),'_',LPAD(ctg_id,5,'0'))   sort2
				from category m
				where ctg_level = 1 and news_flg = $news_flg) m1
				where m.parent_id = m1.ctg_id
				and m.ctg_level = 2 )
				union
				(select m3.*, CONCAT(m2.sort2,'_',LPAD(m3.sort,5,'0'),'_',LPAD(m3.ctg_id,5,'0')) sort2,0 as cnt_child
				from category m3,
				(select m.* , CONCAT(m1.sort2,'_',LPAD(m.sort,5,'0'),'_',LPAD(m.ctg_id,5,'0')) sort2
				from 
				category m,
				(select * ,CONCAT(LPAD(sort,5,'0'),'_',LPAD(ctg_id,5,'0'))   sort2
				from category m
				where ctg_level = 1 and news_flg = $news_flg) m1
				where m.parent_id = m1.ctg_id
				and m.ctg_level = 2 ) m2
				where m3.parent_id = m2.ctg_id
				and m3.ctg_level = 3)

				) t where t.del_flg = 0
				ORDER BY t.sort2";
		
		return $this->query($sql);
	}
	public function get_ctg_list($level,$news_flg=0)
	{
		return $this->query("select m.ctg_id,m.parent_id, m.ctg_no,m.ctg_name,m.ctg_level,m.del_flg,m.sort,m1.ctg_name ctg_name_1
				from category m
				LEFT JOIN category m1 on m1.ctg_id = m.parent_id 
				where m.ctg_level = $level
				and  m.news_flg = $news_flg
				ORDER BY m.sort" );
	}
	public static function get_ctg_menu(){
		$db = new Category_model();
		$data = $db->get_category_rows(0);
		$menu = array();
		$db->set_child($menu,$data,0);
		return $menu;
	}
	public static function get_ctg_level1(){
		$db = new Category_model();
		$data = $db->get_ctg_list(1,0);
		return $data;
	}
	public function set_child(&$menu,&$list,$parent_id){
		foreach($list as $key=>$item){
			if($item['parent_id'] == $parent_id){
				if($item['cnt_child'] == 0){
					$menu[] = $item ;
					//unset($list[$key]);				 
				}else{
					$data = $item;
					//unset($list[$key]);	
					$data['child']= array();				
					$this->set_child($data['child'],$list,$data['ctg_id']);
					$menu[]= $data;
				}
			}
		}
	}
}
/* ファイルの終わり */
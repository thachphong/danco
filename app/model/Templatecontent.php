<?php
class Templatecontent_model extends ACWModel
{	
	public static function init()
	{
		Login_model::check_admin();	
	}
	public static function action_index()
	{		
		$db = new Templatecontent_model();
		$data['list'] = $db->get_content_list();
		return ACWView::template_admin('template_content.html',$data);
	}
	
	
	public static function action_new()
	{		
		$param['con_id'] = null;	
		$param['section_id'] = '';
		$param['column_id'] = '';
		$param['mod_outner_id'] = 0;
		$param['mod_inner_id'] = 0;
		$param['con_name'] = '';	
		$param['sort'] = 1;
		$db = new Templatecontent_model();
		$param['module_list']=$db->get_module_list();
		return ACWView::template_admin('template_content/edit.html', $param);
	}
	public static function action_edit()
	{
		$param = self::get_param(array('acw_url'));	
		
		$db = new Templatecontent_model();
		$param = $db->get_content($param['acw_url'][0]);
		$param['module_list']=$db->get_module_list();
		return ACWView::template_admin('template_content/edit.html', $param);
	}
	
	public static function action_update(){
		$param = self::get_param(array(
				'con_id',
			    'con_name',
				'section_id',	
				'column_id',					 
				'mod_outner_id',	
				'mod_inner_id',
				'sort'			
			));		
		$result = array('status' => 'OK');
		$result['status'] = 'OK';	
		$result['msg'] = 'Cập nhật thành công!';		
		$db = new Templatecontent_model();
		$msg = $db->check_validate_update($param);
		if($msg == ""){
			if(strlen($param['con_id'])==0){			
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
		if(strlen($param['con_name'])== 0){
			return "Bạn chưa nhập giá trị content name!";
		}		
		return "";
	}
	
	protected function update($param)
	{				
		//$login_info = ACWSession::get('user_info');
		//$param['user_id'] = $login_info['user_id'];		
		$sql = "update template_content
					set con_name = :con_name,
					  section_id = :section_id,
					  column_id  = :column_id,
					  mod_outner_id = :mod_outner_id,
					  mod_inner_id = :mod_inner_id,
					  sort =:sort
					where con_id = :con_id
				";
		
 		$sql_par =  ACWArray::filter($param, array('con_name','section_id','column_id','mod_outner_id','mod_inner_id','con_id','sort'));			
		$this->execute($sql,$sql_par );
		return TRUE;	
	}
	private function insert($param){		
		//$login_info = ACWSession::get('user_info');
		//$param['user_id'] = $login_info['user_id'];		
		
		$sql = "INSERT INTO template_content
					(	
					  con_name,
					  section_id,
					  column_id,
					  mod_outner_id,
					  mod_inner_id,
					  sort,
					  template_id
					)
				VALUES
					(
					  :con_name,
					  :section_id,
					  :column_id,
					  :mod_outner_id,
					  :mod_inner_id	,
					  :sort,
					  (select template_id from template where active='1')			
					)
				";
		$sql_par =  ACWArray::filter($param, array('con_name','section_id','column_id','mod_outner_id','mod_inner_id','sort'));			
		$this->execute($sql,$sql_par );
		return TRUE;
	}
	protected function update_template($param)
	{				
		//$login_info = ACWSession::get('user_info');
		//$param['user_id'] = $login_info['user_id'];	
		if($param['active']=='1'){
			$sql="update template set active = 0";
			$this->execute($sql );
		}	
		$sql = "update template
					set template_no = :template_no,
					template_name = :template_name,
					active = :active	
					where template_id = :template_id
				";
		
 		$sql_par =  ACWArray::filter($param, array('template_no','template_name','active','template_id'	));			
		$this->execute($sql,$sql_par );
		return TRUE;	
	}
	public function get_structure($mod_id = 0){
		$sql="select t.*,d.struct_val ,d.template_detail_id
				from template_structure  t
				LEFT JOIN template_detail d
				on d.struct_id = t.struct_id and d.template_id = (SELECT template_id from template where active=1)
				where t.mod_id =:mod_id
				order by sort";
		return $this->query($sql,array('mod_id'=>$mod_id));
	}
	public function get_option(){
		$sql="select *
				from template_option  	
				";
		return $this->query($sql);
	}
	public function get_module_list(){
		$sql="select *
				from template_module  	";
		return $this->query($sql);
	}
	public function get_content_list(){
		$sql="select c.*,
				outn.mod_name outner_name,
				inn.mod_name 	inner_name
				from template_content c 
				LEFT JOIN template_module  outn 
							on outn.mod_id = c.mod_outner_id
				LEFT JOIN template_module  inn 
							on inn.mod_id = c.mod_inner_id
				INNER JOIN template t
							on t.template_id = c.template_id and t.active = 1
				order by c.sort
				";
		return $this->query($sql);
	}
	public function get_template_list(){
		$sql="select *	from template";
		return $this->query($sql);
	}
	public function get_content($con_id){
		$sql="select c.*,
				outn.mod_name outner_name,
				inn.mod_name 	inner_name
				from template_content c 
				LEFT JOIN template_module  outn 
							on outn.mod_id = c.mod_outner_id
				LEFT JOIN template_module  inn 
							on inn.mod_id = c.mod_inner_id
				where c.con_id = $con_id

				";
		return $this->query_first($sql);
	}
	public static function action_create()
	{
		$db = new Template_model();
		$data = $db->get_structure(1);
		$main_style=array();
		foreach($data as $row){			
			$main_style[$row['model']][$row['struct_key']]= $row['struct_val'];			
		}
		$html = new Htmldom_lib();
		$html->create_basic_html(ACW_TEMPLATE_DIR.'/index.html');
		$css_file = PUBLIC_TEMPLATE_PATH.'/css/style.css';
		$style = new Cssstyle_lib($main_style['body']);
		$css = new Cssdom_lib($css_file);
		$css->set_style('body',$style,TRUE);
		$style = new Cssstyle_lib();
		$style->margin_right = '0px';
		$style->margin_left = '0px';
		$css->set_style('.row',$style);
		
		$html->add_logo();
		$html->add_menu($main_style['menu_top']);
		if($main_style['slide_top']['vitri']== '0'){ //full
			$width = '100%';
			$height = $main_style['slide_top']['height'];
			$speed = $main_style['slide_top']['speed'];
			$html->add_slides("content",$width,$height,$speed);
		}
		$html->add_content_section(1);
		if(isset($main_style['content']['total_column'])){
			$html->split_column_section('section_1',$main_style['content']['total_column'],$main_style['content']['vitri_content']);
		}
		$html->add_cssfile('<%$smarty.const.ACW_BASE_URL_TEMPLATE%>/css/bootstrap.min.css');	
		$html->add_cssfile('<%$smarty.const.ACW_BASE_URL_TEMPLATE%>/css/style.css');
		$html->add_jsfile('<%$smarty.const.ACW_BASE_URL_TEMPLATE%>/js/jquery.min.js');
		$html->add_jsfile('<%$smarty.const.ACW_BASE_URL_TEMPLATE%>/js/bootstrap.min.js');
		//$html->ad
		$res['msg']='Tạo thành công !';
		return ACWView::template_admin('create.html',$res);
	}
	//public function()
}


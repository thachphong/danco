<?php

class News_model extends ACWModel
{	
	public static function init()
	{
		Login_model::check();	
	}
		
	public static function action_index()
	{
		$param = self::get_param(array(
			'search_user_name'
		));
		$model = new News_model();
		$rows = $model->get_news_all();		
		return ACWView::template_admin('news.html', array(
			'news' => $rows			
		));
	}
	public function get_news_all(){
		$sql="select n.news_id,
				  n.news_no,
				  n.news_name,				 
				  n.content,				 
				  n.des,
				  n.img_path,
				  n.add_date,
				  n.add_user,
				  n.upd_date,
				  n.upd_user,				  
				  n.del_flg,
					c.ctg_name
			  from news n
				inner JOIN category c on c.ctg_id = n.ctg_id	";
		return $this->query($sql);
	}
	public static function action_new()
	{
		$param = self::get_param(array('parent_id'));
		$ctg = new Category_model();
		$param['news_id'] = null;
		$param['news_no'] = null;
		$param['news_name'] = "";
		$param['img_path'] = NULL;
		$param['des'] = "";
		$param['content'] = "";
		$param['del_flg'] = "";
		$param['ctg_id'] =null;
		$param['ctg_list'] = $ctg->get_category_rows(1);	
		 
			
		return ACWView::template_admin('news/edit.html', $param);		
	}
	public static function action_update(){
		$param = self::get_param(array(
			  'news_id',
			  'news_no',
			  'news_name',			
			  'content',
			  'des',
			  'ctg_id',
			  'img_path',
			  'add_date',
			  'add_user',
			  'upd_date',
			  'upd_user',			 
			  'del_flg'
			));
		
		$result = array('status' => 'OK');
		$result['status'] = 'OK';	
		$result['msg'] = 'Cập nhật thành công!';		
		$db = new News_model();
		$msg = $db->check_validate_update($param);
		if($msg == ""){
			if(strlen($param['news_id'])==0){
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
		if(strlen($param['news_name'])== 0){
			return "Bạn chưa nhập tên trang !";
		}
		$param['news_no'] =str_replace(' ','-', ACWModel::convert_vi_to_en($param['news_name']));
		
		$news_no = $param['news_no'];
		if(strlen($param['news_id']) == 0){
			$sql = "select news_id from news	where news_no = :news_no";
			$row = $this->query($sql, array ('news_no' => $param['news_no'] ));
			//$row = $this->get_ctg_byno($param['page_no']);
			$i = 0;
			while(count($row) > 0){
				$i++;
				$param['news_no'] = $news_no.'-'.$i;
				$row = $this->query($sql, array ('news_no' => $param['news_no'] ));
			}
		}else{
			$sql = "select news_id from news	where news_no = :news_no	and news_id <> :news_id";
			$res = $this->query($sql, array ('news_no' => $param['news_no'] , 'news_id'=>$param['news_id']));
			$i = 0;
			while(count($res) > 0){
				$i++;
				$param['news_no'] = $news_no.'-'.$i;
				$res = $this->query($sql, array ('news_no' => $param['news_no'] , 'news_id'=>$param['news_id']));
			}
		}
		return "";
	}
	private function insert($param){
		$this->begin_transaction();

		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['user_id'];
		//$param['ctg_no'] =str_replace(' ','-', ACWModel::convert_vi_to_en( $param['ctg_name']));
		$sql = "INSERT INTO news
					(					
					  news_no,
					  news_name,
					  ctg_id,
					  des,
					  img_path,
					  content,
					  add_date,
					  add_user,
					  upd_date,
					  upd_user,					 
					  del_flg
					)
				VALUES
					(					  
					  :news_no,
					  :news_name,
					  :ctg_id,
					  :des,
					  :img_path,
					  :content,
					  now(),
					  :user_id,
					  now(),
					  :user_id,				 
					  :del_flg
					)
				";
		 
		$this->execute($sql, ACWArray::filter($param, array(					  		  
					  'news_no',
					  'news_name',
					  'content',
					  'ctg_id',
					  'des',
					  'img_path',					  
					  'user_id',					 
					  'del_flg',
					  'content'
					)));
		$this->commit();
		return TRUE;
	}
	protected function update($param){
		
		$this->begin_transaction();

		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['user_id'];		
		$sql = "update news
					set news_no = :news_no,
					  news_name = :news_name,
					  ctg_id = :ctg_id,
					  des = :des,
					  img_path = :img_path ,
					  content = :content,					 
					  upd_date = now(),
					  upd_user = :user_id,					 
					  del_flg = :del_flg
					where news_id = :news_id
				";
		
 		$sql_par = ACWArray::filter($param, array(
 					'news_id',
					'news_no',
					  'news_name',
					  'content',
					  'ctg_id',
					  'des',
					  'img_path',						  
					  'user_id',					 
					  'del_flg'					  
					));
		$this->execute($sql,$sql_par );			
		
		$this->commit();
		return TRUE;	
	}
	/*public function get_product_byno($pro_no){
		$sql=" select pro_name,
			  pro_id,
			  pro_no,
			  ctg_id ,
			  hieu_xe ,
			  doi_xe,
			  mau_xe ,
			  kieu_xe ,
			  giathue_ngay,
			  giathue_thang,
			  xedi_noithanh,
			  phutroi_ngoaigio,
			  phutroi_quakm,
			  songay_sudung,
			  sudung_cuoituan,
			  sudung_le,
			  img_path,
			  video_path
			  from product where pro_no = :pro_no";
		$res = $this->query($sql ,array('pro_no'=>$pro_no));
		if(count($res)> 0){
			return $res[0];
		}
		return null;
	}*/
	
	public static function action_delete()
	{
		$param = self::get_param(array(
			'acw_url'
		));
		$model = new News_model();
			
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
	private function delete($news_id){
		$sql ="delete from news where news_id = :news_id"; 
		return $this->execute($sql,array('news_id' =>$news_id));
	}
	public static function action_edit()
	{
		$param = self::get_param(array('acw_url'));	
		$db = new News_model();
		$ctg = new Category_model();
		$result = $db->get_news_info($param['acw_url'][0]);		
		$result['ctg_list'] = $ctg->get_category_rows(1);	
		return ACWView::template_admin('news/edit.html', $result);
	}
	public function get_news_info($news_id){
		$sql=" select n.news_id,
				  n.news_no,
				  n.news_name,				 
				  n.content,				 
				  n.des,
				  n.img_path,
				  n.add_date,
				  n.add_user,
				  n.upd_date,
				  n.upd_user,				  
				  n.del_flg,
				  n.ctg_id,
					c.ctg_name
			  from news n
				inner JOIN category c on c.ctg_id = n.ctg_id
			   where n.news_id = :news_id";
		$res = $this->query($sql ,array('news_id'=>$news_id));
		if(count($res)> 0){
			return $res[0];
		}
		return null;
	}
	
}

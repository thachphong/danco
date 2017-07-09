<?php

class Product_model extends ACWModel
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
		$model = new Product_model();
		$rows = $model->get_product_all($param);		
		return ACWView::template_admin('product.html', array(
			'products' => $rows			
		));
	}
	public function get_product_all(){
		$sql="select p.pro_id,
				p.pro_name,
				p.price_new,
				p.price_old,
				(case when p.status = 0 then 'còn hàng' else 'hết hàng' end) status,
				--(case when p.disp_home= 1 then 'hiện' else 'không' end) disp_home,
				p.disp_home,
				c.ctg_name,
				p.del_flg
				FROM
				product p
				INNER JOIN category  c on c.ctg_id = p.ctg_id 
				";
		return $this->query($sql);
	}
	public static function action_new()
	{
		$param = self::get_param(array('parent_id'));
		$ctg = new Category_model();
		$param['pro_id'] = null;
		$param['pro_name'] = null;
		$param['del_flg'] = 0;
		$param['ctg_id'] = NULL;		
		$param['price_new'] = null;
		$param['price_old'] = null;
		$param['disp_home'] = null;
		$param['status'] = null;		
		$param['img_path'] = null;		
		$param['content'] = null;
		$param['good_sell'] = null;
		$param['ctg_list'] = $ctg->get_category_rows(0);//addslashes(json_encode($ctg->get_category_rows()));
		$param['img_list'] = array();
		$param['price_list'] = array();
		$param['new_flg'] = 1;
		/*if(strlen($param['parent_id'])==0){
			$param['parent_id'] = 0;
		}*/
		//$param['sort'] = 1;
		return ACWView::template_admin('product/edit.html', $param);
		//return ACWView::template_admin('product/edit.html', $param,FALSE);
	}
	public static function action_update(){
		$param = self::get_param(array(
			  'pro_id',			  
			  'pro_name',
			  'ctg_id' ,
			  'price_old' ,
			  'price_new',
			  'status' ,
			  'disp_home' ,
			  'good_sell',
			  'img_add',
			  'content',			 
			  'del_flg'	,
			  'avata_id',
			  'img_del',
			  'pro_size'		 
			));
				
		$result['status'] = 'OK';	
		$result['msg'] = 'Cập nhật thành công!';		
		$db = new Product_model();
		$msg = $db->check_validate_update($param);
		if($msg == ""){
			if(strpos($param['avata_id'],'add') !== FALSE){
				$id_add = str_replace("add_","",$param['avata_id']);
				//$param['img_path'] = $param['img_add'][$id_add];
				//$param['img_path'] = "data".str_replace(ACW_BASE_URL."tmp","",$param['img_path']) ;
			}else{
				//$param['img_path'] = $db->get_img_path($param['avata_id']);
			}
			$pro_id = $param['pro_id'] ;
			if(strlen($param['pro_id'])==0){
				$pro_id = $db->insert($param);
				$db->insert_pro_img($pro_id,$param['img_add'],str_replace("add_","",$param['avata_id']) );
			}else{
				$db->update($param);
				if(isset($param['img_add']) && count($param['img_add']) > 0){
					if(strpos($param['avata_id'],'add') !== FALSE){
						// reset cai cu
						$db->reset_img_bypro($pro_id);
						$db->insert_pro_img($pro_id,$param['img_add'],str_replace("add_","",$param['avata_id']) );
					}else{
						$db->insert_pro_img($pro_id,$param['img_add'],-1);
						//reset all
						$db->reset_img_bypro($pro_id);
						$db->update_pro_img($param['avata_id'],1);						
					}
				}
			}
			
			if(isset($param['img_del']) && count($param['img_del']) > 0){
				$db->delete_pro_img($param['img_del']);
			}
		}else{
			$result['status'] = 'ER';	
			$result['msg'] = $msg;
		}
		return ACWView::json($result);
	}
	public function get_img_path($pro_img_id){
		$sql="select img_path from product_img where pro_img_id = $pro_img_id";
		$res = $this->query($sql);
		if(count($res)>0){
			return $res[0]['img_path'];
		}
		return "";
	}
	public function insert_pro_img($pro_id, $img_list,$avata_key){
		$sql="insert into product_img(pro_id,img_path,avata_flg,img_thumb) values(:pro_id,:img_path,:avata_flg,:img_thumb)" ;
		$param['pro_id'] = $pro_id;
		$file_lb = new FilePHPDebug_lib();
		foreach($img_list as $key => $item){			
			$param['img_thumb'] = str_replace(ACW_BASE_URL_DATA_TMP,"",$item) ;
			$param['img_path'] = str_replace("_thumb","",$param['img_thumb']) ;
			$param['avata_flg']	= 0;
			if($avata_key == $key){
				$param['avata_flg'] = 1;
			}		
			$this->execute($sql,$param);
			$info = explode("/",$param['img_path']);
			ACWLog::debug_var('tesx',$info);
			$img_folder = DATA_MAIN_PATH.'/'.$info[0];
			if(is_dir($img_folder)==false){
				 @mkdir($img_folder, 0777, true);
			}
			$img_folder .="/".$info[1];
			if(is_dir($img_folder)==false){
				 @mkdir($img_folder, 0777, true);
			}
			$img_des = DATA_MAIN_PATH.'/'.$param['img_path'];
			$img_src = DATA_TMP_PATH.'/'.$param['img_path'];
			$img_des_thumb = DATA_MAIN_PATH.'/'.$param['img_thumb'];
			$img_src_thumb =DATA_TMP_PATH.'/'.$param['img_thumb'];
			$file_lb->CopyFile($img_src,$img_des);
			$file_lb->CopyFile($img_src_thumb,$img_des_thumb);
			$file_lb->DeleteFile($img_src);
			$file_lb->DeleteFile($img_src_thumb);
		}
	}
	public function delete_pro_img($img_list){
		$sql="delete from  product_img where img_thumb= :img_thumb" ;
		$file_lb = new FilePHPDebug_lib();
		foreach($img_list as $item){
			$img_path = str_replace(ACW_BASE_URL_DATA,"",$item);		
			$this->execute($sql,array('img_thumb'=>$img_path));
			if(strlen($img_path) > 0){
				$file_lb->DeleteFile(DATA_MAIN_PATH.'/'.$img_path);
				$file_lb->DeleteFile(DATA_MAIN_PATH.'/'.str_replace("_thumb","",$img_path));
			}			
		}
	}
	public function reset_img_bypro($pro_id){
		$sql ="update product_img set avata_flg = 0 where pro_id = $pro_id";
		$this->execute($sql);
	}
	public function update_pro_img($pro_img_id,$avata_flg){
		$sql ="update product_img set avata_flg = $avata_flg where pro_img_id = $pro_img_id";
		$this->execute($sql);
	}
	public function check_validate_update(&$param){
		if(strlen($param['pro_name'])== 0){
			return "Bạn chưa nhập tên sản phẩm !";
		}
		$pro_no = ACWModel::convert_vi_to_en($param['pro_name']);
		$arr_rep = array(' - ',';',',',':','!','&','%',"'",'"','(',')','/',"\\" );
		$pro_no =str_replace($arr_rep,'', $pro_no);		
		$param['pro_no'] =str_replace(' ','-', $pro_no);

		$param['price_old'] = ACWModel::convert_string_to_number($param['price_old']);
		$param['price_new'] = ACWModel::convert_string_to_number($param['price_new']);
		$pro_no =  $param['pro_no'];
		if(strlen($param['pro_id']) == 0){
			$sql = "select pro_id from product	where pro_no = :pro_no";
			$row = $this->query($sql, array ('pro_no' => $param['pro_no'] ));
			//$row = $this->get_ctg_byno($param['pro_no']);
			$i = 0;
			while($row != null){
				$i++;
				$param['pro_no'] = $pro_no.'-'.$i;
				$row = $this->query($sql, array ('pro_no' => $param['pro_no'] ));
			}
		}else{
			$sql = "select pro_id from product	where pro_no = :pro_no	and pro_id <> :pro_id";
			$res = $this->query($sql, array ('pro_no' => $param['pro_no'] , 'pro_id'=>$param['pro_id']));
			$i = 0;
			while(count($res) > 0){
				$i++;
				$param['pro_no'] = $pro_no.'-'.$i;
				$res = $this->query($sql, array ('pro_no' => $param['pro_no'] , 'pro_id'=>$param['pro_id']));
			}
		}
		return "";
	}
	private function insert($param){
		//$this->begin_transaction();

		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['user_id'];
		//$param['ctg_no'] =str_replace(' ','-', ACWModel::convert_vi_to_en( $param['ctg_name']));
		$sql = "INSERT INTO product
					(
					  pro_name,					
					  pro_no,
					  ctg_id ,
					  price_old ,
					  price_new,
					  disp_home ,
					  good_sell ,
					  status,					 
					 -- img_path,	
					  del_flg,
					  content,				 
					  add_date,
					  add_user,
					  upd_date,
					  upd_user
					  
					)
				VALUES
					(
					  :pro_name,					
					  :pro_no,
					  :ctg_id ,
					  0,
					  0,
					  :disp_home ,
					  0 ,
					  :status,				 
					
					  :del_flg,
					  :content,	
					  now(),
					  :user_id,
					  now(),
					  :user_id
					)
				";
		
 
		$this->execute($sql, ACWArray::filter($param, array(					  		  
					  'pro_name',					
					  'pro_no',
					  'ctg_id' ,
					  //'price_old' ,
					  //'price_new',
					  'disp_home' ,
					  //'good_sell' ,
					  'status',					 
					
					  'del_flg',
					  'content',
					  'user_id'					  
					)));
		$result = $this->query("SELECT LAST_INSERT_ID() AS pro_id");					
		$new_id = $result[0]['pro_id'];
		//$this->commit();
		$sql_par['pro_id']= $new_id;
		foreach($param['pro_size'] as $key=>$item){
			if(strlen($item) >0){
				$sql_par['avata_flg'] =0;
				if($key == 0){
					$sql_par['avata_flg'] =1;
				}
				$sql_par['size'] = $item;
				$sql_par['price_new'] = $param['price_new'][$key];
				$sql_par['price_old'] = $param['price_old'][$key];
				$this->insert_price($sql_par);
			}
		}

		return $new_id;
	}
	private function insert_price($param){		
		$sql = "INSERT INTO product_price
					(					  			
					  pro_id,
					  size ,					  
					  price_new,
					  price_old,
					  avata_flg
					)
				VALUES
					(
					  :pro_id,					
					  :size,					 
					  :price_new,
					  :price_old,
					  :avata_flg
					)
				";	
 		if(strlen($param['price_old'])==0){
 			$param['price_old'] = 0;
 		}
		$this->execute($sql,$param);
	}
	protected function update($param){
		
		$this->begin_transaction();

		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['user_id'];		
		$sql = "update product
					set pro_name= :pro_name ,					
					  pro_no = :pro_no,
					  ctg_id = :ctg_id,					  
					  status = :status,					  				 				
					  upd_date =now(),
					  upd_user =:user_id,
					  del_flg =:del_flg,
					  content =:content,
					  disp_home =:disp_home
					where pro_id = :pro_id
				";
		
 		$sql_par = ACWArray::filter($param, array(
					'pro_id',
					'pro_name',					
					  'pro_no',
					  'ctg_id' ,
					 // 'price_old' ,
					 // 'price_new',
					 'disp_home' ,
					//  'good_sell' ,
					  'status',	
					  'del_flg',
					  'content',
					  'user_id'	
					));
		$this->execute($sql,$sql_par );			
		
		$this->execute("delete from product_price where pro_id=".$sql_par['pro_id']);
		$price_par['pro_id']= $sql_par['pro_id'];
		foreach($param['pro_size'] as $key=>$item){
			if(strlen($item) >0){
				$price_par['avata_flg'] =0;
				if($key == 0){
					$price_par['avata_flg'] =1;
				}
				$price_par['size'] = $item;
				$price_par['price_new'] = $param['price_new'][$key];
				$price_par['price_old'] = $param['price_old'][$key];
				$this->insert_price($price_par);
			}
		}
		$this->commit();
		return TRUE;	
	}
	public function get_product_byno($pro_no){
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
	}
	
	public static function action_delete()
	{
		$param = self::get_param(array(
			'acw_url'
		));
		$model = new Product_model();
			
		$result['status']="OK";
		if(!isset($param['acw_url'][0]) || strlen($param['acw_url'][0])== 0 ){
			$result['status']="NOT";
			$result['msg']= $msg;
		}else
		{
			$img_list = $model->get_img_list($param['acw_url'][0]);
			$model->delete($param['acw_url'][0]);			
			$file_lb = new FilePHPDebug_lib();
			foreach($img_list as $item){
				if(strlen($item['img_path']) > 0){
					$file_lb->DeleteFile(DATA_MAIN_PATH.'/'.$item['img_path']);
				}
				if(strlen($item['img_thumb']) > 0){
					$file_lb->DeleteFile(DATA_MAIN_PATH.'/'.$item['img_thumb']);
				}
			}			
		}
		
		return ACWView::json($result);
	}
	private function delete($pro_id){
		$sql ="delete from product where pro_id = :pro_id"; 
		$this->execute($sql,array('pro_id' =>$pro_id));
		$sql ="delete from  product_img where pro_id= :pro_id" ;
		$this->execute($sql,array('pro_id' =>$pro_id));
		
	}
	public static function action_edit()
	{
		$param = self::get_param(array('acw_url'));	
		$db = new Product_model();
		$result = $db->get_product_info($param['acw_url'][0]);
		$ctg = new Category_model();
		$result['ctg_list'] = $ctg->get_category_rows(0);
		$result['img_list'] = $db->get_img_list($param['acw_url'][0]);
		$result['price_list'] = $db->get_price_list($param['acw_url'][0]);
		$result['new_flg'] =0;
		return ACWView::template_admin('product/edit.html', $result);
	}
	public function get_product_info($pro_id){
		$sql=" select pro_name,
					  pro_id,
					  pro_no,			
					  ctg_id ,
					  price_old ,
					  price_new,
					  disp_home ,
					  good_sell ,
					  status,					 
					  img_path,	
					  del_flg,
					  content
			  from product where pro_id = :pro_id";
		$res = $this->query($sql ,array('pro_id'=>$pro_id));
		if(count($res)> 0){
			return $res[0];
		}
		return null;
	}
	public function get_img_list($pro_id){
		$sql="select img_path,pro_img_id,avata_flg,img_thumb from product_img where pro_id = $pro_id";
		return  $this->query($sql);		
	}
	public function get_price_list($pro_id){
		$sql="select *  from product_price where pro_id  = $pro_id";
		return  $this->query($sql);		
	}
	public static function action_upload()
	{
		
		$file_name="abc.jpg";
		return ACWView::template_admin('product/upload.html', array(
			'file_name' => $file_name			
		));
	}
}

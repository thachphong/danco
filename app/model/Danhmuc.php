<?php
class Danhmuc_model extends ACWModel
{		
	
	public static function action_index()
	{
		$param = self::get_param(array('acw_url'));	
		if(self::get_validate_result() == false){
			ACWError::add('acw_url', 'Tham số không hợp lệ !');
		}
		$db = new Sanpham_model();
		$param['sanphams']=$db->get_sanpham_byctgno($param['acw_url'][0]);
		return ACWView::template_admin('danhmuc.html', $param);
	}
	public static function action_ds()
	{
		$param = self::get_param(array('acw_url','page'));	
		if(self::get_validate_result() == false){
			ACWError::add('acw_url', 'Tham số không hợp lệ !');
		}
		$db = new Danhmuc_model();
		$start_row = 0;
		if(isset($param['page']) && $param['page'] > 1){
			$start_row = $param['page']*PAGE_LIMIT_RECORD;
		}else{
			$param['page'] = 1;
		}
		$info = $db->get_danhmuc_info($param['acw_url'][0]);
		$param['ctg_name'] = $info['ctg_name'];
		$param['sanphams']=$db->get_sanpham_byctgno($param['acw_url'][0],$start_row);
		$param['total_page']= round($db->get_total_rows($param['acw_url'][0])/PAGE_LIMIT_RECORD);
		$param['ctg_no'] = $param['acw_url'][0];
		return ACWView::template('danhmuc.html', $param);
	}
	public static function action_tim()
	{
		$param = self::get_param(array('acw_url','page','txt'));	
		/*if(self::get_validate_result() == false){
			ACWError::add('acw_url', 'Tham số không hợp lệ !');
		}*/
		$db = new Danhmuc_model();
		$start_row = 0;
		if(isset($param['page']) && $param['page'] > 1){
			$start_row = $param['page']*PAGE_LIMIT_RECORD;
		}else{
			$param['page'] = 1;
		}
		//$info = $db->get_danhmuc_info($param['acw_url'][0]);
		$param['ctg_name'] = "Kết quả tìm";
		$param['sanphams']=$db->search_sanpham($param['txt'],$start_row);
		$param['total_page']= round($db->search_sanpham_total($param['txt'])/PAGE_LIMIT_RECORD);
		$param['ctg_no'] = "tim";
		return ACWView::template('danhmuc.html', $param);
	}
	public function get_sanpham_byctgno($ctg_no,$start_row=0){
		$limit = PAGE_LIMIT_RECORD;
		$sql="select p.pro_no,p.pro_name,im.img_thumb,t.price_new,p.price_old
				from product p
				INNER JOIN product_price t
					on t.pro_id = p.pro_id and t.avata_flg = 1 
				INNER JOIN product_img im on im.pro_id = p.pro_id and im.avata_flg = 1
				INNER JOIN category c on c.ctg_id = p.ctg_id and c.del_flg = 0
				where c.ctg_no =:ctg_no
				and p.del_flg = 0 
				limit $limit
				OFFSET $start_row
				";
		return $this->query($sql,array('ctg_no'=>$ctg_no));
	}	
	public function get_total_rows($ctg_no){
		$sql = "select count(p.pro_id) cnt
				from product p
				INNER JOIN category c on c.ctg_id = p.ctg_id and c.del_flg = 0
				where c.ctg_no = :ctg_no
				and p.del_flg = 0";
		$res = $this->query($sql,array('ctg_no'=>$ctg_no));
		if(count($res) > 0){
			return $res[0]['cnt'];
		}
		return 0;
	}
	public static function get_danhmucs(){
		$db = new Danhmuc_model();
		return $db->get_danhmuc_all();
	}
	public function get_danhmuc_all(){
		$sql ="select ctg_no,ctg_name from category 
				where del_flg = 0 and news_flg =0
				";
		return $this->query($sql);
	}
	public function get_danhmuc_info($ctg_no){
		$sql ="select ctg_no,upper(ctg_name) ctg_name from category 
				where del_flg = 0 and ctg_no =:ctg_no
				";
		$res = $this->query($sql,array('ctg_no'=>$ctg_no));
		if(count($res) > 0){
			return $res[0];			
		}
		return NULL;
	}
	public function search_sanpham($search,$start_row=0){
		$txt =str_replace('   ',' ', $search);
		$txt =str_replace('  ',' ', $txt);
		$txt ='%'. str_replace(' ','-', ACWModel::convert_vi_to_en($txt)). '%';
		$limit = PAGE_LIMIT_RECORD;
		$sql="select p.pro_no,p.pro_name,im.img_thumb,p.price_new,p.price_old
				from product p
				INNER JOIN product_img im on im.pro_id = p.pro_id and im.avata_flg = 1				
				where p.pro_no like :txt
				and p.del_flg = 0 
				limit $limit
				OFFSET $start_row
				";
		return $this->query($sql,array('txt'=>$txt));
	}
	public function search_sanpham_total($search){
		$txt =str_replace('   ',' ', $search);
		$txt =str_replace('  ',' ', $txt);
		$txt ='%'. str_replace(' ','-', ACWModel::convert_vi_to_en($txt)) .'%';		
		$sql="select count(p.pro_no) cnt
				from product p
				INNER JOIN product_img im on im.pro_id = p.pro_id and im.avata_flg = 1				
				where p.pro_no like :txt
				and p.del_flg = 0 			
				";
		$res = $this->query($sql,array('txt'=>$txt));
		if(count($res) > 0){
			return $res[0]['cnt'];
		}
		return 0;
	}	
}

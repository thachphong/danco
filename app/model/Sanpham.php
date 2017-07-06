<?php

class Sanpham_model extends ACWModel
{		
		
	public static function action_index()
	{
		$param = self::get_param(array(
			'search_user_name'
		));
		$model = new Sanpham_model();
		$rows = $model->get_sanpham_all($param);		
		return ACWView::template_admin('sanpham.html', array(
			'products' => $rows,
			'lang_list'=>'',
			'search_user_name'=>$param['search_user_name']
		));
	}
	public function get_sanpham_new($limit = 1){
		$sql="select p.pro_no,p.pro_name,im.img_thumb,t.price_new,t.price_old,IFNULL(round(( 1 - t.price_new/t.price_old)*100),0) as giamgia
				from product p
				INNER JOIN product_img im on im.pro_id = p.pro_id and im.avata_flg = 1	
				INNER JOIN product_price t on t.pro_id = p.pro_id and t.avata_flg = 1			
				ORDER BY p.pro_id desc
				limit $limit
				";
		return $this->query($sql);
	}	
	public function get_sanpham_byhome($ctg_id,$limit = 1){
		$sql="select p.pro_no,p.pro_name,im.img_thumb,t.price_new,t.price_old,IFNULL(round(( 1 - t.price_new/t.price_old)*100),0) as giamgia
				from product p
				INNER JOIN product_img im on im.pro_id = p.pro_id and im.avata_flg = 1
				INNER JOIN product_price t on t.pro_id = p.pro_id and t.avata_flg = 1
				where ctg_id = $ctg_id
				and p.del_flg = 0
				ORDER BY p.pro_id desc
				limit $limit
				";
		return $this->query($sql);
	}
	public function get_sanpham_banchay($limit = 1){
		$sql="select p.pro_no,p.pro_name,im.img_thumb,p.price_new,p.price_old
				from product p
				INNER JOIN product_img im on im.pro_id = p.pro_id and im.avata_flg = 1
				where p.good_sell = 1
				limit ".GOODSELL_LIMIT_RECORD."
				";
		return $this->query($sql);
	}
	public static function get_sanpham_moi(){
		$db= new Sanpham_model();
		return $db->get_sanpham_new(4);
	}
	public static function get_sanpham_home($ctg_id){
		$db= new Sanpham_model();
		return $db->get_sanpham_byhome($ctg_id,3);
	}
	public static function get_sanpham_goodsell(){
		$db= new Sanpham_model();
		return $db->get_sanpham_banchay();
	}
	public static function action_v()
	{
		$param = self::get_param(array(
			'acw_url'
		));
		$model = new Sanpham_model();
		$pro_no = $param['acw_url'][0];
		$rows = $model->get_sanpham_info($pro_no);		
		return ACWView::template('sanpham.html', array(
			'pro' => $rows	
			//,'relates'=>$model->get_sanpham_lienquan($pro_no)	
			,'imglist'=>$model->get_list_img($rows['pro_id'])
			,'pricelist'=>$model->get_price_list($rows['pro_id'])
			
		),FALSE);
	}
	public function get_sanpham_info($pro_no){
		$sql=" select p.pro_no,p.pro_name,t.price_new,p.price_old,c.ctg_name,p.pro_id,c.ctg_no,p.content
				from product p
				INNER JOIN product_price t
					on t.pro_id = p.pro_id and t.avata_flg = 1 			
				INNER JOIN category c on c.ctg_id = p.ctg_id and c.del_flg = 0
				where  p.del_flg = 0 
				and pro_no =:pro_no ";
		$res = $this->query($sql ,array('pro_no'=>$pro_no));
		if(count($res)> 0){
			return $res[0];
		}
		return null;
	}	
	public function get_sanpham_lienquan($pro_no){
		$sql="select p.pro_no,p.pro_name,im.img_thumb,p.price_new,p.price_old
				from product p
				INNER JOIN product_img im on im.pro_id = p.pro_id and im.avata_flg = 1
				INNER JOIN category c on c.ctg_id = p.ctg_id and c.del_flg = 0
				where 
				 p.del_flg = 0 
				 and pro_no <> :pro_no
				and p.ctg_id in (
									select ctg_id
									from product 
									where del_flg = 0
									and pro_no = :pro_no
				)
				limit 4";
		$res = $this->query($sql ,array('pro_no'=>$pro_no));		
		return $res;
	}
	public function get_list_img($pro_id){
		$sql ="select img_thumb,img_path from product_img where pro_id = $pro_id";
		return $this->query($sql);
	}
	public function get_price_list($pro_id){
		$sql="select *  from product_price where pro_id  = $pro_id";
		return  $this->query($sql);		
	}
}

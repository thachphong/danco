<?php

class Cart_model extends ACWModel
{
	
	public static function init()
	{
		//Login_model::check();	
	}
	
	public static function action_index()
	{				
		$db = new Cart_model();
		$cart = ACWSession::get("cart_info");
		//ACWLog::debug_var('--cart--',$cart );
		$products = array();
		$total_amount = 0;
		if($cart != NULL){
			$products = $db->get_products($cart);			
			foreach($products as &$item){
				$item['qty'] = $cart[$item['pro_price_id']];
				$item['amount'] = $item['qty']*$item['price_new'];
				$total_amount += $item['amount'];
			}
		}
		
		return ACWView::template('cart.html',array(
			'carts' =>$products
			,'total_amount'=>$total_amount
		));
	}
	public static function action_success()
	{	
		$param = self::get_param(array('acw_url'));
		$order_id  = $param['acw_url'][0];
		$db = new Cart_model();
		$result['ord'] = $db->get_order($order_id);
		$result['ord_detail'] = $db->get_order_detail($order_id);
		return ACWView::template('success.html',$result);
	}
	public static function action_add(){

		$param = self::get_param(array(
			'pro_id',
			'pro_qty',
			'pro_size'
		));
		$cart = ACWSession::get("cart_info");
		if($cart != NULL){
			if(isset($cart[$param['pro_size']])){
				$cart[$param['pro_size']]= $cart[$param['pro_size']] + $param['pro_qty'];				
			}else{
				//$size_info =$this->get_price($param['pro_size']);
				$cart[$param['pro_size']]=  $param['pro_qty'];
				//$cart[$param['pro_size']]['price'] = $size_info['price_new'];
				//$cart[$param['pro_size']]['size'] = $size_info['size'];
			}
		}else{
			//$size_info =$this->get_price($param['pro_size']);
			$cart[$param['pro_size']] =  $param['pro_qty'];
			//$cart[$param['pro_size']]['price'] = $size_info['price_new'];
			//$cart[$param['pro_size']]['size'] = $size_info['size'];
		}
		
		ACWSession::set("cart_info",$cart);
		return ACWView::redirect(ACW_BASE_URL . 'cart');
	}
	public static function action_delete(){

		$param = self::get_param(array('acw_url'));
		$pro_price_id  = $param['acw_url'][0];	
		$cart = ACWSession::get("cart_info");
		
		unset($cart[$pro_price_id ]);
		
		ACWSession::set("cart_info",$cart);
		return ACWView::redirect(ACW_BASE_URL . 'cart');
	}
	public static function action_pay(){
		$param = self::get_param(array(
			'pro_id',
			'quantity'
		));
		$db = new Cart_model();
		$cart = array();//ACWSession::get("cart_info");
		foreach($param['pro_id'] as $key=>$val){
			$cart[$val] = $param['quantity'][$key];
		}
		/*if($cart != NULL){
			if(isset($cart[$param['pro_id']])){
				$cart[$param['pro_id']] = $cart[$param['pro_id']] + $param['quantity'];
			}
		}*/
		//$cart[$param['pro_id']] =  $param['quantity'];
		ACWSession::set("cart_info",$cart);
		ACWLog::debug_var('cart',$cart);
		$products = $db->get_products($cart);
		$total_amount = 0;
		foreach($products as &$item){
			$item['qty'] = $cart[$item['pro_id']];
			$item['amount'] = $item['qty']*$item['price_new'];
			$total_amount += $item['amount'];
		}
		return ACWView::template('payment.html',array(
			'carts' =>$products
			,'total_amount'=>$total_amount
		));
	}
	public static function action_update(){
		//ACWLog::debug_var('---cart---',$_POST);
		$param = self::get_param(array(
			  'bill_first_name',
			  'bill_last_name',
			  'bill_company',
			  'bill_email',
			  'bill_phone',
			  'bill_address' ,
			  'order_comments',
			  'payment_method' ,
			  'pro_qty',
			  'pro_price_id',
			  'address_ship'
		));
		//ACWLog::debug_var('----test--',$param);
		$cart = array();//ACWSession::get("cart_info");
		foreach($param['pro_price_id'] as $key=>$val){
			$cart[$val] = $param['pro_qty'][$key];
		}		
		$db = new Cart_model();
		$products = $db->get_products($cart);
		$total_amount = 0;
		foreach($products as &$item){
			$item['qty'] = $cart[$item['pro_price_id']];
			$item['amount'] = $item['qty']*$item['price_new'];
			$total_amount += $item['amount'];
		}
		$param['total_amount']=($total_amount + $total_amount*0.1);
		$param['total_vat'] = $total_amount*0.1;
		$param['full_name'] = $param['bill_first_name'].' '.$param['bill_last_name'];
		$ord_id = $db->insert_order($param);
		$db->insert_order_detail($ord_id,$products);
		$db->sendmail($param,$products);
			
		ACWSession::set("cart_info",NULL);
		//return ACWView::template('success.html');
		return ACWView::redirect(ACW_BASE_URL . 'cart/success/'.$ord_id);
	}
	public function get_order($ord_id){
		$sql="select *,DATE_FORMAT(ord_date,'%d/%m/%Y') add_datetime from orders where ord_id = $ord_id";
		return $this->query_first($sql);
	}
	public function get_order_detail($ord_id){
		$sql="select d.*,p.pro_name,p.pro_no from 
				orders_detail d
				INNER JOIN product p
					on p.pro_id = d.pro_id
				where d.ord_id  = $ord_id";
		return $this->query($sql);
	}
	public function get_price($pro_price_id){
		$sql="select * from product_price
				where pro_price_id = $pro_price_id";
		return $this->query_first($sql);
	}
	public function get_products($cart){
		$listid = array_keys($cart);
		
		$sql ="select p.pro_id,p.pro_no,p.pro_name,im.img_thumb,t.price_new,p.price_old,t.size,t.pro_price_id
				from product p
				INNER JOIN product_price t on t.pro_id = p.pro_id
				INNER JOIN product_img im on im.pro_id = p.pro_id and im.avata_flg = 1
				where t.pro_price_id in (".implode(',',$listid).")";
		return $this->query($sql);
	}
	public function insert_order($param){
		$this->begin_transaction();
		$sql=" insert into orders(
					ord_date
					,full_name
					,company
					,email
					,phone
					,address
					,note
					,total_amount
					,payment_method
					,address_ship
					,total_vat
					)
				values(
					now()
					,:full_name
					,:bill_email
					,:bill_company
					,:bill_phone
					,:bill_address
					,:order_comments
					,:total_amount
					,:payment_method
					,:address_ship
					,:total_vat
				)";
		$sql_pa = ACWArray::filter($param, array(
					'full_name'
					,'bill_email'
					,'bill_company'
					,'bill_phone'
					,'bill_address'
					,'order_comments'
					,'total_amount'
					,'address_ship'
					,'total_vat'));
		if($param['payment_method']=='ck'){
			$sql_pa['payment_method']='Chuyển khoản';
		}else if($param['payment_method']=='tm'){
			$sql_pa['payment_method']='Tiền mặt';
		}
		$this->execute($sql,$sql_pa);
		$result = $this->query("SELECT LAST_INSERT_ID() AS ord_id");			
		$new_id = $result[0]['ord_id'];
		$this->commit();
		return $new_id;
	}
	public function insert_order_detail($ord_id,$param){
		$sql=" insert into orders_detail(
					ord_id
					,pro_id
					,price
					,qty
					,total
					,size
					)					
				values(
					:ord_id
					,:pro_id
					,:price
					,:qty
					,:total
					,:size
				)";		
		$sql_pa['ord_id']=$ord_id;
		foreach($param as $item){
			$sql_pa['pro_id']= $item['pro_id'];
			$sql_pa['price']= $item['price_new'];
			$sql_pa['qty']= $item['qty'];
			$sql_pa['size']= $item['size'];
			$sql_pa['total']= $item['amount'];
			$this->execute($sql,$sql_pa);
		}
		return TRUE;
	}
	public function sendmail($param,$cart){
		$email = new Mail_lib();
		
		$body_tmp = $this->get_body($param,$cart);		
		$replacements['HEADER'] = '<h2><strong>Thông tin đặt hàng </strong></h2>';
		$replacements['BODY'] = $body_tmp;
		$db = new Define_model();
		$data = $db->get_define(DEFINE_KEY_EMAIL);
		$mail_to[]['mail_address']= $data['define_val'];
		$mail_to[]['mail_address']=$param['bill_email'];	
		ACWLog::debug_var('--mail',$mail_to);	
		ACWLog::debug_var('--mail',$data);
		$email->AddListAddress($mail_to);
                
		$email->Subject = 'Thông tin đặt hàng - '.date('d/m/Y H:i:s');                
		$email->loadbody('template_mail.html');
		$email->replaceBody($replacements);
		$result = $email->send();
	}
	public function get_body($param,$cart){
		$html="<h3>Thông tin khách hàng</h3><table>";
		
		if(strlen($param['full_name']) > 0){
			$html .= "<tr><td><strong>Họ tên: </strong></td><td>".$param['full_name']."</td></tr>"."\r\n";
		} 
		if(strlen($param['bill_company']) > 0){
			$html .= "<tr><td><strong>Công ty: </strong></td><td>".$param['bill_company']."</td></tr>"."\r\n";
		}
		if(strlen($param['bill_email']) > 0){
			$html .= "<tr><td><strong>Email: </strong></td><td>".$param['bill_email']."</td></tr>"."\r\n";
		}
		if(strlen($param['bill_phone']) > 0){
			$html .= "<tr><td><strong>Điện thoại: </strong></td><td>".$param['bill_phone']."</td></tr>"."\r\n";
		}
		if(strlen($param['bill_address']) > 0){
			$html .= "<tr><td><strong>Địa chỉ: </strong></td><td>".$param['bill_address']."</td></tr>"."\r\n";
		}
		if(strlen($param['address_ship']) > 0){
			$html .= "<tr><td><strong>Địa chỉ nhận hàng: </strong></td><td>".$param['address_ship']."</td></tr>"."\r\n";
		}
		if(strlen($param['order_comments']) > 0){
			$html .= "<tr><td><strong>Ghi chú: </strong></td><td>".$param['order_comments']."</td></tr>"."\r\n";
		}
		$html .="</table>";
		$html .="<h3>Hình thức thanh toán</h3>";
		if($param['payment_method']=='ck'){
			$html .="<p>Chuyển khoản</p>";
		}else if($param['payment_method']=='tm'){
			$html .="<p>Tiền mặt</p>";
		}
		
		$html .="<h3>Thông tin đơn hàng</h3><table style=\"border-collapse: collapse;\">";
		$html .= "<tr><td style=\"border: 1px solid #d7dbdb;padding:10px;\">STT</td>
				  <td style=\"border: 1px solid #d7dbdb;padding:10px;\">Tên hàng</td>
				  <td style=\"border: 1px solid #d7dbdb;padding:10px;\">Kích thước</td>
				  <td style=\"border: 1px solid #d7dbdb;padding:10px;\">Giá</td>
				  <td style=\"border: 1px solid #d7dbdb;padding:10px;\">Số lượng</td>
				  <td style=\"border: 1px solid #d7dbdb;padding:10px;\">Thành tiền</td>
				  </tr>";
		foreach($cart as $key=>$item){			
			$html .= "<tr><td style=\"border: 1px solid #d7dbdb;padding:5px;\">".($key+1)."</td>
			<td style=\"border: 1px solid #d7dbdb;padding:10px;\">".$item['pro_name']."</td>
			<td style=\"border: 1px solid #d7dbdb;padding:10px;\">".$item['size']."</td>
			<td style=\"border: 1px solid #d7dbdb;padding:10px;\">".self::currency_format($item['price_new'])." VNĐ</td>
			<td style=\"border: 1px solid #d7dbdb;padding:10px;\">".self::currency_format($item['qty'])."</td>
			<td style=\"border: 1px solid #d7dbdb;padding:10px;\">".self::currency_format($item['amount'])." VNĐ</td></tr>";
		}
		$html .='<tr><td colspan="5" style="border: 1px solid #d7dbdb;padding:10px;">Tổng tiền</td>
		<td style="border: 1px solid #d7dbdb;padding:10px;">'.self::currency_format($param['total_amount']-$param['total_vat']).' VNĐ</td></tr>';
		$html .='<tr><td colspan="5" style="border: 1px solid #d7dbdb;padding:10px;">Tổng VAT</td>
		<td style="border: 1px solid #d7dbdb;padding:10px;">'.self::currency_format($param['total_vat']).' VNĐ</td></tr>';
		$html .='<tr><td colspan="5" style="border: 1px solid #d7dbdb;padding:10px;">Tổng tiền thanh toán</td>
		<td style="border: 1px solid #d7dbdb;padding:10px;">'.self::currency_format($param['total_amount']).' VNĐ</td></tr>';
		$html .="</table>";
		return $html;
	}
}

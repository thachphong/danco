<?php

class User_model extends ACWModel
{	
	public static function init()
	{
		Login_model::check();	
	}
	
	public static function validate($action, &$param)
	{
		switch ($action) {
            case 'update':
				return self::_validate_update($param);
			case 'updatepass':
				return self::_validate_updatepass($param);
            case 'index':
                    if($param['search_user_name'] != ''){
                        $s_user_name = $param['search_user_name'];
                        $param['s_user_name'] = strtolower($s_user_name);
                    }
                break;  
        }
		return true;
	}
	
	public static function action_index()
	{
		$param = self::get_param(array(
			'search_user_name'
		));
		$model = new User_model();
		$rows = $model->get_user_rows($param);		
		return ACWView::template_admin('user.html', array(
			'user_rows' => $rows,
			'lang_list'=>'',
			'search_user_name'=>$param['search_user_name']
		));
	}
	public static function action_editpass()
	{
		$param = self::get_param(array('acw_url'));	
				
		$model = new User_model();
		$user_row = $model->get_user_row($param['acw_url'][0]);
		return ACWView::template_admin('user/editpass.html', $user_row);
	}
	public static function action_updatepass()
	{
		$params = self::get_param(array(			
			'old_pass',
			'new_pass',
            'new_pass_re',
            'user_no'
		));
	    
		if (self::get_validate_result() === true) {
			$model = new User_model();
			$obj = $model->updatepass($params);
		}
		
		if (ACWError::count() <= 0) {
		    $result['status'] = 'OK';
		} else {
			$result['status'] = 'NG';
			$result['msg'] = ACWError::get_list();
		}

		return ACWView::json($result);
	}
	public static function action_edit()
	{
		$param = self::get_param(array('acw_url'));	
				
		$model = new User_model();
		$user_row = $model->get_user_row($param['acw_url'][0]);
		
		return ACWView::template_admin('user/edit.html', $user_row);
	}
	
	private static function _validate_update(&$param)
	{
	    
		/*$validate = new Validate_lib();
		
		$param['user_id'] = $validate->trim_ext($param['user_id']);
		$param['user_name'] = $validate->trim_ext($param['user_name']);
       
        
		if ($validate->type_str('user_name', 'Tên đăng nhập', $param['user_name'], true) == false) {
			return false;
		}        
        if($param['user_name'] != ''){
            if(strlen($param['user_name']) > 50){
                ACWError::add('lelng', 'Tên đăng nhập không được quá 50 ký tự');
                return false;
            }
        }*/
		return true;
	}
	private static function _validate_updatepass(&$param)
	{		
		$login = new Login_model();
		//$login_info = ACWSession::get('user_info');
		$user = $login->check_login(array('passwd'=>$param['old_pass'],'user_id'=>$param['user_no']));
		if($user == null){
			ACWError::add('saipass',"Mật khẩu cũ không đúng !"); //'Mật khẩu cũ không đúng !'
            return false;
		}
		return true;
	}
    public static function action_checkmaxlenght() {
        
        $result['error'] = array();        
        return ACWView::json($result);    
    }
   
	
	/**
	* 更新
	*/
	public static function action_update()
	{
		$params = self::get_param(array(			
			'user_id',
			'user_name',          
            'email',
		));	   
		if (self::get_validate_result() === true) {
			$model = new User_model();
			$obj = $model->update($params);
		}
		
		if (ACWError::count() <= 0) {
		    $result['status'] = 'OK';
		} else {
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		}
		return ACWView::json($result);
	}
	
	
	public function get_user_rows($param)
	{
		$sql = "
			SELECT				 
				 *
			FROM
				user 
							
		";
		
		if (isset($param['s_user_name'])) {
			$sql_param = array(
					'user_name' =>  '%' . SQL_lib::escape_like($param['s_user_name']) . '%'
				);
			$sql .= " WHERE lower(usr.user_name) like lower(:user_name) ";
		} else {
			$sql_param = array();
		}
		
		$sql .= "
			ORDER BY
				user_id
		";
		//var_dump($sql);die;
		return $this->query($sql, $sql_param);
	}
		
	public function update($params)
	{
		$this->begin_transaction();
							
			
			$sql = "
				UPDATE
					user
				SET					 
					 user_name = :user_name					
                    ,email=:email	
					, upd_date = NOW()					
				WHERE
					user_id = :user_id
			";			
		$sql_params = ACWArray::filter($params, array(
					'user_name'
					,'email'					
					,'user_id'								
					));
		$this->execute($sql, $sql_params);		
		
		$this->commit();
		
		return true;
	}
	public function updatepass($params)
	{
		$this->begin_transaction();
		$sql = "
				UPDATE
					user
				SET					
					 pass = :pass					
					, upd_date= NOW()					
				WHERE
					user_no = :user_no
			";
		$login_info = ACWSession::get('user_info');
			$pass_md5 = md5(AKAGANE_SALT . $params['new_pass']);
			$sql_params['user_no'] = $login_info['user_no'];
			$sql_params['pass'] = $pass_md5;
		
		$this->execute($sql, $sql_params);		
		
		$this->commit();
		
		return true;
	}
	
	
	public function get_user_row($m_user_id)
	{
		$rows = $this->query("
				SELECT
					usr.*
				FROM
					user usr			
                WHERE
					usr.user_id = :m_user_id
			", array("m_user_id" => $m_user_id)
		);
		
		return $rows[0];
	}
    public function check_only_upload($m_user_id){
        $data_row = $this->get_user_row($m_user_id);
        if($data_row['upload'] > 0 && $data_row['kiemtra']=='0'&& $data_row['duyet']=='0'&& $data_row['trungtam_quanly']=='0'&& $data_row['phanbo']=='0'&& $data_row['admin']=='0'){
            return TRUE;
        }
        return FALSE;
    }
	public function get_user_bylevel($level)
	{
		$sql = " SELECT		usr.*
				FROM	m_user usr				
	          left join level l on l.level_id = usr.level					
	          WHERE	usr.del_flg=0 ";
		if(isset($level['kiemtra'])&& $level['kiemtra']==1){
			$sql .= " AND l.kiemtra = 1";
		}			
		if(isset($level['duyet'])&& $level['duyet']==1){
			$sql .= " AND l.duyet = 1";
		}
		if(isset($level['trungtam_quanly'])&& $level['trungtam_quanly']==1){
			$sql .= " AND l.trungtam_quanly = 1";
		}
		return $this->query($sql);
	}
	public function get_donvi()
	{
		$sql = "select * FROM don_vi where del_flg= 0 ";
		return $this->query($sql);
	}
    public function get_phongban()
	{
		$sql = "select * FROM phong_ban where del_flg= 0 ";
		return $this->query($sql);
	}   
    public function get_tonhom()
	{
		$sql = "select * FROM to_nhom where del_flg= 0 ";
		return $this->query($sql);
	}	
	private function get_user_id_count($param)
	{
		$sql = "
			SELECT
				COUNT(*) AS cnt
			FROM
				m_user
			WHERE
				user_name = :user_name
		";
		
		$filter = ACWArray::filter($param, array('user_name'));
		$rows = $this->query($sql, $filter);
		return $rows[0];
	}
	public static function action_redirect(){
		$user_info = ACWSession::get('user_info');
		
		if($user_info['upload'] > 0 || $user_info['kiemtra'] > 0 || $user_info['duyet'] > 0 || $user_info['trungtam_quanly'] > 0){
            return ACWView::redirect(ACW_BASE_URL . 'don');
        }else if($user_info['print'] > 0){
            return ACWView::redirect(ACW_BASE_URL . 'file/blank');
        }else if($user_info['phanbo'] > 0){  // cap phat
            return ACWView::redirect(ACW_BASE_URL . 'file/capphat');
        }else if($user_info['admin'] > 0){  
            return ACWView::redirect(ACW_BASE_URL . 'file/donvi');
        }
        return ACWView::redirect(ACW_BASE_URL . 'don');
        
    }
	
}
/* ファイルの終わり */
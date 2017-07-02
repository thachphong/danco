<?php
/**
 * capphat
 *
*/
class Download_model extends ACWModel
{
	/**
	* 共通初期化
	*/
	public static function init()
	{
		Login_model::check();	// ログインチェック
	}
	
	public static function action_index()
	{
		$param = self::get_param(array(
			's_donvi'
            ,'s_user_id'
            ,'s_tonhom'
            ,'tu_ngay'
            ,'den_ngay'
            ,'file_name'
            ,'loai'
		));
		$model = new Download_model();
		$rows = $model->get_print_rows($param);		
        $usr = new User_model();
		return ACWView::template('download.html', array(
			'data_rows' => $rows			
			,'search_data'=>$param
            ,'donvi_list' =>$usr->get_donvi()
            ,'user_list' =>$usr->get_user_rows(null)
           // ,'tonhom_list' =>$usr->get_tonhom()
		));
	}
    public function insert_download($file_id){
        $sql="insert into download(file_id,user_id,add_datetime)
              values(:file_id,:user_id,now())";
        $user= ACWSession::get('user_info');
        $this->execute($sql,array('file_id'=>$file_id,'user_id'=>$user['user_id']));
    }
    public function get_print_rows($param)
	{
		$sql = "
			select d.donvi_name,
            u.user_name,
            p.phongban_name,
            n.tonhom_name,
			f.file_name,";
        $group="";
        if (isset($param['loai']) && $param['loai']=="1" ) {			
			$sql .= " DATE_FORMAT(t.add_datetime,'%d/%m/%Y') add_datetime,
						count(t.file_id) soluong";
            $group =" group by d.donvi_name,
            p.phongban_name,
            n.tonhom_name,
             u.user_name,
			f.file_name,DATE_FORMAT(t.add_datetime,'%d/%m/%Y')";
		}else{
            $sql .= " DATE_FORMAT(t.add_datetime,'%d/%m/%Y %H:%i:%s') add_datetime  ,
			         1 as soluong";
        }  
        $sql .="
            from download t
                INNER JOIN file f on f.file_id = t.file_id 
                INNER JOIN m_user  u on u.user_id = t.user_id
                INNER JOIN don_vi d on d.donvi_id = u.donvi
                INNER JOIN phong_ban p on p.phongban_id= u.phong_ban
                LEFT JOIN to_nhom n on n.tonhom_id = u.to_nhom
            where 1 = 1
		";
		$sql_param = array();
		if (isset($param['s_donvi']) ) {
			$sql_param['donvi'] =$param['s_donvi'];
			$sql .= " and d.donvi_id = :donvi ";
		}
        if (isset($param['s_user_id']) ) {
			$sql_param['user_id'] =$param['s_user_id'];
			$sql .= " and u.user_id = :user_id ";
		}
        if (isset($param['s_tonhom']) ) {
			$sql_param['tonhom'] =$param['s_tonhom'];
			$sql .= " and n.tonhom_id = :tonhom ";
		}
		if (isset($param['file_name']) && empty($param['file_name'])==FALSE) {
			$sql_param['file_name'] = '%'.SQL_lib::escape_like($param['file_name']).'%';
			$sql .= " and lower(f.file_name) like lower(:file_name) ";
		}
        
        $sql_param['tu_ngay'] ='00/00/0000';        
        if (isset($param['tu_ngay']) && empty($param['tu_ngay'])==FALSE) {
			$sql_param['tu_ngay'] = $param['tu_ngay'];
		}
        if (isset($param['den_ngay']) && empty($param['den_ngay'])==FALSE) {
			$sql_param['den_ngay'] = $param['den_ngay'];
            $sql .= " and t.add_datetime between STR_TO_DATE(:tu_ngay,'%d/%m/%Y %H:%i') and STR_TO_DATE(:den_ngay,'%d/%m/%Y %H:%i')";
		}else{
            $sql .= " and t.add_datetime between STR_TO_DATE(:tu_ngay,'%d/%m/%Y %H:%i') and SYSDATE()";
        }
        
		$sql .=$group. "
			ORDER BY   t.download_id
		";		
		return $this->query($sql, $sql_param);
	}
	
	
	
}
/* ファイルの終わり */
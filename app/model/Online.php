<?php
class Online_model extends ACWModel
{		
	public function get_all(){
		
		$time = time();
		$time_current = $time-1200;  //20phut
		$time_day = $time-86400; //24gio
		$time_week = $time-604800; //7 ngay
		$time_month = $time-2592000;
		$sql ="select
				( select count(*) from online where time > $time_current) current_ol,
				( select count(*) from online where time > $time_day ) day_ol,
				( select count(*) from online where time > $time_week) week_ol,
				( select count(*) from online where time > $time_month) month_ol,
				( select count(*) from online ) total_ol";
		
		 
		$res = $this->query($sql);
		return $res[0];
	}
	public function check_online(){
		$time = time();
		$time_current = $time-1200;  //20phut
		//session_start();
		$session    = session_id();
		$sql=" select count(*) cnt from online where section_id ='$session' and time > $time_current";
		$res = $this->query($sql);
		if($res[0]['cnt'] == 0){
			$this->_insert($session,$time);
		}else{
			$this->_update($session,$time);
		}
	}
	private function _insert($section_id,$time){
		$sql="insert into online(
					section_id
					,time
					,upd_time
					,ip_address)
				values(
					:section_id
					,:time
					,now()
					,:ip_address
				)";
		$param['section_id'] = $section_id;
		$param['time'] = $time;
		$param['ip_address']=$this->get_client_ip();
		$this->execute($sql,$param);		
	}
	private function _update($section_id,$time){
		$sql ="update online set time = $time , upd_time=now() where section_id = '$section_id' ";
		$this->execute($sql);
	}
	public static function get_online(){
		$db = new Online_model();
		$db->check_online();
		return $db->get_all();		
	}
	function get_client_ip() {
	    $ipaddress = '';
	    if (isset($_SERVER['HTTP_CLIENT_IP']))
	        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED'];
	    else if(isset($_SERVER['REMOTE_ADDR']))
	        $ipaddress = $_SERVER['REMOTE_ADDR'];
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}
}

<?php
/** 
 * @author	   Phong
 * @copyright  2016 
 * @version    1.0
*/
function acwork_db($target)
{
	$param = array();
	
	$param[''] = array(
		//'dsn' => 'mysql:dbname=qlfile1;host=localhost',
		'dsn' => 'mysql:dbname=danco;host=localhost;charset=utf8',
		'username' => 'root',
		'password' => '',
		'driver_options' => array(PDO::ATTR_PERSISTENT => false)
		);
	
	
	return $param[$target];
}


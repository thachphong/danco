<?php


//define('ACW_PROJECT', 'shopping');	
define('PHO_TEMPLATE_NAME', 'template1');

define('ACW_PUBLIC_DIR', str_replace("\\", '/', __DIR__));



define('ACW_ROOT_DIR', ACW_PUBLIC_DIR);


define('ACW_SYSTEM_DIR', ACW_ROOT_DIR . '/acwork');	
define('ACW_APP_DIR', ACW_ROOT_DIR . '/app');
define('ACW_USER_CONFIG_DIR', ACW_ROOT_DIR . '/user_config');
define('ACW_SMARTY_PLUGIN_DIR', ACW_APP_DIR . '/ext/smarty');
define('ACW_TEMPLATE_DIR', ACW_APP_DIR . '/template/fontend/'.PHO_TEMPLATE_NAME);
define('ACW_TEMPLATE_DIR_ADMIN', ACW_APP_DIR . '/template/backend');
define('ACW_VENDOR_DIR', ACW_APP_DIR . '/vendor');

define('ACW_TMP_DIR', ACW_ROOT_DIR . '/tmp');
define('ACW_TEMPLATE_CACHE_DIR', ACW_TMP_DIR . '/template_cache');
define('ACW_LOG_DIR', ACW_TMP_DIR . '/log');
define('ACW_TMP_DIR_IMG', ACW_TMP_DIR . '/images');
define('ACW_TMP_DIR_BAT', ACW_TMP_DIR . '/bat');


require ACW_USER_CONFIG_DIR . '/initialize.php';


ACWCore::acwork();


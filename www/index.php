<?php
mb_internal_encoding('UTF-8');
header('Content-Type: text/html; charset=UTF-8');
//setlocale(LC_COLLATE | LC_CTYPE | LC_TIME, 'ru_RU.UTF-8', 'ru_RU', 'ru', 'russian');
define('MAIN_DIR', __DIR__.'/');
define('AJAX', true);
require_once MAIN_DIR.'defines.php';
//языковые константы, используются в событиях (actions)
require_once MAIN_DIR.'langs/ru-lang.php';

//подключаем функции из файлов includes/functions/*-functions.php
require_once MAIN_DIR.'includes/functions/other-functions.php';
$files = file_list(MAIN_DIR.'includes/functions/', '.php', '^.*?-functions');
for($i=0; $i<sizeof($files); $i++){
	if($files[$i] != 'other-functions.php')
		require_once MAIN_DIR.'includes/functions/'.$files[$i];
}

require_once MAIN_DIR.'includes/phpmailer/PHPMailer.php';
require_once MAIN_DIR.'includes/phpmailer/Exception.php';
require_once MAIN_DIR.'includes/phpmailer/SMTP.php';

require_once MAIN_DIR.'includes/classes/log-class.php';
if(defined('USE_LOG') && USE_LOG)
	$LOG = new rad_log(MAIN_DIR.'files/logs/');

require_once MAIN_DIR.'includes/classes/db-class.php';
$DB = new rad_db(array('host' => MAIN_DBHOST, 'user' => MAIN_DBUSER, 'pass' => MAIN_DBPASS, 'db' => MAIN_DBNAME));


$OPTIONS = array();
//$OPTIONS['browser_data'] = $_SERVER['HTTP_USER_AGENT'];//get_browser
$OPTIONS['protocol'] = get_protocol();
$OPTIONS['user_agent'] = empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'];
$OPTIONS['time_start'] = $_SERVER['REQUEST_TIME_FLOAT'];
$OPTIONS['user_ip'] = get_ip();
$OPTIONS['referer_data'] = parse_url(empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER']);

require_once MAIN_DIR.'includes/classes/user-class/03-user-class.php';
$USER = new rad_user();

require_once MAIN_DIR.'includes/actions/actions.php';
$ret = do_actions();
if(isset($ret['code'])){
	http_response_code($ret['code']);
	$error = STR_UNDEFINED_ERROR;
	if(isset($ret['error'])){
		$error = esc_html($ret['error']);
	}
	die(json_encode(['error' => $error]));
}else if(isset($ret['error'])){
	http_response_code(400);
	die(json_encode(['error' => $ret['error']]));
}
die(json_encode($ret));

?>
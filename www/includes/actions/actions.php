<?php
if(!defined('MAIN_DIR'))
	die();

//подключаем файлы с функциями действий
$files = file_list(MAIN_DIR.'includes/actions/', '.php', '^.*?-actions');
for($i=0; $i<sizeof($files); $i++){
	require_once MAIN_DIR.'includes/actions/'.$files[$i];
}

//перебирает события в массиве $_REQUEST
//и вызывает для них обработчики
//возвращает резльтаты работы функций в виде массива
//array($_REQUEST['action'][0] => result, $_REQUEST['action'][1] => result, ...)
function do_actions(){
	if(isset($_REQUEST['action'])){
		$action = $_REQUEST['action'];
		if($action && is_array($action)){
			$action = current($action);
		}
		$ret = array();
		if(function_exists('action_'.$action)){
			$ret = call_user_func('action_'.$action);
		}else{
			$ret['error'] = 'Undefined method';
			$ret['code'] = 404;
		}
		return $ret;
	}
	return null;
}

/**
 * login-actions.php
 * @see action_login
 * @see action_register
 * @see action_check_login
 * @see action_check_email
 * @see action_exit
 * @see action_logout
 * 
 * profile-actions.php
 * @see action_profile
 */

?>
<?php

/**
 * событие регистрации пользователя
 * @param $_POST = ['login' => string, 'password' => string, 'email' => string]
 * @return array ['user_id' => int, 'sid' => string], ['user_id' => int] если зарегестрирован
 * ['code' => 400, 'error' => string]
 */
function action_register(){
	if(!isset($_POST['login'], $_POST['password'], $_POST['email']))
		return ['code' => 400, 'error' => STR_EMPTY_DATA];
	global $USER;
	if($USER->get_id())
		return ['user_id' => $USER->get_id()];
	
	$new_user_id = rad_user::create_new_user($_POST['login'], $_POST['password'], $_POST['email'], rad_user_roles::USER, '', 'body{background-color:hsl('.random_int(0, 360).'deg,50%,40%);}');
	
	$ret = '';
	$status = 0;
	switch($new_user_id){
		case -1:
			return ['code' => 400, 'error' => STR_ACTION_LOGIN_1];
			break;
		case -2:
			return ['code' => 400, 'error' => STR_ACTION_SIGNIN_2];
			break;
		case -3:
			return ['code' => 400, 'error' => STR_ACTION_SIGNIN_3];
			break;
		case -4:
			return ['code' => 400, 'error' => STR_ACTION_SIGNIN_4];
			break;
		case -5:
			return ['code' => 400, 'error' => STR_ACTION_SIGNIN_5];
			break;
		case -6:
			return ['code' => 400, 'error' => STR_ACTION_SIGNIN_6];
			break;
		case -7:
			return ['code' => 400, 'error' => STR_ACTION_SIGNIN_7];
			break;
		case -10:
			return ['code' => 400, 'error' => STR_ACTION_SIGNIN_71];
			break;
		case -8:
			throw new Exception(STR_ACTION_SIGNIN_8);
			return ['code' => 400, 'error' => STR_UNDEFINED_ERROR];
			break;
		case -9:
			return ['code' => 400, 'error' => STR_ACTION_SIGNIN_9];
			break;
		default:
			break;
	}
	
	//TODO переделать
	send_verified_mail($new_user_id);
	//$USER->user_logout();
	$USER->load_user($new_user_id);
	$ret = $USER->create_token('remember');
	if($ret['status']){
		return array('code' => 400, 'error' => STR_UNDEFINED_ERROR);
	}
	
	return ['user_id' => $USER->get_id(), 'sid' => $ret['token']];
}


/**
 * событие входа пользователя, если не ajax запрос и все прошло успешно, то редирктит на главную
 * @param $_POST = ['loginemail' => string, 'password' => string]
 * @return array ['user_id' => int, 'sid' => string], ['user_id' => int] если зарегестрирован
 * ['code' => 400, 'error' => string]
 */
function action_login(){
	if(!isset($_POST['loginemail'], $_POST['password']))
		return ['code' => 400, 'error' => STR_EMPTY_DATA];

	global $USER;

	if($USER->get_id())
		return ['user_id' => $USER->get_id()];
	if(!$USER->load_by_loginemailpass($_POST['loginemail'], $_POST['password'])){
		return ['code' => 400, 'error' => STR_ACTION_LOGIN_1];
	}
	$type = 'remember';
	$ret = $USER->create_token($type);
	if($ret['status'] == -3){
		return ['code' => 400, 'error' => STR_ACTION_LOGIN_2];
	}else if($ret['status']){
		return ['code' => 400, 'error' => STR_UNDEFINED_ERROR];
	}
	return ['user_id' => $USER->get_id(), 'sid' => $ret['token']];
}

/**
 * Проверяет незанятость логина
 * @param $_REQUEST = ['login' => string]
 * @return bool true - логин свободен
 */
function action_check_login(){
	if(!isset($_REQUEST['login']))
		return false;
	return rad_user::check_login($_REQUEST['login']);
}

/**
 * Проверяет незанятость почты
 * @param $_REQUEST = ['email' => string]
 * @return bool true - почта свободен
 */
function action_check_email(){
	if(!isset($_REQUEST['email']))
		return false;
	return rad_user::check_email($_REQUEST['email']);
}


/**
 * событие выхода пользователя
 */
function action_exit(){
	global $USER;
	$USER->user_logout();
	return true;
	//redirect('/login');
}

/**
 * событие выхода пользователя
 */
function action_logout(){
	action_exit();
	return true;
}
?>
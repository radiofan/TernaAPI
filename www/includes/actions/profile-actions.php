<?php

/**
 * возвращаются данные о пользователе
 * @param $_REQUEST = ['user' => int|null]
 * @return array ['id' => int, 'nickname' => string, 'avatar' => string, 'bio' => string, 'bugs' => int, 'features' => int, 'forks' => int]
 * ['code' => 400, 'error' => string]
 */
function action_profile(){
	global $USER, $DB;
	
	if(!isset($_REQUEST['user']) && $USER->get_id() == 0)
		return ['code' => 400, 'error' => STR_EMPTY_DATA];
	
	$user_id = isset($_REQUEST['user']) ? absint($_REQUEST['user']) : $USER->get_id();
	
	$current_user = new rad_user($user_id);
	
	if($current_user->get_id() == 0)
		return ['code' => 400, 'error' => STR_ACTION_PROFILE_1];
	
	
	$ret = [
		'id' => $current_user->get_id(),
		'nickname' => $current_user->get_login(),
		'avatar' => $current_user->get_avatar(),
		'bio' => esc_html($current_user->get_bio()),
	];
	
	$bugs_features = $DB->getRow('SELECT SUM(bugs) AS all_bugs, SUM(features) AS all_features FROM p_posts WHERE user_id = ?i', $current_user->get_id());
	$ret['bugs'] = (int)$bugs_features['all_bugs'];
	$ret['features'] = (int)$bugs_features['all_features'];
	
	$ret['forks'] = (int) $DB->getOne('SELECT COUNT(*) FROM p_posts WHERE parent_id = ?i', $current_user->get_id());
	
	return $ret;
}
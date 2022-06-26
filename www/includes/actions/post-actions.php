<?php


/**
 * создает пост от лица текущего пользователя, если он может create_post
 * @param $_POST = ['lang' => str, 'feat' => int|null, 'text' => string, tags => string[]]
 * @return array
 * @see action_post()
 * ['code' => 400, 'error' => string]
 * ['code' => 403, 'error' => string]
 */
function action_publish(){
	global $USER;
	
	if(!isset($_POST['lang'], $_POST['text']))
		return ['code' => 400, 'error' => STR_EMPTY_DATA];
	
	if(!can_user('create_post'))
		return ['code' => 403, 'error' => STR_ACTION_BIO_1];
	
	$post = new rad_post();
	if(false === $post->create_post($USER->get_id(), $_POST['text'], $_POST['lang'], isset($_POST['feat']) ? $_POST['feat'] : null)){
		return ['code' => 400, 'error' => STR_UNDEFINED_ERROR];
	}
	
	$post->load_author_data();
	$post->load_parent_data();
	
	$ret = [
		'id' => $post->id,
		'author' => $post->author_data,
		'parent' => empty($post->parent_data['post']) ? null : $post->parent_data,
		'mark' => 0,
		'data' => [
			'lang' => $post->lang,
			'text' => $post->text,
			'bugs' => $post->bugs,
			'features' => $post->features,
			'forks' => $post->forks,
			]
		];
	
	return $ret;
}


/**
 * возврщает список постов указанного пользователя (или все) начиная с переданного id (не включая), от самого свежего поста до старого
 * @param $_REQUEST = ['user' => null|int, 'anchor' => int|null]
 * @return array
 * [
 *  массив постов
 * ]
 * @see action_post()
 */
function action_posts(){
	global $USER, $DB;
	
	$where = '';
	if(isset($_REQUEST['user'])){
		$where .= $DB->parse('WHERE `user_id` = ?i ', $_REQUEST['user']);
	}
	if(isset($_REQUEST['anchor'])){
		$where .= mb_strlen($where) ? 'AND ' : 'WHERE ';
		$where .= $DB->parse('`id` < ?i ', $_REQUEST['anchor']);
	}
	
	$posts_id = $DB->getCol('SELECT `id` FROM `p_posts` '.$where.'ORDER BY `id` DESC LIMIT ?i', POSTS_PER_PAGE) ?: [];
	
	$posts = [];
	$post = new rad_post();
	$len = sizeof($posts_id);
	for($i=0; $i<$len; $i++){
		$post->load_all_from_db($posts_id[$i]);
		
		$posts[] = [
			'id' => $post->id,
			'author' => $post->author_data,
			'parent' => empty($post->parent_data['post']) ? null : $post->parent_data,
			'mark' => $post->get_user_mark($USER->get_id()),
			'data' => [
				'lang' => $post->lang,
				'text' => $post->text,
				'bugs' => $post->bugs,
				'features' => $post->features,
				'forks' => $post->forks,
			]
		];
	}
	
	return $posts;
}

/**
 * возврщает пост по указанному id
 * @param $_REQUEST = ['id' => int]
 * @return array
 * [
 *  'id' => int,
 *  'author' => [
 *      'user' => int, //user_id
 *      'avatar' => string,
 *      'nickname' => string, //login
 *  ],
 *  'mark' => int, // -1 dislike, 0 - undefined, 1 - like
 *  'parent' => null|[
 *      'post' => int, //parent post id
 *      'user' => int, //parent post author id
 *      'nickname' => string, //parent post author login
 *  ],
 *  'data' => [
 *      'lang' => string,
 *      'text' => string,
 *      'bugs' => int,
 *      'features' => int,
 *      'forks' => int,
 *  ]
 * ]
 * ['code' => 400, 'error' => string]
 */
function action_post(){
	global $USER;
	
	if(!isset($_REQUEST['id']))
		return ['code' => 400, 'error' => STR_EMPTY_DATA];
	
	$post = new rad_post();
	$post->load_all_from_db($_REQUEST['id']);
	if($post->id == 0)
		return ['code' => 400, 'error' => STR_ACTION_POST_1];
	
	$ret = [
		'id' => $post->id,
		'author' => $post->author_data,
		'parent' => empty($post->parent_data['post']) ? null : $post->parent_data,
		'mark' => $post->get_user_mark($USER->get_id()),
		'data' => [
			'lang' => $post->lang,
			'text' => $post->text,
			'bugs' => $post->bugs,
			'features' => $post->features,
			'forks' => $post->forks,
		]
	];
	
	return $ret;
}

function action_bug(){
	global $USER;
	
	if(!isset($_REQUEST['post']))
		return ['code' => 400, 'error' => STR_EMPTY_DATA];
	
	if(!can_user('create_post'))
		return ['code' => 403, 'error' => STR_ACTION_BIO_1];
	
	$post = new rad_post();
	$post->load_from_db($_REQUEST['post']);
	
	if($post->id == 0)
		return ['code' => 400, 'error' => STR_ACTION_POST_1];
	
	$post->set_bug($USER->get_id());
	return ['bugs' => $post->bugs, 'features' => $post->features];
}

function action_feature(){
	global $USER;
	
	if(!isset($_REQUEST['post']))
		return ['code' => 400, 'error' => STR_EMPTY_DATA];
	
	if(!can_user('create_post'))
		return ['code' => 403, 'error' => STR_ACTION_BIO_1];
	
	$post = new rad_post();
	$post->load_from_db($_REQUEST['post']);
	
	if($post->id == 0)
		return ['code' => 400, 'error' => STR_ACTION_POST_1];
	
	$post->set_feature($USER->get_id());
	return ['bugs' => $post->bugs, 'features' => $post->features];
}
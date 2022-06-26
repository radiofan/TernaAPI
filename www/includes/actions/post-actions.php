<?php


/**
 * создает пост от лица текущего пользователя, если он может create_post
 * @param $_POST = ['lang' => str, 'feat' => int|null, 'text' => string, tags => string[]]
 * @return array
 * [
 *  'id' => int,
 *  'author' => [
 *      'user' => int, //user_id
 *      'avatar' => string,
 *      'nickname' => string, //login
 *  ],
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
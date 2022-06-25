<?php


/**
 * создает пост от лица текущего пользователя, если он может create_post
 * @param $_POST = ['lang' => str, 'feat' => int|null, 'text' => string, tags => string[]]
 * @return array
 * ['code' => 400, 'error' => string]
 * ['code' => 403, 'error' => string]
 */
function action_publish(){
	global $USER, $DB;
	
	if(!isset($_POST['lang'], $_POST['text']))
		return ['code' => 400, 'error' => STR_EMPTY_DATA];
	
	if(!can_user('create_post'))
		return ['code' => 403, 'error' => STR_ACTION_BIO_1];
	
	$post = new rad_post();
	$post->create_post($_POST['text'], $_POST['lang'], $_POST['feat'] ?: null);
	
	$post->load_author_data();
	$post->load_parent_data();
	
	$ret = [
		'id' => $post->id,
		'author' => $post->author_data,
		'parent' => $post->author_data,
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
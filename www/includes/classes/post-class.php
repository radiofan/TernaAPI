<?php


class rad_post{
	public $id;
	public $user_id;
	public $text;
	public $lang;
	public $date;
	
	public $bugs;
	public $features;
	public $forks;
	
	public $tags;
	
	public $author_data;
	public $parent_data;
	
	public function __construct(){
		$this->set_default();
	}
	
	public function set_default(){
		$this->id = 0;
		$this->text = '';
		$this->lang = '';
		$this->date = '';
		$this->user_id = 0;
		
		$this->bugs = 0;
		$this->features = 0;
		$this->forks = 0;
		
		$this->tags = [];
		$this->author_data = [];
		$this->parent_data = [];
	}
	
	public function load_from_db($id){
		global $DB;
		$this->set_default();
		
		$this->id = absint($id);
		
		$res = $DB->getRow('SELECT `user_id`, `parent_id`, `date`, `lang`, `text`, `bugs`, `features`, `forks` FROM `p_posts` WHERE `id` = ?i', $this->id);
		
		if(!$res){
			$this->id = 0;
			return false;
		}
		
		$this->text = $res['text'];
		$this->lang = $res['lang'];
		$this->date = $res['date'];
		$this->user_id = (int)$res['user_id'];
		
		$this->bugs = absint($res['bugs']);
		$this->features = absint($res['features']);
		$this->forks = absint($res['forks']);
		
		$this->parent_data = ['post' => (int)$res['parent_id']];
		
		return true;
	}
	
	public function load_author_data(){
		global $DB;
		if($this->id == 0)
			return;
		
		$this->author_data = $DB->getRow('SELECT `id` AS `user`, `avatar`, `login` AS `nickname` FROM `our_u_users` WHERE `id` = ?i', $this->user_id);
		$this->author_data['user'] = absint($this->author_data['user']);
	}
	
	public function load_parent_data(){
		global $DB;
		if($this->id == 0)
			return;
		if(empty($this->parent_data['post']))
			return;
		
		$parent_post_user_id = $DB->getOne('SELECT `user_id` FROM `p_posts` WHERE id = ?i', $this->parent_data['post']);
		$parent_user = $DB->getRow('SELECT `id` AS `user`, `login` AS `nickname` FROM `our_u_users` WHERE `id` = ?i', $parent_post_user_id);
		$this->parent_data = array_merge($this->parent_data, $parent_user);
		$this->parent_data['user'] = absint($this->parent_data['user']);
	}
	
	public function get_user_mark($user_id){
		global $DB;
		if($this->id == 0)
			return;
		$ret = $DB->getOne('SELECT `type` FROM `p_bugs_features` WHERE `post_id` = ?i AND `user_id` = ?i', $this->id, $user_id);
		return (int) $ret;
	}
	
	public function load_all_from_db($id){
		if($this->load_from_db($id)){
			$this->load_author_data();
			$this->load_parent_data();
		}
	}
	
	public function create_post($user_id, $text, $lang, $parent_id=null){
		global $DB;
		
		if($user_id == 0)
			return false;
		
		if(!is_null($parent_id)){
			$parent_id = (int) $DB->getOne('SELECT `id` FROM `p_posts` WHERE `id` = ?i', $parent_id);
			if($parent_id == 0)
				return false;
		}
		
		if(false !== $DB->query('INSERT INTO `p_posts` (`text`, `user_id`, `parent_id`, `lang`) VALUES (?s, ?i, ?i, ?s)', $text, $user_id, $parent_id, $lang)){
			$insert_id = $DB->insertId();
			if($parent_id){
				$DB->query('UPDATE `p_posts` SET `forks` = `forks` + 1 WHERE `id` = ?i', $parent_id);
			}
			$this->set_default();
			$this->load_from_db($insert_id);
			return $this->id;
		}else{
			return false;
		}
	}
}
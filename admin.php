<?php
include_once('class/DataGrid.php');
dispatch_get('/admin','admin');

dispatch_get('/admin/news/:id','read_notification');
dispatch_post('/admin/news','add_notification');
dispatch_delete('/admin/news/:id','delete_notification');

dispatch_get('/admin/monsters','get_monsters');
dispatch_get('/admin/monsters/:id','get_monster');
dispatch_put('/admin/monsters','add_monster');
dispatch_post('/admin/monsters/:id','update_monster');
dispatch_delete('/admin/monsters/:id','delete_monster');

dispatch_get('/admin/users','get_users');
dispatch_get('/admin/users/:id','get_user');
dispatch_post('/admin/users/:id','update_user');
dispatch_delete('/admin/users/:id','delete_user');

dispatch_get('/admin/items','get_items');
dispatch_get('/admin/items/:id','get_item');
dispatch_put('/admin/items','add_item');
dispatch_delete('/admin/items/:id','delete_item');
dispatch_post('/admin/items/:id','update_item');

dispatch_get('/admin/reports','get_reports');
dispatch_post('/admin/reports/:id','update_report');
dispatch_delete('/admin/reports/:id','delete_report');

dispatch_get('/admin/cities','get_cities');
dispatch_get('/admin/cities/:id','get_city');
dispatch_post('/admin/cities/:id','update_city');

function is_admin() {
  if(array_key_exists('player',$_SESSION) && !empty($_SESSION['player'])) {
  $player = unserialize($_SESSION['player']);
  
    if($player->admin) {
      layout('admin.html.php');
      set('player',$player);
      
      $news = R::find('news','approved = 1 order by post_date desc');
      set('news',$news);
      
      return $player;
    }
  }
  redirect_to('/');
}

function modify($val,$row) {
  return '<a href="'.url_for('admin','user',$row['id']).'">'.$val.'</a>'; 
}

function admin() {
  $player = is_admin();
  
  $levels = R::getAll('select id,username,level from player order by level desc, username asc limit 5');
  $level_dg = new OPCDataGrid($levels);
  $level_dg->fields(array(
    'username' => 'Username',
    'level' => 'Level'
  ));
  
  $level_dg->modify('username', 'modify');
  
  $gold = R::getAll('select id,username,level,gold from player order by gold desc, username asc limit 5');
  $gold_dg = new OPCDataGrid($gold);
  $gold_dg->fields(array(
    'username' => 'Username',
    'level' => 'Level',
    'gold' => 'Gold'
  ));
  $gold_dg->modify('username', 'modify');
  
  $submenu = array(
    
  );
  set('submenu',$submenu);
  set('levels',$level_dg->build());
  set('gold',$gold_dg->build());
  return render('adminhome.html.php');
}

function add_notification() {
  $player = is_admin();
  
  $news = R::dispense('news');
  $news->import($_POST,'title,news');
  $news->post_date = time();
  $news->posted_by = $player->username;
  $news->posted_by_id = $player->id;
  
  R::store($news);
  
  set('notification','Saved!');
  return admin();
}

function monsters_modify($val,$row) {
  return '<a href="'.url_for('admin','monsters',$row['id']).'">'.$val.'</a>';
}

function get_monsters() {
  $player = is_admin();
  
  $monsters = R::getAll('select id,name,level from monster order by level asc, name asc');
  $dg = new OPCDataGrid();
  $dg->source($monsters);
  $dg->fields(array(
    'name' => 'Name',
    'level' => 'Level'
  ));
  $dg->modify('name', 'monsters_modify');
  
  $submenu = array(
    array('url' => url_for('admin','monsters'), 'name' => 'View List'),
    array('url' => url_for('admin','monsters','create'), 'name' => 'Create New')
  );
  set('submenu',$submenu);
  set('page',$dg->build());
  return render('monsters.html.php');
}

function add_monster() {
  $monster = R::dispense('monster');
  $monster->import($_POST,array('name','level','vit','str','tough','agi','luck','current_hp','exp','gold','city'));
  $min = explode(',',$_POST['min_bounds']);
  $max = explode(',',$_POST['max_bounds']);
  
  $monster->min_x = $min[0];
  $monster->min_y = $min[1];
  $monster->max_x = $max[0];
  $monster->max_y = $max[1];
  
  $id = R::store($monster);
  return get_monster($id);
}

function update_monster($id) {
  $monster = R::findOne('monster','id = ?',array($id));
  if(!empty($monster)) {
    $monster->import($_POST,array('name','level','vit','str','tough','agi','luck','current_hp','exp','gold','city'));
    $min = explode(',',$_POST['min_bounds']);
    $max = explode(',',$_POST['max_bounds']);
    
    $monster->min_x = $min[0];
    $monster->min_y = $min[1];
    $monster->max_x = $max[0];
    $monster->max_y = $max[1];
    
    R::store($monster);
    
    return get_monster($id);
  }
}

function get_monster($id) {
  $player = is_admin();
  
  if(is_numeric($id)) {
    $monster = R::findOne('monster','id = ?',array($id));
    set('monster',$monster);
  }

  $submenu = array(
    array('url' => url_for('admin','monsters'), 'name' => 'View List'),
    array('url' => url_for('admin','monsters','create'), 'name' => 'Create New')
  );
  
  $cities = R::find('city','1 order by id asc');
  set('cities',$cities);
  
  set('submenu',$submenu);
  return render('monsters.html.php');
}

function users_modify($val,$row) {
	return '<a href="'.url_for('admin','users',$row['id']).'">'.$val.'</a>';
}

function get_users() {
	$player = is_admin();
	
	$players = R::getAll('select id,username,email,level,class_name from player order by name asc, level asc');
	$dg = new OPCDataGrid();
	$dg->source($players);
	$dg->fields(array(
		'level' => 'Level',
	    'username' => 'Name',
	    'class_name' => 'Class',
	    'email' => 'Email'
	));
	$dg->modify('username', 'users_modify');
	
	$submenu = array(
	array('url' => url_for('admin','users'), 'name' => 'View List')
	);
	set('submenu',$submenu);
	set('page',$dg->build());
	return render('users.html.php');
}

function get_user($id) {
	$player = is_admin();

	if(is_numeric($id)) {
		$user = R::findOne('player','id = ?',array($id));
		set('user',$user);
	}

	$submenu = array(
	array('url' => url_for('admin','monsters'), 'name' => 'View List')
	);
	set('submenu',$submenu);
	return render('users.html.php');
}

function fix_time($val,$row) {
	return date('j M Y @ h:i:sa',$val);
}

function get_reports() {
	$player = is_admin();
	
	$reports = R::getAll('select id,fromuser,message,post_time from report order by post_time desc');
	$dg = new OPCDataGrid();
	
	$dg->source($reports);
	$dg->fields(array(
		'fromuser' => 'By',
		'message' => 'Report',
		'post_time' => 'Time'
	));
	
	$dg->addFieldAfter('Resolve','resolve','Edit','post_time');
	
	$dg->modify('post_time','fix_time');
	
	set('submenu','');
	set('page',$dg->build());
	return render('reports.html.php');
}

function update_report($id) {
	
}

function delete_report($id) {
	
}

function get_cities() {
	$player = is_admin();
	
	$reports = R::getAll('select id,name,min_x,min_y,max_x,max_y,zone from city where id > 1 order by name desc');
	$dg = new OPCDataGrid();
	
	$dg->source($reports);
	$dg->fields(array(
			'name' => 'City',
			'min_x' => 'Min Bounds',
			'max_x' => 'Max Bounds',
			'id' => 'Action'
	));

	$dg->modify('min_x','min_bounds');
	$dg->modify('max_x','max_bounds');
	$dg->modify('id','edit_city');
	
	set('submenu','');
	set('page',$dg->build());
	return render('cities.html.php');
}

function get_city($id) {
	$player = is_admin();

	if(is_numeric($id)) {
		$city = R::findOne('city','id = ?',array($id));
		set('city',$city);
	}

	set('submenu','');
	return render('cities.html.php');
}

function update_city($id) {
	$city = R::findOne('city','id = ?',array($id));
	if(!empty($city)) {
		$city->import($_POST,array('name','min_x','min_y','max_x','max_y'));
		$city->buildings = '';
		R::store($city);

		return get_city($id);
	}
}

function edit_city($val,$row) {
	return '<a href="'.url_for('admin','cities',$val).'">Edit</a>';
}

function min_bounds($val,$row) {
	return '('.$val.','.$row['min_y'].')';
}

function max_bounds($val,$row) {
	return '('.$val.','.$row['max_y'].')';
}
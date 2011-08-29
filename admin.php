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

dispatch_get('/admin/items','get_items');
dispatch_get('/admin/items/:id','get_item');
dispatch_put('/admin/items','add_item');
dispatch_delete('/admin/items/:id','delete_item');
dispatch_post('/admin/items/:id','update_item');

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
  $monster->import($_POST);
  $id = R::store($monster);
  return get_monster($id);
}

function update_monster($id) {
  $monster = R::findOne('monster','id = ?',array($id));
  if(!empty($monster)) {
    $monster->import($_POST);
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
  set('submenu',$submenu);
  return render('monsters.html.php');
}
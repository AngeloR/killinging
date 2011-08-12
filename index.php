<?php 

include('lib/app.php');
include('lib/limonade.php');
include('lib/rb.php');


include('model/player.php');
include('model/city.php');
include('model/building.php');
include('model/store.php');
include('model/item.php');

function configure() {
	appinit();
	$db = appconfig(appconfig('use_db'));
	
	
	R::setup('mysql:host='.$db['host'].';dbname='.$db['name'],$db['user'],$db['pass']);
	//R::debug(true);
}

/**
 * 
 * Sets up the default user information. If the user is not logged in 
 * then we load up the website. If they are logged in, we can point 
 * them to the application theme.
 */
function before() {
	//option('base_uri','/sobuyit');
	
	option('views_dir','view/theme/default/');
    
	set('THEME','default');
	set('THEMEDIR','view/theme/default');
	

	layout('layout.html.php');
	
	set('player',set_player());
}

function set_player() {
	if(array_key_exists('player',$_SESSION)) {
		set('player',unserialize($_SESSION['player']));
		
		layout('game.html.php');
	}
}


/***
 * ROUTES
 */
dispatch_get('/','homepage');

dispatch_post('/login','login');
dispatch_post('/signup','signup');
dispatch_get('/logout','logout');

dispatch_get('/game','game');


dispatch_get('/move/:dir','movement_handler');
dispatch_post('/fight','fight_handler');
dispatch_get('/item/info/:id','get_item_info');
dispatch_get('/building/info/:id','get_building_info');
dispatch_post('/inventory/:id','buy_item');

dispatch_get('/inventory','get_inventory');

function homepage() {
	$classes = R::find('class','1 order by name asc');
	set('classes',$classes);
	return render('homepage.html.php');
}

function login() {
	$player = R::findOne('player','username = ? and password = ?',array($_POST['username'], pw_hash($_POST['password'])));
	
	if(!empty($player)) {
		$_SESSION['player'] = serialize($player);
		redirect_to('game');
	}
	else {
		set('login_notification','Ok, try again cause that didn\'t work...');
		return homepage();
	}
}

function logout() {
	if(array_key_exists('player',$_SESSION)) {
		unset($_SESSION['player']);
	}
	redirect_to('/');
}

function signup() {
	$player = R::dispense('player');
	
	$fields = array('username','password','email','class_id');
	foreach($fields as $i => $f) {
		if(!array_key_exists($f,$_POST) || empty($_POST[$f])) {
			set('signup_notification','Ok.. have you never filled one of these out before?');
			return homepage();
		}
	}
	
	if($_POST['password'] === $_POST['confirm']) {
		$player->import($_POST,$fields);
		$player->new_setup();
		R::store($player);
		
		$_SESSION['player'] = serialize($player);
		redirect_to('game');
	}
	
	set('signup_notification','Ok.. have you never filled one of these out before?');
	return homepage();
}




function game() {
	$player = unserialize($_SESSION['player']);
	
	$city = R::findOne('city','id = ?',array($player->city));
	set('city',$city);
	
	$monsters = R::find('monster','1 order by level asc');
	set('monsters',$monsters);
	
	$buildings = R::find('building_type','1 order by cost asc');
	set('buildings',$buildings);

	return render('map.html.php');
}


function movement_handler($dir) { 
	$player = unserialize($_SESSION['player']);
	switch($dir) {
		case 'ne':
			$player->loc_x += 1;
			$player->loc_y -= 1;
			break;
			
		case 'n':
			$player->loc_y -= 1;
			break;
			
		case 'nw':
			$player->loc_x -= 1;
			$player->loc_y -= 1;
			break;
			
		case 'w':
			$player->loc_x -= 1;
			break;
		
		case 'e':
			$player->loc_x += 1;
			break;
			
		case 'sw':
			$player->loc_x -= 1;
			$player->loc_y += 1;
			break;
			
		case 's':
			$player->loc_y += 1;
			break;
			
		case 'se':
			$player->loc_x += 1;
			$player->loc_y += 1;
			break;
	}
	
	if($player->getMeta('tainted')) {
		R::store($player);
		$_SESSION['player'] = serialize($player);
	}
	
	return game();
}

function fight_club_calc_damage($attacker,$defender) {
	$damage = $attacker->str * $attacker->agi;
	
	$defence = $defender->str;
	
	$damage = floor($damage - $defence);
	
	if($damage < 0) {
		$damage = 0;
	}
	
	return $damage;
}

function fight_club($p1,$p2) {
	if($p1->agi > $p2->agi) {
		$first = $p1;
		$second = $p2;
	}
	else {
		$first = $p2;
		$second = $p1;
	}
	
	$rounds = 0;
	$messages = array();
	while($first->current_hp > 0 && $second->current_hp > 0) {
		++$rounds;
		$damage = fight_club_calc_damage($first,$second);
		$second->current_hp -= $damage;
		
		if(isset($first->name)) {
			$messages[] = $first->name.' attacked '.$second->username.' for '.$damage.' damage.';
		}
		else {
			$messages[] = $first->username.' attacked '.$second->name.' for '.$damage.' damage.';
		}
		
		if($second->current_hp <= 0) {
			continue;
		}
		
		$damage = fight_club_calc_damage($second,$first);
		$first->current_hp -= $damage;
		
		if(isset($second->name)) {
			$messages[] = $second->name.' attacked '.$first->username.' for '.$damage.' damage.';
		}
		else {
			$messages[] = $second->username.' attacked '.$first->name.' for '.$damage.' damage.';
		}
	}
	
	if($first->current_hp <= 0) {
		return array($second,$rounds,$messages);
	}
	return array($first,$rounds,$messages);
}

function fight_handler() {
	 $monster = R::findOne('monster','id = ?',array($_POST['monster_id']));
	 if(!empty($monster)) {
	 		$player = unserialize($_SESSION['player']);
	 		
	 		if($player->current_hp > 0) {
	 			list($winner,$rounds,$messages) = fight_club($player,$monster);

	 			if(isset($winner->username)) {
	 				// player won
	 				$player->current_hp = $winner->current_hp;
	 				$player->gold += $monster->gold;
	 				$player->current_exp += $monster->exp;
	 			}
	 			else {
	 				$player->gold = 0;
	 			}
	 			
	 			R::store($player);
	 			$_SESSION['player'] = serialize($player);
	 		}
	 		else {
	 			return json(array(
	 				'messages' => array('You couldn\'t attack that monster because your HP is too low.')
	 			));
	 		}
	 }
	 
	 return json(array(
	   'messages' => $messages,
	 	 'rounds' => $rounds,
	 	 'monster' => $monster->name,
	 	 'stats' => array(
	 	 		'current_hp' => (int)$player->current_hp,
	 	 		'total_hp' => (int)$player->total_hp,
	 	 		'current_mp' => (int)$player->current_mp,
	 	 		'total_mp' => (int)$player->total_mp,
	 	 		'current_exp' => (int)$player->current_exp,
	 	 		'total_exp' => (int)$player->exp_to_level(),
	 	 		'gold' => (int)$player->gold,
	 	 		'level' => (int)$player->level,
	 	 )
	 ));
}

function get_item_info($id) {
	$item = R::findOne('item','id = ?',array($id));
	return json($item->tojson());
}

function get_building_info($id) {
	$building = R::findOne('building_type','id = ?',array($id));
	return json($building->tojson());
}

function buy_item() {
	$item = R::findOne('item','id = ?',array($_POST['item_id']));
	$player = unserialize($_SESSION['player']);
	
	// check if store is owners store
	
	if(($player->gold - $item->cost) >= 0 ) {
		// can buy item
		$player->gold -= $item->cost;
		$owned_item = R::dispense('owned_item');
		// copy from bean
		$owned_item->import($item->export(),'name,cost,level,str,def,agi,luck');
		$owned_item->owner = $player->id;
		$owned_item->equipped = false;
		$owned_item->cost *= 0.5;
		
		R::store($owned_item);// add item to inventory
		R::store($player);		// save new player info
		R::trash($item);			// remove item from stores
		
		$_SESSION['player'] = serialize($player);
		
		return json(array(
			'gold' => $player->gold
		));
	}
	else {
		return json(array((bool)false));
	}
}

function get_inventory() {
	$player = unserialize($_SESSION['player']);
	$owned_items = R::find('owned_item','owner = ?',array($player->id));
	
	return json($owned_items);
}

run();
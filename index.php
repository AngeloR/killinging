<?php 

date_default_timezone_set('America/Toronto');

include('lib/app.php');
include('lib/limonade.php');
include('lib/rb.php');

include('class/ChatManager.php');

include('interface/building.php');
include('interface/store.php');
include('interface/quarry.php');


include('model/player.php');
include('model/city.php');
include('model/building.php');
include('model/item.php');
include('model/owned.php');
include('model/message.php');

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
dispatch_get('/class/:id','class_info');

dispatch_post('/login','login');
dispatch_post('/signup','signup');
dispatch_get('/logout','logout');

dispatch_get('/game','game');


dispatch_get('/move/:dir','movement_handler');
dispatch_post('/fight','fight_handler');
dispatch_post('/pvp','pvp_handler');
dispatch_get('/item/info/:id','get_item_info');

dispatch_get('/inventory/info/:id','get_inventory_info');
dispatch_get('/building/info/:id','get_building_info');
dispatch_post('/inventory/:id','buy_item');
dispatch_get('/inventory','get_inventory');
dispatch_post('/skill/:type','skillup');

dispatch_post('/store/add','add_item_to_store');

dispatch_post('/mine','mine');
dispatch_post('/upgrade/:building_id','upgrade_building');

dispatch_get('/chat/:since','get_chat_messages');
dispatch_post('/chat','post_chat_message');

include('admin.php');

function homepage() {
	$classes = R::find('class','preform = 0 order by name asc');
	set('classes',$classes);
	return render('homepage.html.php');
}

function class_info($id) {
	$class = R::findOne('class','id = ?',array($id));
	
	return json(array(
		'id' => (int)$class->id,
		'name' => $class->name,
		'vit' => (int)$class->vit,
		'str' => (int)$class->str,
		'tough' => (int)$class->tough,
		'agi' => (int)$class->agi,
		'luck' => (int)$class->luck,
		'mining' => (int)$class->mining,
		'smithing' => (int)$class->smithing,
		'description' => $class->description
	));
}

function login() {
	$player = R::findOne('player','username = ? and password = ?',array($_POST['username'], pw_hash($_POST['password'])));
	
	if(!empty($player)) {
		$_SESSION['player'] = serialize($player);
		$_SESSION['flash'] = array();
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
	
	$city = R::findOne('city','zone = ? and min_x <= ? and min_y <= ? and max_x >= ? and max_x >= ?',array($player->zone,$player->loc_x,$player->loc_y,$player->loc_x,$player->loc_y));
	if(empty($city)) {
		$city = R::findOne('city','id = 1');
	}
	set('city',$city);
	
	$buildings = R::find('building_type','1 order by cost asc');
	set('buildings',$buildings);
	
	set('gamemessages',$_SESSION['flash']);
	$_SESSION['flash'] = array();
	
	
	return render('game.html.php');
}


function movement_handler($dir) { 
	$player = unserialize($_SESSION['player']);
	$city = R::findOne('city','zone = ? and min_x <= ? and min_y <= ? and max_x >= ? and max_x >= ?',array($player->zone,$player->loc_x,$player->loc_y,$player->loc_x,$player->loc_y));
	if(empty($city)) {
		$city = R::findOne('city','id = 1');
	}
	switch($dir) {
		case 'ne':
			if($city->can_move_to($player->loc_x + 1, $player->loc_y - 1)) {
				$player->loc_x += 1;
				$player->loc_y -= 1;
			}
			break;
			
		case 'n':
			if($city->can_move_to($player->loc_x, $player->loc_y - 1)) {
				$player->loc_y -= 1;
			}
			break;
			
		case 'nw':
			if($city->can_move_to($player->loc_x - 1, $player->loc_y - 1)) {
				$player->loc_x -= 1;
				$player->loc_y -= 1;
			}
			break;
			
		case 'w':
			if($city->can_move_to($player->loc_x - 1, $player->loc_y)) {
				$player->loc_x -= 1;
			}
			break;
		
		case 'e':
			if($city->can_move_to($player->loc_x + 1, $player->loc_y)) {
				$player->loc_x += 1;
			}
			break;
			
		case 'sw':
			if($city->can_move_to($player->loc_x - 1, $player->loc_y + 1)) {
				$player->loc_x -= 1;
				$player->loc_y += 1;
			}
			break;
			
		case 's':
			if($city->can_move_to($player->loc_x, $player->loc_y + 1)) {
				$player->loc_y += 1;
			}
			break;
			
		case 'se':
			if($city->can_move_to($player->loc_x + 1, $player->loc_y + 1)) {
				$player->loc_x += 1;
				$player->loc_y += 1;
			}
			break;
	}
	
	if($player->getMeta('tainted')) {
		// Check to see if the player should find a random item or not.
		// This will be based on luck and the "mining/woodworking" skill. You 
		// can only find wood OR stone on each step.
		// The amount of wood or stone you find is dependent on your skill in that 
		// particular stat.
		$rand = rand(0,10000);
		$stoneBound = $player->luck + rand($player->mining,$player->mining+100) + rand(0,100); 
		if($rand <= $stoneBound) {
			$player->stone++;
		}

		R::store($player);
		$_SESSION['player'] = serialize($player);
		
	}
	
	$players = R::find('player','city = ? and loc_x = ? and loc_y = ? and player_id != ?',array($player->city,$player->loc_x,$player->loc_y,$player->id));
	set('players',$players);
	
	return game();
}

function fight_club_calc_damage($attacker,$defender) {
	$damage = round($attacker->str * $attacker->str* ($attacker->agi/2));
	
	$defence = $defender->tough*+$defender->vit*($defender->tough/2);
	
	// crits!
	$crit_rate = rand(0,$attacker->luck);
	$crit = false;
	if($crit_rate <= ($attacker->luck*0.1)) {
		$damage *= 0.75;
		$crit = true;
	}
	
	$damage = floor($damage - $defence);
	
	if($damage <= 0) {
		$damage = 0;
	}
	
	return array($damage,$crit);
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
		$second->current_hp -= $damage[0];
		
		if(isset($first->name) && !empty($first->name)) {
			$messages[] = 'The '.$first->name.' '.(($damage[0]==0)?'missed':'attacked '.$second->username.' '. (($damage[1])?'critically':'') .' for '.$damage[0].' damage.');
		}
		else {
			$messages[] = 'You '. (($damage[0]==0)?'missed':'attacked the '.$first->name.' '. (($damage[1])?'critically':'') .' for '.$damage[0].' damage.');
		}
		
		if($second->current_hp <= 0) {
			continue;
		}
		
		$damage = fight_club_calc_damage($second,$first);
		$first->current_hp -= $damage[0];
		
		if(isset($second->name)) {
			$messages[] = 'The '.$second->name.' '.(($damage[0]==0)?'missed':'attacked '.$first->username.' '. (($damage[1])?'critically':'') .' for '.$damage[0].' damage.');
		}
		else {
			$messages[] = 'You '. (($damage[0]==0)?'missed':'attacked the '.$second->name.' '. (($damage[1])?'critically':'') .' for '.$damage[0].' damage.');
		}
	}
	
	if($first->current_hp <= 0) {
		return array($second,$rounds,$messages);
	}
	return array($first,$rounds,$messages);
}

function fight_handler() {
	if(array_key_exists('battle',$_SESSION) && !empty($_SESSION['battle'])) {
		
	 $monster = unserialize($_SESSION['battle']);
	 unset($_SESSION['battle']);
	 if(!empty($monster)) {
	 		$player = unserialize($_SESSION['player']);
	 		// store the monster as the most recent battle
	 		$player->last_battled = $monster->id;
	 		
	 		if($player->current_hp > 0) {
	 			list($winner,$rounds,$messages) = fight_club($player,$monster);

	 			if(isset($winner->username)) {
	 				// player won
	 				$player->current_hp = $winner->current_hp;
	 				$player->gold += $monster->gold;
	 				$player->current_exp += $monster->exp;
					R::store($player);
					$_SESSION['player'] = serialize($player);
	 			}
	 			else {
	 				$player->gold = 0;
					R::store($player);
					$_SESSION['player'] = serialize($player);
					$_SESSION['flash'][] = 'Whoops, the '.$monster->name.' killed you! You have been sent to '.$player->loc_x.','.$player->loc_y;
					return json('f331d3ad');
	 			}
	 			
	 			
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
	 	 		'level' => (int)$player->level
	 	 )
	 ));
	}
}
/*
function pvp_handler() {
	$player = unserialize($_SESSION['player']);
	if($player->city == 1) {
	$other_player = R::findOne('player','id = ? and loc_x = ? and loc_y = ? and city = ?',array($_POST['player'],$player->loc_x,$player->loc_y,$player->city));
	if(!empty($other_player)) {
		if($player->current_hp > 0 && $other_player->current_hp > 0) {
			list($winner,$rounds,$messages) = fight_club($player,$other_player);
			
			if(isset($winner->username)) {
	 			// player won
	 			$player->current_hp = $winner->current_hp;
	 			$player->gold += $monster->gold;
				$player->current_exp += $monster->exp;
				R::store($player);
				$_SESSION['player'] = serialize($player);
			}
	 		else {
	 			$player->gold = 0;
				R::store($player);
				$_SESSION['player'] = serialize($player);
				$_SESSION['flash'][] = 'Whoops, the '.$monster->name.' killed you! You have been sent to '.$player->loc_x.','.$player->loc_y;
				return json('f331d3ad');
	 		}
		}
		else {
			return json(array(
				'messages' => array('One of you is quite dead... but not because of this battle.')
			));
		}
	}
}
//*/
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
	$store = R::findOne('building','id = ? and building_type = 1',array($item->store_id));
	$player = unserialize($_SESSION['player']);
	
	// check if store is owners store, if its not, subtract the cost!
	if($store->owner != $player->id ) {
		$player->gold -= $item->cost;
	}
	// can buy item
	if($player->gold >= 0 || $store->owned_id == $player->id ) {
		$owned_item = R::dispense('owned_item');
		// copy from bean
		$owned_item->import($item->export(),'name,cost,level,str,def,agi,luck');
		
		R::store($owned_item);// add item to inventory
		R::store($player);		// save new player info
		R::trash($item);			// remove item from stores
		
		$_SESSION['player'] = serialize($player);
		
		return json(array(
			'gold' => $player->gold
		));
	}
	else {
		$player->gold += $item->cost;
		return json(array((bool)false));
	}
}

function get_inventory() {
	$player = unserialize($_SESSION['player']);
	$owned_items = R::find('owned_item','owner = ?',array($player->id));
	
	$tmp = array();
	foreach($owned_items as $item) {
		$tmp[] = $item->tojson();
	}
	
	return json($tmp);
}

function get_inventory_info($id) {
	$item = R::findOne('owned_item','id = ?',array($id));
	
	return json($item->tojson());
}

function add_item_to_store() {
	$player = unserialize($_SESSION['player']);
	if(array_key_exists('id',$_POST) && array_key_exists('price',$_POST)) {
		$owned_item = R::findOne('owned_item','id = ? and owner = ?',array($_POST['id'],$player->id));
		
		if(!empty($owned_item)) {
			$item = R::dispense('item');
			$item->import($owned_item->export(), 'name,level,str,def,agi,luck');
			$building = R::findOne('building','owner = ? and loc_x = ? and loc_y = ? and building_type = 1',array($player->id,$player->loc_x,$player->loc_y));
			if(!empty($building)) {
				$item->store_id = $building->id;
				$item->cost = intval($_POST['price']);
				R::store($item);
				R::trash($owned_item);
				
				return json($item->tojson());
			}
			return json('wrong-location');
		}
		return json('non-existent');
	}
	
	return json('malformed');
}

function mine() {
	$player = unserialize($_SESSION['player']);
	$mine = R::findOne('building', 'loc_x = ? and loc_y = ? and building_type = 3', array($player->loc_x,$player->loc_y));
	
	if(array_key_exists('type',$_POST) && !empty($mine)) {
		$type = intval($_POST['type']);
		switch($type) {
			case 1: 
				// stone
				$rand = rand(0,10000);
				$stone = ceil(1 + round(($player->mining * 0.85))) * $mine->level;
				$crit = rand($player->luck,$player->luck+100) + rand(0,10000); 
				
				if($rand <= $crit) {
					$stone += $stone;
					$player->mining_exp += 3;
				}
					
				$player->stone += $stone;
				$old_level = $player->mining;
				$player->mining_exp += 1;
					
				R::store($player);
				$_SESSION['player'] = serialize($player);
					
				if($old_level == $player->mining) {
					return json(array((int)$stone,(bool)false,'stone'));
				}
				else {
					return json(array((int)$stone,(bool)true, 'stone'));
				}
				
				break;
				
			case 2:
				//copper
				
				$rand = rand(0,15000);
				$copper = ceil(1 + round(($player->mining * 0.85))) * $mine->level;
				$crit = rand($player->luck*.1,$player->luck) + rand(0,10000); 
				
				if($rand <= $crit) {
					$copper += $copper;
					$player->mining_exp += 3;
				}
					
				$player->copper += $copper;
				$old_level = $player->mining;
				$player->mining_exp += 1;
					
				R::store($player);
				$_SESSION['player'] = serialize($player);
					
				if($old_level == $player->mining) {
					return json(array((int)$copper,(bool)false,'copper'));
				}
				else {
					return json(array((int)$copper,(bool)true,'copper'));
				}
				break;
				
			case 3:
				//tin
				$rand = rand(0,15000);
				$tin = ceil(1 + round(($player->mining * 0.85))) * $mine->level;
				$crit = rand($player->luck*.1,$player->luck) + rand(0,10000); 
				
				if($rand <= $crit) {
					$tin += $tin;
					$player->mining_exp += 3;
				}
					
				$player->tin += $tin;
				$old_level = $player->mining;
				$player->mining_exp += 1;
					
				R::store($player);
				$_SESSION['player'] = serialize($player);
					
				if($old_level == $player->mining) {
					return json(array((int)$tin,(bool)false,'tin'));
				}
				else {
					return json(array((int)$tin,(bool)true,'tin'));
				}
				break;
				
			case 4: 
				//iron
				break;
				
			case 5: 
				//silver
				break;
				
			case 6:
				//gold
				break;
		}
	}
}

function upgrade_building($building_id) {
	$building_id = intval($building_id);
	$player = unserialize($_SESSION['player']);
	
	$building = R::findOne('building','id = ? and owner = ?',array($building_id,$player->id));
	
	if(!empty($building)) {
		if($player->gold - $building->cost >= 0 && $player->stone - $building->stone >= 0) {
			$player->gold -= $building->cost;
			$player->stone -= $building->stone;
			
			R::stonre($player);
			$_SESSION['player'] = serialize($player);
			
			return json((bool)true);
		}
		else {
			return json((bool)false);
		}
	}
	
	return json((bool)false);
}

function get_chat_messages($since = 0) {
	$since = intval($since);
	$time = time();
	$player = unserialize($_SESSION['player']);

	if($since < strtotime('-1 hour',$time)) {
		$since = strtotime('-1 hour',$time);
	}
	
	$messages = R::find('message','post_time > ? and (touser is null or (touser = ? or fromuser = ?)) order by post_time desc limit 30',array($since,$player->username,$player->username)); 
	
	if(!empty($messages)) {
		$tmp = array();
		foreach($messages as $i => $message) {
			$tmp[] = $message->tojson();
		}
	
		reset($messages);
		$c = current($messages);
		return json(array('time' => (int)$c->post_time, 'messages' => $tmp));
	}
	else {
		return json(array('time' => (int)$time, 'messages' => array()));
	}
}

function post_chat_message() {
	$player = unserialize($_SESSION['player']);
	if(array_key_exists('message',$_POST) && !empty($_POST['message'])) {
		// Commands!
		$chat = new ChatManager($_POST['message']);
		$time = $chat->execute();
		
		return json((int)$time-1);
	}
}

function skillup($type) {
	$types = array('vit','str','agi','luck','tough');
	$player = unserialize($_SESSION['player']);
	if(in_array($type,$types)) {
		if($player->skill_points - 1 >= 0) {
			$player->skillup($type);
			
			R::store($player);
			$_SESSION['player'] = serialize($player);
			
			return json(array(
				$type => (int)$player->$type,
				'type' => $type,
				'total_hp' => (int)$player->total_hp,
				'damage' => (int)$player->damage(),
				'defence' => (int)$player->defence()
			));
		}
	}
}

run();
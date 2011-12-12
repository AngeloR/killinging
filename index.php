<?php session_start();

date_default_timezone_set('America/Toronto');

include('lib/app.php');
include('lib/limonade.php');
include('lib/rb.php');

include('class/ChatManager.php');

include('interface/building.php');
include('interface/store.php');
include('interface/crafting.php');
include('interface/quarry.php');
include('interface/bank.php');
include('interface/tavern.php');


include('model/player.php');
include('model/monster.php');
include('model/city.php');
include('model/building.php');
include('model/item.php');
include('model/item_all.php');
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

dispatch_get('/game','gameframe');
dispatch_get('/gameframe','game');


dispatch_get('/move/:dir','movement_handler');
dispatch_post('/fight','fight_handler');
dispatch_post('/pvp','pvp_handler');
dispatch_post('/item/info/:id','get_item_info');

dispatch_get('/building/info/:id','get_building_info');
dispatch_post('/building','build');
dispatch_post('/bank/:id/deposit','deposit');
dispatch_post('/bank/:id/withdraw','withdraw');
dispatch_post('/tavern/:id','heal');

dispatch_get('/inventory/info/:id','get_inventory_info');
dispatch_post('/inventory/:id','buy_item');
dispatch_get('/inventory','get_inventory');

dispatch_get('/craft/:id', 'craft_item');

dispatch_post('/skill/:type','skillup');

dispatch_post('/store/add','add_item_to_store');

dispatch_post('/mine','mine');
dispatch_post('/upgrade/:building_id','upgrade_building');

dispatch_get('/chat/:since','get_chat_messages');
dispatch_post('/chat','post_chat_message');


// dont clutter the routes.
if(array_key_exists('player',$_SESSION)) {
	$player = unserialize($_SESSION['player']); 
	if($player->admin > 0) {
		include('admin.php');
	}
} 

function round_to_nearest( $number, $toNearest = 5 ) {

	$retval = 0;

	$mod = $number % $toNearest;

	if( $mod >= 0 ) {
		$retval = ( $mod > ( $toNearest / 2 )) ? $number + ( $toNearest - $mod )
		: $number - $mod;

	} else {
		$retval = ( $mod > ( -$toNearest / 2 )) ? $number - $mod : $number +
		( -$toNearest - $mod );

	}
	return $retval;

}

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
		
		post_to_chat($player->username.' has logged in.');
		
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

/**
*
* This is what renders the main game section. All the tabs and the map section
* are generated here and then populated outside this frame via JS.
*/
function gameframe() {	
	$player = unserialize($_SESSION['player']);
	set('player',$player);
	
	return render('game.html.php');
}


function game() {
	$player = unserialize($_SESSION['player']);
	
	// Get all player items
	$helm = R::getAll('select id,name from helm where owner = ? order by id asc',array($player->id)); 
	$weapon = R::find('weapon','owner = ? order by id asc',array($player->id));

	set('helms',$helm);
	set('weapons',$weapon);
	
	// Get the city where the player is.
	$city = R::findOne('city','zone = ? and min_x <= ? and min_y <= ? and max_x >= ? and max_x >= ?',array($player->zone,$player->loc_x,$player->loc_y,$player->loc_x,$player->loc_y));
	if(empty($city)) {
		$city = R::findOne('city','id = 1');
	}
	set('city',$city);
	
	$buildings = R::find('building_type','1 order by cost asc');
	set('buildings',$buildings);
	
	set('gamemessages',$_SESSION['flash']);
	$_SESSION['flash'] = array();
	
	set('player',unserialize($_SESSION['player']));
	
	return partial('tabs.html.php');
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
			$_SESSION['flash'][] = 'You found 1 stone.';
		}
		
		
		// Check to see if the player should gain any HP. This is lucked based.
		$chance = $player->luck; 
		$rand = rand(0,1000); 
		if($rand <= $chance) {
			$gain = round($player->luck/100);
			if($gain == 0) {
				$gain = 1;
			}
			
			if($player->current_hp+$gain <= $player->total_hp()) {
				$player->current_hp += $gain;
				$_SESSION['flash'][] = 'You feel a little more rested.. you\'ve gained '.$gain.' hp.';
			}
		}
		R::store($player);
		$_SESSION['player'] = serialize($player);
		
	}
	
	$players = R::find('player','city = ? and loc_x = ? and loc_y = ? and player_id != ?',array($player->city,$player->loc_x,$player->loc_y,$player->id));
	set('players',$players);
	
	find_monster($city->id);
	
	return game();
}

function find_monster($id) {
	$player = unserialize($_SESSION['player']);
	// chance to find monster. Current its' a 50% chance to find a monster and then 
	// each monster has equal chance of showing up, but eventually each monster 
	// will have its own chance of appearing, allowing for rare Boss monsters.
	$rand = rand(0,1);
	if($rand == 1) {
		$monsters = R::find('monster','city = ? and level <= ? and min_x <= ? and min_y <= ? and max_x >= ? and max_y >= ?', array($id,$player->level,$player->loc_x,$player->loc_y,$player->loc_x,$player->loc_y));
		if(!empty($monsters)) {
			$monster = $monsters[array_rand($monsters)];
			unset($_SESSION['battle']);
	
			if(!empty($monster)) {
				$_SESSION['battle'] = serialize($monster);
				set('monster',$monster);
			}
		}
	}
}

function fight_club_calc_damage($attacker,$defender) {
	$damage = $attacker->damage();
	
	$defence = $defender->defence();
	
	// crits!
	$crit_rate = rand(0,$attacker->luck*100 - $attacker->luck);
	$crit = false;
	if($crit_rate <= $attacker->luck) {
		$damage += $attacker->str/2;
		$crit = true;
	}
	
	$damage = floor($damage - $defence);
	
	if($damage <= 0) {
		$damage = 0;
	}
	
	return array($damage,$crit);
}

function fight_club($p1,$p2) {
	// MAX ROUNDS
	$MAX_ROUNDS = 15;
	
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
		if($rounds < $MAX_ROUNDS) { 
			++$rounds;
			$damage = fight_club_calc_damage($first,$second);
			$second->current_hp -= $damage[0];
			
			if(isset($first->name)) {
				$messages[] = 'The '.$first->name.' '.(($damage[0]==0)?'<span class="missed">missed</span>':'attacked you'. (($damage[1])?' <i>critically</i>':'') .' for <span class="they-attacked">'.$damage[0].' damage.</span>');
			}
			else {
				$messages[] = 'You '. (($damage[0]==0)?'<span class="missed">missed</span>':'attacked the '.$second->name. (($damage[1])?' <i>critically</i>':'') .' for <span class="you-attacked">'.$damage[0].' damage.</span>');
			}
			
			if($second->current_hp <= 0) {
				break;
			}
			
			$damage = fight_club_calc_damage($second,$first);
			$first->current_hp -= $damage[0];
			
			if(isset($second->name)) {
				$messages[] = 'The '.$second->name.' '.(($damage[0]==0)?'<span class="missed">missed</span>':'attacked you'. (($damage[1])?' <i>critically</i>':'') .' for <span class="they-attacked">'.$damage[0].' damage.</span>');
			}
			else {
				$messages[] = 'You '. (($damage[0]==0)?'<span class="missed">missed</span>':'attacked the '.$first->name. (($damage[1])?' <i>critically</i>':'') .' for <span class="you-attacked">'.$damage[0].' damage.</span>');
			}
		}
		else {
			break;
		}
	}
	// return format
	// winner,loser,rounds,messages
	if($first->current_hp <= 0) {
		return array($second,$first,$rounds,$messages);
	}
	else if($second->current_hp <= 0) {
		return array($first,$second,$rounds,$messages);
	}
	else {
		// max round length was reached
		$messages[] = 'The monster escaped! Maybe you should get a bit stronger before trying this one...';
		if(isset($first->name)) {
			return array($second,$first,$rounds,$messages);
		}
		else {
			return array($first,$second,$rounds,$messages);
		}
	}
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
	 			list($winner,$loser,$rounds,$messages) = fight_club($player,$monster);

	 			if(isset($winner->username)) {
	 				// player won
	 				if($loser->current_hp <= 0) {
	 					$player->current_hp = $winner->current_hp;
	 					$player->gold += $monster->gold;
	 					$player->current_exp += $monster->exp;
	 					$tie = false;
	 				}
	 				else {
	 					$player->current_hp = $winner->current_hp;
	 					$tie = true;
	 				}
	 			}
	 			else {
	 				$player->gold = 0;
	 				
	 				R::store($player);
	 				$_SESSION['player'] = serialize($player);
	 				
					$_SESSION['flash'][] = 'Whoops, the '.$monster->name.' killed you! You have been sent to your milestone.';
					return json('f331d3ad');
	 			}
	 			
	 		}
	 		else {
	 			return json(array(
	 				'messages' => array('You couldn\'t attack that monster because your HP is too low.')
	 			));
	 		}
	 }
	 
	 R::store($player);
	 $_SESSION['player'] = serialize($player);
	 
	 return json(array(
	   'messages' => $messages,
	 	 'rounds' => $rounds,
	 	 'monster' => $monster->name,
	 	 'tie' => (bool)$tie,
	 	 'stats' => array(
	 	 		'current_hp' => (int)$player->current_hp,
	 	 		'total_hp' => (int)$player->total_hp,
	 	 		'current_mp' => (int)$player->current_mp,
	 	 		'total_mp' => (int)$player->total_mp,
	 	 		'current_exp' => (int)$player->current_exp,
	 	 		'total_exp' => (int)$player->exp_to_level(),
	 	 		'gold' => (int)$monster->gold,
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
	$item = R::findOne($_POST['type'],'id = ?',array($id));
	return json($item->tojson());
}

function get_building_info($id) {
	$building = R::findOne('building_type','id = ?',array($id));
	return json($building->tojson());
}

function build() {
	$player = unserialize($_SESSION['player']);
	$building = R::findOne('building_type','id = ?',array($_POST['build']));
	
	// make sure that building type exists
	if(!empty($building)) {
		
		// make sure the player has enough resources
		if($player->gold >= $building->cost && $player->stone >= $building->stone) {
			// remove those resources from the player inventory
			$player->gold -= $building->cost;
			$player->stone -= $building->stone; 
			
			// add the building to the buildings list
			$building_set = R::dispense('building');
			
			$building_set->building_type = $building->id;
			$building_set->cost = $building->cost;
			$building_set->stone = $building->stone;
			$building_set->map_id = $player->city;
			$building_set->loc_x = $player->loc_x;
			$building_set->loc_y = $player->loc_y; 
			$building_set->level = 1;
			$building_set->owner = $player->id;
			$building_set->owner_type = 'player';
			$building_set->name = ucfirst($building->name).' by '.$player->username;
			
			// Banks are special. A "Tax Rate" is added for them
			if($building->id == 5) {
				$building_set->tax = 1.5;
			}
			
			R::store($building_set);
			R::store($player);
			$_SESSION['player'] = serialize($player);
			
			
			
			
			// notify users that there is a building on the map
			post_to_chat($player->username.' just built a '.$building->name.' at '.$player->loc_x.','.$player->loc_y);
			$array = array('status' => 'success', 'data' => array('gold' => $player->gold, 'stone' => $player->stone));
		}
		else {
			// notify the user they didn't have enough resources
			$array = array('status' => 'failed', 'data' => 'You don\'t have enough resources to build a '.$building->name);
		}
	}
	else {
		$array = array('status' => 'failed', 'data' => 'You need to select a building that you want to build on this land.');
	}
	
	return json($array);
}

function deposit($bank_id) {
	$player = unserialize($_SESSION['player']);
	
	if($player->gold >= $_POST['gold'] || $_POST['gold'] < 0) {
		// player has enough gold		
		$transaction = R::findOne('transaction','player = ? and bank_id = ?', array($player->id,$bank_id));
	
	
		if(empty($transaction)) {
			// a transaction doesnt exist. First time using this bank.
			$transaction = R::dispense('transaction');
			$transaction->player = $player->id;
			$transaction->gold = 0;
			$transaction->bank_id = $bank_id;
		}
		
		if($_POST['gold'] < 0) {
			$_POST['gold'] = $player->gold;
		}
		$transaction->gold += $_POST['gold'];
		
		$player->gold -= $_POST['gold'];
		
		R::store($transaction);
		R::store($player);
		
		$_SESSION['player'] = serialize($player);
		
		$array = array('status' => 'success', 'data' => array('player' => number_format($player->gold,0), 'bank' => number_format($transaction->gold,0)));
	}
	else {
		// trying to deposit more than they have
		$array = array('status' => 'failed', 'data' => 'You can\'t deposit that much money.');
	}
	
	return json($array);
}

function withdraw($bank_id) {
	$player = unserialize($_SESSION['player']);
	
	$transaction = R::findOne('transaction','player = ? and bank_id = ?',array($player->id,$bank_id));
	// check if the player has any gold stored in the bank
	if(!empty($transaction)) {
		// check if the player has enough gold stored in the bank
		if($transaction->gold >= $_POST['gold']) {
			// withdraw the gold
			if($_POST['gold'] < 0) {
				$_POST['gold'] = $transaction->gold;
			}
			$transaction->gold -= $_POST['gold'];
			$player->gold += $_POST['gold'];
			
			// remove the transaction if there is no more gold left | update if there is
			if($transaction->gold == 0) {
				R::trash($transaction);
			}
			else {
				R::store($transaction);
			}
			
			R::store($player);
			$_SESSION['player'] = serialize($player);
			
			$array = array('status' => 'success', 'data' => array('player'=>number_format($player->gold,0), 'bank' => number_format($transaction->gold,0)));
		}
		else {
			$array = array('status' => 'failed', 'data' => 'You don\'t have enough gold in your bank.');
		}

	}
	else {
		$array = array('status' => 'failed', 'data' => 'You don\'t have any gold in this bank.');
	}
	
	return json($array);
}

function heal($id) {
	$player = unserialize($_SESSION['player']);
	$tavern = R::findOne('building','id = ? and loc_x = ? and loc_y = ?',array($id,$player->loc_x,$player->loc_y)); 
	
	if(!empty($tavern)) {
		$tavern_interface = new TavernInterface($tavern);
		$cost = $tavern_interface->cost_to_heal_player($player);
		
		if($player->gold >= $cost) {
			$player->gold -= $cost;
			$player->current_hp = $player->total_hp();
			
			R::store($player);
			$_SESSION['player'] = serialize($player);
			
			$array = array('status' => 'success', 'data' => array('gold' => number_format($player->gold,0)));

		}
		else {
			$array = array('status'=>'failed', 'data' => 'You don\'t have enough money to stay at a tavern.');
		}
	}
	else {
		$array = array('status' => 'failed', 'data' =>'There is no tavern at that location.');
	}
	
	return json($array);
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

function craft_item($recipe_id) {
	$recipe = R::findOne('crafting_recipe','id = ?',array($recipe_id));
	
	if(!empty($recipe)) {
		$player = unserialize($_SESSION['player']);
		// Loop through all the costs and subtract them from the player.
		$costs = array('copper','tin','bar_bronze','iron','bar_cast_iron');
		
		foreach($costs as $i => $cost) {
			if(($player->$cost - $recipe->$cost) < 0) {
				$array = array((bool)false,'Not enough resources.');
				return json($array);
			}
			else {
				$player->$cost = $player->$cost - $recipe->$cost;
			}
		}
		
		
		
		if(strpos($recipe->type,'bar_') === 0) {
			$i = $recipe->type;
			if(!isset($player->$i)) {
				$player->$i = 0;
			}
			$player->$i = (int)$player->$i+1; 


			R::store($player);
			$_SESSION['player'] = serialize($player);
			
			$returns = array(
				(bool)true,
				'recipe' => array('name' => $recipe->name, 'icon' => $recipe->icon),
				'resources'=> array(
					'copper' => $player->copper,
					'tin' => $player->tin,
					'bronze' => $player->bar_bronze,
					'iron' => $player->iron,
					'cast_iron' => $player->bar_cast_iron
				)
			);
			
			return json($returns);
		}
		else {
			// We are crafting a weapon/armor/accessory
			// Craft item
			$item = R::dispense($recipe->type);

			// Set item stats
			// Are we generating a terrible, below-average,average,above-average, awesome or godly item?
			$type = rand(1,100);
			$stats =  array('str','tough','agi','luck','vit');
			
			// Mod types are added here in the following format
			// array(array(list,of,prefixes),modifier, how many stats to modify)
			$mod_type = array(
				array(array('Awful'), 0.5,5),
				array(array('Inferior'), 0.75,3),
				array(array('Regular','Formidable','Excellent'),1,0),
				array(array('Magnificent'), 1.5,3),
				array(array('Superior','Superb'),1.5,5),
				array(array('Radiant'),2.5,5)
			);
			
			if($type < 5) {
				$modification_type = 0;
			}
			else if($type < 20) { 
				$modification_type = 1;
			}
			else if($type < 85) {
				$modification_type = 2;
			}
			else if($type < 97) {
				$modification_type = 3;
			}
			else if($type < 99) {
				$modification_type = 4;
			}
			else {
				$modification_type = 5;
			}
			
			$modified = 0;
			$cost_base = 0;
			foreach($stats as $i => $stat) {
				if($modification_type != 2) {
					// Grand Modifications only apply to non-regular crafted items
					$grand_mod = rand(0,1);
				}
				
				if(isset($grand_mod) && $grand_mod && $mod_type[$modification_type][2] > $modified) {
					
					// If we should modify a stat, we check if the stat is positively or negatively modified. 
					
					if($modification_type > 2) {
						// if it is positively modified we check to see if the stat is 0, if it is, we add 1 and
						// then calculate
						if($recipe->$stat == 0) {
							$offset = 1;
						}
					}
					
					else if($modification_type <= 1) {
						if($recipe->$stat == 0) {
							$offset = -1;
						}
					}
					else {
						$offset = $recipe->$stat;
					}
					
					
					$item->$stat = floor($mod_type[$modification_type][1]*$offset);

					$modified++;
					$cost_base += $item->$stat;
				}
				else {
					$item->$stat = $recipe->$stat;
					$cost_base += $item->$stat;
				}
			}
			
			$name = rand(0,count($mod_type[$modification_type][0]) - 1);
				
			$item->name = $mod_type[$modification_type][0][$name].' '.$recipe->name;
			$item->owner = $player->id;
			$item->cost = $cost_base*7;
			$item->icon = $recipe->icon;
			$item->level = floor($recipe->level*$mod_type[$modification_type][1]);
			$player->crafting_exp += $recipe->exp;
				
			R::store($player);
			$_SESSION['player'] = serialize($player);
			R::store($item);
			
			$return = array(
				(bool)true,
				'recipe' => $item->tojson(),
				'type' => $recipe->type,
				'resources'=> array(
									'copper' => $player->copper,
									'tin' => $player->tin,
									'bronze' => $player->bar_bronze,
									'iron' => $player->iron,
									'cast_iron' => $player->bar_cast_iron
				)
			);
			
			return json($return);
		}
	}
	else {
		return json(array((bool)false, 'That crafting recipe is now known to you yet.'));
	}
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
				
				$total_resources = $stone;
				$resource_type = 'stone';
					
				
				
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
					
				$total_resources = $copper;
				$resource_type = 'copper';
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
					
				$total_resources = $tin;
				$resource_type = 'tin';
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
		
		// Globalized return so that we have a consistent interface for all mining
		// functions
	 	$details = array((int)$total_resources,(bool)($old_level != $player->mining),$resource_type, $player->mining_exp, $player->exp_to_mining());
		
		return json($details);
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

function post_to_chat($message) {
	$server = new stdClass();
	$server->username = 'Server';
	$server->admin = 2;
	
	$chat = new ChatManager($message,$server);
	$chat->execute();
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
				'damage' => $player->damage_uncalc(),
				'defence' => (int)$player->defence()
			));
		}
	}
}

run();
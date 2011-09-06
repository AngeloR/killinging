<?php
class Model_City extends RedBean_SimpleModel {
	
	private $land = array(
			'0,153,0' => array('name'=>'grass','walk'=>1),
			'20,68,20' => array('name'=>'trees','walk'=>1),
			'0,32,151' => array('name'=>'water','walk'=>0),
			'104,104,104' => array('name'=>'stone','walk'=>1),
		);
	
	public function open() {
		$player = unserialize($_SESSION['player']);
		$map_size = 4;
		$this->buildings = R::find('building','map_id = ? and (loc_x > ? and loc_x < ?) and (loc_y > ? and loc_y < ?)',array($this->id,$player->loc_x - $map_size, $player->loc_x + $map_size, $player->loc_y - $map_size, $player->loc_y + $map_size));
		
	}
	
	public function update() {
		if(isset($this->buildings)) {
			unset($this->buildings);
		}
	}
	
	public function draw($center_x,$center_y,$size) {
		$player = unserialize($_SESSION['player']);
		$map = imagecreatefrompng('maps/zone1.png');
		
		$neg = $size * (-1);
		$min_x = $center_x + $neg;
		$min_y = $center_y + $neg;
		$max_x = $center_x + $size;
		$max_y = $center_y + $size;

		// chance to find monster!
		$rand = rand(0,1);
		if($rand == 1) {
			$monsters = R::find('monster','city = ? and level <= ? and min_x <= ? and min_y <= ? and max_x >= ? and max_y >= ?', array($this->id,$player->level,$player->loc_x,$player->loc_y,$player->loc_x,$player->loc_y));
			if(!empty($monsters)) {
				$monster = $monsters[array_rand($monsters)];
				unset($_SESSION['battle']);
				
				if(!empty($monster)) {
					$_SESSION['battle'] = serialize($monster);
					set('monster',$monster);
				}
			}
		}
		
		$land_types = array_keys($this->land);
		
		
		for($y = $neg; $y <= $size; ++$y) {
			for($x = $neg; $x <= $size; ++$x) {
				$rgb = imagecolorat($map,$center_x + $x,$center_y + $y);
				$tile = imagecolorsforindex($map, $rgb);
				
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				
				$key =	$r.','.$g.','.$b;
				if(array_key_exists($key,$this->land)) {
					echo '<img src="/killinging/tiles/'.$this->land[$key]['name'].'.png" width="60" height="60">';
				}
			}
			echo '<br>';
		}
	}
	
	public function can_move_to($x,$y) {
		$map = imagecreatefrompng('maps/zone1.png');
		$rgb = imagecolorat($map,$x,$y);
		$tile = imagecolorsforindex($map, $rgb);
				
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		
		$key =	$r.','.$g.','.$b;
			
		return (array_key_exists($key,$this->land) && $this->land[$key]['walk']);
	}

	public function at($x,$y) {
		$size = count($this->buildings);
		$player = unserialize($_SESSION['player']);

		foreach($this->buildings as $building) {
			
			if($building->loc_x == $x && $building->loc_y == $y) {
				switch($building->building_type) {
					case 1:
						$point = new StoreInterface($building);
						$point->owner = $player->id;
						return $point;
						break;
						
					case 3:
						$quarry = new QuarryInterface($building);
						$quarry->owner = $player->id;
						return $quarry;
						break;
				}
			}
		}
		return false;
	}
	
	public function building_at($x,$y) {
		foreach($this->buildings as $building) {
			if($building->loc_x == $x && $building->loc_y == $y) {
				return $building;
			}
		}
		return false;
	}
	
	public function can_build_here($x,$y) {
		$player = unserialize($_SESSION['player']);
		if($this->at($x,$y) ) {
			return false;
		}
		else {
			
			$safe_zone = 3;
			$size = count($this->buildings);
			
			foreach($this->buildings as $building) {
				$coords = array(
					array($building->loc_x - $safe_zone, $building->loc_y - $safe_zone),			// top left
					array($building->loc_x + $safe_zone, $building->loc_y - $safe_zone),		// top right
					array($building->loc_x - $safe_zone, $building->loc_y + $safe_zone),		// bottom left
					array($building->loc_x + $safe_zone, $building->loc_y + $safe_zone)			// bottom right
				);
				
				
				if($x > $coords[0][0] && $x < $coords[1][0] && $y > $coords[0][1] && $y < $coords[2][1]) {
					return ($building->owner == $player->id);
				}
			}
		}
		
		return true;
	}
	
	public function player_owns_location($x,$y) {
		$player = unserialize($_SESSION['player']);
		foreach($this->buildings as $building) {
			if($building->owner == $player->id && $building->loc_x == $x && $building->loc_y == $y) {
				return true;
			}
		}
		return false;
	}
}
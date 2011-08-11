<?php
class Model_City extends RedBean_SimpleModel {
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

	public function at($x,$y) {
		$size = count($this->buildings);

		foreach($this->buildings as $building) {
			
			if($building->loc_x == $x && $building->loc_y == $y) {
				switch($building->building_type) {
					case 1:
						$point = new StoreInterface($building);
						return $point;
						break;
				}
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
}
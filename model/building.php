<?php

class Model_Building_Type extends RedBean_SimpleModel {
	
	public function update() {
		
	}
	
	public function new_building() {
		$player = unserialize($_SESSION['player']);
		
		$this->owner = $player->id;
		$this->owner_type = $type;
	}
	
	public function tojson() {
		$tmp = array(
			'id' => (int)$this->id,
			'name' => $this->name,
			'cost' => number_format($this->cost),
			'stone' => (int)number_format($this->stone),
			'time' => (int)$this->time,
			'description' => $this->description
		);
		
		return $tmp;
	}
}
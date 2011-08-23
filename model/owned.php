<?php

class Model_Owned_Item extends RedBean_SimpleModel {
	
	public function update() {
		if(!isset($this->owner)) {
			$player = unserialize($_SESSION['player']);
			$this->owner = $player->id;
			$this->equipped = false;
			$this->cost *= 0.5;
		}
	}
	
	public function tojson() {
		return array(
			'id' => (int)$this->id,
			'name' => $this->name,
			'cost' => (int)$this->cost,
			'level' => (int)$this->level,
			'str' => (int)$this->str,
			'def' => (int)$this->def,
			'agi' => (int)$this->agi,
			'luck' => (int)$this->luck,
			'equipped' => (bool)$this->equipped
		);
	}
}
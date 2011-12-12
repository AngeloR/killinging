<?php

class Model_Item extends RedBean_SimpleModel {
	
	public function update() {
		if(!isset($this->equipped)) {
			$this->equipped = 0;
		}
	}

	public function tojson() {
		$tmp = array (
				'id' => $this->id,
				'name' => $this->name,
				'cost' => $this->cost,
				'level' => $this->level,
				'str' => $this->str,
				'tough' => $this->tough,
				'vit' => $this->vit,
				'agi' => $this->agi,
				'luck' => $this->luck,
				'icon' => $this->icon
		);
	
		return $tmp;
	}
}
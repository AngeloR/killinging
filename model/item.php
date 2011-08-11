<?php

class Model_Item extends RedBean_SimpleModel {
	
	public function update() {
		if(!isset($this->store_id)) {
			$this->store_id = 1;
		}
	}
	
	public function tojson() {
		$tmp = array (
				'id' => $this->id,
				'name' => $this->name,
				'cost' => $this->cost,
				'level' => $this->level,
				'str' => $this->str,
				'def' => $this->def,
				'agi' => $this->agi,
				'luck' => $this->luck,
		);
	
		return $tmp;
	}
}
<?php

class Model_Player extends RedBean_SimpleModel {
	
	public function update() {
		if($this->current_exp > $this->exp_to_level()) {
			
			$this->current_exp -= $this->exp_to_level();
			$this->level += 1;
		}
		
		if($this->current_hp < 0) {
			$this->current_hp = 0;
		}
		
		if(isset($this->name)) {
			unset($this->name);
		}
		
		if(isset($this->exp)) {
			unset($this->exp);
		}
	}
	
	public function new_setup() {
		$this->password = pw_hash($this->password);
			
		$class = R::findOne('class','id = ?',array($this->class_id));
			
			
		$this->total_hp = $class->hp;
		$this->current_hp = $class->hp;
			
		$this->total_mp = $class->mp;
		$this->current_mp = $class->mp;
		
		$this->current_exp = 0;
		$this->gold = 1000;
		
		$this->str = $class->str;
		$this->def = $class->def;
		$this->agi = $class->agi;
		$this->luck = $class->luck;
			
		$this->city = 1;
		$this->loc_x = 50;
		$this->loc_y = 50;
	}
	
	public function exp_to_level() {
		return floor(log($this->level + 1) * $this->level * 100);
	}
}
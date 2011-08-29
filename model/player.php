<?php

class Model_Player extends RedBean_SimpleModel {
	
	public function update() {
		if($this->current_exp >= $this->exp_to_level()) {
			
			$times = 0;
			while($this->current_exp >= $this->exp_to_level()) {
				$this->current_exp -= $this->exp_to_level();
				++$times;
			}
			
			$this->level = $this->level + (1*$times);
			$this->skill_points = $this->skill_points + (1*$times);

			$this->total_hp = $this->total_hp();
			$this->current_hp = $this->total_hp;
		}
		
		if($this->mining_exp > $this->exp_to_mining()) {
			$this->mining_exp -= $this->exp_to_mining();
			$this->mining += 1;
		}
		
		if($this->current_hp <= 0) {
			$this->current_hp = $this->total_hp();
			$this->city = 1;
			$this->loc_x = 50;
			$this->loc_y = 50;
		}
		
		if(isset($this->name)) {
			unset($this->name);
		}
		
		if(isset($this->exp)) {
			unset($this->exp);
		}
	}
	
	public function damage() {
		return round($this->str * $this->str + ($this->agi/2));
	}
	
	public function defence() {
		return $this->tough*+$this->vit*($this->tough/2);
	}
	
	public function total_hp() {
		return round($this->vit*$this->tough + $this->vit*($this->tough/2));
	}
	
	public function skillup($skill) {
		$this->skill_points--;
		$this->$skill += 1;
		
		$this->total_hp = $this->total_hp();
	}
	
	public function new_setup() {
		$this->password = pw_hash($this->password);
			
		$class = R::findOne('class','id = ?',array($this->class_id));
		
		$this->skill_points = 1;
			
		$this->total_mp = $class->mp;
		$this->current_mp = $class->mp;
		
		$this->current_exp = 0;
		$this->gold = 100;
		
		$this->vit = $class->vit;
		$this->str = $class->str;
		$this->tough = $class->tough;
		$this->agi = $class->agi;
		$this->luck = $class->luck;
		
		$this->level = 1;
		
		
		$this->total_hp = $this->total_hp();
		$this->current_hp = $this->total_hp;
		
		$this->mining = $class->mining;
		$this->smithing = $class->smithing;
			
		$this->city = 1;
		$this->loc_x = 50;
		$this->loc_y = 50;
	}
	
	public function exp_to_level() {
		return floor((log($this->level + 1) * $this->level * 100)/2);
	}
	
	public function exp_to_mining() {
		return floor(log($this->mining + 1) * $this->mining * 65);
	}
}
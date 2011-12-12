<?php

class Model_Player extends RedBean_SimpleModel {
	
	public function update() {
		if($this->current_exp >= $this->exp_to_level()) {
			
			$times = 0;
			while($this->current_exp >= $this->exp_to_level()) {
				$this->current_exp -= $this->exp_to_level();
				++$times;
				
				if($this->level == 5) {
					post_to_chat('Welcome to Rising Legends '.$this->username);
				}
				else if($this->level % 100 == 0) {
					post_to_chat($this->username.' has reached level '.$this->level.'!');
				}
			}
			
			$this->level = $this->level + (1*$times);
			$this->skill_points = $this->skill_points + (1*$times);

			$this->total_hp = $this->total_hp();
			$this->current_hp = $this->total_hp;
		}
		
		if($this->mining_exp >= $this->exp_to_mining()) {	
			
			$this->mining_exp -= $this->exp_to_mining();
			$this->mining += 1;
			
			if($this->mining % 10 == 0) {
				post_to_chat($this->username.' has reached mining level '.$this->mining.'!');
			}
		}
		
		if($this->crafting_exp >= $this->exp_to_crafting()) {
			$this->crafting_exp -= $this->exp_to_crafting();
			$this->crafting_exp += 1;
			
			if($this->crafting % 10 == 0) {
				post_to_chat($this->username.' has reached crafting level '.$this->crafting.'!');
			}
		}
		
		if($this->current_hp <= 0) {
			$this->current_hp = $this->total_hp();
			$this->city = 2;
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
	
	/**
	 * 
	 * Damage is calculated rather simply. Basically we take your strength, and 
	 * then divide it by 6. For every 6 points in strength you get an additional 
	 * d6+str/3.. so at 12 str, you would get 12/3+2d6 dealing a minimum of 5dm 
	 * and a max of 15.
	 */
	public function damage() {
		$damage = floor($this->str/3);
		
		$runs = $this->str/6;
		
		if($runs == 0) {
			return $this->str;
		}
		
		for($i = 0; $i < $runs; ++$i) {
			$damage += rand(1,6);
		}
		
		return $damage;
	}
	
	/**
	 * 
	 * Calculates what damage would look like
	 */
	public function damage_uncalc() {
		$damage = floor($this->str/3);
		
		$runs = floor($this->str/6); 
		
		if($runs == 0) {
			return $this->str.' + 0d6';
		}
		else {
			return $damage .' + '.$runs.'d6';
		}
	}
	
	public function defence() {
		return $this->tough + floor($this->tough*0.3);
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
		
		$this->class_name = $class->name;
		
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
		
		$this->stone = 0;
		$this->copper = 0;
		$this->tin = 0;
		
		$this->level = 1;
		
		
		$this->total_hp = $this->total_hp();
		$this->current_hp = $this->total_hp;
		
		$this->mining = $class->mining;
		$this->smithing = $class->smithing;
			
		$this->zone = 1;
		$this->city = 2;
		$this->loc_x = 50;
		$this->loc_y = 50;
	}
	
	public function exp_to_level() {
		return floor(round_to_nearest(((log($this->level + 1) * $this->level * 100)/2),10));
	}
	
	public function exp_to_mining() {
		return floor(round_to_nearest(log($this->mining + 1) * $this->mining * 65,10));
	}
	
	public function exp_to_crafting() {
		return floor(round_to_nearest(log($this->mining + 1) * $this->mining * 65,10));
	}
}
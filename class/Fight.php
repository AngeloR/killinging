<?php
class Fight {

	private $first;
	private $second;

	private $winner;
	private $loser;
	private $player;
	private $monster;
	private $flee;
	
	private $rounds;

	public function __construct($attacker1,$attacker2) {
		if($attacker1->agi > $attacker2->agi) {
			$this->first = $attacker1;
			$this->second = $attacker2;
		}
		else {
			$this->second = $attacker1;
			$this->first = $attacker2;
		}

		$this->setPlayerAndMonster();
		$this->flee = false;
	}

	public function setPlayerAndMonster() {
		if(is_null($this->first('player_id'))) {
			$this->player = 'second';
			$this->monster = 'first';
		}
		else {
			$this->player = 'first';
			$this->monster = 'second';
		}
	}

	public function canContinue() {

		return ($this->first->current_hp > 0 && $this->second->current_hp > 0);
	}

	public function attack() {

		$damage = $this->calculateDamage($this->first,$this->second);
		$this->second->current_hp -= $damage;
		$str .= ' for '.$damage.' damage.<br>';
		if(!$this->withinBounds($this->second['hp'],0,$this->second['max_hp'])) {
			$this->second['hp'] = $this->forceBounds($this->second['hp'],0,$this->second['max_hp']);
			$this->winner = 'first';
			$this->loser = 'second';
			echo $str;
			return;
		}
		echo $str;

		$str = $this->second['name'].' attacks '.$this->first['name'];
		$damage = $this->calculateDamage($this->second,$this->first);
		$this->first['hp'] -= $damage;
		$str .= ' for '.$damage.' damage.<br>';
		if(!$this->withinBounds($this->first['hp'],0,$this->first['max_hp'])) {
			$this->first['hp'] = $this->forceBounds($this->first['hp'],0,$this->first['max_hp']);
			$this->winner = 'second';
			$this->loser = 'first';
			echo $str;
			return;
		}
		echo $str;

	}

	public function monsterAttack() {
		$m = $this->monster;
		$p = $this->player;
		$monster = $this->$m;
		$player = $this->$p;

		$str = $monster['name'].' attacks '.$player['name'];
		$damage = $this->calculateDamage($monster,$player);
		$player['hp'] -= $damage;

		$str .= ' for '.$damage.' damage.<br>';
		if(!$this->withinBounds($player['hp'],0,$player['max_hp'])) {
			$player['hp'] = $this->forceBounds($player['hp'],0,$player['max_hp']);
			$this->winner = $m;
			$this->loser = $p;

			$this->$p = $player;
			echo $str;
			return;
		}
		echo $str;

	}

	public function flee() {
		$player = $this->player();
		$monster = $this->monster();

		$rand = rand(1,$player['agility'])+(0.25*$player['luck']);
		$rand2 = rand(1,$monster['agility']);

		if($rand > $rand2) {
			$m = $this->monster;
			$monster['hp'] = 0;
			$this->$m = $monster;
			$this->winner = $this->player;
			$this->loser = $this->monster;
			$this->flee = true;
			return true;
		}

		return false;
	}

	public function escaped() {
		return $this->flee;
	}

	public function withinBounds($value,$lower,$upper) {
		return ($value > $lower && $value < $upper);
	}

	public function forceBounds($value,$lower,$upper) {
		$value = ($value < $lower)?$lower:$value;
		$value = ($value > $upper)?$upper:$value;
		return $value;
	}

	public function winner() {
		$x = $this->winner;
		return $this->$x;
	}

	public function loser() {
		$x = $this->loser;
		return $this->$x;
	}

	public function first($key = null) {
		return $this->first[$key];
	}

	public function second($key = null) {
		return $this->second[$key];
	}

	public function player() {
		$x = $this->player;
		return $this->$x;
	}

	public function monster() {
		$x = $this->monster;
		return $this->$x;
	}

	private function calculateDamage($attacker,$defender) {
		$attack = ($attacker['strength'] < $defender['defence'])?1:$attacker['strength'] - $defender['defence'];

		if($this->wasCritical($attacker)) {
			$attack *= 2;
		}
		return $attack;
	}

	private function wasCritical($attacker) {
		$luck = rand(0,100);
		return ($luck <= $attacker['luck']);
	}

}
<?php

class TavernInterface implements BuildingInterface{

	public $tavern;
	public $owner;
	
	public function __construct($tavern) {
		$this->tavern = $tavern;
	}
	
	public function __get($key) {
		if(isset($this->tavern->$key)) {
			return $this->tavern->$key;
		}
	}
	
	public function __set($key,$value) {
		if(isset($this->tavern->$key)) {
			$this->tavern->$key = $value;
		}
	}
	
	public function cost_to_level() {
		$array = array();
	
		$array['cost'] = $this->tavern->cost * ($this->tavern->level+1);
		$array['stone'] = $this->tavern->stone * ($this->tavern->level * 1.25);
	
		return $array;
	}
	
	public function cost_to_heal_player($player) {
		return floor(($player->total_hp-$player->current_hp)*($player->level*3.14159)/25);
	}
	
	public function display() {
		echo '<img src="/tiles/tavern.png" width="48" height="48" class="building">';
	}
	
	public function render() {
		$player = unserialize($_SESSION['player']);
		$cost = $this->cost_to_heal_player($player);
		?>
		<p>This is the tavern. It will cost you <?php echo $cost; ?> gold to heal yourself.</p>
		<form action="<?php echo url_for('tavern',$this->id); ?>" method="post" id="form-tavern">
		<button type="submit">Heal for <?php echo $cost; ?></button>
		</form>
		<?php 
	}
	
	public function management() {
		
	}
}

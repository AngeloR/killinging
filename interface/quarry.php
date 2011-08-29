<?php

class QuarryInterface implements BuildingInterface{
	
	public $quarry; 
	public $owner; 
	
	public function __construct($quarry) {
		$this->quarry = $quarry;
	}
	
	public function __get($key) {
		if(isset($this->quarry->$key)) {
			return $this->quarry->$key;
		}
	}
	
	public function __set($key,$value) {
		if(isset($this->quarry->$key)) {
			$this->quarry->$key = $value;
		}
	}
	
	public function cost_to_level() {
		$array = array();
		
		$array['cost'] = $this->quarry->cost * ($this->quarry->level+1);
		$array['stone'] = $this->quarry->stone * ($this->quarry->level * 1.25);
		
		return $array;
	}
	
	public function display() {
		echo '<img src="/tiles/quarry.png" width="32" height="32" class="building">';
	}
	
	public function render() {
		?>
		<b><?php echo $this->name; ?> quarry (level: <?php echo $this->level; ?>)</b>
		<form id="mine">
			Mine for 
			<select name="resource-type" id="resource-type">
				
				<option value="1" selected="selected">Stone</option>
				<option value="2">Copper</option>
				<option value="3">Tin</option>
				<?php if($this->level > 1): ?>
				<option value="4">Iron</option>
				<?php endif; ?>
				<?php if($this->level > 3): ?>
				<option value="5">Silver</option>
				<?php endif; ?>
				<?php if($this->level > 6): ?>
				<option value="6">Gold</option>
				<?php endif; ?>
			</select>
			
			<select name="length" id="length">
				<option value="1" selected="selected">Once</option>
				<option value="10">10 Times</option>
				<option value="25">25 Times</option>
			</select>
			
			<button type="submit" id="mine-button">Start Mining!</button>
		</form>
		
		<ul id="resource-results">
		
		</ul>
		<?php 
	}
	
	public function management() {
		$stats = $this->cost_to_level();
		?>
		<p>This mine is currently at level <?php echo $this->quarry->level; ?></p>
		<p>It will cost <b><?php echo number_format($stats['cost']); ?></b> gold and <b><?php echo number_format($stats['stone']); ?></b> stone to take this building to the next level. 
		<p class="center"><a href="<?php echo url_for('upgrade',$this->quarry->id); ?>" id="building-upgrade">Upgrade to Level <?php echo $this->quarry->level+1; ?></a></p>
		<?php 
	}
}
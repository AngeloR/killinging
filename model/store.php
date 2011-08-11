<?php

class StoreInterface{
	
	public $store;
	
	public function __construct($store) {
		$this->store = $store;
	}
	
	public function __get($key) {
		if(isset($this->store->$key)) {
			return $this->store->$key;
		}
	}
	
	public function __set($key,$value) {
		if(isset($this->store->$key)) {
			$this->store->$key = $value;
		}
	}
	
	public function display() {
		echo 'S';
	}
	
	/**
	 * 
	 * This is what the store will look like to teh user! nifty right?
	 * It makes adding new "interaction" types very easy. Like.. .QUESTS?!
	 */
	public function render() {
		$player = unserialize($_SESSION['player']);
		echo $this->name.(($this->owner == $player->id)?' <span class="help">(Your Store)</span>':'').'<br>';
		
		
		$itmes = R::find('item','store_id = ?',array($this->id));
		
		echo '<table class="shop-list"><tr><th class="name">Item</th><th class="price">Price</th><th class="action"></th>';
		foreach($itmes as $item) {
			echo '<tr><td class="name">'.$item->name.'</td><td class="price">'.(($this->owner == $player->id)?0:$item->cost).'</td><td class="action"><a href="'.url_for('item','info',$item->id).'" class="item-info">info</a> | buy</td></tr>';
		}
		echo '</table>';
	}
}
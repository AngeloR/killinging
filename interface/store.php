<?php

class StoreInterface implements BuildingInterface{
	
	public $store;
	public $owner;
	
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
	
	public function cost_to_level() {
		
	}
	
	public function display() {
		echo '<img src="/killinging/tiles/shop.png" width="32" height="32" class="building">';
	}
	
	public function management() {
		$player = unserialize($_SESSION['player']);
		$items = R::find('item','store_id = ?',array($this->id));
		
		echo '<table class="shop-list" id="store-manager"><tr><th class="name">Item</th><th class="price">Price</th><th class="action"></th>';
		if(!empty($items)) {	
			foreach($items as $item) {
				echo '<tr><td class="name">'.$item->name.'</td><td class="price"><input type="text" class="item-price" value="'.$item->cost.'"></td><td><a href="#" class="remove-item">x</a></td></tr>';
			}
			echo '</table>';
		}
		else {
			echo '</table>';
			echo 'There are no items in your store. You can add some below.';
		}
		echo '<hr>';
		
		$owned_items = R::find('owned_item','owner = ?',array($player->id));
		
		echo '<table class="shop-list" id="inventory-manager"><tr><th class="name">Item</th><th class="price">Price</th><th class="action"></th>';
		if(!empty($owned_items)) {	
			foreach($owned_items as $owned_item) {
				echo '<tr id="im-'.$owned_item->id.'"><td class="name">'.$owned_item->name.'</td><td class="price"><input type="text" class="item-price" value="'.$owned_item->cost.'"></td><td><a href="#" class="add-item-to-store">Add to Store</a></td></tr>';
			}
			echo '</table>';
		}
		else {
			echo '</table>';
			echo 'There are no items in your store. You can add some below.';
		}
	}
	
	/**
	 * 
	 * This is what the store will look like to teh user! nifty right?
	 * It makes adding new "interaction" types very easy. Like.. .QUESTS?!
	 */
	public function render() {
		$player = unserialize($_SESSION['player']);
		echo $this->name.(($this->store->owner == $player->id)?' <span class="help">(Your Store)</span>':'').'<br>';
		
		
		$items = R::find('item','store_id = ?',array($this->id));
		
		if(!empty($items)) {
			echo '<table class="shop-list"><tr><th class="name">Item</th><th class="price">Price</th><th class="action"></th>';
			foreach($items as $item) {
				echo '<tr><td class="name">'.$item->name.'</td><td class="price">'.(($this->store->owner == $player->id)?'0 ('.$item->cost.')':$item->cost).'</td><td class="action"><a href="'.url_for('item','info',$item->id).'" class="item-info">info</a> | <a href="'.url_for('inventory',$item->id).'" class="buy">buy</a></td></tr>';
			}
			echo '</table>';
		}
		else {
			echo 'This store currently has no items.';
		}
	}
}
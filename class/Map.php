<?php

class Map {
	
	public $info;
	
	private $grass = array(00,99,00);
	private $trees = array(14,44,14);
	private $water = array(00,20,97);
	
	public function __construct($id) {
		$map = R::findOne('city','id = ?',array($id));
		if(!empty($map)) {
			$this->info = $map;
		}
	}
	
	/**
	 * 
	 * Loads a map file and displays the surrounding co-ordinates of a player. 
	 * 
	 * @param int $center_x
	 * @param int $center_y
	 * @param int $show_x 
	 * @param int $show_yz
	 */
	public function load($center_x,$center_y,$show_x = 3, $show_y = 3) {
		
	}
}
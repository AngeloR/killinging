<?php

class Model_Monster extends RedBean_SimpleModel {
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
	 * Defence is simple and grows exponentially.
	 */
	public function defence() {
		return $this->tough + floor($this->tough*0.3);
	}
}
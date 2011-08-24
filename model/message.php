<?php

class Model_Message extends RedBean_SimpleModel {
	
	public function tojson() {
		return array(
			'from' => $this->from,
			'text' => $this->text,
			'classification' => (int)$this->classification,
		);
	}
}
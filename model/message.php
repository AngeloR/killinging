<?php

class Model_Message extends RedBean_SimpleModel {
	
	public function tojson() {
		return array(
			'from' => $this->fromuser,
			'text' => $this->text,
			'classification' => (int)$this->classification,
			'touser' => $this->touser,
			'time' => $this->post_time
		);
	}
}
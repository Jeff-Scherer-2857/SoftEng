<?php 
require_once __DIR__ . '/Node.php';

class FloorNode extends Node {
	// class variables
	public $CalledUp = 0;
	public $CalledDown = 0;
	
	// class methods
	
	public function GetCalledUp() {
		return $this->CalledUp;
	}
	
	public function SetCalledUp($called) {
		$this->CalledUp = $called;
	}
	
	public function GetCalledDown() {
		return $this->CalledDown;
	}
	
	public function SetCalledDown($called) {
		$this->CalledDown = $called;
	}
}

?>
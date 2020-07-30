<?php 
require_once __DIR__ . '/Node.php';

// SINGLETON - there should only be ONE elevator car!
class CarNode extends Node {
	// class variables
	public static $CurrentFloor;
	
	private static $instance = null;
	
	// class methods
	
	public function __construct($ID) {
		$this->CANID = $ID;
	}
	
	public static function Instance($ID) {
		
		// SINGLETON!
		if (static::$instance == null) {
			static::$instance = new static($ID);
			//parent::$CANID = $ID;
		} else {
			echo "There is already an instance of CarNode! <br>";
		}
		
		return static::$instance;
	}
	
	public function GetCurrentFloor() {
		return self::$CurrentFloor;
	}
	
	public function SetCurrentFloor($floor) {
		self::$CurrentFloor = $floor;
	}
}

?>
<?php 
require_once __DIR__ . '/Node.php';

// SINGLETON - there should only be ONE elevator controller!
class ControllerNode extends Node {
	// class variables
	public static $Position = 0;
	public static $Speed = 0;
	public static $IsTravelling = 0;
	
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
			echo "There is already an instance of ControllerNode!<br>";
		}
		
		return static::$instance;
	}
	
	public function GetPosition() {
		return static::$Position;
	}
	
	public function SetPosition($pos) {
		static::$Position = $pos;
	}
	
	public function GetSpeed() {
		return static::$Speed;
	}
	
	public function SetSpeed($spd) {
		static::$Speed = $spd;
	}
	
	public function GetIsTravelling() {
		return static::$IsTravelling;
	}
	
	public function SetIsTravelling($travelling) {
		static::$IsTravelling = $travelling;
	}
}

?>